<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanPenjualanController extends Controller
{
    /**
     * Query transaksi yang dihitung sebagai penjualan valid.
     */
    private function transaksiValid()
    {
        return DB::table('transactions')
            ->where('payment_status', 'PAID')
            ->whereNotIn('order_status', [
                'CANCELLED',
                'VOID',
                'REFUNDED'
            ]);
    }

    /**
     * Menentukan rentang tanggal berdasarkan filter periode.
     */
    private function rentangPeriode(Request $request)
    {
        $periode = $request->input('periode', 'this_month');

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

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [$periode, $startDate, $endDate];
    }

    /**
     * 1. LAPORAN PENJUALAN PER PERIODE
     * Rekap omzet dan transaksi per tanggal dalam rentang periode.
     */
    public function periode(Request $request)
    {
        [$periode, $startDate, $endDate] = $this->rentangPeriode($request);

        $summary = $this->transaksiValid()
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay()
            ])
            ->selectRaw('COUNT(*) as total_transactions')
            ->selectRaw('SUM(grand_total) as total_sales')
            ->selectRaw('AVG(grand_total) as average_transaction')
            ->first();

        $dailySales = $this->transaksiValid()
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay()
            ])
            ->selectRaw('DATE(created_at) as sale_date')
            ->selectRaw('COUNT(*) as total_transactions')
            ->selectRaw('SUM(grand_total) as total_sales')
            ->selectRaw('AVG(grand_total) as average_transaction')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('sale_date', 'asc')
            ->get();

        return view('sb-admin-2.laporan.penjualan.periode', compact(
            'periode',
            'startDate',
            'endDate',
            'summary',
            'dailySales'
        ));
    }

    /**
     * 2. LAPORAN PENJUALAN PER HARI
     * Menampilkan detail transaksi pada tanggal yang dipilih.
     */
    public function perHari(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        $transactions = $this->transaksiValid()
            ->whereBetween('created_at', [
                $tanggal->copy()->startOfDay(),
                $tanggal->copy()->endOfDay()
            ])
            ->select(
                'transaction_id',
                'invoice_number',
                'source',
                'cashier_id',
                'grand_total',
                'payment_status',
                'order_status',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $summary = $this->transaksiValid()
            ->whereBetween('created_at', [
                $tanggal->copy()->startOfDay(),
                $tanggal->copy()->endOfDay()
            ])
            ->selectRaw('COUNT(*) as total_transactions')
            ->selectRaw('SUM(grand_total) as total_sales')
            ->selectRaw('AVG(grand_total) as average_transaction')
            ->first();

        return view('sb-admin-2.laporan.penjualan.perhari', compact(
            'tanggal',
            'transactions',
            'summary'
        ));
    }

    /**
     * 3. LAPORAN PENJUALAN PER JAM
     * Menampilkan rekap omzet dan transaksi pada jam 00-23.
     */
    public function perJam(Request $request)
    {
        $tanggal = $request->filled('tanggal')
            ? Carbon::parse($request->tanggal)
            : Carbon::today();

        $hourlyData = $this->transaksiValid()
            ->whereBetween('created_at', [
                $tanggal->copy()->startOfDay(),
                $tanggal->copy()->endOfDay()
            ])
            ->selectRaw('HOUR(created_at) as sale_hour')
            ->selectRaw('COUNT(*) as total_transactions')
            ->selectRaw('SUM(grand_total) as total_sales')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('sale_hour')
            ->get()
            ->keyBy('sale_hour');

        // Buat 24 baris agar jam tanpa transaksi tetap terlihat.
        $hourlySales = collect();

        for ($hour = 0; $hour < 24; $hour++) {
            $data = $hourlyData->get($hour);

            $hourlySales->push((object) [
                'sale_hour' => $hour,
                'hour_label' => sprintf('%02d:00 - %02d:59', $hour, $hour),
                'total_transactions' => $data->total_transactions ?? 0,
                'total_sales' => $data->total_sales ?? 0
            ]);
        }

        $summary = $this->transaksiValid()
            ->whereBetween('created_at', [
                $tanggal->copy()->startOfDay(),
                $tanggal->copy()->endOfDay()
            ])
            ->selectRaw('COUNT(*) as total_transactions')
            ->selectRaw('SUM(grand_total) as total_sales')
            ->selectRaw('AVG(grand_total) as average_transaction')
            ->first();

        $peakHour = $hourlySales
            ->sortByDesc('total_sales')
            ->first();

        return view('sb-admin-2.laporan.penjualan.jam', compact(
            'tanggal',
            'hourlySales',
            'summary',
            'peakHour'
        ));
    }
}