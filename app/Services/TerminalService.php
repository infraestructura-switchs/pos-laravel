<?php

namespace App\Services;

use App\Models\Terminal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use App\Exceptions\CustomException;

class TerminalService
{
    public static function verifyTerminal(): void
    {
        if (auth()->user()->terminals()->active()->get()->count() === 0) {
            throw new CustomException('No se ha configurado una terminal para este usuario');
        }
    }

    public function create(array $data): int
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|min:5|max:50|unique:terminals,name',
            'numbering_range_id' => 'required|exists:numbering_ranges,id',
            'factus_numbering_range_id' => 'nullable|integer',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return DB::transaction(function () use ($data) {
            $terminal = Terminal::create([
                'name' => $data['name'],
                'numbering_range_id' => $data['numbering_range_id'],
                'factus_numbering_range_id' => $data['factus_numbering_range_id'] ?? null,
            ]);

            if (!empty($data['users'])) {
                $terminal->users()->syncWithoutDetaching($data['users']);
            }

            return $terminal->id;
        });
    }

    public function update(int $id, array $data): bool
    {
        $terminal = Terminal::findOrFail($id);

        $validator = Validator::make($data, [
            'name' => [ 'sometimes', 'string', 'min:5', 'max:50',
                Rule::unique('terminals', 'name')->ignore($terminal->id),
            ],
            'numbering_range_id' => 'sometimes|exists:numbering_ranges,id',
            'factus_numbering_range_id' => 'nullable|integer',
            'status' => 'sometimes|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return DB::transaction(function () use ($terminal, $data) {
            $terminal->update([
                'name' => $data['name'] ?? $terminal->name,
                'numbering_range_id' => $data['numbering_range_id'] ?? $terminal->numbering_range_id,
                'factus_numbering_range_id' => $data['factus_numbering_range_id'] ?? $terminal->factus_numbering_range_id,
                'status' => $data['status'] ?? $terminal->status,
            ]);

            if (isset($data['users'])) {
                $terminal->users()->sync($data['users']);
            }

            return true;
        });
    }

    public function getById(int $id): Terminal
    {
        return Terminal::with('users')->findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = Terminal::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['numbering_range_id'])) {
            $query->where('numbering_range_id', $filters['numbering_range_id']);
        }

        if (!empty($filters['factus_numbering_range_id'])) {
            $query->where('factus_numbering_range_id', $filters['factus_numbering_range_id']);
        }

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortOrder = $filters['sort_order'] ?? 'ASC';
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }
}
