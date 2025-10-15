<?php

namespace App\Http\Livewire\Admin\InventoryRemissionsDetail;

use App\Models\InventoryRemission;
use App\Models\Product;
use App\Models\RemissionDetail;
use Livewire\Component;
use App\Traits\LivewireTrait;

class Edit extends Component
{
    use LivewireTrait;

    protected $listeners = ['openEdit'];

    public $openEdit = false;
    public $remissionDetail;
    public $remission_id, $product_id, $quantity, $unit_cost, $total_cost;

    public $products;

    protected function rules()
    {
        return [
            'remission_id' => 'required|exists:inventory_remissions,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
        ];
    }

    public function updatedQuantity()
    {
        $this->calculateTotalCost();
    }

    public function updatedUnitCost()
    {
        $this->calculateTotalCost();
    }

    private function calculateTotalCost()
    {
        if (is_numeric($this->quantity) && is_numeric($this->unit_cost)) {
            $this->total_cost = $this->quantity * $this->unit_cost;
        } else {
            $this->total_cost = 0;
        }
    }

    public function render()
    {
        $this->products = Product::orderBy('name', 'ASC')->pluck('name', 'id');

        return view('livewire.admin.inventory_remissions_detail.edit');
    }

    public function openEdit(RemissionDetail $remissionDetail)
    {
        $this->remissionDetail = $remissionDetail;
        $this->remission_id = $remissionDetail->remission_id;
        $this->product_id = $remissionDetail->product_id;
        $this->quantity = $remissionDetail->quantity;
        $this->unit_cost = $remissionDetail->unit_cost;
        $this->total_cost = $remissionDetail->total_cost;

        $this->resetValidation();
        $this->openEdit = true;
    }

    public function closeEdit()
{
    $this->openEdit = false;
}

    public function update()
    {
        $this->validate();

        $this->calculateTotalCost(); 
        $this->remissionDetail->update([
            'remission_id' => $this->remission_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
        ]);

        $this->emit('success', 'Detalle de remisión actualizado con éxito');
        $this->emitTo('admin.inventory-remissions.index', 'render');
        $this->reset();
        $this->openEdit = false;
    }
}