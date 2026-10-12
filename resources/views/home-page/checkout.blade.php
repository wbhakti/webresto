@extends('home-page.layouts.app-home')

@section('content')

<style>

:root {
    --payment-navy: #172554;
    --payment-navy-dark: #0F1B40;
    --payment-navy-light: #EFF6FF;
    --payment-orange: #F59E0B;
    --payment-orange-hover: #D97706;
    --payment-bg: #F8FAFC;
    --payment-white: #FFFFFF;
    --payment-border: #E2E8F0;
    --payment-text: #1E293B;
    --payment-muted: #64748B;
    --payment-success: #15803D;
    --payment-success-bg: #F0FDF4;
    --payment-success-border: #BBF7D0;
}

/* =====================================
   GLOBAL PAYMENT PAGE
===================================== */

.payment-page {
    background: var(--payment-bg);
    color: var(--payment-text);
    min-height: 100vh;
    padding-bottom: 48px;
}

.payment-page,
.payment-page * {
    box-sizing: border-box;
}

/* =====================================
   HEADER
===================================== */

.payment-header {
    background: var(--payment-navy);
    color: var(--payment-white);
    padding: 34px 16px;
    position: relative;
    overflow: hidden;
}

.payment-header::after {
    content: "";
    display: block;
    width: 100px;
    height: 4px;
    background: var(--payment-orange);
    border-radius: 10px;
    margin: 18px auto 0;
}

.payment-header h1 {
    color: var(--payment-white);
    font-size: clamp(24px, 4vw, 34px);
    font-weight: 800;
    letter-spacing: -0.5px;
    line-height: 1.3;
    margin-bottom: 8px;
}

.payment-header p {
    color: #CBD5E1;
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
}

/* =====================================
   MAIN CONTAINER
===================================== */

.payment-container {
    width: 100%;
    max-width: 760px;
    margin: 0 auto;
    padding: 26px 15px;
}

/* =====================================
   STATUS ALERT
===================================== */

.payment-alert {
    background: var(--payment-success-bg);
    color: #166534;
    border: 1px solid var(--payment-success-border);
    border-left: 5px solid var(--payment-success);
    border-radius: 12px;
    padding: 17px 18px;
    margin-bottom: 20px;
}

.payment-alert h5 {
    color: #166534;
    font-size: 16px;
    font-weight: 800;
    line-height: 1.4;
    margin-bottom: 6px;
}

.payment-alert p {
    color: #166534;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 0;
}

/* =====================================
   CARD
===================================== */

.payment-card {
    background: var(--payment-white);
    border: 1px solid var(--payment-border);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 5px 20px rgba(15, 23, 42, 0.035);
}

.payment-section-title {
    color: var(--payment-navy);
    font-size: 18px;
    font-weight: 800;
    line-height: 1.4;
    margin-bottom: 20px;
}

/* Garis aksen di bawah judul */

.payment-section-title::after {
    content: "";
    display: block;
    width: 42px;
    height: 3px;
    background: var(--payment-orange);
    border-radius: 4px;
    margin-top: 10px;
}

/* =====================================
   TRANSACTION INFORMATION
===================================== */

.payment-info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 13px 0;
    border-bottom: 1px solid #F1F5F9;
    font-size: 14px;
    line-height: 1.6;
}

.payment-info-row:last-of-type {
    border-bottom: none;
}

.payment-info-label {
    color: var(--payment-muted);
    flex: 1;
}

.payment-info-value {
    color: var(--payment-text);
    font-weight: 700;
    text-align: right;
    overflow-wrap: anywhere;
}

/* =====================================
   TOTAL PAYMENT
===================================== */

.payment-total {
    background: var(--payment-navy-light);
    border: 1px solid #DBEAFE;
    border-radius: 12px;
    padding: 20px;
    margin-top: 18px;
}

.payment-total-label {
    color: #475569;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
}

.payment-total-value {
    color: var(--payment-navy);
    font-size: clamp(24px, 5vw, 32px);
    font-weight: 800;
    letter-spacing: -0.5px;
    line-height: 1.35;
    overflow-wrap: anywhere;
}

/* =====================================
   ORDER DETAILS
===================================== */

.payment-order-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #F1F5F9;
}

.payment-order-item:first-of-type {
    padding-top: 0;
}

.payment-order-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.payment-product-name {
    color: var(--payment-text);
    font-size: 15px;
    font-weight: 700;
    line-height: 1.5;
    margin-bottom: 5px;
}

.payment-product-detail {
    color: var(--payment-muted);
    font-size: 13px;
    line-height: 1.8;
    overflow-wrap: anywhere;
}

.payment-product-price {
    color: var(--payment-navy);
    font-size: 14px;
    font-weight: 800;
    line-height: 1.6;
    text-align: right;
    white-space: nowrap;
}

/* =====================================
   QRIS SECTION
===================================== */

.payment-qr-box {
    background: var(--payment-white);
    border: 1px solid var(--payment-border);
    border-radius: 14px;
    padding: 24px 18px;
    text-align: center;
}

.payment-qr-box h6 {
    color: var(--payment-navy);
    font-size: 17px;
    font-weight: 800;
    line-height: 1.5;
}

.payment-qr-box #qrcode {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 340px;
    min-height: 300px;
    margin: 18px auto;
    padding: 10px;
    background: #FFFFFF;
    border: 1px solid #F1F5F9;
    border-radius: 12px;
}

/* Jangan mengubah rasio QRIS */

.payment-qr-box #qrcode canvas,
.payment-qr-box #qrcode img {
    display: block;
    width: 300px !important;
    max-width: 100%;
    height: auto !important;
    object-fit: contain;
    image-rendering: pixelated;
}

.payment-qr-caption {
    color: var(--payment-muted);
    font-size: 13px;
    line-height: 1.7;
}

/* =====================================
   PRIMARY BUTTONS
===================================== */

.payment-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    min-height: 48px;
    padding: 13px 18px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 800;
    line-height: 1.4;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    transition:
        background-color 0.2s ease,
        border-color 0.2s ease,
        box-shadow 0.2s ease,
        transform 0.2s ease;
}

.payment-btn-green {
    background: var(--payment-orange);
    border: 1px solid var(--payment-orange);
    color: #172033;
}

.payment-btn-green:hover,
.payment-btn-green:focus {
    background: var(--payment-orange-hover);
    border-color: var(--payment-orange-hover);
    color: #FFFFFF;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);
}

.payment-btn:active {
    transform: translateY(1px);
}

.payment-btn:focus-visible,
.payment-back-btn:focus-visible {
    outline: 3px solid #93C5FD;
    outline-offset: 3px;
}

/* =====================================
   PAYMENT INSTRUCTIONS
===================================== */

.payment-steps {
    list-style: none;
    padding: 0;
    margin: 0;
    counter-reset: payment-step;
}

.payment-steps li {
    counter-increment: payment-step;
    position: relative;
    padding: 3px 0 17px 44px;
    color: var(--payment-text);
    font-size: 14px;
    line-height: 1.8;
}

.payment-steps li:last-child {
    padding-bottom: 0;
}

.payment-steps li::before {
    content: counter(payment-step);
    position: absolute;
    top: 0;
    left: 0;
    width: 29px;
    height: 29px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--payment-navy-light);
    border: 1px solid #DBEAFE;
    color: var(--payment-navy);
    border-radius: 50%;
    font-size: 13px;
    font-weight: 800;
}

/* =====================================
   UPLOAD FORM
===================================== */

.payment-upload-input {
    display: block;
    width: 100%;
    min-height: 50px;
    padding: 12px;
    background: #FAFCFF;
    border: 1px dashed #94A3B8;
    border-radius: 10px;
    color: var(--payment-text);
    font-size: 14px;
}

.payment-upload-input:hover {
    border-color: var(--payment-navy);
}

.payment-upload-input:focus {
    background: #FFFFFF;
    border-color: var(--payment-navy);
    outline: none;
    box-shadow: 0 0 0 3px rgba(23, 37, 84, 0.1);
}

.payment-card .form-label {
    color: var(--payment-text);
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 9px;
}

/* =====================================
   SUCCESS / ERROR STATES
===================================== */

.payment-page .text-success {
    color: var(--payment-success) !important;
}

#buktiPembayaranPreview .alert-success {
    background: var(--payment-success-bg);
    color: #166534;
    border: 1px solid var(--payment-success-border);
    border-radius: 10px;
    font-size: 14px;
}

/* =====================================
   BACK TO MENU
===================================== */

.payment-back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 46px;
    padding: 12px 22px;
    background: #FFFFFF;
    color: var(--payment-navy);
    border: 1px solid #CBD5E1;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    transition: all 0.2s ease;
}

.payment-back-btn:hover {
    background: var(--payment-navy);
    border-color: var(--payment-navy);
    color: #FFFFFF;
    text-decoration: none;
}

/* =====================================
   EXISTING BOOTSTRAP MODALS
===================================== */

.large-button {
    width: 100%;
    max-width: 300px;
    min-height: 48px;
    padding: 12px 20px;
    background: var(--payment-orange);
    color: #172033;
    border: 1px solid var(--payment-orange);
    border-radius: 10px;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.large-button:hover {
    background: var(--payment-orange-hover);
    border-color: var(--payment-orange-hover);
    color: #FFFFFF;
}

.modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

.modal-header {
    border-bottom-color: var(--payment-border);
}

.modal-footer {
    border-top-color: var(--payment-border);
}

/* =====================================
   RESPONSIVE MOBILE
===================================== */

@media (max-width: 576px) {
    .payment-header {
        padding: 27px 16px;
    }

    .payment-container {
        padding: 18px 12px;
    }

    .payment-card {
        padding: 18px 16px;
        margin-bottom: 15px;
        border-radius: 13px;
    }

    .payment-section-title {
        font-size: 17px;
        margin-bottom: 17px;
    }

    .payment-info-row {
        gap: 12px;
        padding: 12px 0;
        font-size: 13px;
    }

    .payment-total {
        padding: 17px 15px;
    }

    .payment-order-item {
        gap: 10px;
        padding: 14px 0;
    }

    .payment-product-name {
        font-size: 14px;
    }

    .payment-product-detail {
        font-size: 12px;
    }

    .payment-product-price {
        font-size: 12px;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .payment-qr-box {
        padding: 18px 12px;
    }

    .payment-qr-box #qrcode {
        min-height: 0;
        padding: 8px;
    }

    .payment-steps li {
        padding-left: 40px;
        font-size: 13px;
    }

    .payment-btn {
        font-size: 14px;
    }
}

/* =====================================
   REDUCED MOTION ACCESSIBILITY
===================================== */

@media (prefers-reduced-motion: reduce) {
    .payment-btn,
    .payment-back-btn,
    .large-button {
        transition: none;
    }
}


/* COLLAPSE PEMBAYARAN */
.payment-collapse-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 18px;
    border: 0;
    background: #fff;
    color: #142B4A;
    text-align: left;
    font-size: 15px;
    font-weight: 700;
    border-radius: 12px;
    cursor: pointer;
}

.payment-collapse-toggle:hover {
    background: #F7F9FC;
}

.payment-collapse-toggle:focus-visible {
    outline: 2px solid #F28C28;
    outline-offset: -3px;
}

.collapse-label {
    flex-shrink: 0;
    color: #142B4A;
    font-size: 13px;
    font-weight: 600;
}

.payment-collapse-toggle:hover .collapse-label {
    color: #F28C28;
}

.payment-collapse-content {
    padding: 4px 18px 18px;
}

.payment-collapse-card {
    padding: 0;
    overflow: hidden;
}

.payment-collapse-card .payment-info-row:first-child,
.payment-collapse-card .payment-order-item:first-child {
    border-top: 1px solid #E5EAF0;
}

.payment-collapse-card .payment-total {
    margin-top: 16px;
}

.payment-collapse-subtitle {
    color: #748094;
    font-size: 12px;
    font-weight: 400;
    margin-left: 8px;
}

@media (max-width: 576px) {
    .payment-collapse-toggle {
        padding: 15px;
        font-size: 14px;
    }

    .payment-collapse-content {
        padding: 4px 15px 15px;
    }

    .payment-collapse-subtitle {
        display: block;
        margin: 4px 0 0;
    }
}
</style>

<script src="{{ asset('qriscode/qrcode.min.js')}}"></script>

<!-- Header -->
<header class="payment-header">
    <div class="container text-center">
        <h1>Konfirmasi Pembayaran</h1>
        <p>Periksa pesanan Anda dan selesaikan pembayaran.</p>
    </div>
</header>

<main class="payment-page">
    <div class="payment-container">

        <!-- STATUS TRANSAKSI -->
        <div class="payment-alert" role="status">
            <h5>{{ $head }}</h5>
            <p>{{ $body }}</p>
        </div>

        <!-- INFORMASI PEMESANAN -->
        <section class="payment-card payment-collapse-card">

        <button
            type="button"
            class="payment-collapse-toggle"
            data-bs-toggle="collapse"
            data-bs-target="#collapseInformasi"
            aria-expanded="false"
            aria-controls="collapseInformasi">

            <span>
                🧾 Informasi Pemesanan
                <span class="payment-collapse-subtitle">
                    {{ $invoiceNumber }}
                </span>
            </span>

            <span class="collapse-label">Tampilkan</span>
        </button>

        <div id="collapseInformasi" class="collapse">
            <div class="payment-collapse-content">

                <div class="payment-info-row">
                    <span class="payment-info-label">ID Transaksi</span>
                    <span class="payment-info-value">
                        {{ $invoiceNumber }}
                    </span>
                </div>

                <div class="payment-info-row">
                    <span class="payment-info-label">Nama</span>
                    <span class="payment-info-value">
                        {{ $nama }}
                    </span>
                </div>

                <div class="payment-info-row">
                    <span class="payment-info-label">Metode Pembayaran</span>
                    <span class="payment-info-value">
                        {{ $metodePembayaran }}
                    </span>
                </div>

                <div class="payment-info-row">
                    <span class="payment-info-label">Subtotal Pemesanan</span>
                    <span class="payment-info-value">
                        Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                    </span>
                </div>

                @if (($discount ?? 0) > 0)
                    <div class="payment-info-row">
                        <span class="payment-info-label">Diskon</span>
                        <span class="payment-info-value text-success">
                            - Rp {{ number_format($discount, 0, ',', '.') }}
                        </span>
                    </div>
                @endif

            </div>
        </div>

        <!-- Total tetap terlihat meskipun collapse tertutup -->
        <div class="payment-total mx-3 mb-3">
            <div class="payment-total-label">
                Total yang harus dibayar
            </div>

            <div class="payment-total-value">
                Rp {{ number_format($totalTagihan - ($discount ?? 0), 0, ',', '.') }}
            </div>
        </div>

        </section>

        
        <!-- DETAIL PESANAN -->
        <section class="payment-card payment-collapse-card">

        <button
            type="button"
            class="payment-collapse-toggle"
            data-bs-toggle="collapse"
            data-bs-target="#collapseDetailPesanan"
            aria-expanded="false"
            aria-controls="collapseDetailPesanan">

            <span>
                🍽️ Detail Pesanan
                <span class="payment-collapse-subtitle">
                    {{ count($details) }} item
                </span>
            </span>

            <span class="collapse-label">Tampilkan</span>
        </button>

        <div id="collapseDetailPesanan" class="collapse">
            <div class="payment-collapse-content">

                @foreach ($details as $item)
                    @php
                        $productDiscount = $item->discount ?? 0;
                        $subtotalProduk = $item->quantity * $item->price;
                        $diskonProduk = $item->quantity * $productDiscount;
                        $totalProduk = $subtotalProduk - $diskonProduk;
                    @endphp

                    <div class="payment-order-item">
                        <div>
                            <div class="payment-product-name">
                                {{ $item->product_name }}
                            </div>

                            <div class="payment-product-detail">
                                {{ $item->quantity }} ×
                                Rp {{ number_format($item->price, 0, ',', '.') }}

                                @if (!empty($item->note))
                                    <br>
                                    Catatan: {{ $item->note }}
                                @endif

                                @if ($productDiscount > 0)
                                    <br>
                                    Diskon produk:
                                    Rp {{ number_format($diskonProduk, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>

                        <div class="payment-product-price">
                            Rp {{ number_format($totalProduk, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        </section>

        <!-- PEMBAYARAN QRIS -->
        @if ($isQRIS)
            <section class="payment-card">
                <h5 class="payment-section-title">
                    📲 Pembayaran QRIS
                </h5>

                <div class="payment-qr-box">
                    <h6 class="fw-bold mb-2">
                        Scan QRIS untuk Membayar
                    </h6>

                    <p class="payment-qr-caption">
                        Gunakan aplikasi mobile banking atau e-wallet
                        yang mendukung QRIS.
                    </p>

                    <div id="qrcode"></div>

                    <div class="payment-total mt-3">
                        <div class="payment-total-label">
                            Nominal pembayaran
                        </div>

                        <div class="payment-total-value">
                            Rp {{ number_format($totalTagihan - $discount, 0, ',', '.') }}
                        </div>
                    </div>

                    <button
                        type="button"
                        class="btn payment-btn payment-btn-green mt-3"
                        onclick="downloadQR(@js($invoiceNumber))">
                        ⬇️ Simpan QRIS
                    </button>

                    <p class="payment-qr-caption mt-3 mb-0">
                        Simpan gambar QRIS jika Anda ingin
                        membukanya dari perangkat lain.
                    </p>
                </div>
            </section>

            <!-- PANDUAN PEMBAYARAN -->
            <section class="payment-card">
                <h5 class="payment-section-title">
                    📋 Panduan Pembayaran
                </h5>

                <ol class="payment-steps">
                    <li>Scan QRIS menggunakan aplikasi pembayaran Anda.</li>
                    <li>Pastikan nominal pembayaran sesuai total tagihan di atas.</li>
                    <li>Selesaikan pembayaran dan simpan bukti transaksi.</li>
                    <li>Upload bukti pembayaran melalui formulir di bawah.</li>
                    <li>Setelah proses upload, tekan tombol konfirmasi untuk melanjutkan melalui WhatsApp.</li>
                </ol>
            </section>

            <!-- UPLOAD BUKTI PEMBAYARAN -->
            <section class="payment-card">
                <h5 class="payment-section-title">
                    📸 Upload Bukti Pembayaran
                </h5>

                <p class="payment-qr-caption mb-3">
                    Pilih gambar bukti pembayaran dari perangkat Anda.
                </p>

                <form
                    id="uploadBuktiForm"
                    enctype="multipart/form-data">

                    @csrf

                    <input
                        type="hidden"
                        name="idtransaksi"
                        value="{{ $idtransaksi }}">

                    <input
                        type="hidden"
                        name="nama"
                        value="{{ $nama }}">

                    <label
                        for="bukti_pembayaran"
                        class="form-label fw-bold">
                        File bukti pembayaran
                    </label>

                    <input
                        type="file"
                        name="bukti_pembayaran"
                        id="bukti_pembayaran"
                        class="form-control payment-upload-input"
                        accept="image/*"
                        required>

                    <button
                        type="submit"
                        class="btn payment-btn payment-btn-green mt-3">
                        Upload Bukti Pembayaran
                    </button>
                </form>

                <div
                    id="buktiPembayaranPreview"
                    class="mt-3 d-none"
                    role="status">
                    <div class="alert alert-success mb-0">
                        ✓ Bukti pembayaran berhasil diproses.
                    </div>
                </div>
            </section>
        @endif

    </div>
</main>

<!-- GENERATE QRIS -->
@if ($isQRIS)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qrBox = document.getElementById('qrcode');

            if (!qrBox || typeof QRCode === 'undefined') {
                console.error('Elemen QRIS atau library QRCode tidak tersedia.');
                return;
            }

            qrBox.innerHTML = '';

            new QRCode(qrBox, {
                text: @js($qrisDynamic),
                width: 300,
                height: 300,
                correctLevel: QRCode.CorrectLevel.M
            });
        });
    </script>
@endif

<!-- Modal Bootstrap CASH-->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">{{ $head }}</h4>
                    <p>{{ $body }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="sendWa">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bootstrap QRIS-->
<div class="modal fade" id="successModalQris" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Upload Berhasil</h4>
                    <p>Bukti transaksi sudah berhasil dikirim, silahkan klik konfirmasi untuk melanjutkan pemesanan!</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="large-button" id="sendWaQris">Konfirmasi Disini</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bootstrap UPLOAD-->
<div class="modal fade" id="modalUpload" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Upload Bukti Bayar</h4>
                    <p>Silahkan Tunggu Sebentar</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bootstrap ERROR-->
<div class="modal fade" id="modalUploadError" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">Upload Bukti Bayar Gagal</h4>
                    <p>Silahkan klik konfirmasi untuk melanjutkan pemesanan!, Terimakasih</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="large-button" id="sendWaQrisFail">Konfirmasi Disini</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ========================================
// 1. MEMBUAT FRAME QR CODE
// ========================================
function createQRFrame(invoiceNumber) {
    const qrCanvas = document.querySelector("#qrcode canvas");

    if (!qrCanvas || !qrCanvas.width || !qrCanvas.height) {
        throw new Error("QR Code belum tersedia.");
    }

    const padding = 40;
    const qrSize = 320;
    const headerHeight = 85;
    const footerHeight = 75;

    const frameWidth = qrSize + padding * 2;
    const frameHeight = headerHeight + qrSize + footerHeight;

    const canvas = document.createElement("canvas");
    canvas.width = frameWidth;
    canvas.height = frameHeight;

    const ctx = canvas.getContext("2d");

    // Background putih
    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(0, 0, frameWidth, frameHeight);

    // Border hijau tua
    ctx.strokeStyle = "#033800";
    ctx.lineWidth = 5;

    ctx.beginPath();
    ctx.roundRect(
        3, 3,
        frameWidth - 6,
        frameHeight - 6,
        24
    );
    ctx.stroke();

    // Judul
    ctx.fillStyle = "#033800";
    ctx.font = "bold 27px Arial";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText("SCAN QR CODE", frameWidth / 2, 43);

    // QR Code
    ctx.imageSmoothingEnabled = false;
    ctx.drawImage(
        qrCanvas,
        padding,
        headerHeight,
        qrSize,
        qrSize
    );

    // Garis pemisah
    ctx.strokeStyle = "#D9E2D7";
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(padding, headerHeight + qrSize + 18);
    ctx.lineTo(frameWidth - padding, headerHeight + qrSize + 18);
    ctx.stroke();

    // Label invoice
    ctx.fillStyle = "#333333";
    ctx.font = "16px Arial";
    ctx.fillText(
        "INVOICE",
        frameWidth / 2,
        headerHeight + qrSize + 38
    );

    // Nomor invoice
    ctx.fillStyle = "#033800";
    ctx.font = "bold 18px Arial";

    let invoiceText = String(invoiceNumber);
    const maxTextWidth = frameWidth - 40;

    while (
        ctx.measureText(invoiceText).width > maxTextWidth &&
        invoiceText.length > 4
    ) {
        invoiceText = invoiceText.slice(0, -1);
    }

    if (invoiceText !== String(invoiceNumber)) {
        invoiceText += "...";
    }

    ctx.fillText(
        invoiceText,
        frameWidth / 2,
        headerHeight + qrSize + 59
    );

    return canvas;
}


// ========================================
// 2. CANVAS MENJADI PNG BLOB
// ========================================
function canvasToBlob(canvas) {
    return new Promise((resolve, reject) => {
        canvas.toBlob(blob => {
            if (blob) {
                resolve(blob);
            } else {
                reject(new Error("Gagal membuat gambar PNG."));
            }
        }, "image/png");
    });
}


// ========================================
// 3. DOWNLOAD / SHARE QR CODE
// ========================================
async function downloadQR(invoiceNumber) {
    try {
        const filename =
            String(invoiceNumber)
                .replace(/[^a-zA-Z0-9_-]/g, "_") + ".png";

        // Buat frame
        const framedCanvas = createQRFrame(invoiceNumber);

        // Konversi ke PNG
        const blob = await canvasToBlob(framedCanvas);

        const file = new File([blob], filename, {
            type: "image/png"
        });

        // iPhone / iPad: gunakan Share Sheet jika mendukung file
        const isIOS =
            /iPhone|iPad|iPod/i.test(navigator.userAgent) ||
            (navigator.platform === "MacIntel" &&
             navigator.maxTouchPoints > 1);

        if (
            isIOS &&
            navigator.share &&
            navigator.canShare &&
            navigator.canShare({ files: [file] })
        ) {
            await navigator.share({
                files: [file],
                title: "QR Code " + invoiceNumber
            });

            return;
        }

        // Fallback: download PNG
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");

        link.href = url;
        link.download = filename;

        document.body.appendChild(link);
        link.click();
        link.remove();

        setTimeout(() => {
            URL.revokeObjectURL(url);
        }, 60000);

    } catch (error) {
        if (error.name === "AbortError") {
            return;
        }

        console.error("Download QR error:", error);
        alert("Gagal menyimpan QR Code: " + error.message);
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.payment-collapse-toggle').forEach(function (button) {
        const targetSelector = button.getAttribute('data-bs-target');
        const collapseElement = document.querySelector(targetSelector);
        const label = button.querySelector('.collapse-label');

        if (!collapseElement || !label) return;

        function updateLabel() {
            label.textContent =
                button.getAttribute('aria-expanded') === 'true'
                    ? 'Sembunyikan'
                    : 'Tampilkan';
        }

        collapseElement.addEventListener('show.bs.collapse', function () {
            label.textContent = 'Sembunyikan';
        });

        collapseElement.addEventListener('hide.bs.collapse', function () {
            label.textContent = 'Tampilkan';
        });

        updateLabel();
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isQRIS = {{ $isQRIS ? 'true' : 'false' }};

        if (!isQRIS) {
            let modal = new bootstrap.Modal(document.getElementById("successModal"));
            modalConfirmation.show();

            document.getElementById("sendWa").addEventListener("click", function() {
                // Data Pesanan
                let phoneWa = "{{ $phone_wa }}";
                let invoiceNumber = "{{ $invoiceNumber }}";
                let nama = "{{ $nama }}";
                let meja = "{{ $meja }}";
                let metodePembayaran = "{{ $metodePembayaran }}";
                let totalDiscount = "Rp {{ number_format($discount, 0, ',', '.') }}";
                let totalTagihan = "Rp {{ number_format($totalTagihan, 0, ',', '.') }}";
                let totalPembayaran = "Rp {{ number_format(($totalTagihan-$discount), 0, ',', '.') }}";

                // Detail Pesanan
                let pesanDetail = "";
                @foreach ($details as $item)
                    pesanDetail += "{{ $item->product_name }} [{{ $item->quantity }}] Rp {{ ($item->price * $item->quantity) - ($item->discount * $item->quantity) }} %0A";
                    pesanDetail += "Catatan : {{ $item->note }} %0A";
                @endforeach

                // Format Pesan WhatsApp
                let waMessage = `Hallo, saya ingin mengkonfirmasi pesanan saya.%0A%0A` +
                                `ID Transaksi: ${invoiceNumber}%0A` +
                                `Nama: ${nama}%0A` +
                                `Nomor Meja: ${meja}%0A` +
                                `Metode Pembayaran: ${metodePembayaran}%0A` +
                                `Sub Total Pemesanan: ${totalTagihan}%0A` +
                                `Diskon: ${totalDiscount}%0A` +
                                `Total Pembayaran: ${totalPembayaran}%0A%0A` +
                                `*Detail Pesanan:*%0A` + pesanDetail + `%0A%0A `;

                let waLink = `https://wa.me/${phoneWa}?text=${waMessage}`;

                window.open(waLink, '_blank');
                modalConfirmation.hide();

            });
        }
    });
</script>

<script>
    document.getElementById('uploadBuktiForm').addEventListener('submit', function (event) {
        event.preventDefault();

        const dialogUpload = document.getElementById('modalUpload');
        const modalUpload = bootstrap.Modal.getOrCreateInstance(dialogUpload);
        const modalConfirmation = new bootstrap.Modal(document.getElementById("successModalQris"));
        const modalConfirmationErr = new bootstrap.Modal(document.getElementById("modalUploadError"));

        modalUpload.show();

        let formData = new FormData(this);
        fetch("{{ route('upload') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
            }    
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {

                setTimeout(() => {
                    modalUpload.hide();
                }, 1000);

                setTimeout(() => {
                    modalConfirmation.show();
                }, 1000);

                

                document.getElementById("sendWaQris").addEventListener("click", function() {
                    let imageUrl = data.imageUrl;
                    // Data Pesanan
                    let phoneWa = "{{ $phone_wa }}";
                    let invoiceNumber = "{{ $invoiceNumber }}";
                    let nama = "{{ $nama }}";
                    let meja = "{{ $meja }}";
                    let metodePembayaran = "{{ $metodePembayaran }}";
                    let totalDiscount = "Rp {{ number_format($discount, 0, ',', '.') }}";
                    let totalTagihan = "Rp {{ number_format($totalTagihan, 0, ',', '.') }}";
                    let totalPembayaran = "Rp {{ number_format(($totalTagihan-$discount), 0, ',', '.') }}";

                        // Detail Pesanan
                    let pesanDetail = "";
                    @foreach ($details as $item)
                        pesanDetail += "{{ $item->product_name}} [{{ $item->quantity }}] Rp {{ ($item->price * $item->quantity) - ($item->discount * $item->quantity) }} %0A";
                        pesanDetail += "Catatan : {{ $item->note }} %0A";
                    @endforeach

                    // Format Pesan WhatsApp
                    let waMessage = `Hallo, saya ingin mengkonfirmasi pesanan saya.%0A%0A` +
                                    `ID Transaksi: ${invoiceNumber}%0A` +
                                    `Nama: ${nama}%0A` +
                                    `Nomor Meja: ${meja}%0A` +
                                    `Metode Pembayaran: ${metodePembayaran}%0A` +
                                    `Sub Total Pemesanan: ${totalTagihan}%0A` +
                                    `Diskon: ${totalDiscount}%0A` +
                                    `Total Pembayaran: ${totalPembayaran}%0A%0A` +
                                    `Bukti Transfer: ${imageUrl}%0A%0A` +
                                    `*Detail Pesanan:*%0A` + pesanDetail + `%0A%0A `;

                    let waLink = `https://wa.me/${phoneWa}?text=${waMessage}`;

                    document.getElementById('buktiPembayaranPreview').classList.remove('d-none');

                    // Buka WhatsApp
                    window.open(waLink, '_blank');
                    modalConfirmation.hide();

                });

            } else {
                setTimeout(() => {
                    modalUpload.hide();
                    modalConfirmationErr.show();
                }, 1000);

                //GAGAL
                document.getElementById("sendWaQrisFail").addEventListener("click", function() {
                    // Data Pesanan
                    let phoneWa = "{{ $phone_wa }}";
                    let invoiceNumber = "{{ $invoiceNumber }}";
                    let nama = "{{ $nama }}";
                    let meja = "{{ $meja }}";
                    let metodePembayaran = "{{ $metodePembayaran }}";
                    let totalDiscount = "Rp {{ number_format($discount, 0, ',', '.') }}";
                    let totalTagihan = "Rp {{ number_format($totalTagihan, 0, ',', '.') }}";
                    let totalPembayaran = "Rp {{ number_format(($totalTagihan-$discount), 0, ',', '.') }}";

                        // Detail Pesanan
                    let pesanDetail = "";
                    @foreach ($details as $item)
                        pesanDetail += "{{ $item->product_name }} [{{ $item->quantity }}] Rp {{ ($item->price * $item->quantity) - ($item->discount * $item->quantity) }} %0A";
                        pesanDetail += "Catatan : {{ $item->note }} %0A";
                    @endforeach

                    // Format Pesan WhatsApp
                    let waMessage = `Hallo, saya ingin mengkonfirmasi pesanan saya.%0A%0A` +
                                    `ID Transaksi: ${invoiceNumber}%0A` +
                                    `Nama: ${nama}%0A` +
                                    `Nomor Meja: ${meja}%0A` +
                                    `Metode Pembayaran: ${metodePembayaran}%0A` +
                                    `Sub Total Pemesanan: ${totalTagihan}%0A` +
                                    `Diskon: ${totalDiscount}%0A` +
                                    `Total Pembayaran: ${totalPembayaran}%0A%0A` +
                                    `Bukti Transfer: GAGAL UPLOAD%0A%0A` +
                                    `*Detail Pesanan:*%0A` + pesanDetail + `%0A%0A `;

                    let waLink = `https://wa.me/${phoneWa}?text=${waMessage}`;

                    document.getElementById('buktiPembayaranPreview').classList.remove('d-none');

                    // Buka WhatsApp
                    window.open(waLink, '_blank');
                    modalConfirmationErr.hide();
                });
            }
        }).catch(error => {
            console.error('Error:', error);

            setTimeout(() => {
                modalUpload.hide();
                modalConfirmationErr.show();
            }, 1000);

            //GAGAL
            document.getElementById("sendWaQrisFail").addEventListener("click", function() {
                    // Data Pesanan
                    let phoneWa = "{{ $phone_wa }}";
                    let invoiceNumber = "{{ $invoiceNumber }}";
                    let nama = "{{ $nama }}";
                    let meja = "{{ $meja }}";
                    let metodePembayaran = "{{ $metodePembayaran }}";
                    let totalDiscount = "Rp {{ number_format($discount, 0, ',', '.') }}";
                    let totalTagihan = "Rp {{ number_format($totalTagihan, 0, ',', '.') }}";
                    let totalPembayaran = "Rp {{ number_format(($totalTagihan-$discount), 0, ',', '.') }}";

                        // Detail Pesanan
                    let pesanDetail = "";
                    @foreach ($details as $item)
                        pesanDetail += "{{ $item->product_name }} [{{ $item->quantity }}] Rp {{ ($item->price * $item->quantity) - ($item->discount * $item->quantity) }} %0A";
                        pesanDetail += "Catatan : {{ $item->note }} %0A";
                    @endforeach

                    // Format Pesan WhatsApp
                    let waMessage = `Hallo, saya ingin mengkonfirmasi pesanan saya.%0A%0A` +
                                    `ID Transaksi: ${invoiceNumber}%0A` +
                                    `Nama: ${nama}%0A` +
                                    `Nomor Meja: ${meja}%0A` +
                                    `Metode Pembayaran: ${metodePembayaran}%0A` +
                                    `Sub Total Pemesanan: ${totalTagihan}%0A` +
                                    `Diskon: ${totalDiscount}%0A` +
                                    `Total Pembayaran: ${totalPembayaran}%0A%0A` +
                                    `Bukti Transfer: GAGAL UPLOAD%0A%0A` +
                                    `*Detail Pesanan:*%0A` + pesanDetail + `%0A%0A `;

                    let waLink = `https://wa.me/${phoneWa}?text=${waMessage}`;

                    document.getElementById('buktiPembayaranPreview').classList.remove('d-none');

                    // Buka WhatsApp
                    window.open(waLink, '_blank');
                    modalConfirmationErr.hide();
                });
            
        });  
    });
</script>


@endsection
