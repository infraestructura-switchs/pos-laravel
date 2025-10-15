<?php

namespace App\Http\Livewire\Admin\StockMovements;

use App\Models\StockMovement;
use App\Models\StockMovementDetail;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\InventoryRemission;
use Livewire\Component;
use App\Traits\LivewireTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class Edit extends Component
{
    use LivewireTrait;

    public $openEdit = false;
    public $stockMovement;
    public $warehouse_id = "", $remission_id, $user_id, $stock_movements_date;
    public $warehouses, $users, $products, $remissions;
    public $details = [];

    protected $listeners = ['openEdit', 'refreshWarehouses', 'refreshUsers', 'addDetail', 'removeDetail', 'refresh' => '$refresh'];

    public function mount()
    {
        $this->details = [];
    }

    public function render()
    {
        $this->warehouses = Warehouse::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->users = User::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->products = Product::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->remissions = InventoryRemission::orderBy('folio', 'ASC')->pluck('folio', 'id');

        return view('livewire.admin.stock_movements.edit');
    }

    public function refreshWarehouses()
    {
        $this->warehouses = Warehouse::orderBy('name', 'ASC')->pluck('name', 'id');
    }

    public function refreshUsers()
    {
        $this->users = User::orderBy('name', 'ASC')->pluck('name', 'id');
    }

    protected function rules()
    {
        return [
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
    }

    public function updated($property, $value)
    {
        if (str_starts_with($property, 'details.')) {
            $parts = explode('.', $property);
            if (count($parts) == 3) {
                $index = $parts[1];
                $key = $parts[2];

                if (!isset($this->details[$index])) {
                    return;
                }

                $this->details[$index][$key] = $value ?? null;

                if (in_array($key, ['quantity', 'unit_cost'])) {
                    $this->calculateTotalCost($index);
                }
            }
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

    public function openEdit(StockMovement $stockMovement)
    {
        $this->stockMovement = $stockMovement;
        $this->warehouse_id = $stockMovement->warehouse_id;
        $this->remission_id = $stockMovement->remission_id;
        $this->user_id = $stockMovement->user_id;
        $this->stock_movements_date = $stockMovement->stock_movements_date;

        $stockMovement->load('stockMovementDetails');

        $detailsCollection = $stockMovement->stockMovementDetails ?? collect([]);
        $this->details = $detailsCollection->map(function ($detail) {
            return [
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unit_cost' => $detail->unit_cost,
                'total_cost' => $detail->total_cost,
                'adjustment_type' => $detail->adjustment_type ?? 'increment',
            ];
        })->toArray();

        if (empty($this->details)) {
            $this->details = [['adjustment_type' => 'increment']];
        }

        foreach (array_keys($this->details) as $index) {
            $this->calculateTotalCost($index);
        }

        $this->resetValidation();
        $this->openEdit = true;
    }

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $oldDetails = $this->stockMovement->stockMovementDetails ?? collect([]);
            foreach ($oldDetails as $oldDetail) {
                $product = Product::findOrFail($oldDetail->product_id);
                $oldQuantity = floatval($oldDetail->quantity);
                $oldType = $oldDetail->adjustment_type ?? 'increment';

                if ($oldType === 'decrement') {
                    $product->increment('stock', $oldQuantity);
                } else {
                    $product->decrement('stock', $oldQuantity);
                }
            }

            $this->stockMovement->stockMovementDetails()->delete();

            foreach (array_keys($this->details) as $index) {
                $this->calculateTotalCost($index);
            }

            $this->stockMovement->update([
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

                StockMovementDetail::create([
                    'stock_movements_id' => $this->stockMovement->id,
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

            $this->emit('success', 'Movimiento de stock y detalles actualizados con éxito');
            $this->emitTo('admin.stock-movements.index', 'render');
            $this->resetExcept('types');
            $this->stockMovement = null;
            $this->details = [['adjustment_type' => 'increment']];
            $this->openEdit = false;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('error', 'Error al actualizar el movimiento de stock: ' . $e->getMessage());
        }
    }

    public function closeEdit()
    {
        $this->openEdit = false;
        $this->details = [['adjustment_type' => 'increment']];
        $this->resetValidation();
    }
}
