<?php

namespace App\Services;

use App\Models\Presentation;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class PresentationService
{
    public function create(array $input): int
    {
        $validator = Validator::make($input, [
            'name'       => 'required|string|max:255',
            'price'      => 'required|integer|min:0',
            'quantity'   => 'required|integer|min:1',
            'product_id' => 'required|exists:products,id',
        ]);

        $validator->validate();

        $presentation = Presentation::create($input);

        return $presentation->id;
    }

    public function update(int $id, array $input): bool
    {
        $presentation = Presentation::findOrFail($id);

        $validator = Validator::make($input, [
            'name'       => 'sometimes|string|max:255',
            'price'      => 'sometimes|integer|min:0',
            'quantity'   => 'sometimes|integer|min:1',
            'status'     => 'sometimes|in:0,1',
            'product_id' => 'sometimes|exists:products,id',
        ]);

        $validator->validate();

        return $presentation->update($input);
    }

    public function getById(int $id): Presentation
    {
        return Presentation::findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = Presentation::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['price'])) {
            $query->where('price', $filters['price']);
        }

        if (isset($filters['quantity'])) {
            $query->where('quantity', $filters['quantity']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        $orderBy = $filters['orderBy'] ?? 'id';
        $orderDir = $filters['orderDir'] ?? 'asc';
        $perPage  = $filters['perPage'] ?? 10;

        return $query->orderBy($orderBy, $orderDir)->paginate($perPage);
    }
}
