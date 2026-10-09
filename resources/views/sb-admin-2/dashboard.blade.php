@extends('sb-admin-2.layouts.app')

@section('content')

<style>
    /* ================================
       DASHBOARD LAPORAN
    ================================= */

    .report-card {
        border: 0;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .report-card .card-body {
        padding: 20px;
    }

    .report-title {
        font-size: 22px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 4px;
    }

    .report-subtitle {
        font-size: 13px;
        color: #858796;
    }


    /* ================================
       FILTER
    ================================= */

    .report-filter {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .report-filter label {
        font-size: 12px;
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 5px;
    }

    .report-filter .form-control,
    .report-filter .custom-select {
        height: 40px;
        font-size: 13px;
    }


    /* ================================
       KPI CARD
    ================================= */

    .kpi-card {
        border: 0;
        border-left: 4px solid;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .kpi-card .card-body {
        padding: 18px;
    }

    .kpi-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: #858796;
        margin-bottom: 5px;
    }

    .kpi-value {
        font-size: 22px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .kpi-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 18px;
    }

    .kpi-footer {
        font-size: 11px;
        color: #858796;
    }

    .kpi-up {
        color: #1cc88a;
        font-weight: 700;
    }

    .kpi-down {
        color: #e74a3b;
        font-weight: 700;
    }


    /* ================================
       CHART
    ================================= */

    .chart-container {
        position: relative;
        width: 100%;
        height: 320px;
    }


    /* ================================
       TABLE
    ================================= */

    .report-table {
        margin-bottom: 0;
    }

    .report-table thead th {
        border-top: 0;
        border-bottom: 1px solid #e3e6f0;
        font-size: 11px;
        text-transform: uppercase;
        color: #858796;
        font-weight: 700;
    }

    .report-table tbody td {
        vertical-align: middle;
        font-size: 13px;
    }

    .product-rank {
        width: 30px;
        height: 30px;
        border-radius: 50%;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        background: #f1f3f5;
        color: #5a5c69;

        font-size: 12px;
        font-weight: 700;
    }

    .product-name {
        font-weight: 600;
        color: #3a3b45;
    }

    .product-qty {
        font-weight: 700;
        color: #4e73df;
    }


    /* ================================
       CATEGORY
    ================================= */

    .category-item {
        margin-bottom: 18px;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .category-name {
        font-size: 13px;
        font-weight: 600;
        color: #5a5c69;
    }

    .category-value {
        font-size: 12px;
        color: #858796;
    }

    .category-progress {
        height: 8px;
        border-radius: 10px;
        background-color: #eaecf4;
    }

    .category-progress .progress-bar {
        border-radius: 10px;
    }


    /* ================================
       MOBILE
    ================================= */

    @media (max-width: 767px) {

        .report-title {
            font-size: 20px;
        }

        .kpi-value {
            font-size: 19px;
        }

        .chart-container {
            height: 260px;
        }

        .report-filter .form-group {
            margin-bottom: 10px;
        }

    }
</style>


<div class="container-fluid">

    <!-- =========================================
         HEADER
    ========================================== -->

    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <div>
            <h1 class="report-title">
                Dashboard Penjualan
            </h1>

            <div class="report-subtitle">
                Ringkasan penjualan dan performa bisnis
            </div>
        </div>

    </div>


    <!-- =========================================
         FILTER PERIODE
    ========================================== -->

    <div class="report-filter mb-4">

        <form method="GET"
              action="{{ url('/laporan/dashboard') }}">

            <div class="form-row align-items-end">

                <div class="form-group col-md-3">
                    <label for="periode">
                        Periode
                    </label>

                    <select
                        name="periode"
                        id="periode"
                        class="custom-select">

                        <option value="today"
                            {{ request('periode', 'today') == 'today' ? 'selected' : '' }}>
                            Hari Ini
                        </option>

                        <option value="yesterday"
                            {{ request('periode') == 'yesterday' ? 'selected' : '' }}>
                            Kemarin
                        </option>

                        <option value="7days"
                            {{ request('periode') == '7days' ? 'selected' : '' }}>
                            7 Hari Terakhir
                        </option>

                        <option value="this_month"
                            {{ request('periode') == 'this_month' ? 'selected' : '' }}>
                            Bulan Ini
                        </option>

                        <option value="last_month"
                            {{ request('periode') == 'last_month' ? 'selected' : '' }}>
                            Bulan Lalu
                        </option>

                        <option value="custom"
                            {{ request('periode') == 'custom' ? 'selected' : '' }}>
                            Custom
                        </option>

                    </select>
                </div>


                <div class="form-group col-md-3 custom-date">
                    <label for="start_date">
                        Dari
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        id="start_date"
                        class="form-control"
                        value="{{ request('start_date') }}">
                </div>


                <div class="form-group col-md-3 custom-date">
                    <label for="end_date">
                        Sampai
                    </label>

                    <input
                        type="date"
                        name="end_date"
                        id="end_date"
                        class="form-control"
                        value="{{ request('end_date') }}">
                </div>


                <div class="form-group col-md-3">

                    <button
                        type="submit"
                        class="btn btn-primary btn-block">

                        <i class="fas fa-filter mr-1"></i>
                        Terapkan Filter

                    </button>

                </div>

            </div>

        </form>

    </div>


    <!-- =========================================
         KPI
    ========================================== -->

    <div class="row">


        <!-- Total Penjualan -->
        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card kpi-card"
                 style="border-left-color:#4e73df;">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col">

                            <div class="kpi-label">
                                Total Penjualan
                            </div>

                            <div class="kpi-value">
                                Rp {{ number_format($totalSales ?? 12450000, 0, ',', '.') }}
                            </div>

                            <div class="kpi-footer">

                                @if(($salesGrowth ?? 0) >= 0)

                                    <span class="kpi-up">
                                        <i class="fas fa-arrow-up"></i>
                                        {{ $salesGrowth ?? '12,5' }}%
                                    </span>

                                @else

                                    <span class="kpi-down">
                                        <i class="fas fa-arrow-down"></i>
                                        {{ abs($salesGrowth ?? 0) }}%
                                    </span>

                                @endif

                                vs periode sebelumnya

                            </div>

                        </div>

                        <div class="col-auto">

                            <div class="kpi-icon"
                                 style="background:#e8efff;color:#4e73df;">

                                <i class="fas fa-money-bill-wave"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Total Transaksi -->
        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card kpi-card"
                 style="border-left-color:#1cc88a;">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col">

                            <div class="kpi-label">
                                Total Transaksi
                            </div>

                            <div class="kpi-value">
                                {{ number_format($totalTransactions ?? 428, 0, ',', '.') }}
                            </div>

                            <div class="kpi-footer">

                                @if(($transactionGrowth ?? 0) >= 0)

                                    <span class="kpi-up">
                                        <i class="fas fa-arrow-up"></i>
                                        {{ $transactionGrowth ?? '8,2' }}%
                                    </span>

                                @else

                                    <span class="kpi-down">
                                        <i class="fas fa-arrow-down"></i>
                                        {{ abs($transactionGrowth ?? 0) }}%
                                    </span>

                                @endif

                                vs periode sebelumnya

                            </div>

                        </div>

                        <div class="col-auto">

                            <div class="kpi-icon"
                                 style="background:#e5faf3;color:#1cc88a;">

                                <i class="fas fa-receipt"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Average Transaction -->
        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card kpi-card"
                 style="border-left-color:#36b9cc;">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col">

                            <div class="kpi-label">
                                Rata-rata Transaksi
                            </div>

                            <div class="kpi-value">
                                Rp {{ number_format($averageTransaction ?? 29089, 0, ',', '.') }}
                            </div>

                            <div class="kpi-footer">

                                @if(($averageGrowth ?? 0) >= 0)

                                    <span class="kpi-up">
                                        <i class="fas fa-arrow-up"></i>
                                        {{ $averageGrowth ?? '3,1' }}%
                                    </span>

                                @else

                                    <span class="kpi-down">
                                        <i class="fas fa-arrow-down"></i>
                                        {{ abs($averageGrowth ?? 0) }}%
                                    </span>

                                @endif

                                vs periode sebelumnya

                            </div>

                        </div>

                        <div class="col-auto">

                            <div class="kpi-icon"
                                 style="background:#e5f8fb;color:#36b9cc;">

                                <i class="fas fa-calculator"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Produk Terjual -->
        <div class="col-xl-3 col-md-6 mb-4">

            <div class="card kpi-card"
                 style="border-left-color:#f6c23e;">

                <div class="card-body">

                    <div class="row align-items-center">

                        <div class="col">

                            <div class="kpi-label">
                                Produk Terjual
                            </div>

                            <div class="kpi-value">
                                {{ number_format($totalProductsSold ?? 1284, 0, ',', '.') }}
                            </div>

                            <div class="kpi-footer">

                                @if(($productGrowth ?? 0) >= 0)

                                    <span class="kpi-up">
                                        <i class="fas fa-arrow-up"></i>
                                        {{ $productGrowth ?? '5,4' }}%
                                    </span>

                                @else

                                    <span class="kpi-down">
                                        <i class="fas fa-arrow-down"></i>
                                        {{ abs($productGrowth ?? 0) }}%
                                    </span>

                                @endif

                                vs periode sebelumnya

                            </div>

                        </div>

                        <div class="col-auto">

                            <div class="kpi-icon"
                                 style="background:#fff7df;color:#f6c23e;">

                                <i class="fas fa-box"></i>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================
         GRAFIK PENJUALAN
    ========================================== -->

    <div class="row">

        <div class="col-xl-8 col-lg-7 mb-4">

            <div class="card report-card">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="m-0 font-weight-bold text-primary">
                                Penjualan
                            </h6>

                            <small class="text-muted">
                                Performa penjualan berdasarkan periode
                            </small>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="chart-container">

                        <canvas id="salesChart"></canvas>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================
             PRODUK TERLARIS
        ====================================== -->

        <div class="col-xl-4 col-lg-5 mb-4">

            <div class="card report-card">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <h6 class="m-0 font-weight-bold text-primary">
                            Produk Terlaris
                        </h6>

                        <a href="{{ url('/laporan/produk/terlaris') }}"
                           class="small">
                            Lihat semua
                        </a>

                    </div>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table report-table">

                            <thead>

                                <tr>
                                    <th class="pl-3">#</th>
                                    <th>Produk</th>
                                    <th class="text-right pr-3">
                                        Terjual
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                @forelse(($topProducts ?? []) as $index => $product)

                                    <tr>

                                        <td class="pl-3">
                                            <span class="product-rank">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="product-name">
                                                {{ $product->name }}
                                            </span>
                                        </td>

                                        <td class="text-right pr-3">

                                            <span class="product-qty">
                                                {{ number_format($product->total_qty, 0, ',', '.') }}
                                            </span>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="3"
                                            class="text-center text-muted py-4">

                                            Belum ada data produk.

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================
         KATEGORI
    ========================================== -->

    <div class="row">

        <div class="col-xl-6 mb-4">

            <div class="card report-card">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="m-0 font-weight-bold text-primary">
                                Penjualan per Kategori
                            </h6>

                            <small class="text-muted">
                                Kontribusi penjualan setiap kategori
                            </small>

                        </div>

                        <a href="{{ url('/laporan/produk/per-kategori') }}"
                           class="small">

                            Lihat detail

                        </a>

                    </div>

                </div>

                <div class="card-body">

                    @forelse(($categorySales ?? []) as $category)

                        <div class="category-item">

                            <div class="category-header">

                                <span class="category-name">
                                    {{ $category->name }}
                                </span>

                                <span class="category-value">

                                    Rp {{ number_format($category->total_sales, 0, ',', '.') }}

                                    ({{ number_format($category->percentage, 1) }}%)

                                </span>

                            </div>

                            <div class="progress category-progress">

                                <div class="progress-bar"
                                     role="progressbar"
                                     style="width: {{ $category->percentage }}%;"
                                     aria-valuenow="{{ $category->percentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="text-center text-muted py-4">

                            Belum ada data kategori.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>


        <!-- =====================================
             CATEGORY CHART
        ====================================== -->

        <div class="col-xl-6 mb-4">

            <div class="card report-card">

                <div class="card-header bg-white py-3">

                    <h6 class="m-0 font-weight-bold text-primary">
                        Distribusi Kategori
                    </h6>

                </div>

                <div class="card-body">

                    <div class="chart-container">

                        <canvas id="categoryChart"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================
     CHART.JS
========================================= -->

@php
    $chartSalesLabels = $salesLabels ?? [
        '08:00',
        '09:00',
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
    ];

    $chartSalesData = $salesData ?? [
        450000,
        620000,
        850000,
        1100000,
        1650000,
        1300000,
        950000,
        780000,
        920000,
        1150000,
        1250000,
        1350000,
        1180000,
    ];

    $chartCategoryLabels = $categoryLabels ?? [
        'Coffee',
        'Non Coffee',
        'Food',
        'Snack',
    ];

    $chartCategoryData = $categoryData ?? [
        45,
        25,
        20,
        10,
    ];
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =====================================
       SALES CHART
    ====================================== */

    const salesLabels = @json($chartSalesLabels);
    const salesData = @json($chartSalesData);

    const salesCtx = document.getElementById('salesChart');

    if (salesCtx) {

        new Chart(salesCtx, {
            type: 'line',

            data: {
                labels: salesLabels,

                datasets: [{
                    label: 'Penjualan',
                    data: salesData,
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index'
                },

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {

                            label: function(context) {

                                return ' Rp ' +
                                    new Intl.NumberFormat('id-ID')
                                        .format(context.raw);

                            }

                        }
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            callback: function(value) {

                                return 'Rp ' +
                                    new Intl.NumberFormat('id-ID', {
                                        notation: 'compact',
                                        maximumFractionDigits: 1
                                    }).format(value);

                            }

                        }

                    },

                    x: {

                        grid: {
                            display: false
                        }

                    }

                }

            }

        });

    }


    /* =====================================
       CATEGORY CHART
    ====================================== */

    const categoryLabels = @json($chartCategoryLabels);
    const categoryData = @json($chartCategoryData);

    const categoryCtx = document.getElementById('categoryChart');

    if (categoryCtx) {

        new Chart(categoryCtx, {

            type: 'doughnut',

            data: {

                labels: categoryLabels,

                datasets: [{

                    data: categoryData,

                    borderWidth: 2

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '65%',

                plugins: {

                    legend: {

                        position: 'bottom',

                        labels: {

                            padding: 15,
                            usePointStyle: true

                        }

                    },

                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                return context.label +
                                    ': ' +
                                    context.raw +
                                    '%';

                            }

                        }

                    }

                }

            }

        });

    }

});




/* =========================================
   CUSTOM DATE
========================================= */

function toggleCustomDate() {

    const periode =
        document.getElementById('periode').value;

    const customDate =
        document.querySelectorAll('.custom-date');

    customDate.forEach(function(element) {

        if (periode === 'custom') {

            element.style.display = '';

        } else {

            element.style.display = 'none';

        }

    });

}


document.addEventListener('DOMContentLoaded', function () {

    toggleCustomDate();

    document
        .getElementById('periode')
        .addEventListener('change', toggleCustomDate);

});

</script>

@endsection