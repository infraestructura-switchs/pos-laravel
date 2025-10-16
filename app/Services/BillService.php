<?php
namespace App\Services;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;
use App\Models\Bill;
use App\Models\Product;
use App\Services\Factro\FactroElectronicBillService;
use App\Services\Factus\ElectronicBillService;
use App\Services\FactusConfigurationService;
use App\Services\FactroConfigurationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Exception\RequestException;

class BillService
{
    public static function store(Collection $products, $payment_method_id, $cash, $customer_id, $observation, $tip = 0): Bill
    {
        $taxRate = $products->map(fn ($item) => ['tax_amount' => collect($item['tax_rates'])->sum('tax_amount')])->sum('tax_amount');

        $bill = Bill::create([
            'reference_code' => Str::uuid()->toString(),
            'payment_method_id' => $payment_method_id,
            'tip' => $tip,
            'cost' => $products->sum('cost'),
            'subtotal' => ($products->sum('total') - $taxRate) + $products->sum('discount'),
            'discount' => $products->sum('discount'),
            'total' => $products->sum('total'),
            'cash' => $cash,
            'status' => 1,
            'observation' => $observation,
            'terminal_id' => getTerminal()->id,
            'customer_id' => $customer_id,
            'user_id' => auth()->user()->id,
        ]);

        return $bill;
    }

    public static function validateInventory(Collection $products, Collection $productsDB): void
    {
        foreach ($productsDB as $productDB) {
            if (! intval($productDB->has_inventory)) {
                if ($productDB->has_presentations === '0') {
                    $result = $products->where('id', $productDB->id);
                    $presentations = $productDB->presentations;
                    $quantity = 0;
                    foreach ($result as $value) {
                        if ($presentations->contains($value['presentation']['id'])) {
                            $presentation = $presentations->find($value['presentation']['id']);
                            $quantity += $presentation->quantity * $value['amount'];
                        } else {
                            Log::error('No se encontro la presentacion', ['presentation' => $value['presentation']]);
                            throw new CustomException('Ha ocurrido un error inesperado al momento de registrar la factura');
                        }
                    }
                    if ($quantity > $productDB->units) {
                        Log::error('La cantidad excede el stock', ['product' => $productDB->toArray()]);
                        throw new CustomException("La cantidad del producto {$productDB->name} supera el stock");
                    }
                } else {
                    $amount = $products->where('id', $productDB->id)->sum('amount');
                    if ($amount > $productDB->stock) {
                        throw new CustomException("No se pudo completar la compra. Solo quedan {$productDB->stock} de {$productDB['name']} en el stock");
                    }
                }
            }
        }

        foreach ($products as $value) {
            if (array_key_exists('discount', $value) && $value['discount'] > ($value['price'] * $value['amount'])) {
                throw new CustomException("El valor del descuento supera al valor total del siguiente producto: {$value['name']}");
            }
        }
    }

    public static function getUniqueProductsDB(Collection $collection): Collection
    {
        $collection->transform(fn ($item) => $item['id']);

        return Product::whereIn('id', $collection->unique())->get();
    }

    public static function addCostToItems(Collection &$products, Collection $productDB): void
    {
        foreach ($products as $key => $value) {
            $product = $productDB->find($value['id']);

            if (! $product) {
                Log::error('No se encontro el producto en la DB', ['product' => $value]);
                throw new CustomException("No se encontro el producto {$value['name']} en la base de datos");
            }

            if (! intval($product->has_presentations)) {
                $presentation = $product->presentations->find($value['presentation']['id']);
                $units = $presentation->quantity * $value['amount'];
                $costForUnit = bcdiv($product->cost, $product->quantity, 2);
                $value['cost'] = bcdiv($costForUnit * $units, '1');
            } else {
                $value['cost'] = $product->cost * $value['amount'];
            }

            $products[$key] = $value;
        }
    }

    public static function updateStock(Collection $productsDB): void
    {
        foreach ($productsDB as $value) {
            if (intval($value->has_inventory)) {
                continue;
            }

            $product = Product::find($value->id);
            if (! intval($product->has_presentations)) {
                $product->stock = bcdiv($product->units / $product->quantity, '1');
                $product->save();
            }
        }
    }

    public static function calcTotales(Collection &$products, Collection $productsDB): void
    {
        foreach ($products as $key => $value) {
            $product = $productsDB->find($value['id']);

            if (! $product) {
                Log::error('No se encontro el producto en la DB', ['product' => $value]);
                throw new CustomException('Ha ocurrido un error inesperado. Vuelve a intentarlo');
            }

            if (! array_key_exists('discount', $value)) {
                $value['discount'] = 0;
            }

            if ($product->has_presentations === '0') {
                $presentation = $product->presentations->find($value['presentation']['id']);
                $value['price'] = $presentation->price;
                $value['total'] = ($presentation->price * $value['amount']) - $value['discount'];
            } else {
                $value['price'] = $product->price;
                $value['total'] = ($product->price * $value['amount']) - $value['discount'];
            }

            $products[$key] = $value;
        }
    }

    public static function updateUnitsOrStock(array $product, Collection $productsDB): void
    {
        $result = $productsDB->find($product['id']);

        if (intval($result->has_inventory)) {
            return;
        }

        $units = 0;

        if (! intval($result->has_presentations)) {
            $presentation = $result->presentations->find($product['presentation']['id']);
            $units = $presentation->quantity * $product['amount'];
            Product::where('id', $product['id'])->decrement('units', $units);
        } else {
            Product::where('id', $product['id'])->decrement('stock', $product['amount']);
        }
    }

    public static function validateElectronicBill(Bill $bill): void
    {
        // Registrar el estado de las APIs Factus y Factro
        Log::info('Estado de las APIs de facturación electrónica:', [
            'Factus API habilitada' => FactusConfigurationService::isApiEnabled(),
            'Factro API habilitada' => FactroConfigurationService::isApiEnabled(),
        ]);

        // Lógica existent
        if (FactusConfigurationService::isApiEnabled()) {
            BillService::validateElectronicBillFactus($bill);
        }

        if (FactroConfigurationService::isApiEnabled()) {
            BillService::validateElectronicBillFactro($bill);
        }
    }

    public static function validateElectronicBillFactus(Bill $bill): void
    {
        if (!FactusConfigurationService::isApiEnabled()) {
            Log::info('API FACTUS no habilitada, saltando', ['bill_id' => $bill->id]);
            return;
        }

        try {
            Log::info('Iniciando validación FACTUS', ['bill_id' => $bill->id]);
            $response = ElectronicBillService::validate($bill);
            $responseData = $response->json();
            
            ElectronicBillService::saveElectronicBill($responseData, $bill);
            
            Log::info('✅ Factura electrónica FACTUS validada exitosamente', [
                'bill_id' => $bill->id,
                'cufe' => $bill->fresh()->electronicBill->cufe ?? 'N/A'
            ]);
        } catch (CustomException $e) {
            Log::error('❌ Error de validación FACTUS', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('❌ Error inesperado FACTUS', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            throw new CustomException('Error al validar la factura electrónica con Factus: ' . $e->getMessage());
        }
    }

    public static function validateElectronicBillFactro(Bill $bill): void
    {
        if (!FactroConfigurationService::isApiEnabled()) {
            Log::info('API FACTRO no habilitada, saltando', ['bill_id' => $bill->id]);
            return;
        }

        try {
            Log::info('Iniciando validación ARQFE para FACTRO', ['bill_id' => $bill->id]);
            $response = FactroElectronicBillService::validate($bill);
            $responseData = $response->json();
            Log::info('Respuesta ARQFE recibida', ['status' => $response->status(), 'bill_id' => $bill->id]);

            FactroElectronicBillService::saveElectronicBill($response, $responseData, $bill);
            Log::info('Validación ARQFE guardada', ['bill_id' => $bill->id]);
        } catch (RequestException $e) {
            Log::error('Timeout en llamada a Factro', ['bill_id' => $bill->id, 'message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            Log::error('Error validación ARQFE', [
                'bill_id' => $bill->id,
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);
        }
    }

    public static function storeElectronicCreditNote(Bill $bill): void
    {
        if (FactusConfigurationService::isApiEnabled() && $bill->electronicBill && ! $bill->electronicCreditNote) {
            ElectronicBillService::storeCreditNote($bill);
        }
    }

    public static function validateElectronicCreditNote(Bill $bill): void
    {
        $bill->refresh();
        if (FactusConfigurationService::isApiEnabled() && $bill->electronicCreditNote) {
            $response = FactroElectronicBillService::validate($bill);
            $responseData = $response->json();
            FactroElectronicBillService::saveElectronicBill($response, $responseData, $bill); // Pasa $response
        }
    }

    public function create(array $input): int
    {
        $validator = Validator::make($input, [
            'reference_code'     => 'nullable|string|unique:bills,reference_code',
            'number'             => 'nullable|string|unique:bills,number',
            'cost'               => 'required|integer|min:0',
            'tip'                => 'required|integer|min:0',
            'subtotal'           => 'required|integer|min:0',
            'discount'           => 'required|integer|min:0',
            'total'              => 'required|integer|min:0',
            'cash'               => 'required|integer|min:0',
            'status'             => 'required|in:0,1',
            'observation'        => 'nullable|string',
            'terminal_id'        => 'required|exists:terminals,id',
            'customer_id'        => 'required|exists:customers,id',
            'user_id'            => 'required|exists:users,id',
            'payment_method_id'  => 'required|exists:payment_methods,id',
            'numbering_range_id' => 'nullable|exists:numbering_ranges,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $bill = Bill::create($input);

        return $bill->id;
    }

    public function update(int $id, array $input): bool
    {
        $bill = Bill::findOrFail($id);

        $validator = Validator::make($input, [
            'reference_code'     => 'nullable|string|unique:bills,reference_code,' . $id,
            'number'             => 'nullable|string|unique:bills,number,' . $id,
            'cost'               => 'sometimes|required|integer|min:0',
            'tip'                => 'sometimes|required|integer|min:0',
            'subtotal'           => 'sometimes|required|integer|min:0',
            'discount'           => 'sometimes|required|integer|min:0',
            'total'              => 'sometimes|required|integer|min:0',
            'cash'               => 'sometimes|required|integer|min:0',
            'status'             => 'sometimes|required|in:0,1',
            'observation'        => 'nullable|string',
            'terminal_id'        => 'sometimes|required|exists:terminals,id',
            'customer_id'        => 'sometimes|required|exists:customers,id',
            'user_id'            => 'sometimes|required|exists:users,id',
            'payment_method_id'  => 'sometimes|required|exists:payment_methods,id',
            'numbering_range_id' => 'nullable|exists:numbering_ranges,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $bill->update($input);

        return true;
    }

    public function getById(int $id): array
    {
        $bill = Bill::findOrFail($id);
        return $bill->toArray();
    }

    public function getByFilters(array $filters): array
    {
        $query = Bill::query();

        if (!empty($filters['reference_code'])) {
            $query->where('reference_code', 'like', '%' . $filters['reference_code'] . '%');
        }

        if (!empty($filters['number'])) {
            $query->where('number', 'like', '%' . $filters['number'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['terminal_id'])) {
            $query->where('terminal_id', $filters['terminal_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['payment_method_id'])) {
            $query->where('payment_method_id', $filters['payment_method_id']);
        }

        if (!empty($filters['numbering_range_id'])) {
            $query->where('numbering_range_id', $filters['numbering_range_id']);
        }

        $orderBy = $filters['order_by'] ?? 'id';
        $orderDir = $filters['order_dir'] ?? 'ASC';
        $perPage = $filters['per_page'] ?? 10;

        return $query->orderBy($orderBy, $orderDir)->paginate($perPage)->toArray();
    }
}
