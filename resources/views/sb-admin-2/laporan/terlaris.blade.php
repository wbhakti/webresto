
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

    .kpi-card {
        border: 0;
        border-left: 4px solid #172554;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,.05);
    }

    .kpi-card.orange {
        border-left-color: #F28C28;
    }

    .kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #858796;
        text-transform: uppercase;
    }

    .kpi-value {
        font-size: 22px;
        font-weight: 700;
        color: #2d3748;
    }

    .kpi-icon {
        font-size: 28px;
        color: #cbd5e1;
    }

    .report-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        text-transform: uppercase;
        border-top: 0;
        padding: 14px 12px;
    }

    .report-table tbody td {
        font-size: 13px;
        vertical-align: middle;
        padding: 14px 12px;
    }

    .rank-number {
        display: inline-flex;
        width: 30px;
        height: 30px;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #eff6ff;
        color: #172554;
        font-weight: 700;
    }

    .rank-number.top-one {
        background: #fff7ed;
        color: #c2410c;
    }

    .product-name {
        font-weight: 700;
        color: #2d3748;
    }

    .product-id {
        font-size: 11px;
        color: #9ca3af;
    }

    .quantity-badge {
        background: #eff6ff;
        color: #1e40af;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 700;
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

    .btn-orange {
        background: #F28C28;
        border-color: #F28C28;
        color: #fff;
    }

    .btn-orange:hover {
        background: #dc7615;
        color: #fff;
    }

    .period-info {
        font-size: 12px;
        color: #858796;
    }
</style>

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="report-title mb-1">Produk Terlaris</h1>
            <div class="report-subtitle">
                Ranking produk berdasarkan jumlah unit yang terjual.
            </div>
        </div>

        <a href="{{ url('/dashboard') }}"
           class="btn btn-outline-secondary btn-sm mt-2">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Dashboard
        </a>
    </div>

    {{-- FILTER --}}
    <div class="report-filter mb-4">

        <form method="GET"
              action="{{ url('/laporan/produk/terlaris') }}">

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

                <div class="form-group col-md-3">
                    <label>Cari Produk</label>
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Nama produk..."
                           value="{{ $keyword }}">
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

    {{-- SUMMARY --}}
    <div class="row mb-4">

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="kpi-label">Total Produk Terjual</div>
                            <div class="kpi-value">
                                {{ number_format($totalQty, 0, ',', '.') }}
                                <small class="text-muted">unit</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card kpi-card orange h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="kpi-label">Omzet Produk</div>
                            <div class="kpi-value">
                                Rp {{ number_format($totalSales, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="kpi-label">Jenis Produk Terjual</div>
                            <div class="kpi-value">
                                {{ number_format($totalProducts, 0, ',', '.') }}
                                <small class="text-muted">produk</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags kpi-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="card report-card mb-4">

        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-trophy mr-2 text-warning"></i>
                    Ranking Produk
                </h6>
                <small class="text-muted">
                    Diurutkan dari jumlah unit terjual terbanyak.
                </small>
            </div>

            <span class="badge badge-light p-2 mt-2">
                {{ $products->total() }} produk
            </span>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table report-table table-hover mb-0">

                    <thead>
                        <tr>
                            <th width="65">Rank</th>
                            <th>Nama Produk</th>
                            <th class="text-center">Qty Terjual</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-right">Harga Rata-rata</th>
                            <th class="text-right">Total Penjualan</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($products as $index => $product)

                            @php
                                $rank = ($products->currentPage() - 1)
                                    * $products->perPage() + $index + 1;
                            @endphp

                            <tr>

                                <td>
                                    <span class="rank-number {{ $rank == 1 ? 'top-one' : '' }}">
                                        {{ $rank }}
                                    </span>
                                </td>

                                <td>
                                    <div class="product-name">
                                        {{ $product->product_name }}
                                    </div>
                                    <div class="product-id">
                                        ID Produk: {{ $product->product_id ?? '-' }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="quantity-badge">
                                        {{ number_format($product->total_qty, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    {{ number_format($product->total_transactions, 0, ',', '.') }}
                                </td>

                                <td class="text-right">
                                    Rp {{ number_format($product->average_price, 0, ',', '.') }}
                                </td>

                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($product->total_sales, 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <div class="font-weight-bold text-dark">
                                        Data produk tidak ditemukan
                                    </div>
                                    <small class="text-muted">
                                        Tidak ada penjualan pada periode yang dipilih.
                                    </small>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">

                <small class="text-muted mb-2">
                    Menampilkan {{ $products->firstItem() ?? 0 }}
                    sampai {{ $products->lastItem() ?? 0 }}
                    dari {{ $products->total() }} produk
                </small>

                {{ $products->links() }}

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
