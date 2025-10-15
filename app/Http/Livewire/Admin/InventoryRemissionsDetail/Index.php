<?php

namespace App\Http\Livewire\Admin\InventoryRemissionsDetail;

use App\Exports\RemissionDetailsExport;
use App\Models\RemissionDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['render'];
    public $remissionDetails;

    public $search, $filter = '1';
    public $filters = [
        1 => 'Producto',
        2 => 'Remisión'
    ];


public function render()
    {

        $this->remissionDetails = RemissionDetail::with(['product', 'remission'])
            ->orderBy('created_at', 'desc')
            ->get(); 

        return view('livewire.admin.inventory-remissions.index');
    }


    public function updatedSearch()
    {
        $this->resetPage(); 
    }


    public function updatedFilter()
    {
        $this->resetPage(); 
    }


    public function exportRemissionDetails()
    {
        return Excel::download(new RemissionDetailsExport(), 'Detalles_Remision.xlsx');
    }
}
