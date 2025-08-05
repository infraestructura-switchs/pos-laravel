<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Output;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Obtener estadísticas del dashboard
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth());
        $dateTo = $request->get('date_to', now()->endOfMonth());

        // Estadísticas de ventas
        $salesStats = [
            'total_sales' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total'),
            'total_bills' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'average_ticket' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->avg('total') ?? 0,
            'sales_today' => Bill::whereDate('created_at', today())->sum('total'),
            'bills_today' => Bill::whereDate('created_at', today())->count(),
        ];

        // Estadísticas de productos
        $productStats = [
            'total_products' => Product::count(),
            'low_stock_products' => Product::where('stock', '<', 10)->count(),
            'out_of_stock_products' => Product::where('stock', 0)->count(),
        ];

        // Estadísticas de clientes
        $customerStats = [
            'total_customers' => Customer::count(),
            'new_customers_this_month' => Customer::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
        ];

        // Ventas por método de pago
        $paymentMethods = [
            'cash' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('cash'),
            'card' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('card'),
            'transfer' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('transfer'),
        ];

        // Productos más vendidos
        $topProducts = DB::table('detail_bills')
            ->join('products', 'detail_bills.product_id', '=', 'products.id')
            ->join('bills', 'detail_bills.bill_id', '=', 'bills.id')
            ->whereBetween('bills.created_at', [$dateFrom, $dateTo])
            ->select('products.name', DB::raw('SUM(detail_bills.amount) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Ventas por día (últimos 7 días)
        $dailySales = Bill::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as bills_count'),
            DB::raw('SUM(total) as total_sales')
        )
        ->whereBetween('created_at', [now()->subDays(6), now()])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sales_stats' => $salesStats,
                'product_stats' => $productStats,
                'customer_stats' => $customerStats,
                'payment_methods' => $paymentMethods,
                'top_products' => $topProducts,
                'daily_sales' => $dailySales,
            ]
        ]);
    }

    /**
     * Obtener alertas del sistema
     */
    public function alerts()
    {
        $alerts = [];

        // Productos con bajo stock
        $lowStockProducts = Product::where('stock', '<', 10)
            ->select('name', 'stock')
            ->get();

        if ($lowStockProducts->count() > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => 'Productos con bajo stock',
                'count' => $lowStockProducts->count(),
                'data' => $lowStockProducts
            ];
        }

        // Productos sin stock
        $outOfStockProducts = Product::where('stock', 0)
            ->select('name', 'stock')
            ->get();

        if ($outOfStockProducts->count() > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'Productos sin stock',
                'count' => $outOfStockProducts->count(),
                'data' => $outOfStockProducts
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Obtener resumen de ventas por período
     */
    public function salesSummary(Request $request)
    {
        $period = $request->get('period', 'month'); // week, month, year
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        if (!$dateFrom || !$dateTo) {
            switch ($period) {
                case 'week':
                    $dateFrom = now()->startOfWeek();
                    $dateTo = now()->endOfWeek();
                    break;
                case 'year':
                    $dateFrom = now()->startOfYear();
                    $dateTo = now()->endOfYear();
                    break;
                default:
                    $dateFrom = now()->startOfMonth();
                    $dateTo = now()->endOfMonth();
                    break;
            }
        }

        $summary = [
            'period' => $period,
            'date_from' => $dateFrom->format('Y-m-d'),
            'date_to' => $dateTo->format('Y-m-d'),
            'total_sales' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('total'),
            'total_bills' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'average_ticket' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->avg('total') ?? 0,
            'cash_sales' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('cash'),
            'card_sales' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('card'),
            'transfer_sales' => Bill::whereBetween('created_at', [$dateFrom, $dateTo])->sum('transfer'),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
} 