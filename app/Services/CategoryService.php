<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function create(array $data): int
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Category::create([
            'name' => $data['name'],
        ])->id;
    }

    public function update(int $id, array $data): bool
    {
        $category = Category::findOrFail($id);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $category->update([
            'name' => $data['name'],
        ]);

        return true;
    }

    public function getById(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = Category::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        $perPage = $filters['per_page'] ?? 10;
        $orderBy = $filters['order_by'] ?? 'id';
        $direction = $filters['order_dir'] ?? 'asc';

        return $query->orderBy($orderBy, $direction)->paginate($perPage);
    }
}
