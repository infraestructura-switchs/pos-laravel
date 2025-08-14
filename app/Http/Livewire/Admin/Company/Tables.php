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
        \Log::info('🚀 Tables component montado');
        // Obtener el número actual de mesas ACTIVAS
        $this->numberOfTables = Order::where('is_active', true)->count();
        \Log::info('📊 Número inicial de mesas activas:', ['count' => $this->numberOfTables]);
    }

  
    public function updateTables()
{
    \Log::info('🔧 updateTables() llamado', [
        'numberOfTables' => $this->numberOfTables,
        'tablePrefix' => $this->tablePrefix
    ]);
    
    $this->validate();

    // Obtener todas las órdenes ACTIVAS existentes
    $existingOrders = Order::where('is_active', true)->get();
    $currentCount = $existingOrders->count();

    \Log::info('📊 Estado actual:', [
        'currentCount' => $currentCount,
        'requestedTables' => $this->numberOfTables
    ]);

    if ($this->numberOfTables > $currentCount) {
        // Agregar mesas faltantes
        \Log::info('➕ Agregando mesas', ['desde' => $currentCount + 1, 'hasta' => $this->numberOfTables]);
        for ($i = $currentCount + 1; $i <= $this->numberOfTables; $i++) {
            $newOrder = Order::create([
                'name' => $this->tablePrefix . ' ' . $i,
                'customer' => [], // Array vacío, no string
                'products' => [], // Array vacío, no string
                'total' => 0,
                'delivery_address' => '',
                'is_active' => true // Asegurar que esté activa
            ]);
            \Log::info('✅ Mesa creada:', ['id' => $newOrder->id, 'name' => $newOrder->name]);
        }
    } elseif ($this->numberOfTables < $currentCount) {
        // Desactivar mesas excedentes (solo las que estén vacías)
        \Log::info('➖ Desactivando mesas', ['cantidad' => $currentCount - $this->numberOfTables]);
        $ordersToDeactivate = Order::where('total', 0)
            ->where('is_active', true)
            ->whereJsonLength('customer', 0)  // Array vacío
            ->whereJsonLength('products', 0)  // Array vacío
            ->orderBy('id', 'desc')
            ->limit($currentCount - $this->numberOfTables)
            ->get();
        
        \Log::info('🔍 Mesas a desactivar:', $ordersToDeactivate->pluck('id', 'name')->toArray());
        
        foreach ($ordersToDeactivate as $order) {
            $order->update(['is_active' => false]);
            \Log::info('❌ Mesa desactivada:', ['id' => $order->id, 'name' => $order->name]);
        }
    } else {
        \Log::info('✅ No hay cambios en cantidad');
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
        
        \Log::info('✅ Prefijos actualizados correctamente');
    }

    public function render()
    {
        return view('livewire.admin.company.tables');
    }
}