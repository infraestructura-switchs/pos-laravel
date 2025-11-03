<?php

namespace App\Http\Livewire\Admin\WarehouseTransfers;

use App\Models\WarehouseTransfer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;
use App\Models\StockMovement; // Importar el modelo
use App\Models\StockMovementDetail; // Importar el modelo
use Livewire\Component;
use Illuminate\Support\Facades\DB; // Asegúrate de usar DB para transacciones

class Create extends Component
{
    public $openCreate = false;
    public $origin_warehouse_id;
    public $destination_warehouse_id;
    public $user_id;
    public $description;
    public $date;
    public $status = 'pending';
    protected $listeners = ['openCreate'];

    public $details = [];

    // Declarar las propiedades públicas vacías para que Livewire las maneje
    public $warehouses = [];
    public $users = [];
    public $products = [];

    protected $rules = [
        'origin_warehouse_id' => 'required|exists:warehouses,id|different:destination_warehouse_id',
        'destination_warehouse_id' => 'required|exists:warehouses,id',
        'user_id' => 'required|exists:users,id',
        'description' => 'nullable|string|max:255',
        'date' => 'required|date',
        'details' => 'required|array|min:1',
        'details.*.product_id' => 'required|exists:products,id',
        'details.*.quantity' => 'required|numeric|min:0.01',
        'details.*.unit_cost' => 'required|numeric|min:0',
    ];

    // NO usar mount() para cargar datos aquí en este caso
    // public function mount()
    // {
    //     $this->warehouses = Warehouse::all(); // No hacer esto
    // }

    public function render()
    {
        // Cargar los datos en el render() como lo hace StockMovements
        // Usar pluck('label', 'value') para que sea un array asociativo
        $this->warehouses = Warehouse::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->users = User::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->products = Product::orderBy('name', 'ASC')->pluck('name', 'id');

        return view('livewire.admin.warehouse-transfers.create');
    }

    public function openCreate()
    {
        $this->resetValidation();
        $this->reset(['origin_warehouse_id', 'destination_warehouse_id', 'user_id', 'description', 'date', 'status']);
        $this->details = [];
        $this->addDetail();
        $this->openCreate = true;
    }

    public function addDetail()
    {
        $this->details[] = [
            'product_id' => '',
            'quantity' => 0,
            'unit_cost' => 0,
            'total_cost' => 0,
        ];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function updatedDetails($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) === 3 && $parts[1] === 'quantity') {
            $index = $parts[0];
            $quantity = $this->details[$index]['quantity'];
            $unit_cost = $this->details[$index]['unit_cost'];
            $this->details[$index]['total_cost'] = $quantity * $unit_cost;
        }
        if (count($parts) === 3 && $parts[1] === 'unit_cost') {
            $index = $parts[0];
            $quantity = $this->details[$index]['quantity'];
            $unit_cost = $this->details[$index]['unit_cost'];
            $this->details[$index]['total_cost'] = $quantity * $unit_cost;
        }
    }

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            // Generar folio único (ejemplo simple)
            $folio = 'TR-' . strtoupper(uniqid());

            $transfer = WarehouseTransfer::create([
                'origin_warehouse_id' => $this->origin_warehouse_id,
                'destination_warehouse_id' => $this->destination_warehouse_id,
                'user_id' => $this->user_id,
                'description' => $this->description,
                'transfer_date' => $this->date,
                'status' => $this->status, // Generalmente será 'pending'
                'folio' => $folio,
            ]);

            foreach ($this->details as $detail) {
                // --- Calcular el total_cost explícitamente ---
                $detailTotalCost = floatval($detail['quantity']) * floatval($detail['unit_cost']);

                $transferDetail = $transfer->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_cost' => $detail['unit_cost'],
                    'total_cost' => $detailTotalCost, // <-- Usar el valor calculado aquí
                ]);

                // --- Nueva Lógica: Crear Stock Movements ---
                // Generar folios únicos para los movimientos de stock basados en el folio del traspaso y el ID del detalle
                $folioStockOut = 'OUT-' . $folio . '-' . $transferDetail->id;
                $folioStockIn = 'IN-' . $folio . '-' . $transferDetail->id;

                // Convertir a float explícitamente antes de calcular el total_cost para StockMovementDetail
                $quantity = floatval($detail['quantity']);
                $unit_cost = floatval($detail['unit_cost']);
                $total_cost_calculated = $quantity * $unit_cost;

                // Movimiento de Salida (OUT) desde la bodega de origen
                $stockMovementOut = StockMovement::create([
                    'warehouse_id' => $this->origin_warehouse_id,
                    'user_id' => $this->user_id,
                    'stock_movements_date' => $this->date,
                    'folio' => $folioStockOut,
                    'concept' => 'Salida por Traspaso ID: ' . $transfer->id,
                    'note' => 'Traspaso desde ' . $transfer->originWarehouse->name . ' a ' . $transfer->destinationWarehouse->name,
                    'movement_type' => 'OUT',
                ]);

                StockMovementDetail::create([
                    'stock_movements_id' => $stockMovementOut->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $quantity,
                    'unit_cost' => $unit_cost,
                    'total_cost' => $total_cost_calculated,
                    'movement_type' => 'OUT',
                ]);

                // Movimiento de Entrada (IN) hacia la bodega de destino
                $stockMovementIn = StockMovement::create([
                    'warehouse_id' => $this->destination_warehouse_id,
                    'user_id' => $this->user_id,
                    'stock_movements_date' => $this->date,
                    'folio' => $folioStockIn,
                    'concept' => 'Entrada por Traspaso ID: ' . $transfer->id,
                    'note' => 'Traspaso desde ' . $transfer->originWarehouse->name . ' a ' . $transfer->destinationWarehouse->name,
                    'movement_type' => 'IN',
                ]);

                StockMovementDetail::create([
                    'stock_movements_id' => $stockMovementIn->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $quantity,
                    'unit_cost' => $unit_cost,
                    'total_cost' => $total_cost_calculated,
                    'movement_type' => 'IN',
                ]);
            }
            // foreach ($this->details as $detail) {
            //     $transferDetail = $transfer->details()->create([
            //         'product_id' => $detail['product_id'],
            //         'quantity' => $detail['quantity'],
            //         'unit_cost' => $detail['unit_cost'],
            //         'total_cost' => $detail['total_cost'], // Asegura que este también se guarde en el detalle del traspaso si es necesario
            //     ]);

            //     // --- Nueva Lógica: Crear Stock Movements ---
            //     // Generar folios únicos para los movimientos de stock basados en el folio del traspaso y el ID del detalle
            //     $folioStockOut = 'OUT-' . $folio . '-' . $transferDetail->id;
            //     $folioStockIn = 'IN-' . $folio . '-' . $transferDetail->id;

            //     // Convertir a float explícitamente antes de calcular el total_cost para StockMovementDetail
            //     $quantity = floatval($detail['quantity']);
            //     $unit_cost = floatval($detail['unit_cost']);
            //     $total_cost_calculated = $quantity * $unit_cost;

            //     // Movimiento de Salida (OUT) desde la bodega de origen
            //     $stockMovementOut = StockMovement::create([
            //         'warehouse_id' => $this->origin_warehouse_id,
            //         'user_id' => $this->user_id,
            //         'stock_movements_date' => $this->date,
            //         'folio' => $folioStockOut,
            //         'concept' => 'Salida por Traspaso ID: ' . $transfer->id,
            //         'note' => 'Traspaso desde ' . $transfer->originWarehouse->name . ' a ' . $transfer->destinationWarehouse->name,
            //         'movement_type' => 'OUT',
            //     ]);

            //     StockMovementDetail::create([
            //         'stock_movements_id' => $stockMovementOut->id,
            //         'product_id' => $detail['product_id'],
            //         'quantity' => $quantity, // Usar el valor convertido
            //         'unit_cost' => $unit_cost, // Usar el valor convertido
            //         'total_cost' => $total_cost_calculated, // Usar el valor calculado y convertido
            //         'movement_type' => 'OUT',
            //     ]);

            //     // Movimiento de Entrada (IN) hacia la bodega de destino
            //     $stockMovementIn = StockMovement::create([
            //         'warehouse_id' => $this->destination_warehouse_id,
            //         'user_id' => $this->user_id,
            //         'stock_movements_date' => $this->date,
            //         'folio' => $folioStockIn,
            //         'concept' => 'Entrada por Traspaso ID: ' . $transfer->id,
            //         'note' => 'Traspaso desde ' . $transfer->originWarehouse->name . ' a ' . $transfer->destinationWarehouse->name,
            //         'movement_type' => 'IN',
            //     ]);

            //     StockMovementDetail::create([
            //         'stock_movements_id' => $stockMovementIn->id,
            //         'product_id' => $detail['product_id'],
            //         'quantity' => $quantity, // Usar el valor convertido
            //         'unit_cost' => $unit_cost, // Usar el valor convertido
            //         'total_cost' => $total_cost_calculated, // Usar el valor calculado y convertido
            //         'movement_type' => 'IN',
            //     ]);
            // }

            DB::commit();

            $this->emit('transferCreated');
            $this->openCreate = false;
            $this->emit('success', 'Movimiento de stock y detalles creados con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('error', 'Error al crear el movimiento de stock: ' . $e->getMessage());
        }
    }

    public function closeCreate()
    {
        $this->openCreate = false;
    }
}
