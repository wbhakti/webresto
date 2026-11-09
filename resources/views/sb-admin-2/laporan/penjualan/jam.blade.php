@extends('sb-admin-2.layouts.app')

@section('title', 'Penjualan Per Jam')

@section('content')

<style>
    .report-title {
        font-size: 22px;
        font-weight: 700;
        color: #2d3748;
    }

    .report-subtitle {
        font-size: 13px;
        color: #858796;
    }

    .report-card {
        border: 0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
    }

    .report-filter {
        background: #fff;
        border-radius: 10px;
        padding: 18px;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
    }

    .report-filter label {
        font-size: 12px;
        font-weight: 600;
        color: #5a5c69;
    }

    .report-filter .form-control,
    .report-filter .custom-select {
        height: 40px;
        font-size: 13px;
    }

    .btn-navy {
        background: #142B4A;
        color: #fff;
        border: 1px solid #142B4A;
    }

    .btn-navy:hover {
        background: #203e65;
        color: #fff;
    }

    .btn-orange {
        background: #F28C28;
        color: #fff;
        border: 1px solid #F28C28;
    }

    .btn-orange:hover {
        background: #dc791b;
        color: #fff;
    }
</style>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 font-weight-bold" style="color:#29364D;">Penjualan Per Jam</h1>
            <p class="text-muted mb-0">Analisis omzet dan jumlah transaksi berdasarkan jam operasional.</p>
        </div>

        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
        </a>
    </div>

    {{-- Filter tanggal --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius:16px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('laporan.penjualan.jam') }}">
                <div class="row align-items-end">

                    <div class="col-md-3 mb-2">
                        <label class="font-weight-bold text-secondary">Pilih Tanggal</label>
                        <input type="date"
                               name="tanggal"
                               class="form-control"
                               value="{{ $tanggal->format('Y-m-d') }}"
                               style="height:54px;">
                    </div>

                    <div class="col-md-2 mb-2">
                        <button class="btn btn-block text-white"
                                style="height:54px;background:#192653;">
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
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Penjualan
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        Rp {{ number_format($summary->total_sales ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Total Transaksi
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($summary->total_transactions ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Jam Penjualan Tertinggi
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        @if(($summary->total_transactions ?? 0) > 0)
                            {{ sprintf('%02d:00', $peakHour->sale_hour) }}
                        @else
                            -
                        @endif
                    </div>
                    <small class="text-muted">
                        @if(($summary->total_transactions ?? 0) > 0)
                            Rp {{ number_format($peakHour->total_sales, 0, ',', '.') }}
                        @else
                            Belum ada transaksi
                        @endif
                    </small>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabel penjualan per jam --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold" style="color:#192653;">
                Rekap Penjualan Per Jam
            </h6>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead style="background:#192653;color:white;">
                        <tr>
                            <th width="65">No</th>
                            <th>Jam</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-right">Total Penjualan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($hourlySales as $index => $hour)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <strong>{{ $hour->hour_label }}</strong>
                                </td>

                                <td class="text-center">
                                    {{ number_format($hour->total_transactions, 0, ',', '.') }}
                                </td>

                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($hour->total_sales, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot style="background:#F5F6FA;">
                        <tr>
                            <th colspan="2" class="text-right">TOTAL</th>
                            <th class="text-center">
                                {{ number_format($summary->total_transactions ?? 0, 0, ',', '.') }}
                            </th>
                            <th class="text-right">
                                Rp {{ number_format($summary->total_sales ?? 0, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection