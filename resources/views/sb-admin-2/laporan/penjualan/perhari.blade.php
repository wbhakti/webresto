@extends('sb-admin-2.layouts.app')

@section('title', 'Penjualan Per Hari')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 font-weight-bold" style="color:#29364D;">Penjualan Per Hari</h1>
            <p class="text-muted mb-0">Detail transaksi penjualan berdasarkan tanggal.</p>
        </div>

        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
        </a>
    </div>

    {{-- Filter tanggal --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius:16px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('laporan.penjualan.perhari') }}">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold text-secondary">Pilih Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ $tanggal->format('Y-m-d') }}"
                               style="height:54px;">
                    </div>

                    <div class="col-md-2 mb-2">
                        <button class="btn btn-block text-white" style="height:54px;background:#192653;">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </div>

                <div class="text-muted mt-2">
                    Tanggal:
                    <strong>{{ $tanggal->translatedFormat('l, d F Y') }}</strong>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Penjualan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        Rp {{ number_format($summary->total_sales ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Transaksi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($summary->total_transactions ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Rata-rata Transaksi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        Rp {{ number_format($summary->average_transaction ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar transaksi --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold" style="color:#192653;">Daftar Transaksi</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead style="background:#192653;color:white;">
                        <tr>
                            <th width="60">No</th>
                            <th>Waktu</th>
                            <th>No. Invoice</th>
                            <th>Sumber</th>
                            <th>Kasir ID</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($transactions as $index => $transaction)
                            <tr>
                                <td>{{ $transactions->firstItem() + $index }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i:s') }}</td>
                                <td><strong>{{ $transaction->invoice_number }}</strong></td>
                                <td>{{ $transaction->source ?? '-' }}</td>
                                <td>{{ $transaction->cashier_id ?? '-' }}</td>
                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada transaksi pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Menampilkan {{ $transactions->firstItem() ?? 0 }}
                    - {{ $transactions->lastItem() ?? 0 }}
                    dari {{ $transactions->total() }} transaksi
                </small>

                {{ $transactions->links() }}
            </div>
        </div>
    </div>

</div>
@endsection