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
        border-left: 4px solid #142B4A;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 2px 12px rgba(20, 43, 74, .07);
    }

    .kpi-card.orange {
        border-left-color: #F28C28;
    }

    .kpi-card.red {
        border-left-color: #dc3545;
    }

    .kpi-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #858796;
        letter-spacing: .5px;
    }

    .kpi-value {
        font-size: 21px;
        font-weight: 700;
        color: #142B4A;
    }

    .report-table thead th {
        background: #142B4A;
        color: #fff;
        border-color: #142B4A;
        font-size: 12px;
        vertical-align: middle;
    }

    .report-table tbody td {
        vertical-align: middle;
        font-size: 13px;
    }

    .rank-number {
        display: inline-flex;
        width: 28px;
        height: 28px;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f3f8;
        color: #142B4A;
        font-weight: 700;
    }

    .quantity-badge {
        background: #fff0e0;
        color: #C66A0A;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 700;
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
@endpush

<div class="container-fluid">

{{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="report-title mb-1">Produk Terendah</h1>
            <div class="report-subtitle">
            Daftar produk dengan jumlah penjualan paling rendah.
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
              action="{{ url('/laporan/produk/terendah') }}">

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
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="kpi-label">Total Produk Aktif</div>
                    <div class="kpi-value">
                        {{ number_format($summary->total_products ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card kpi-card red h-100">
                <div class="card-body">
                    <div class="kpi-label">Produk Belum Terjual</div>
                    <div class="kpi-value">
                        {{ number_format($summary->unsold_products ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card kpi-card orange h-100">
                <div class="card-body">
                    <div class="kpi-label">Total Omzet Produk</div>
                    <div class="kpi-value">
                        Rp {{ number_format($summary->total_sales ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabel --}}
    <div class="card report-card mb-4">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold report-title">
                    Ranking Produk Penjualan Terendah
                </h6>
                <small class="text-muted">
                    {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </small>
            </div>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover report-table">
                    <thead>
                        <tr>
                            <th class="text-center" width="65">Rank</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th class="text-center">Qty Terjual</th>
                            <th class="text-center">Jumlah Transaksi</th>
                            <th class="text-right">Total Penjualan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $index => $product)
                            <tr>
                                <td class="text-center">
                                    <span class="rank-number">
                                        {{ $products->firstItem() + $index }}
                                    </span>
                                </td>

                                <td>
                                    <strong>{{ $product->product_name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        ID: {{ $product->product_id }}
                                    </small>
                                </td>

                                <td>
                                    {{ $product->category_name ?? 'Tanpa Kategori' }}
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
                                    <strong>
                                        Rp {{ number_format($product->total_sales, 0, ',', '.') }}
                                    </strong>
                                </td>

                                <td class="text-center">
                                    @if($product->total_qty == 0)
                                        <span class="badge badge-danger">Belum Terjual</span>
                                    @else
                                        <span class="badge badge-primary">Ada Penjualan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data produk.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Menampilkan {{ $products->firstItem() ?? 0 }}
                    - {{ $products->lastItem() ?? 0 }}
                    dari {{ $products->total() }} produk
                </small>

                {{ $products->links('pagination::bootstrap-4') }}
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


