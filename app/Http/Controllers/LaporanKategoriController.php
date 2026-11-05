<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanKategoriController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'this_month');
        $keyword = trim($request->input('keyword', ''));

        [$startDate, $endDate] = $this->getPeriod($request, $periode);

        // Transaksi yang valid
        $validTransactions = DB::table('transactions')
            ->where('payment_status', 'PAID')
            ->whereNotIn('order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED',
            ])
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->select('transaction_id');

        // Query dasar penjualan per kategori
        $baseQuery = DB::table('transaction_items')
            ->joinSub($validTransactions, 'valid_transactions', function ($join) {
                $join->on(
                    'transaction_items.transaction_id',
                    '=',
                    'valid_transactions.transaction_id'
                );
            })
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id');

        if ($keyword !== '') {
            $baseQuery->where('categories.name', 'like', '%' . $keyword . '%');
        }

        // Ringkasan keseluruhan
        $summary = (clone $baseQuery)
            ->selectRaw('
                COALESCE(SUM(transaction_items.quantity), 0) as total_qty,
                COALESCE(SUM(transaction_items.subtotal), 0) as total_sales,
                COUNT(DISTINCT categories.id) as total_categories
            ')
            ->first();

        // Data kategori
        $categoryQuery = (clone $baseQuery)
            ->select(
                'categories.id as category_id',
                'categories.name as category_name'
            )
            ->selectRaw('
                COALESCE(SUM(transaction_items.quantity), 0) as total_qty,
                COALESCE(SUM(transaction_items.subtotal), 0) as total_sales,
                COUNT(DISTINCT transaction_items.product_id) as total_products,
                COUNT(DISTINCT transaction_items.transaction_id) as total_transactions
            ')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->orderByDesc('total_qty');

        // Grafik menggunakan seluruh kategori hasil filter, bukan hanya halaman aktif
        $chartCategories = (clone $categoryQuery)->get();

        // Persentase kontribusi omzet
        $totalSales = (float) $summary->total_sales;

        $chartCategories->transform(function ($category) use ($totalSales) {
            $category->percentage = $totalSales > 0
                ? ($category->total_sales / $totalSales) * 100
                : 0;

            return $category;
        });

        // Pagination
        $productsPerPage = 15;
        $currentPage = max(1, (int) $request->input('page', 1));

        $categoryRows = $chartCategories
            ->slice(($currentPage - 1) * $productsPerPage, $productsPerPage)
            ->values();

        $categories = new \Illuminate\Pagination\LengthAwarePaginator(
            $categoryRows,
            $chartCategories->count(),
            $productsPerPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Data grafik Top 10 kategori berdasarkan omzet
        $topCategories = $chartCategories->take(10);

        $categoryLabels = $topCategories
            ->pluck('category_name')
            ->values()
            ->all();

        $categoryData = $topCategories
            ->pluck('total_sales')
            ->values()
            ->all();

        return view('sb-admin-2.laporan.per-kategori', [
            'periode' => $periode,
            'keyword' => $keyword,
            'startDate' => $startDate,
            'endDate' => $endDate,

            'totalQty' => $summary->total_qty,
            'totalSales' => $summary->total_sales,
            'totalCategories' => $summary->total_categories,

            'categories' => $categories,
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
        ]);
    }

    private function getPeriod(Request $request, string $periode): array
    {
        $today = Carbon::today();

        switch ($periode) {
            case 'today':
                $start = $today->copy();
                $end = $today->copy();
                break;

            case 'yesterday':
                $start = $today->copy()->subDay();
                $end = $start->copy();
                break;

            case '7days':
                $start = $today->copy()->subDays(6);
                $end = $today->copy();
                break;

            case 'last_month':
                $start = $today->copy()->subMonthNoOverflow()->startOfMonth();
                $end = $start->copy()->endOfMonth();
                break;

            case 'custom':
                $start = $request->filled('start_date')
                    ? Carbon::parse($request->input('start_date'))
                    : $today->copy()->startOfMonth();

                $end = $request->filled('end_date')
                    ? Carbon::parse($request->input('end_date'))
                    : $today->copy();

                if ($start->gt($end)) {
                    [$start, $end] = [$end, $start];
                }
                break;

            case 'this_month':
            default:
                $start = $today->copy()->startOfMonth();
                $end = $today->copy();
                break;
        }

        return [
            $start->startOfDay(),
            $end->endOfDay(),
        ];
    }
}