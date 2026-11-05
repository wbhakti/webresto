@extends('sb-admin-2.layouts.app')

@section('content')

@push('styles')
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
        background: #172554;
        border-color: #172554;
        color: #fff;
    }

    .btn-navy:hover {
        background: #243b70;
        color: #fff;
    }
</style>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 font-weight-bold" style="color:#29364D;">
                Penjualan Per Periode
            </h1>
            <p class="text-muted mb-0">
                Ringkasan penjualan berdasarkan rentang periode.
            </p>
        </div>

        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
        </a>
    </div>

    {{-- FILTER --}}
    <div class="report-filter mb-4">

        <form method="GET"
              action="{{ url('/laporan/penjualan/periode') }}">

            <div class="form-row align-items-end">

                <div class="form-group col-md-3">
                    <label>Periode</label>
                    <select name="periode"
                            id="periode"
                            class="form-control custom-select">

                        <option value="today"
                            {{ $periode == 'today' ? 'selected' : '' }}>
                            Hari Ini
                        </option>

                        <option value="yesterday"
                            {{ $periode == 'yesterday' ? 'selected' : '' }}>
                            Kemarin
                        </option>

                        <option value="7days"
                            {{ $periode == '7days' ? 'selected' : '' }}>
                            7 Hari Terakhir
                        </option>

                        <option value="this_month"
                            {{ $periode == 'this_month' ? 'selected' : '' }}>
                            Bulan Ini
                        </option>

                        <option value="last_month"
                            {{ $periode == 'last_month' ? 'selected' : '' }}>
                            Bulan Lalu
                        </option>

                        <option value="custom"
                            {{ $periode == 'custom' ? 'selected' : '' }}>
                            Custom
                        </option>

                    </select>
                </div>

                <div class="form-group col-md-2 custom-date">
                    <label>Tanggal Mulai</label>
                    <input type="date"
                           name="start_date"
                           class="form-control"
                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                </div>

                <div class="form-group col-md-2 custom-date">
                    <label>Tanggal Akhir</label>
                    <input type="date"
                           name="end_date"
                           class="form-control"
                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                </div>

                <div class="form-group col-md-2">
                    <button type="submit"
                            class="btn btn-navy btn-block">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                </div>

            </div>

        </form>

        <div class="period-info">
            Periode:
            <strong>{{ $startDate->format('d M Y') }}</strong>
            -
            <strong>{{ $endDate->format('d M Y') }}</strong>
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

    {{-- Rekap Harian --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold" style="color:#192653;">Rekap Penjualan Harian</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead style="background:#192653;color:white;">
                        <tr>
                            <th width="60">No</th>
                            <th>Tanggal</th>
                            <th class="text-center">Transaksi</th>
                            <th class="text-right">Rata-rata Transaksi</th>
                            <th class="text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailySales as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->sale_date)->translatedFormat('d M Y') }}</td>
                                <td class="text-center">{{ number_format($row->total_transactions, 0, ',', '.') }}</td>
                                <td class="text-right">Rp {{ number_format($row->average_transaction, 0, ',', '.') }}</td>
                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($row->total_sales, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada penjualan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const periode = document.getElementById('periode');
    const customDates = document.querySelectorAll('.custom-date');

    function toggleCustomDates() {
        const isCustom = periode.value === 'custom';

        customDates.forEach(function (element) {
            element.style.display = isCustom ? '' : 'none';
        });
    }

    periode.addEventListener('change', toggleCustomDates);
    toggleCustomDates();

});
</script>

@endsection
