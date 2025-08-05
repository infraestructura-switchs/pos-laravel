<?php

namespace App\Services;

use App\Models\TaxRate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaxRateService
{
    public function create(array $input): int
    {
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'has_percentage' => ['required', 'boolean'],
            'rate' => ['required', 'numeric', 'min:0'],
            'default' => ['required', 'boolean'],
            'tribute_id' => ['required', 'exists:tributes,id'],
        ]);

        $validator->validate();

        $taxRate = TaxRate::create($input);

        return $taxRate->id;
    }

    public function update(int $id, array $input): bool
    {
        $validator = Validator::make($input, [
            'name' => ['sometimes', 'string', 'max:255'],
            'has_percentage' => ['sometimes', 'boolean'],
            'rate' => ['sometimes', 'numeric', 'min:0'],
            'default' => ['sometimes', 'boolean'],
            'status' => ['required', Rule::in(['0', '1'])],
            'tribute_id' => ['sometimes', 'exists:tributes,id'],
        ]);

        $validator->validate();

        $taxRate = TaxRate::findOrFail($id);
        $taxRate->update($input);

        return true;
    }

    public function getById(int $id): TaxRate
    {
        return TaxRate::findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = TaxRate::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderBy(
            $filters['order_by'] ?? 'id',
            $filters['order_direction'] ?? 'asc'
        );

        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }
}
