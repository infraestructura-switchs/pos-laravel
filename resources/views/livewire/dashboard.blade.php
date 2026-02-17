<div class="px-4 pt-4 pb-10">
    @if ($this->isDomainEnabled())
        <!-- Filters -->
        <div class="flex justify-center mb-6">
            <div class="inline-flex rounded-md shadow-sm" role="group">
                @foreach (['day' => 'Diario', 'week' => 'Semanal', 'month' => 'Mensual', 'year' => 'Anual'] as $key => $label)
                    <button type="button" wire:click="setFilter('{{ $key }}')"
                        class="px-4 py-2 text-sm font-medium border {{ $filterType === $key ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-900 border-gray-200 hover:bg-gray-100' }} {{ $loop->first ? 'rounded-l-lg' : '' }} {{ $loop->last ? 'rounded-r-lg' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Tickets -->
            <div class="bg-blue-500 rounded-lg shadow-lg p-6 flex items-center justify-between text-white">
                <div class="p-3 bg-blue-600 rounded-lg">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ $totalTickets }}</div>
                    <div class="text-xs uppercase tracking-wider font-semibold">TOTAL DE TICKETS</div>
                </div>
            </div>

            <!-- Venta por día (Total Sales) -->
            <div class="bg-red-600 rounded-lg shadow-lg p-6 flex items-center justify-between text-white">
                 <div class="p-3 bg-red-700 rounded-lg">
                    <i class="fas fa-tags text-3xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">@formatToCop($totalSales)</div>
                    <div class="text-xs uppercase tracking-wider font-semibold">
                        @if($filterType == 'day') VENTA POR DÍA @else VENTA TOTAL @endif
                    </div>
                </div>
            </div>

            <!-- Venta por Ticket (Average) -->
            <div class="bg-green-500 rounded-lg shadow-lg p-6 flex items-center justify-between text-white">
                 <div class="p-3 bg-green-600 rounded-lg">
                    <i class="fas fa-info-circle text-3xl"></i>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">@formatToCop($averageTicket)</div>
                    <div class="text-xs uppercase tracking-wider font-semibold">VENTA POR TICKET</div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Line Chart: Cantidad de artículos (Sales Trend) -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-4" x-data="salesChart()" x-init="initChart()">
                <div class="border-b pb-2 mb-4">
                    <h3 class="text-gray-600 font-semibold">Cantidad de artículos</h3>
                </div>
                <div id="sales-chart" class="w-full h-80"></div>
            </div>

            <!-- Pie Chart: Top 5 productos -->
            <div class="bg-white rounded-lg shadow p-4" x-data="topProductsChart()" x-init="initChart()">
                 <div class="border-b pb-2 mb-4 flex justify-between items-center">
                    <h3 class="text-gray-600 font-semibold">Top 5 de productos más vendidos</h3>
                </div>
                <div id="top-products-chart" class="w-full h-80 flex items-center justify-center"></div>
            </div>
        </div>

        <!-- ApexCharts Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        
        <script>
            function salesChart() {
                return {
                    chart: null,
                    initChart() {
                        const data = JSON.parse(@json($salesChartEncoded));
                        
                        const options = {
                            series: data.series,
                            chart: {
                                height: 320,
                                type: 'line',
                                toolbar: { show: false },
                                zoom: { enabled: false }
                            },
                            dataLabels: { enabled: false },
                            stroke: { curve: 'smooth', width: 3 },
                            colors: ['#3B82F6'], // Blue
                            xaxis: {
                                categories: data.categories,
                            },
                            yaxis: {
                                labels: {
                                    formatter: function (value) {
                                        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
                                    }
                                }
                            },
                            grid: {
                                borderColor: '#f1f1f1',
                            }
                        };

                        if (this.chart) {
                            this.chart.destroy();
                        }
                        this.chart = new ApexCharts(document.querySelector("#sales-chart"), options);
                        this.chart.render();
                        
                        // Listen for Livewire updates
                        Livewire.on('chartUpdate', () => {
                             // This part is tricky with full re-render, but if component re-renders, alpine re-inits
                        });
                    }
                }
            }

            function topProductsChart() {
                return {
                    chart: null,
                    initChart() {
                        const data = JSON.parse(@json($topProductsEncoded));

                        const options = {
                            series: data.series,
                            chart: {
                                height: 320,
                                type: 'pie',
                            },
                            labels: data.labels,
                            colors: ['#4ade80', '#60a5fa', '#f87171', '#fbbf24', '#a78bfa'], // Tailwind colors
                            legend: {
                                position: 'right',
                            },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 200
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }]
                        };

                        if (this.chart) {
                            this.chart.destroy();
                        }
                        this.chart = new ApexCharts(document.querySelector("#top-products-chart"), options);
                        this.chart.render();
                    }
                }
            }
            
            // Re-initialize charts when Livewire updates the DOM
            document.addEventListener('livewire:load', function () {
                Livewire.hook('message.processed', (message, component) => {
                   // Alpine x-init handles re-initialization on DOM diffs usually, 
                   // but ensuring charts redraw if data changes is key. 
                   // Since key change triggers re-render of separate divs, Alpine should handle it.
                });
            });
        </script>
    @else
        <div class="flex flex-col items-center justify-center p-10">
            <h2 class="text-2xl font-bold text-gray-500">Dashboard no disponible para este dominio</h2>
        </div>
    @endif
</div>
