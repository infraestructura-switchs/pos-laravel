<?php

namespace App\Services;

use App\Models\User;
use App\Http\Controllers\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function create(array $data): int
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:7|max:20',
            'email' => 'required|string|email|max:250|unique:users,email',
            'role' => 'required|string|exists:roles,name',
            'password' => 'required|string|min:8|max:250|confirmed',
            'password_confirmation' => 'required|string|min:8|max:250',
        ]);

        $validator->validate();

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole($data['role']);

            DB::commit();

            return $user->id;
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), $data);
            throw new \Exception('Ha ocurrido un error al registrar el usuario. Vuelve a intentarlo');
        }
    }

    public function update(int $id, array $data): bool
    {
        $user = User::findOrFail($id);

        if ($user->id === 1 || $user->hasRole('Administrador')) {
            throw new \Exception('No está permitido editar este usuario.');
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'nullable|confirmed|min:6',
            'password_confirmation' => 'required|string|min:8|max:250',
            'status' => 'sometimes|in:0,1',
            'role' => 'sometimes|exists:roles,name|not_in:Administrador',
        ]);

        $validator->validate();

        $user->fill(collect($data)->except(['password', 'role'])->toArray());

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return true;
    }

    public function getById(int $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = User::with('roles');

        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (!empty($filters['phone'])) {
            $query->where('phone', 'like', "%{$filters['phone']}%");
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'like', "%{$filters['email']}%");
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', fn($q) => $q->where('name', $filters['role']));
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $orderBy = $filters['orderBy'] ?? 'id';
        $orderDir = $filters['orderDir'] ?? 'asc';

        $perPage = $filters['perPage'] ?? 10;

        return $query->orderBy($orderBy, $orderDir)->paginate($perPage);
    }
}
