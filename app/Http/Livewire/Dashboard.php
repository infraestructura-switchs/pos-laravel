<?php

namespace App\Http\Livewire;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalSales = 0;
    public $totalTickets = 0;
    public $averageTicket = 0;

    public $filterType = 'day'; // day, week, month, year

    public $salesChartEncoded;
    public $topProductsEncoded;

    protected $enableDomains = [
        ''
    ];

    public function mount()
    {
        if ($this->isDomainEnabled()) {
            $this->getData();
        }
    }

    public function render()
    {
        return view('livewire.dashboard')->with('enableDomains', $this->enableDomains)->layoutData(['title' => 'Dashboard']);
    }

    public function setFilter($type)
    {
        $this->filterType = $type;
        $this->getData();
    }

    protected function getData()
    {
        $query = Bill::query()->where('status', Bill::ACTIVA);

        $dateRange = $this->getDateRange();
        
        $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);

        // Metrics
        $this->totalTickets = (clone $query)->count();
        $this->totalSales = (clone $query)->sum('total');
        $this->averageTicket = $this->totalTickets > 0 ? $this->totalSales / $this->totalTickets : 0;

        // Sales Chart (Line Chart)
        $this->generateSalesChart($query, $dateRange);

        // Top Products Chart (Pie Chart)
        $this->generateTopProductsChart($dateRange);
    }

    protected function getDateRange()
    {
        $now = Carbon::now();
        
        switch ($this->filterType) {
            case 'day':
                return ['start' => $now->copy()->startOfDay(), 'end' => $now->copy()->endOfDay()];
            case 'week':
                return ['start' => $now->copy()->startOfWeek(), 'end' => $now->copy()->endOfWeek()];
            case 'month':
                return ['start' => $now->copy()->startOfMonth(), 'end' => $now->copy()->endOfMonth()];
            case 'year':
                return ['start' => $now->copy()->startOfYear(), 'end' => $now->copy()->endOfYear()];
            default:
                return ['start' => $now->copy()->startOfDay(), 'end' => $now->copy()->endOfDay()];
        }
    }

    protected function generateSalesChart($baseQuery, $dateRange)
    {
        $salesData = [];
        $categories = [];

        // Agrupación y formato según el filtro
        $groupBy = '';
        $dateFormat = '';

        if ($this->filterType === 'day') {
            // Por hora
            $groupBy = 'HOUR(created_at)';
            $dateFormat = 'H:00'; // 13:00
             $results = (clone $baseQuery)
                ->selectRaw('HOUR(created_at) as label, SUM(total) as value')
                ->groupBy('label')
                ->orderBy('label')
                ->get();
             
             // Rellenar horas vacías
             for($i=0; $i<24; $i++) {
                $found = $results->firstWhere('label', $i);
                $categories[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $salesData[] = $found ? $found->value : 0;
             }

        } elseif ($this->filterType === 'week' || $this->filterType === 'month') {
             // Por día
            $results = (clone $baseQuery)
                ->selectRaw('DATE(created_at) as label, SUM(total) as value')
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            $start = $dateRange['start']->copy();
            $end = $dateRange['end']->copy();

            while ($start->lte($end)) {
                $dateStr = $start->format('Y-m-d');
                $found = $results->firstWhere('label', $dateStr);
                $categories[] = $start->format('d/m');
                $salesData[] = $found ? $found->value : 0;
                $start->addDay();
            }

        } elseif ($this->filterType === 'year') {
            // Por mes
            $results = (clone $baseQuery)
                ->selectRaw('MONTH(created_at) as label, SUM(total) as value')
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            for($i=1; $i<=12; $i++) {
                $found = $results->firstWhere('label', $i);
                 $categories[] = Carbon::createFromDate(null, $i, 1)->translatedFormat('M');
                $salesData[] = $found ? $found->value : 0;
            }
        }

        $this->salesChartEncoded = json_encode([
            'series' => [[
                'name' => 'Ventas',
                'data' => $salesData
            ]],
            'categories' => $categories
        ]);
    }

    protected function generateTopProductsChart($dateRange)
    {
        // Necesitamos unir con details y products
        // Asumiendo que DetailBill tiene product_id y quantity, price (o total)
        // Y que Bill tiene status ACTIVA
        
        $topProducts = DB::table('detail_bills')
            ->join('bills', 'detail_bills.bill_id', '=', 'bills.id')
            ->join('products', 'detail_bills.product_id', '=', 'products.id')
            ->where('bills.status', Bill::ACTIVA)
            ->whereBetween('bills.created_at', [$dateRange['start'], $dateRange['end']])
            ->select('products.name', DB::raw('SUM(detail_bills.amount) as total_qty'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $labels = $topProducts->pluck('name')->toArray();
        $series = $topProducts->pluck('total_qty')->toArray();

        // Si no hay datos, mostrar placeholder o vacío
        if (empty($series)) {
            $labels = ['Sin ventas'];
            $series = [1]; // Dummy para que se vea algo o manejarlo en el front
        }

        $this->topProductsEncoded = json_encode([
            'series' => $series,
            'labels' => $labels
        ]);
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
}
