@extends('sb-admin-2.layouts.app')

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

    .category-name {
        font-weight: 700;
        color: #2d3748;
    }

    .category-id {
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

    .percentage-track {
        height: 7px;
        background: #e9edf5;
        border-radius: 10px;
        overflow: hidden;
        min-width: 70px;
    }

    .percentage-fill {
        height: 100%;
        background: #F28C28;
        border-radius: 10px;
    }

    .period-info {
        font-size: 12px;
        color: #858796;
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

    .chart-container {
        position: relative;
        height: 320px;
    }
</style>

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="report-title mb-1">Penjualan per Kategori</h1>
            <div class="report-subtitle">
                Analisis omzet dan jumlah produk terjual berdasarkan kategori.
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
              action="{{ url('/laporan/produk/per-kategori') }}">

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
                    <label>Cari Kategori</label>
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Nama kategori..."
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
                            <div class="kpi-label">Total Omzet</div>
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
            <div class="card kpi-card orange h-100">
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
            <div class="card kpi-card h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="kpi-label">Kategori Terjual</div>
                            <div class="kpi-value">
                                {{ number_format($totalCategories, 0, ',', '.') }}
                                <small class="text-muted">kategori</small>
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

    {{-- CHART --}}
    <div class="card report-card mb-4">

        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-chart-pie mr-2"></i>
                Kontribusi Omzet per Kategori
            </h6>
            <small class="text-muted">
                Menampilkan maksimal 10 kategori berdasarkan omzet tertinggi.
            </small>
        </div>

        <div class="card-body">
            <div class="chart-container">
                <canvas id="categorySalesChart"></canvas>
            </div>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="card report-card mb-4">

        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-dark">
                    Ranking Penjualan Kategori
                </h6>
                <small class="text-muted">
                    Diurutkan berdasarkan omzet terbesar.
                </small>
            </div>

            <span class="badge badge-light p-2 mt-2">
                {{ $categories->total() }} kategori
            </span>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table report-table table-hover mb-0">

                    <thead>
                        <tr>
                            <th width="65">Rank</th>
                            <th>Nama Kategori</th>
                            <th class="text-center">Jumlah Produk</th>
                            <th class="text-center">Qty Terjual</th>
                            <th class="text-center">Transaksi</th>
                            <th width="150">Kontribusi</th>
                            <th class="text-right">Total Omzet</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($categories as $index => $category)

                            @php
                                $rank = ($categories->currentPage() - 1)
                                    * $categories->perPage() + $index + 1;
                            @endphp

                            <tr>

                                <td>
                                    <span class="rank-number {{ $rank == 1 ? 'top-one' : '' }}">
                                        {{ $rank }}
                                    </span>
                                </td>

                                <td>
                                    <div class="category-name">
                                        {{ $category->category_name }}
                                    </div>
                                    <div class="category-id">
                                        ID Kategori: {{ $category->category_id }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    {{ number_format($category->total_products, 0, ',', '.') }}
                                </td>

                                <td class="text-center">
                                    <span class="quantity-badge">
                                        {{ number_format($category->total_qty, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    {{ number_format($category->total_transactions, 0, ',', '.') }}
                                </td>

                                <td>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="font-weight-bold">
                                            {{ number_format($category->percentage, 1, ',', '.') }}%
                                        </small>
                                    </div>

                                    <div class="percentage-track">
                                        <div class="percentage-fill"
                                             style="width: {{ min(100, max(0, $category->percentage)) }}%">
                                        </div>
                                    </div>
                                </td>

                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($category->total_sales, 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>

                                    <div class="font-weight-bold text-dark">
                                        Data kategori tidak ditemukan
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
                    Menampilkan {{ $categories->firstItem() ?? 0 }}
                    sampai {{ $categories->lastItem() ?? 0 }}
                    dari {{ $categories->total() }} kategori
                </small>

                {{ $categories->links() }}

            </div>

        </div>

    </div>

</div>

{{-- FILTER SCRIPT --}}
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

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('categorySalesChart');

    if (!canvas || typeof Chart === 'undefined') {
        return;
    }

    const labels = @json($categoryLabels);
    const values = @json($categoryData);

    if (labels.length === 0) {
        return;
    }

    new Chart(canvas, {
        type: 'doughnut',

        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: [
                    '#172554',
                    '#F28C28',
                    '#3B82F6',
                    '#64748B',
                    '#94A3B8',
                    '#FBBF24',
                    '#0F766E',
                    '#7C3AED',
                    '#DB2777',
                    '#78716C'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'right'
                },

                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = Number(context.raw || 0);

                            return context.label + ': Rp ' +
                                value.toLocaleString('id-ID');
                        }
                    }
                }
            },

            cutout: '60%'
        }
    });

});
</script>

@endsection
