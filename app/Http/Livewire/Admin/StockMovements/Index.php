<?php

namespace App\Http\Livewire\Admin\StockMovements;

use App\Exports\StockMovementsExport;
use App\Exports\StockMovementDetailsExport;
use App\Models\StockMovement;
use App\Models\StockMovementDetail;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Index extends Component
{
    use WithPagination;

    protected $listeners = ['render'];

    public $search, $filter = '1', $selectedStockMovementId = null;
    public $filters = [
        1 => 'Folio',
        2 => 'Almacén'
    ];

    public function selectStockMovement($id)
    {
        $this->selectedStockMovementId = $id;
    }

    public function editStockMovement($id)
    {
        $movement = StockMovement::findOrFail($id);
        $this->emitTo('admin.stock-movements.edit', 'openEdit', $movement);
        $this->selectStockMovement($id);
    }

    public function render()
    {
        $stockMovements = StockMovement::with('warehouse', 'user', 'remission')
            ->when($this->search, function ($query) {
                if ($this->filter == '1') {
                    $query->where('folio', 'LIKE', '%' . $this->search . '%');
                } elseif ($this->filter == '2') {
                    $query->whereHas('warehouse', function ($q) {
                        $q->where('name', 'LIKE', '%' . $this->search . '%');
                    });
                }
            })
            ->latest()
            ->paginate(10);

        if ($this->selectedStockMovementId) {
            $movementDetails = StockMovementDetail::with(['product', 'stockMovement'])
                ->where('stock_movements_id', $this->selectedStockMovementId)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $movementDetails = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }

        return view('livewire.admin.stock_movements.index', compact('stockMovements', 'movementDetails'))
            ->layoutData(['title' => 'Movimientos de Stock y Detalles']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function exportStockMovements()
    {
        return Excel::download(new StockMovementsExport(), 'MovimientosStock.xlsx');
    }

    public function exportStockMovementDetails()
    {
        if (is_null($this->selectedStockMovementId)) {
            return null;
        }
        return Excel::download(new StockMovementDetailsExport($this->selectedStockMovementId), 'Detalles_MovimientoStock.xlsx');
    }
}