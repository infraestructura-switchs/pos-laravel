<?php

namespace App\Http\Livewire\Admin\Warehouses;

use App\Models\Warehouse;
use Livewire\Component;

class Show extends Component
{
    public Warehouse $warehouse;

    public function render()
    {
        return view('livewire.admin.warehouses.show')->layoutData(['title' => 'Ver almacén']);
    }
}


