<?php

namespace App\Http\Livewire\Admin\InventoryRemissions;

use App\Models\InventoryRemission;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\RemissionDetail;
use App\Models\Product;
use Livewire\Component;
use App\Traits\LivewireTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    use LivewireTrait;

    public $openEdit = false;
    public $inventoryRemission;
    public $warehouse_id = "", $user_id = "", $folio, $remission_date, $concept, $note;
    public $warehouses, $users, $products;
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

        return view('livewire.admin.inventory_remissions.edit');
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
            'user_id' => 'required|exists:users,id',
            'folio' => 'required|string|max:20',
            'remission_date' => 'required|date',
            'concept' => 'required|string|max:50',
            'note' => 'nullable|string|max:200',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.quantity' => 'required|numeric|min:0',
            'details.*.unit_cost' => 'required|numeric|min:0',
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
        $this->details[] = []; 
    }

    public function removeDetail($index)
    {
        if (isset($this->details[$index])) {
            unset($this->details[$index]);
            $this->details = array_values(array_filter($this->details)); 
            if (empty($this->details)) {
                $this->details = []; 
            }
        }
    }

    public function openEdit(InventoryRemission $inventoryRemission)
    {
        $this->inventoryRemission = $inventoryRemission;
        $this->warehouse_id = $inventoryRemission->warehouse_id;
        $this->user_id = $inventoryRemission->user_id;
        $this->folio = $inventoryRemission->folio;
        $this->remission_date = $inventoryRemission->remission_date;
        $this->concept = $inventoryRemission->concept;
        $this->note = $inventoryRemission->note;
        $this->details = $inventoryRemission->remissionDetails->map(function ($detail) {
            return [
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unit_cost' => $detail->unit_cost,
                'total_cost' => $detail->total_cost,
            ];
        })->toArray();

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
            foreach (array_keys($this->details) as $index) {
                $this->calculateTotalCost($index);
            }

            $this->inventoryRemission->update([
                'warehouse_id' => $this->warehouse_id,
                'user_id' => $this->user_id,
                'folio' => $this->folio,
                'remission_date' => $this->remission_date,
                'concept' => $this->concept,
                'note' => $this->note,
            ]);

            $this->inventoryRemission->remissionDetails()->delete();

            $detailsToSave = array_filter($this->details, function ($detail) {
                return !empty($detail['product_id']);
            });
            if (empty($detailsToSave)) {
                throw new \Exception('Debe agregar al menos un producto.');
            }
            foreach ($detailsToSave as $detail) {
                RemissionDetail::create([
                    'remission_id' => $this->inventoryRemission->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'] ?? 0,
                    'unit_cost' => $detail['unit_cost'] ?? 0,
                    'total_cost' => $detail['total_cost'] ?? 0,
                ]);
            }

            DB::commit();

            $this->emit('success', 'Remisión y detalles actualizados con éxito');
            $this->emitTo('admin.inventory-remissions.index', 'render');
            $this->resetExcept('types');
            $this->inventoryRemission = null;
            $this->details = []; 
            $this->openEdit = false;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('error', 'Error al actualizar la remisión: ' . $e->getMessage());
        }
    }

    public function closeEdit()
    {
        $this->openEdit = false;
        $this->details = []; 
        $this->resetValidation();
    }
}
