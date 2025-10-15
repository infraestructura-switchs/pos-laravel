<?php

namespace App\Http\Livewire\Admin\Warehouses;

use App\Exports\WarehousesExport;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component {

    use WithPagination;

    protected $listeners = ['render'];

    public $search, $filter='1';

    public $filters = [
        1 => 'Nombre'
    ];

public function render()
{

    $filterField = 'name';

    $warehouses = Warehouse::where($filterField, 'LIKE', '%' . $this->search . '%')
        ->latest()
        ->paginate(10);

    return view('livewire.admin.warehouses.index', compact('warehouses'))
        ->layoutData(['title' => 'Bodegas']);
}


    public function updatedSearch(){
        $this->resetPage();
    }

    public function updatedFilter(){
        $this->resetPage();
    }

    public function exportWarehouses(){
        return Excel::download(new WarehousesExport(), 'Bodegas.xlsx');
    }
}
