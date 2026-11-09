<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Tentukan Periode Laporan
        |--------------------------------------------------------------------------
        */

        $periode = $request->get('periode', 'today');

        [$startDate, $endDate, $previousStart, $previousEnd]
            = $this->getPeriod($request, $periode);

        /*
        |--------------------------------------------------------------------------
        | 2. Query Dasar Transaksi
        |--------------------------------------------------------------------------
        */

        $baseQuery = DB::table('transactions')
            ->where('payment_status', 'PAID')
            ->whereNotIn('order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED',
            ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Data Periode Sekarang
        |--------------------------------------------------------------------------
        */

        $currentTransactions = (clone $baseQuery)
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalSales = (clone $currentTransactions)
            ->sum('grand_total');

        $totalTransactions = (clone $currentTransactions)
            ->count();

        $averageTransaction = $totalTransactions > 0
            ? $totalSales / $totalTransactions
            : 0;

        $totalProductsSold = DB::table('transaction_items')
            ->join(
                'transactions',
                'transactions.transaction_id',
                '=',
                'transaction_items.transaction_id'
            )
            ->where('transactions.payment_status', 'PAID')
            ->whereNotIn('transactions.order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED',
            ])
            ->whereBetween('transactions.created_at', [
                $startDate,
                $endDate,
            ])
            ->sum('transaction_items.quantity');

        /*
        |--------------------------------------------------------------------------
        | 4. Data Periode Sebelumnya
        |--------------------------------------------------------------------------
        */

        $previousTransactions = (clone $baseQuery)
            ->whereBetween('created_at', [
                $previousStart,
                $previousEnd,
            ]);

        $previousSales = (clone $previousTransactions)
            ->sum('grand_total');

        $previousTotalTransactions = (clone $previousTransactions)
            ->count();

        $previousAverageTransaction = $previousTotalTransactions > 0
            ? $previousSales / $previousTotalTransactions
            : 0;

        $previousProductsSold = DB::table('transaction_items')
            ->join(
                'transactions',
                'transactions.transaction_id',
                '=',
                'transaction_items.transaction_id'
            )
            ->where('transactions.payment_status', 'PAID')
            ->whereNotIn('transactions.order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED',
            ])
            ->whereBetween('transactions.created_at', [
                $previousStart,
                $previousEnd,
            ])
            ->sum('transaction_items.quantity');

        /*
        |--------------------------------------------------------------------------
        | 5. Hitung Pertumbuhan
        |--------------------------------------------------------------------------
        */

        $salesGrowth = $this->calculateGrowth(
            $totalSales,
            $previousSales
        );

        $transactionGrowth = $this->calculateGrowth(
            $totalTransactions,
            $previousTotalTransactions
        );

        $averageGrowth = $this->calculateGrowth(
            $averageTransaction,
            $previousAverageTransaction
        );

        $productGrowth = $this->calculateGrowth(
            $totalProductsSold,
            $previousProductsSold
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Grafik Penjualan
        |--------------------------------------------------------------------------
        */

        $salesLabels = [];
        $salesData = [];

        // Untuk hari ini dan kemarin, grafik berdasarkan jam.
        if (in_array($periode, ['today', 'yesterday'])) {

            $hourlySales = DB::table('transactions')
                ->where('payment_status', 'PAID')
                ->whereNotIn('order_status', [
                    'CANCELLED',
                    'VOID',
                    'REFUNDED',
                ])
                ->whereBetween('created_at', [
                    $startDate,
                    $endDate,
                ])
                ->selectRaw('HOUR(created_at) as hour')
                ->selectRaw('SUM(grand_total) as total')
                ->groupByRaw('HOUR(created_at)')
                ->pluck('total', 'hour');

            for ($hour = 0; $hour < 24; $hour++) {
                $salesLabels[] = sprintf('%02d:00', $hour);
                $salesData[] = (float) ($hourlySales[$hour] ?? 0);
            }

        } else {

            // Periode lainnya menggunakan grafik harian.
            $dailySales = DB::table('transactions')
                ->where('payment_status', 'PAID')
                ->whereNotIn('order_status', [
                    'CANCELLED',
                    'VOID',
                    'REFUNDED',
                ])
                ->whereBetween('created_at', [
                    $startDate,
                    $endDate,
                ])
                ->selectRaw('DATE(created_at) as date')
                ->selectRaw('SUM(grand_total) as total')
                ->groupByRaw('DATE(created_at)')
                ->pluck('total', 'date');

            $datePeriod = CarbonPeriod::create(
                $startDate->copy()->startOfDay(),
                '1 day',
                $endDate->copy()->startOfDay()
            );

            foreach ($datePeriod as $date) {
                $key = $date->format('Y-m-d');

                $salesLabels[] = $date->format('d/m');
                $salesData[] = (float) ($dailySales[$key] ?? 0);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Produk Terlaris
        |--------------------------------------------------------------------------
        */

        $topProducts = DB::table('transaction_items')
            ->join(
                'transactions',
                'transactions.transaction_id',
                '=',
                'transaction_items.transaction_id'
            )
            ->where('transactions.payment_status', 'PAID')
            ->whereNotIn('transactions.order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED',
            ])
            ->whereBetween('transactions.created_at', [
                $startDate,
                $endDate,
            ])
            ->select('transaction_items.product_name as name')
            ->selectRaw(
                'SUM(transaction_items.quantity) as total_qty'
            )
            ->groupBy('transaction_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 8. Penjualan per Kategori
        |--------------------------------------------------------------------------
        */

        $categorySales = DB::table('transaction_items')
            ->join(
                'transactions',
                'transactions.transaction_id',
                '=',
                'transaction_items.transaction_id'
            )
            ->join(
                'products',
                'products.id',
                '=',
                'transaction_items.product_id'
            )
            ->join(
                'categories',
                'categories.id',
                '=',
                'products.category_id'
            )
            ->where('transactions.payment_status', 'PAID')
            ->whereNotIn('transactions.order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED',
            ])
            ->whereBetween('transactions.created_at', [
                $startDate,
                $endDate,
            ])
            ->select(
                'categories.id',
                'categories.name'
            )
            ->selectRaw(
                'SUM(transaction_items.subtotal) as total_sales'
            )
            ->groupBy(
                'categories.id',
                'categories.name'
            )
            ->orderByDesc('total_sales')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 9. Persentase Kategori
        |--------------------------------------------------------------------------
        */

        $totalCategorySales = $categorySales->sum('total_sales');

        $categorySales = $categorySales->map(function ($category) use (
            $totalCategorySales
        ) {
            $category->percentage = $totalCategorySales > 0
                ? ($category->total_sales / $totalCategorySales) * 100
                : 0;

            return $category;
        });

        $categoryLabels = $categorySales
            ->pluck('name')
            ->values();

        $categoryData = $categorySales
            ->pluck('percentage')
            ->map(fn ($value) => round($value, 1))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 10. Kirim Data ke Blade
        |--------------------------------------------------------------------------
        */

        return view('sb-admin-2.dashboard', compact(
            'totalSales',
            'salesGrowth',
            'totalTransactions',
            'transactionGrowth',
            'averageTransaction',
            'averageGrowth',
            'totalProductsSold',
            'productGrowth',
            'salesLabels',
            'salesData',
            'topProducts',
            'categorySales',
            'categoryLabels',
            'categoryData'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Helper: Periode Laporan dan Periode Pembanding
    |--------------------------------------------------------------------------
    */

    private function getPeriod(Request $request, $periode)
    {
        $today = Carbon::today();

        switch ($periode) {

            case 'yesterday':

                $start = $today->copy()->subDay()->startOfDay();
                $end = $today->copy()->subDay()->endOfDay();

                $previousStart = $today->copy()->subDays(2)->startOfDay();
                $previousEnd = $today->copy()->subDays(2)->endOfDay();

                break;

            case '7days':

                $start = $today->copy()->subDays(6)->startOfDay();
                $end = Carbon::now();

                $previousStart = $start->copy()->subDays(7);
                $previousEnd = $start->copy()->subSecond();

                break;

            case 'this_month':

                $start = $today->copy()->startOfMonth();
                $end = Carbon::now();

                $previousStart = $start->copy()->subMonth()->startOfMonth();
                $previousEnd = $start->copy()->subSecond();

                break;

            case 'last_month':

                $start = $today->copy()->subMonth()->startOfMonth();
                $end = $today->copy()->subMonth()->endOfMonth();

                $previousStart = $start->copy()->subMonth()->startOfMonth();
                $previousEnd = $start->copy()->subSecond();

                break;

            case 'custom':

                $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ]);

                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();

                $days = $start->diffInDays($end) + 1;

                $previousEnd = $start->copy()->subSecond();
                $previousStart = $previousEnd->copy()
                    ->subDays($days)
                    ->startOfDay();

                break;

            case 'today':
            default:

                $start = $today->copy()->startOfDay();
                $end = Carbon::now();

                $previousStart = $today->copy()->subDay()->startOfDay();
                $previousEnd = $today->copy()->subDay()->endOfDay();

                break;
        }

        return [
            $start,
            $end,
            $previousStart,
            $previousEnd,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Helper: Persentase Pertumbuhan
    |--------------------------------------------------------------------------
    */

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return 0;
        }

        return round(
            (($current - $previous) / $previous) * 100,
            1
        );
    }
}