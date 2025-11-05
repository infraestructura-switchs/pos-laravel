<?php

namespace App\Http\Livewire;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $saleTotal = 0;

    public $costTotal = 0;

    public $filterDate = 6;

    public $startDate = null;

    public $endDate = null;

    protected $enableDomains = [
        ''
    ];

    public function mount()
    {
        if ($this->isDomainEnabled()) {
            $this->initializeDateRange();
        }
    }

    public function render()
    {
        if ($this->isDomainEnabled()) {
            $this->getData();
        }

        return view('livewire.dashboard')->with('enableDomains', $this->enableDomains)->layoutData(['title' => 'Dashboard']);
    }

    protected function getData()
    {
        $dataFrom = Carbon::parse('31-07-2024')->startOfDay();

        $bills = Bill::select(DB::raw('sum(cost) as cost, sum(total) as total'))
            ->where('created_at', '>=', $dataFrom)
            ->date($this->filterDate, $this->startDate, $this->endDate)
            ->status(1)
            ->first();

        $this->saleTotal = $bills->total;
        $this->costTotal = $bills->cost;
    }

    /**
     * Verifica si el dominio actual está habilitado
     */
    public function isDomainEnabled(): bool
    {
        return in_array(request()->getHost(), $this->enableDomains)
        || request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1'
        || str_contains(request()->getHost(), centralDomain());
    }

    /**
     * Inicializa el rango de fechas con el mes actual
     */
    private function initializeDateRange(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }
}
