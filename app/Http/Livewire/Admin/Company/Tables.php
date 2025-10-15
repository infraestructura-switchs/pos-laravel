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

        // Obtener el número actual de mesas ACTIVAS
        $this->numberOfTables = Order::where('is_active', true)->count();

    }

  
    public function updateTables()
{
    
    
    $this->validate();

    // Obtener todas las órdenes ACTIVAS existentes
    $existingOrders = Order::where('is_active', true)->get();
    $currentCount = $existingOrders->count();



    if ($this->numberOfTables > $currentCount) {
        // Agregar mesas faltantes

        for ($i = $currentCount + 1; $i <= $this->numberOfTables; $i++) {
            $newOrder = Order::create([
                'name' => $this->tablePrefix . ' ' . $i,
                'customer' => [], // Array vacío, no string
                'products' => [], // Array vacío, no string
                'total' => 0,
                'delivery_address' => '',
                'is_active' => true // Asegurar que esté activa
            ]);

        }
    } elseif ($this->numberOfTables < $currentCount) {
        // Desactivar mesas excedentes (solo las que estén vacías)

        $ordersToDeactivate = Order::where('total', 0)
            ->where('is_active', true)
            ->whereJsonLength('customer', 0)  // Array vacío
            ->whereJsonLength('products', 0)  // Array vacío
            ->orderBy('id', 'desc')
            ->limit($currentCount - $this->numberOfTables)
            ->get();
        

        
        foreach ($ordersToDeactivate as $order) {
            $order->update(['is_active' => false]);

        }
    } else {

    }

    // Actualizar el prefijo de todas las mesas activas
    $this->updateTablePrefixes();

    $this->emit('success', 'Mesas actualizadas correctamente');
    
    // Emitir evento para actualizar las mesas en ventas rápidas
    $this->dispatchBrowserEvent('tables-updated');
}

    private function updateTablePrefixes()
    {
        \Log::info('🏷️ Actualizando prefijos de mesas', ['prefijo' => $this->tablePrefix]);
        
        // Obtener todas las mesas activas ordenadas por ID
        $activeTables = Order::where('is_active', true)
                           ->orderBy('id', 'asc')
                           ->get();
        
        // Actualizar el nombre de cada mesa con el nuevo prefijo
        foreach ($activeTables as $index => $table) {
            $newName = $this->tablePrefix . ' ' . ($index + 1);
            $oldName = $table->name;
            
            if ($oldName !== $newName) {
                $table->update(['name' => $newName]);
                \Log::info('🏷️ Prefijo actualizado:', [
                    'id' => $table->id, 
                    'nombre_anterior' => $oldName, 
                    'nombre_nuevo' => $newName
                ]);
            }
        }
        

    }

    public function render()
    {
        return view('livewire.admin.company.tables');
    }
}