<?php

namespace App\Services;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Models\Bill;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Presentation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class ProductService
{
    public function store(array $input): int
    {
        $rules = [
            'barcode' => 'required|string|unique:products',
            'reference' => 'required|string|unique:products',
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|min:3|max:250',
            'cost' => 'required|integer|min:0|max:99999999',
            'price' => 'required|integer|min:0|max:99999999',
            'has_inventory' => 'required|min:0|max:1',
            'stock' => 'required|integer|min:0|max:99999999',
            'units' => 'required|integer|min:0|max:99999999',
            'has_presentations' => 'required|integer|min:0|max:1',
            'quantity' => 'nullable|exclude_if:has_presentations,1|integer|min:1|max:99999999',
            'presentations' => 'nullable|exclude_if:has_presentations,1|array|min:1',
            'tax_rates' => 'array|min:1',
            'tax_rates.*.id' => 'required|integer|exists:tax_rates,id',
            'tax_rates.*.value' => 'required|integer|min:0|max:999999999',
        ];

        $messages = [
            'presentations.min' => 'Debes agregar una o más presentaciones',
        ];

        $attributes = [
            'name' => 'nombre',
            'quantity' => 'unidades x producto',
            'presentations' => 'presentaciones',
            'tax_rates' => 'impuestos',
        ];

        if (isset($input['cost'], $input['price']) && $input['cost'] >= $input['price']) {
            throw ValidationException::withMessages(['cost' => 'El costo no debe ser mayor o igual al precio.']);
        }

        $data = Validator::make($input, $rules, $messages, $attributes)->validate();

        if (!intval($data['has_presentations'])) {
            if ($data['units'] >= $data['quantity']) {
                throw ValidationException::withMessages(['units' => 'Las unidades no pueden ser iguales o superiores las unidades por producto.']);
            }
            $data['units'] = $data['stock'] * $data['quantity'] + $data['units'];
        }

        $data['tax_rate_id'] = 5;

        DB::beginTransaction();

        try {
            $product = Product::create($data);

            $product->taxRates()->attach(
                collect($data['tax_rates'])->mapWithKeys(fn($item) => [$item['id'] => ['value' => $item['value']]])
            );

            if (!intval($data['has_presentations'])) {
                foreach ($data['presentations'] ?? [] as $item) {
                    if ($item['quantity'] > $data['quantity']) {
                        throw ValidationException::withMessages([
                            'presentation' => "La cantidad supera las unidades por producto de la presentación {$item['name']}."
                        ]);
                    }
                    $item['product_id'] = $product->id;
                    Presentation::create($item);
                }
            }

            DB::commit();

            return $product->id;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Product $product, array $input): bool
    {
        $validator = Validator::make($input, [
            'barcode' => 'required|string|unique:products,barcode,' . $product->id,
            'reference' => 'required|string|unique:products,reference,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|min:3|max:250',
            'cost' => 'required|integer|max:99999999',
            'price' => 'required|integer|max:99999999',
            'has_inventory' => 'required|min:0|max:1',
            'stock' => 'required|integer|min:0|max:9999999',
            'units' => 'required|integer|min:0|max:9999999',
            'top' => 'required|integer|min:0|max:1',
            'status' => 'required|integer|min:0|max:1',
            'has_presentations' => 'required|integer|min:0|max:1',
            'quantity' => 'exclude_if:has_presentations,1|required|integer|min:1|max:99999999',
            'presentations' => 'nullable|exclude_if:has_presentations,1|array|min:1',
            'tax_rates' => 'array|min:1',
            'tax_rates.*.id' => 'required|integer|exists:tax_rates,id',
            'tax_rates.*.value' => 'required|integer|min:0|max:999999999',
        ], [
            'presentations.min' => 'Agrega una o más presentaciones',
        ], [
            'name' => 'nombre',
            'units' => 'unidades',
            'quantity' => 'unidades x producto',
            'presentations' => 'presentaciones',
            'tax_rates' => 'impuestos',
        ]);

        $data = $validator->validate();

        if ($data['cost'] >= $data['price']) {
            throw ValidationException::withMessages([
                'cost' => 'El costo no debe ser mayor o igual al precio',
            ]);
        }

        if (!intval($data['has_presentations'])) {
            $data['units'] = ($data['stock'] * $data['quantity']) + $data['units'];
        }

        try {
            DB::beginTransaction();

            $product->fill(Arr::except($data, ['presentations']))->save();

            // Sincronizar impuestos
            $product->taxRates()->sync(
                collect($data['tax_rates'])->mapWithKeys(fn($item) => [
                    $item['id'] => ['value' => $item['value']]
                ])
            );

            // Presentaciones
            $product->presentations()->delete();

            if (!intval($data['has_presentations'])) {
                foreach ($data['presentations'] ?? [] as $presentation) {
                    if ($presentation['quantity'] > $data['quantity']) {
                        throw ValidationException::withMessages([
                            'presentation' => "La cantidad supera las unidades por producto de la presentación {$presentation['name']}"
                        ]);
                    }

                    $presentation['product_id'] = $product->id;
                    Presentation::create($presentation);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage(), ['product' => $product->toArray()]);
            throw $e;
        }

        return true;
    }

    public function getById(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public function getByFilters(array $filters)
    {
        $query = Product::query();

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['reference'])) {
            $query->where('reference', 'like', '%' . $filters['reference'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['has_inventory'])) {
            $query->where('has_inventory', $filters['has_inventory']);
        }

        $sortField = $filters['order_by'] ?? 'id';
        $sortOrder = $filters['order_dir'] ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        $query->with([
            'taxRates' => function ($q) {
                $q->where('status', '0')->select('tax_rates.id', 'name', 'has_percentage', 'rate');
            },
            'presentations' => function ($q) {
                $q->where('status', '0')->select('id', 'name', 'price', 'quantity', 'product_id');
            },
        ]);

        $perPage = $filters['per_page'] ?? 10;

        return $query->select('id', 'barcode', 'reference', 'category_id', 'name', 'cost', 'price', 'stock', 'status')
            ->paginate($perPage);
    }

    public function canImport(): bool
    {
        return Bill::count() === 0 && Purchase::count() === 0;
    }

    public function import(UploadedFile $file): void
    {
        DB::beginTransaction();
        try {
            $this->deleteExistingProducts();

            $path = $file->storeAs('files', "products." . $file->getClientOriginalExtension());
            Excel::import(new ProductsImport, $path);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Error al importar productos desde Excel: {$e->getMessage()}");
            throw $e;
        }
    }

    public function downloadTemplate(): string
    {
        $path = public_path('storage/xlsx-templates/plantilla-productos.xlsx');

        if (!file_exists($path)) {
            throw new \Exception('El archivo de plantilla no existe.');
        }

        $content = file_get_contents($path);
        return base64_encode($content);
    }

    protected function deleteExistingProducts(): void
    {
        DB::table('presentations')->delete();
        DB::table('product_tax_rate')->delete();
        DB::table('products')->delete();
    }

    public function exportFiltered(array $params): string
    {
        try {
            $file =  Excel::download(new ProductsExport(), 'Productos.xlsx');
            return base64_encode($file);
        } catch (\Throwable $th) {
            throw new \Exception('Ocurrio un error al exportar los productos.');
        }
    }
}
