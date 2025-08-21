<?php

namespace App\Http\Livewire\Admin\QuickSale;

use App\Exceptions\CustomException;
use App\Http\Controllers\Log;
use App\Models\Bill;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Services\BillService;
use App\Services\DetailBillService;
use App\Services\DocumentTaxService;
use App\Services\FactusConfigurationService;
use App\Services\TerminalService;
use App\Traits\OrderTrait;
use App\Traits\UtilityTrait;
use App\Utilities\CalcItemValues;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Orders extends Component
{
    use OrderTrait;
    use UtilityTrait;

    public $orders = [];

    protected $listeners = [
        'tables-updated' => 'loadOrders',
        'refresh-orders' => 'loadOrders'
    ];

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = $this->getOrders()->toArray();
    }

    public function refreshOrders()
    {
        $this->loadOrders();
        $this->emit('orders-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.quick-sale.orders');
    }


    public function getOrders()
    {
        try {
            \Log::info('🔍 getOrders() llamado desde JavaScript');

            // Filtrar solo las mesas activas
            $orders = Order::where('is_active', 1)
                ->orderBy('id', 'asc')
                ->get();



            // Mapear los datos para asegurar consistencia
            $mappedOrders = $orders->map(function ($order) {
                // Verificar si la mesa está ocupada (tiene productos o total > 0)
                $hasProducts = !empty($order->products) && count($order->products) > 0;
                $hasTotal = $order->total > 0;
                $isOccupied = $hasProducts || $hasTotal;

                // Solo asignar cliente por defecto si la mesa está ocupada
                $customer = $order->customer;
                if ($isOccupied && (empty($customer) || !isset($customer['names']))) {
                    $customer = ['names' => 'Consumidor Final'];
                } elseif (!$isOccupied) {
                    // Mesa disponible - no mostrar cliente
                    $customer = ['names' => ''];
                }

                return [
                    'id' => $order->id,
                    'name' => $order->name,
                    'products' => is_array($order->products) ? $order->products : [],
                    'customer' => $customer,
                    'total' => intval($order->total ?? 0),
                    'delivery_address' => $order->delivery_address ?? '',
                    'is_available' => $isOccupied ? false : true  // Mesa ocupada = no disponible
                ];
            });



            return $mappedOrders;
        } catch (\Exception $e) {
            \Log::error('❌ Error en getOrders():', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Retornar array vacío en caso de error
            return collect([]);
        }
    }
    public function store($order)
    {

        if (! collect($order['products'])->count()) {
            return $this->emit('alert', 'Agrega uno o mas productos');
        }

        if (! Order::find($order['id'])->is_available) {
            return $this->emit('alert', 'Esta mesa se encuentra ocupada. Selecciona otra mesa');
        }

        $this->updateOrder($order);

        $this->emit('success', 'Orden guardada con éxito');

        return 'success';
    }

    public function update($order)
    {
        // Verificar que products existe antes de acceder a él
        if (!isset($order['products']) || !collect($order['products'])->count()) {
            return $this->emit('alert', 'Agrega uno o mas productos');
        }

        if (Order::find($order['id'])->is_available) {
            return $this->emit('alert', 'Esta mesa no se encuentra ocupada. Actualiza la vantana del navegador');
        }

        $this->updateOrder($order);

        $this->emit('success', 'Orden actualizada con éxito');

        return 'success';
    }

    public function updateCustomer(array $orderArray)
    {
        // Verificar si es "Factura en caja" (sin ID) o una mesa (con ID)
        if (!isset($orderArray['id']) || empty($orderArray['id'])) {
            // Es "Factura en caja" - no necesita actualizar BD, solo validar cliente
            if (! count($orderArray['customer']) || ! is_array($orderArray['customer'])) {
                $this->emit('alert', 'Selecciona un cliente');
                return;
            }

            if (! Customer::where('id', $orderArray['customer']['id'])->exists()) {
                $this->emit('alert', "El cliente {$orderArray['customer']['names']} no se encuentra registrado");
                return;
            }

            // Para Factura en caja, solo emitir éxito sin guardar en BD
            $this->emit('success', 'Cliente seleccionado para factura en caja');
            return 'success';
        }

        // Es una mesa con ID - lógica original
        $order = Order::find($orderArray['id']);

        // Validación original pero continuar después de mostrar advertencia
        if ($order->is_available) {
            $this->emit('alert', $order->name . ' no contiene una orden');
            // No hacer return aquí - continuar con el proceso
        }

        if (! count($orderArray['customer']) || ! is_array($orderArray['customer'])) {
            $this->emit('alert', 'Selecciona un cliente');
            return;
        }

        if (! Customer::where('id', $orderArray['customer']['id'])->exists()) {
            $this->emit('alert', "El cliente {$orderArray['customer']['names']} no se encuentra registrado");
            return;
        }

        $order->customer = $orderArray['customer'];
        $order->save();

        $this->emit('success', 'Cliente actualizado con éxito');

        // Emitir evento para refrescar las mesas
        $this->dispatchBrowserEvent('customer-updated');

        // También recargar las mesas del componente
        $this->loadOrders();

        return 'success';
    }

    public function updateTable(array $fromOrder, array $toOrder)
    {
        $fromOrder = Order::find($fromOrder['id']);
        $toOrder = Order::find($toOrder['id']);

        if (! $toOrder->is_available) {
            $this->emit('alert', "La {$toOrder['name']} está ocupada");

            return;
        }

        if ($fromOrder->is_available) {
            $this->emit('alert', 'Ha ocurrido un error inesperado. Vuelve a intentarlo');

            return;
        }

        try {
            DB::beginTransaction();

            $toOrder->customer = $fromOrder->customer;
            $toOrder->products = $fromOrder->products;
            $toOrder->total = $fromOrder->total;
            $toOrder->save();

            $this->destroy($fromOrder->id);

            DB::commit();
        } catch (\Throwable $th) {
            $this->emit('alert', 'Ha ocurrido un error inesperado. Vuelve al intentarlo');
            DB::rollBack();

            return;
        }

        $this->emit('success', 'Orden cambiada de mesa con éxito');

        return 'success';
    }

    public function destroy($order_id)
    {
        $order = Order::find($order_id);

        $orderDefault = [
            'name' => 'Mesa ' . $order->id,
            'customer' => [],
            'products' => [],
            'total' => 0,
            'delivery_address' => ''
        ];

        $order->fill($orderDefault);
        $order->save();

        $this->emit('success', 'Orden eliminada con éxito');

        return 'success';
    }

    public function storeBill(array $order, $cash, $tip, $paymentMethod)
    {
        if (! $order['is_available']) {
            if (Order::find($order['id'])->is_available) {
                return;
            }
        }

        $products = collect($order['products']);
        $productsDB = BillService::getUniqueProductsDB(clone $products);
        BillService::addCostToItems($products, $productsDB);

        try {
            TerminalService::verifyTerminal();
            BillService::validateInventory($products, $productsDB);
            BillService::calcTotales($products, $productsDB);
            $products = CalcItemValues::taxes($products);
        } catch (CustomException $ce) {
            return $this->emit('error', $ce->getMessage());
        }

        $lock = Cache::lock('createBill', 30);

        try {
            $lock->block(30);

            DB::beginTransaction();

            $bill = BillService::store($products, $paymentMethod, $cash, $order['customer']['id'], $tip, '');

            foreach ($products as $product) {
                $detailBill = DetailBillService::store($bill, $product);
                BillService::updateUnitsOrStock($product, $productsDB);
                DocumentTaxService::calcTaxRatesForItems($detailBill, $product['tax_rates']);
            }

            DocumentTaxService::calcTaxRatesForDocument($bill);

            BillService::updateStock($productsDB);

            if (array_key_exists('id', $order)) {
                $this->destroy($order['id']);
            }

            DB::commit();
        } catch (LockTimeoutException $e) {
            Log::error($e);
            DB::rollBack();

            return $this->emit('error', 'Ha ocurrido un error inesperado. Vuelve a intentarlo');
        } catch (CustomException $ce) {
            DB::rollBack();

            return $this->emit('error', $ce->getMessage());
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage(), $products->toArray(), $th->getLine());

            return $this->emit('error', 'Ha sucedido un error inesperado. Vuelve a intentarlo');
        } finally {
            optional($lock)->release();
        }

        if (FactusConfigurationService::isApiEnabled()) {
            if ($this->validateElectronicBill($bill)) {
                return;
            }
        }

        $this->emit('success', 'Factura registrada con éxito');
        $this->dispatchBrowserEvent('quick-sale-print-ticket', $bill->id);

        return 'success';
    }

    public function validateElectronicBill(Bill $bill)
    {
        try {
            BillService::validateElectronicBill($bill);
        } catch (CustomException $ce) {
            Log::error($ce->getMessage());

            return $this->emit('error', $ce->getMessage());
        } catch (ValidationException $ce) {
            $errors = $ce->errors();
            foreach ($errors as $field => $errorMessages) {
                foreach ($errorMessages as $errorMessage) {
                    $this->addError($field, $errorMessage);
                }
            }

            return 'error';
        } catch (\Throwable $th) {
            Log::error($th->getMessage(), [], $th->getLine());

            return $this->emit('error', 'Ha sucedido un error inesperado. Vuelve a intentarlo');
        }
    }

    public function createBillOnlyPrint(array $order, $isCommand = false)
    {
        $products = collect($order['products']);

        $tip = $this->calculateTip($products->sum('total'));

        $data = [
            'customer' => [
                'identification' => $order['customer']['no_identification'],
                'names' => $order['customer']['names'],
            ],
            'bill' => [
                'place' => $order['name'],
                'subtotal' => $products->sum('total') + $tip,
                'tip' => $tip,
                'total' => $products->sum('total') + $tip,
                'user_name' => auth()->user()->name,
                'format_created_at' => Carbon::parse(now())->format('d-m-Y g:i A'),
            ],
            'products' => $products,
            'delivery_address' => $order['delivery_address'] ?? '',
        ];
        if (!$isCommand) {
            $this->dispatchBrowserEvent('print-pre-ticket', $data);
        } else {
            $this->dispatchBrowserEvent('print-command-bill', $data);
        }

        return 'success';
    }

    public function calculateTip($subtotal)
    {
        $company = Company::find(1);
        $percentage_tip = $company->percentage_tip;

        return (int) $subtotal * $percentage_tip / 100;
    }
}
