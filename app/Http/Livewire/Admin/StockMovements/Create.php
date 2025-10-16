<?php

namespace App\Http\Livewire\Admin\StockMovements;

use App\Models\StockMovement;
use App\Models\StockMovementDetail;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\InventoryRemission;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $openCreate = false;
    public $warehouse_id = "", $remission_id, $user_id, $stock_movements_date, $folio, $concept, $note;
    public $warehouses, $users, $products, $remissions;
    public $details = []; 

    protected $listeners = ['openCreate', 'refreshWarehouses', 'refreshUsers', 'refresh' => '$refresh'];

    public function mount()
    {
        $this->details = [[]]; 
    }

    public function openCreate()
    {
        $this->reset(['warehouse_id', 'remission_id', 'user_id', 'stock_movements_date',  'details']);
        $this->details = [[]]; 
        $this->openCreate = true;
    }

    public function closeCreate()
    {
        $this->openCreate = false;
        $this->reset(['warehouse_id', 'remission_id', 'user_id', 'stock_movements_date', 'details']);
        $this->details = [[]];
    }

    public function updatedDetails($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) < 2) {
            return; 
        }

        $index = $parts[1]; 
        $key = end($parts); 

        if (!isset($this->details[$index])) {
            return; 
        }

        $this->details[$index][$key] = $value ?? null;

        if (in_array($key, ['quantity', 'unit_cost'])) {
            $this->calculateTotalCost($index);  
        }
    }

    private function calculateTotalCost($index)
    {
        $quantity = floatval($this->details[$index]['quantity'] ?? 0);  
        $unit_cost = floatval($this->details[$index]['unit_cost'] ?? 0);  
        $this->details[$index]['total_cost'] = $quantity * $unit_cost;  
    }


    public function addDetail()
    {
        $this->details[] = ['adjustment_type' => 'increment']; 

    }

    public function removeDetail($index)
    {
        if (isset($this->details[$index])) {
            unset($this->details[$index]);
            $this->details = array_values(array_filter($this->details)); 
            if (empty($this->details)) {
                $this->details = [['adjustment_type' => 'increment']]; 
            }

        }
    }

    public function render()
    {
        $this->warehouses = Warehouse::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->users = User::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->products = Product::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->remissions = InventoryRemission::orderBy('folio', 'ASC')->pluck('folio', 'id');

        return view('livewire.admin.stock_movements.create');
    }

    public function refreshWarehouses()
    {
        $this->warehouses = Warehouse::orderBy('name', 'ASC')->pluck('name', 'id');
    }

    public function refreshUsers()
    {
        $this->users = User::orderBy('name', 'ASC')->pluck('name', 'id');
    }

    protected $rules = [
        'warehouse_id' => 'required|exists:warehouses,id',
        'remission_id' => 'nullable|exists:inventory_remissions,id',
        'user_id' => 'nullable|exists:users,id',
        'stock_movements_date' => 'required|date',
        'details.*.product_id' => 'required|exists:products,id',
        'details.*.quantity' => 'required|numeric|min:0',
        'details.*.unit_cost' => 'required|numeric|min:0',
        'details.*.adjustment_type' => 'required|in:increment,decrement',
        'details.*.total_cost' => 'nullable|numeric|min:0',
    ];

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            foreach (array_keys($this->details) as $index) {
                $this->calculateTotalCost($index);
            }

            $movement = StockMovement::create([
                'warehouse_id' => $this->warehouse_id,
                'remission_id' => $this->remission_id,
                'user_id' => $this->user_id,
                'stock_movements_date' => $this->stock_movements_date,

            ]);

            $detailsToSave = array_filter($this->details, function ($detail) {
                return !empty($detail['product_id']);
            });
            if (empty($detailsToSave)) {
                throw new \Exception('Debe agregar al menos un producto.');
            }
            foreach ($detailsToSave as $detail) {
                $product = Product::findOrFail($detail['product_id']);
                $quantity = floatval($detail['quantity'] ?? 0);

                $detailModel = StockMovementDetail::create([
                    'stock_movements_id' => $movement->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $quantity,
                    'unit_cost' => floatval($detail['unit_cost'] ?? 0),
                    'total_cost' => floatval($detail['total_cost'] ?? 0),
                    'adjustment_type' => $detail['adjustment_type'] ?? 'increment',
                ]);

                if ($detail['adjustment_type'] === 'decrement') {
                    $product->decrement('stock', $quantity);
                } else {
                    $product->increment('stock', $quantity);
                }
            }

            DB::commit();

            $this->openCreate = false;
            $this->reset(['warehouse_id', 'remission_id', 'user_id', 'stock_movements_date', 'folio', 'concept', 'note', 'details']);
            $this->details = [['adjustment_type' => 'increment']]; 
            $this->emitTo('admin.stock-movements.index', 'render');
            $this->emit('success', 'Movimiento de stock y detalles creados con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('error', 'Error al crear el movimiento de stock: ' . $e->getMessage());

        }
    }
}