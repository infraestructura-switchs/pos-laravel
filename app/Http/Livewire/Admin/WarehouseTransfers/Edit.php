<?php

namespace App\Http\Livewire\Admin\WarehouseTransfers;

use App\Models\WarehouseTransfer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\DB; // Asegúrate de usar DB para transacciones

class Edit extends Component
{
    public $openEdit = false;
    public $transferId;
    public $origin_warehouse_id;
    public $destination_warehouse_id;
    public $user_id;
    public $description;
    public $date;
    public $status;

    public $details = [];

    protected $listeners = ['openEdit'];

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
        'status' => 'required|in:pending,cancelled', // No se puede editar a 'completed' aquí, se completa aparte
        'details' => 'required|array|min:1',
        'details.*.product_id' => 'required|exists:products,id',
        'details.*.quantity' => 'required|numeric|min:0.01',
        'details.*.unit_cost' => 'required|numeric|min:0',
    ];

    // NO usar mount() para cargar datos aquí en este caso
    // public function mount()
    // {
    //     $this->warehouses = Warehouse::all(); // No hacer esto
    //     // ...
    // }

    public function render()
    {
        // Cargar los datos en el render() como lo hace StockMovements
        // Usar pluck('label', 'value') para que sea un array asociativo
        $this->warehouses = Warehouse::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->users = User::orderBy('name', 'ASC')->pluck('name', 'id');
        $this->products = Product::orderBy('name', 'ASC')->pluck('name', 'id');

        return view('livewire.admin.warehouse-transfers.edit');
    }

    public function openEdit($id)
    {
        $transfer = WarehouseTransfer::with('details')->findOrFail($id);

        $this->transferId = $transfer->id;
        $this->origin_warehouse_id = $transfer->origin_warehouse_id;
        $this->destination_warehouse_id = $transfer->destination_warehouse_id;
        $this->user_id = $transfer->user_id;
        $this->description = $transfer->description;
        $this->date = $transfer->transfer_date->format('Y-m-d');
        $this->status = $transfer->status;

        $this->details = $transfer->details->map(fn($d) => [
            'id' => $d->id,
            'product_id' => $d->product_id,
            'quantity' => $d->quantity,
            'unit_cost' => $d->unit_cost,
            'total_cost' => $d->total_cost,
        ])->toArray();

        $this->openEdit = true;
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

    public function update()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $transfer = WarehouseTransfer::findOrFail($this->transferId);

            // No se permite editar si ya está completado o cancelado
            if (!in_array($transfer->status, ['pending'])) {
                throw new \Exception('No se puede editar un traspaso que no esté pendiente.');
            }

            $transfer->update([
                'origin_warehouse_id' => $this->origin_warehouse_id,
                'destination_warehouse_id' => $this->destination_warehouse_id,
                'user_id' => $this->user_id,
                'description' => $this->description,
                'transfer_date' => $this->date,
                'status' => $this->status,
            ]);

            $transfer->details()->delete();

            foreach ($this->details as $detail) {
                $transfer->details()->create([
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'unit_cost' => $detail['unit_cost'],
                    'total_cost' => $detail['total_cost'],
                ]);
            }

            DB::commit();

            $this->emit('transferUpdated');
            $this->openEdit = false;
            $this->dispatch('notify', 'Traspaso actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', 'Error al actualizar el traspaso: ' . $e->getMessage());
        }
    }

    public function closeEdit()
    {
        $this->openEdit = false;
    }
}