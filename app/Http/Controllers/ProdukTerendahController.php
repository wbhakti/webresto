<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProdukTerendahController extends Controller
{
    public function index(Request $request)
    {
        // Filter periode
        $periode = $request->input('periode', 'this_month');
        $keyword = $request->input('keyword');

        $startDate = null;
        $endDate = null;

        switch ($periode) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;

            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday();
                break;

            case '7days':
                $startDate = Carbon::today()->subDays(6);
                $endDate = Carbon::today();
                break;

            case 'last_month':
                $startDate = Carbon::now()->subMonthNoOverflow()->startOfMonth();
                $endDate = Carbon::now()->subMonthNoOverflow()->endOfMonth();
                break;

            case 'custom':
                $startDate = $request->filled('start_date')
                    ? Carbon::parse($request->start_date)
                    : Carbon::today()->startOfMonth();

                $endDate = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)
                    : Carbon::today();
                break;

            case 'this_month':
            default:
                $periode = 'this_month';
                $startDate = Carbon::today()->startOfMonth();
                $endDate = Carbon::today();
                break;
        }

        // Pastikan tanggal awal tidak melewati tanggal akhir
        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // Subquery penjualan per produk
        $salesSubquery = DB::table('transaction_items')
            ->join(
                'transactions',
                'transaction_items.transaction_id',
                '=',
                'transactions.transaction_id'
            )
            ->where('transactions.payment_status', 'PAID')
            ->whereNotIn('transactions.order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED'
            ])
            ->whereBetween('transactions.created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay()
            ])
            ->select('transaction_items.product_id')
            ->selectRaw('SUM(transaction_items.quantity) as total_qty')
            ->selectRaw('SUM(transaction_items.subtotal) as total_sales')
            ->selectRaw(
                'COUNT(DISTINCT transaction_items.transaction_id) as total_transactions'
            )
            ->groupBy('transaction_items.product_id');

        // Query seluruh produk aktif, termasuk yang belum pernah terjual
        $productQuery = DB::table('products')
            ->leftJoinSub(clone $salesSubquery, 'sales', function ($join) {
                $join->on('products.id', '=', 'sales.product_id');
            })
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_active', 1)
            ->whereNull('products.deleted_at')
            ->select(
                'products.id as product_id',
                'products.sku as product_sku',
                'products.name as product_name',
                'products.price',
                'categories.name as category_name'
            )
            ->selectRaw('COALESCE(sales.total_qty, 0) as total_qty')
            ->selectRaw('COALESCE(sales.total_sales, 0) as total_sales')
            ->selectRaw(
                'COALESCE(sales.total_transactions, 0) as total_transactions'
            );

        // Filter nama produk atau kategori
        if (!empty($keyword)) {
            $productQuery->where(function ($query) use ($keyword) {
                $query->where('products.name', 'like', "%{$keyword}%")
                    ->orWhere('categories.name', 'like', "%{$keyword}%");
            });
        }

        // Ringkasan mengikuti filter pencarian yang sama
        $summaryQuery = DB::table('products')
            ->leftJoinSub(clone $salesSubquery, 'sales', function ($join) {
                $join->on('products.id', '=', 'sales.product_id');
            })
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.is_active', 1)
            ->whereNull('products.deleted_at');

        if (!empty($keyword)) {
            $summaryQuery->where(function ($query) use ($keyword) {
                $query->where('products.name', 'like', "%{$keyword}%")
                    ->orWhere('categories.name', 'like', "%{$keyword}%");
            });
        }

        $summary = $summaryQuery
            ->selectRaw('COUNT(products.id) as total_products')
            ->selectRaw(
                'SUM(CASE WHEN COALESCE(sales.total_qty, 0) = 0 THEN 1 ELSE 0 END) as unsold_products'
            )
            ->selectRaw('SUM(COALESCE(sales.total_qty, 0)) as total_qty')
            ->selectRaw('SUM(COALESCE(sales.total_sales, 0)) as total_sales')
            ->first();

        // Produk dengan penjualan terendah ditampilkan lebih dahulu
        $products = $productQuery
            ->orderByRaw('COALESCE(sales.total_qty, 0) ASC')
            ->orderByRaw('COALESCE(sales.total_sales, 0) ASC')
            ->orderBy('products.name', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('sb-admin-2.laporan.terendah', compact(
            'products',
            'summary',
            'periode',
            'keyword',
            'startDate',
            'endDate'
        ));
    }
}