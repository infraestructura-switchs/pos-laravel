<?php

namespace App\Http\Livewire\Admin\Company;

use App\Models\Order;
use Livewire\Component;

class Tables extends Component
{
    public $numberOfTables = 20;
    public $tablePrefix = 'Mesa';

    protected $rules = [
        'numberOfTables' => 'required|integer|min:1|max:100',
        'tablePrefix' => 'required|string|max:20'
    ];

    public function mount()
    {
        // Obtener el número actual de mesas
        $this->numberOfTables = Order::count();
    }

    public function updateTables()
    {
        $this->validate();

        // Obtener todas las órdenes existentes
        $existingOrders = Order::all();
        $currentCount = $existingOrders->count();

        if ($this->numberOfTables > $currentCount) {
            // Agregar mesas faltantes
            for ($i = $currentCount + 1; $i <= $this->numberOfTables; $i++) {
                Order::create([
                    'name' => $this->tablePrefix . ' ' . $i,
                    'customer' => '[]',
                    'products' => '[]',
                    'total' => 0,
                    'delivery_address' => ''
                ]);
            }
        } elseif ($this->numberOfTables < $currentCount) {
            // Eliminar mesas excedentes (solo las que estén vacías)
            $ordersToDelete = Order::where('total', 0)
                ->where('customer', '[]')
                ->where('products', '[]')
                ->orderBy('id', 'desc')
                ->limit($currentCount - $this->numberOfTables)
                ->get();
            
            foreach ($ordersToDelete as $order) {
                $order->delete();
            }
        }

        // Renombrar todas las mesas con el nuevo prefijo
        $orders = Order::orderBy('id')->get();
        foreach ($orders as $index => $order) { 
            $order->update([
                'name' => $this->tablePrefix . ' ' . ($index + 1)
            ]);
        }

        $this->emit('success', 'Mesas actualizadas correctamente');
    }

    public function render()
    {
        return view('livewire.admin.company.tables');
    }
}