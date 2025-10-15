<?php

namespace App\Http\Livewire\Admin\CashOpening;

use App\Models\CashOpening;
use App\Models\Terminal;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $terminal_id = '';
    public $status = '';
    
    public $terminals;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->terminals = Terminal::all();
    }

    public function render()
    {
        $query = CashOpening::query()
            ->with(['user', 'terminal', 'cashClosing'])
            ->latest('opened_at');

        // Filtro por término de búsqueda (usuario o terminal)
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('user', function($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                })
                ->orWhereHas('terminal', function($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            });
        }

        // Filtro por terminal
        if ($this->terminal_id) {
            $query->where('terminal_id', $this->terminal_id);
        }

        // Filtro por estado
        if ($this->status !== '') {
            $query->where('is_active', $this->status == '1');
        }

        $cashOpenings = $query->paginate(15);

        return view('livewire.admin.cash-opening.index', [
            'cashOpenings' => $cashOpenings
        ])->layoutData(['title' => 'Aperturas de Caja']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTerminalId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }
}