<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function create(array $data): int
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web'
            ]);

            if (!empty($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->id;
        });
    }

    public function update(int $id, array $data): bool
    {
        $role = Role::findOrFail($id);

        if (strtolower($role->name) === 'administrador') {
            throw new \Exception('No se puede editar el rol de administrador');
        }

        return DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name']]);

            $role->syncPermissions($data['permissions'] ?? []);

            return true;
        });
    }

    public function getById(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = Role::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (!empty($filters['guard_name'])) {
            $query->where('guard_name', $filters['guard_name']);
        }

        return $query->withCount(['permissions'])->paginate($filters['per_page'] ?? 10);
    }
}
