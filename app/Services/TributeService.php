<?php

namespace App\Services;

use App\Models\Tribute;
use Illuminate\Support\Facades\Validator;

class TributeService
{
    public function create(array $input): int
    {
        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'api_tribute_id' => 'nullable|integer'
        ]);

        $validator->validate();

        $tribute = Tribute::create([
            'name' => $input['name'],
            'description' => $input['description'],
            'api_tribute_id' => $input['api_tribute_id'] ?? 0,
            'status' => '0',
        ]);

        return $tribute->id;
    }

    public function update(int $id, array $input): bool
    {
        $tribute = Tribute::findOrFail($id);

        $validator = Validator::make($input, [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
            'api_tribute_id' => 'nullable|integer',
            'status' => 'required|in:0,1',
        ]);

        $validator->validate();

        $tribute->update($input);

        return true;
    }

    public function getById(int $id): Tribute
    {
        return Tribute::findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = Tribute::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['api_tribute_id'])) {
            $query->where('api_tribute_id', $filters['api_tribute_id']);
        }

        $orderBy = $filters['order_by'] ?? 'id';
        $orderDir = $filters['order_dir'] ?? 'ASC';
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy($orderBy, $orderDir)->paginate($perPage);
    }
}
