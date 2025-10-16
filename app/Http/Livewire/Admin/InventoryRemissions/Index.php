<?php

namespace App\Http\Livewire\Admin\InventoryRemissions;

use App\Models\InventoryRemission;
use App\Models\RemissionDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryRemissionsExport;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = '';
    public $selectedRemissionId = null;
    public $filters = [
        'active' => 'Activos',
        'inactive' => 'Inactivos',
    ];

    protected $inventoryRemissions;
    protected $remissionDetails;

    protected $listeners = ['render'];

    public function render()
    {
        $query = InventoryRemission::query();

        if ($this->search) {
            $query->where('folio', 'like', '%' . $this->search . '%')
                  ->orWhereHas('warehouse', function ($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        if ($this->filter) {
            // Add filter logic if needed
        }

        $this->inventoryRemissions = $query->paginate(10);

        if ($this->selectedRemissionId) {
            $this->remissionDetails = RemissionDetail::where('remission_id', $this->selectedRemissionId)->paginate(10);
        } else {
            $this->remissionDetails = collect();
        }

        return view('livewire.admin.inventory_remissions.index', [
            'inventoryRemissions' => $this->inventoryRemissions,
            'remissionDetails' => $this->remissionDetails,
        ]);
    }

    public function selectRemission($id)
    {
        $this->selectedRemissionId = $id;
    }

    public function editRemission($id)
    {
        $this->emitTo('admin.inventory-remissions.edit', 'openEdit', InventoryRemission::find($id));
    }

public function downloadPdf($id)
{
    return redirect()->route('pdf.remission', $id);
}

    public function exportInventoryRemissions()
    {
        return Excel::download(new InventoryRemissionsExport, 'remissions.xlsx');
    }
}