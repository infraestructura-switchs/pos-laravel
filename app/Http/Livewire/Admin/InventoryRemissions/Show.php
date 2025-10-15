<?php

namespace App\Http\Livewire\Admin\InventoryRemissions;

use App\Models\InventoryRemission;
use Livewire\Component;

class Show extends Component
{
    public InventoryRemission $inventoryRemission;

    public function render()
    {
        return view('livewire.admin.inventory_remissions.show')->layoutData(['title' => 'Ver remisión']);
    }
}


