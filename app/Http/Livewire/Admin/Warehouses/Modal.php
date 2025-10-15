<?php

namespace App\Http\Livewire\Admin\Warehouses;

use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class Modal extends Component {

    use WithPagination;

    protected $listeners = ['openModal'];

    public $openModal=false, $search, $filter='1';

    public $filters = [
        1 => 'Nombres'
    ];

    public function render() {

         $filter = 'name';

        $warehouses = Warehouse::where($filter, 'LIKE', '%' . $this->search . '%')
                    ->where('status', '0')
                    ->orderBy($filter, 'ASC')
                    ->paginate(10);

        return view('livewire.admin.warehouses.modal', compact('warehouses'));
    }

    public function openModal(){
        $this->reset();
        $this->openModal = true;
    }

    public function updatedSearch(){
        $this->resetPage();
    }

    public function updatedFilter(){
        $this->resetPage();
    }

    public function selected(Warehouse $warehouse){
        $this->emitTo('admin.purchases.create', 'setWarehouse', $warehouse->id);
        $this->reset();
    }
}
