<?php

namespace App\Http\Livewire\Admin\WarehouseTransfers;

use App\Models\WarehouseTransfer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    // Filtrar por defecto solo traspasos completados
    public $filter = 'completed';
    public $selectedTransferId = null;
    public $transferDetails; // Detalles del traspaso seleccionado

    protected $listeners = [
        'transferCreated' => '$refresh',
        'transferUpdated' => '$refresh',
    ];

    public function render()
    {
        $query = WarehouseTransfer::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('folio', 'like', '%' . $this->search . '%')
                  ->orWhereHas('originWarehouse', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('destinationWarehouse', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        // Filtrar por estado, por defecto 'completed'
        if ($this->filter) {
            $query->where('status', $this->filter);
        }

        $transfers = $query->with(['originWarehouse', 'destinationWarehouse', 'user'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        $filters = [
            '' => 'Todos los estados',
            'pending' => 'Pendientes',
            'completed' => 'Completados',
            'cancelled' => 'Cancelados',
        ];

        return view('livewire.admin.warehouse-transfers.index', compact('transfers', 'filters'));
    }

    public function selectTransfer($id)
    {
        $this->selectedTransferId = $id;
        // Cargar los detalles del traspaso seleccionado
        $this->transferDetails = WarehouseTransfer::find($id)->details;
    }

    public function editTransfer($id)
    {
        $this->emitTo('admin.warehouse-transfers.edit', 'openEdit', $id);
    }
}