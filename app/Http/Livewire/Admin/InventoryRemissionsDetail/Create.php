<?php

namespace App\Http\Livewire\Admin\InventoryRemissionsDetail;

use App\Models\InventoryRemission;
use App\Models\Product;
use App\Models\RemissionDetail;
use Livewire\Component;
use App\Traits\LivewireTrait;

class Create extends Component
{
    use LivewireTrait;

    protected $listeners = ['openCreate', 'refreshProducts', 'openCreateWithRemission'];

    public $openCreate = false;
    public $remission_id = "", $product_id = "", $quantity, $unit_cost, $total_cost;

    public $products;

    public function openCreate()
    {
        $this->reset(['remission_id', 'product_id', 'quantity', 'unit_cost', 'total_cost']);
        $this->openCreate = true;
    }

    public function openCreateWithRemission($id)
    {
        $this->remission_id = $id;
        $this->openCreate = true;
    }

    public function updatedQuantity()
    {
        $this->calculateTotalCost();
    }

    public function closeCreate()
{
    $this->openCreate = false;
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

        return view('livewire.admin.inventory_remissions_detail.create');
    }

    public function refreshProducts()
    {
        $this->products = Product::orderBy('name', 'ASC')->pluck('name', 'id');
    }

    protected $rules = [
        'remission_id' => 'required|exists:inventory_remissions,id',
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:0',
        'unit_cost' => 'required|numeric|min:0',
        'total_cost' => 'required|numeric|min:0',
    ];

    public function store()
    {
        $this->validate();

        $this->calculateTotalCost(); 

        RemissionDetail::create([
            'remission_id' => $this->remission_id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
        ]);

        $this->openCreate = false;
        $this->reset(['remission_id', 'product_id', 'quantity', 'unit_cost', 'total_cost']);
        $this->emitTo('admin.inventory-remissions.index', 'render');
        $this->emit('success', 'Detalle de remisión creado con éxito');
    }
}