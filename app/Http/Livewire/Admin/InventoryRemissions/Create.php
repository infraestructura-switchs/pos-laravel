<?php

namespace App\Http\Livewire\Admin\InventoryRemissions;

use App\Models\InventoryRemission;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Product;
use App\Models\RemissionDetail;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $openCreate = false;
    public $warehouse_id = "", $user_id = "", $folio, $remission_date, $concept, $note;
    public $warehouses, $users, $products;
    public $details = []; 

    protected $listeners = ['openCreate', 'refreshWarehouses', 'refreshUsers', 'refresh' => '$refresh'];

    public function mount()
    {
        $this->details = [[]]; 
    }

    public function openCreate()
    {
        $this->reset(['warehouse_id', 'user_id', 'folio', 'remission_date', 'concept', 'note', 'details']);
        $this->details = [[]]; 
        $this->openCreate = true;
    }

    public function closeCreate()
    {
        $this->openCreate = false;
        $this->reset(['warehouse_id', 'user_id', 'folio', 'remission_date', 'concept', 'note', 'details']);
        $this->details = [[]]; 
    }

    public function updatedDetails($value)
    {
        foreach (array_keys($this->details) as $index) {
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
        $this->details[] = []; 
    }

    public function removeDetail($index)
    {
        if (isset($this->details[$index])) {
            unset($this->details[$index]);
            $this->details = array_values(array_filter($this->details)); 
            if (empty($this->details)) {
                $this->details = [[]]; 
            }
        }
    }

    public function render()
    {
        $this->warehouses = Warehouse::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->users = User::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->products = Product::orderBy('name', 'ASC')->pluck('name', 'id');

        return view('livewire.admin.inventory_remissions.create');
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

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            foreach (array_keys($this->details) as $index) {
                $this->calculateTotalCost($index);
            }

            $remission = InventoryRemission::create([
                'warehouse_id' => $this->warehouse_id,
                'user_id' => $this->user_id,
                'folio' => $this->folio,
                'remission_date' => $this->remission_date,
                'concept' => $this->concept,
                'note' => $this->note,
            ]);

            $detailsToSave = array_filter($this->details, function ($detail) {
                return !empty($detail['product_id']);
            });
            if (empty($detailsToSave)) {
                throw new \Exception('Debe agregar al menos un producto.');
            }
            foreach ($detailsToSave as $detail) {
                RemissionDetail::create([
                    'remission_id' => $remission->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'] ?? 0,
                    'unit_cost' => $detail['unit_cost'] ?? 0,
                    'total_cost' => $detail['total_cost'] ?? 0,
                ]);
            }

            DB::commit();

            $this->openCreate = false;
            $this->reset(['warehouse_id', 'user_id', 'folio', 'remission_date', 'concept', 'note', 'details']);
            $this->details = [[]];
            $this->emitTo('admin.inventory-remissions.index', 'render');
            $this->emit('success', 'Remisión y detalles creados con éxito');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->emit('error', 'Error al crear la remisión: ' . $e->getMessage());
        }
    }
}
