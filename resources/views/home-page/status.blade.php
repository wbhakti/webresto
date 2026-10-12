
@extends('home-page.layouts.app-home')

@section('content')

<style>
    :root {
        --payment-navy: #142B4A;
        --payment-navy-light: #203F65;
        --payment-orange: #F28C28;
        --payment-text: #253449;
        --payment-muted: #748094;
        --payment-border: #E5EAF0;
        --payment-bg: #F4F7FB;
        --payment-whatsapp: #20A957;
    }

    .payment-success-page {
        min-height: 75vh;
        padding: 40px 16px;
        background: var(--payment-bg);
        color: var(--payment-text);
        font-family: -apple-system, BlinkMacSystemFont,
            "Segoe UI", Roboto, Arial, sans-serif;
    }

    .payment-success-container {
        width: 100%;
        max-width: 510px;
        margin: 0 auto;
    }

    .success-card {
        width: 100%;
        background: #FFFFFF;
        border: 1px solid var(--payment-border);
        border-radius: 20px;
        padding: 36px;
        box-shadow: 0 12px 35px rgba(20, 43, 74, 0.06);
    }

    .success-icon {
        width: 82px;
        height: 82px;
        margin: 0 auto 22px;
        border-radius: 50%;
        background: #E8F7EE;
        color: #229653;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
    }

    .success-title {
        color: var(--payment-navy);
        font-size: 25px;
        font-weight: 800;
        line-height: 1.4;
        margin-bottom: 12px;
    }

    .success-description {
        color: var(--payment-muted);
        font-size: 14px;
        line-height: 1.8;
        margin-bottom: 26px;
    }

    .order-info {
        background: #F7F9FC;
        border: 1px solid var(--payment-border);
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 26px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .info-row + .info-row {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--payment-border);
    }

    .info-label {
        color: var(--payment-muted);
        font-size: 13px;
        flex-shrink: 0;
    }

    .invoice-value {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        min-width: 0;
    }

    .invoice-number {
        color: var(--payment-navy);
        font-size: 13px;
        font-weight: 750;
        overflow-wrap: anywhere;
        text-align: right;
    }

    .btn-copy {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border: 1px solid #D8E0EA;
        border-radius: 8px;
        background: #FFFFFF;
        color: var(--payment-navy);
        cursor: pointer;
    }

    .btn-copy:hover {
        border-color: var(--payment-orange);
        color: var(--payment-orange);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #FFF1DF;
        color: #A95A08;
        border-radius: 20px;
        padding: 7px 10px;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
    }

    .section-title {
        color: var(--payment-navy);
        font-size: 15px;
        font-weight: 750;
        margin-bottom: 14px;
    }

    .btn-whatsapp {
        display: flex;
        width: 100%;
        min-height: 52px;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 14px 18px;
        background: var(--payment-whatsapp);
        border: 0;
        border-radius: 10px;
        color: #FFFFFF;
        text-decoration: none;
        font-size: 15px;
        font-weight: 700;
        transition: background 0.2s ease;
    }

    .btn-whatsapp:hover,
    .btn-whatsapp:focus {
        background: #188A46;
        color: #FFFFFF;
    }

    .btn-whatsapp i {
        font-size: 22px;
    }

    .whatsapp-note {
        margin: 12px 0 0;
        color: var(--payment-muted);
        font-size: 12px;
        line-height: 1.7;
        text-align: center;
    }

    .divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 25px 0;
        color: #9AA5B5;
        font-size: 11px;
    }

    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--payment-border);
    }

    .btn-home {
        display: block;
        width: 100%;
        padding: 13px;
        text-align: center;
        border: 1px solid #D8E0EA;
        border-radius: 10px;
        color: var(--payment-navy);
        background: #FFFFFF;
        text-decoration: none;
        font-size: 14px;
        font-weight: 650;
        transition: all 0.2s ease;
    }

    .btn-home:hover {
        border-color: var(--payment-orange);
        color: var(--payment-navy);
        background: #FFFAF4;
    }

    .payment-success-footer {
        padding: 20px 12px 0;
        color: var(--payment-muted);
        font-size: 12px;
        text-align: center;
    }

    .payment-toast {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        max-width: calc(100% - 32px);
        background: var(--payment-navy);
        color: #FFFFFF;
        padding: 12px 18px;
        border-radius: 9px;
        font-size: 13px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        display: none;
        text-align: center;
    }

    @media (max-width: 576px) {
        .payment-success-page {
            padding: 24px 12px;
        }

        .success-card {
            padding: 26px 20px;
            border-radius: 16px;
        }

        .success-icon {
            width: 72px;
            height: 72px;
            font-size: 33px;
            margin-bottom: 18px;
        }

        .success-title {
            font-size: 22px;
        }

        .success-description {
            font-size: 13px;
        }

        .order-info {
            padding: 14px;
        }

        .info-row {
            align-items: flex-start;
        }

        .info-label {
            padding-top: 5px;
        }

        .status-badge {
            font-size: 10px;
            white-space: normal;
            justify-content: center;
        }
    }


    /* Baris informasi status */
    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #E2E8F0;
    }

    .info-label {
        color: #64748B;
        font-size: 14px;
    }

    /* Badge dasar */
    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.4;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 13px;
    }

    /* UNPAID - Merah */
    .status-unpaid {
        color: #DC2626;
        background: #FEF2F2;
        border: 1px solid #FECACA;
    }

    /* PAID - Oranye */
    .status-paid {
        color: #B45309;
        background: #FFF7ED;
        border: 1px solid #FED7AA;
    }

    /* PROCESSING - Biru */
    .status-processing {
        color: #1D4ED8;
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
    }

    /* COMPLETED - Hijau */
    .status-completed {
        color: #15803D;
        background: #F0FDF4;
        border: 1px solid #BBF7D0;
    }

    /* CANCELLED - Merah */
    .status-cancelled {
        color: #DC2626;
        background: #FEF2F2;
        border: 1px solid #FECACA;
    }

    /* Status tidak dikenal */
    .status-default {
        color: #172554;
        background: #F1F5F9;
        border: 1px solid #CBD5E1;
    }

    /* Tampilan mobile */
    @media (max-width: 480px) {
        .info-row {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .status-badge {
            font-size: 11px;
            padding: 6px 9px;
        }
    }
</style>



<main class="payment-success-page">
    <div class="payment-success-container">

        <section class="success-card">

            {{-- STATUS SUKSES --}}
            <div class="success-icon" aria-hidden="true">
                <i class="fa-solid fa-check"></i>
            </div>

            <div class="text-center">
                <h1 class="success-title">
                    Bukti Pembayaran
                    Berhasil Dikirim!
                </h1>

                <p class="success-description">
                    Langkah selanjutnya silahkan konfirmasi pesanan via.
                </p>
            </div>

            {{-- INFORMASI PESANAN --}}
            <div class="order-info">

                <div class="info-row">
                    <span class="info-label">Nomor Invoice</span>

                    <div class="invoice-value">
                        <span
                            class="invoice-number"
                            id="invoiceNumber">
                            {{ $transaction->invoice_number }}
                        </span>

                        <button
                            type="button"
                            class="btn-copy"
                            onclick="copyInvoice()"
                            aria-label="Salin nomor invoice"
                            title="Salin nomor invoice">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>
                </div>

                
            @php
                $status = strtoupper($transaction->order_status ?? 'UNPAID');

                $statusConfig = [
                    'UNPAID' => [
                        'label' => 'BELUM BAYAR',
                        'class' => 'status-unpaid',
                        'icon'  => 'fa-solid fa-circle-exclamation',
                    ],
                    'PAID' => [
                        'label' => 'SUDAH BAYAR',
                        'class' => 'status-paid',
                        'icon'  => 'fa-solid fa-money-bill-wave',
                    ],
                    'PROCESSING' => [
                        'label' => 'DI PROSES',
                        'class' => 'status-processing',
                        'icon'  => 'fa-solid fa-gear',
                    ],
                    'COMPLETED' => [
                        'label' => 'SELESAI',
                        'class' => 'status-completed',
                        'icon'  => 'fa-solid fa-circle-check',
                    ],
                    'CANCELLED' => [
                        'label' => 'DI BATALKAN',
                        'class' => 'status-cancelled',
                        'icon'  => 'fa-solid fa-circle-xmark',
                    ],
                ];

                $currentStatus = $statusConfig[$status] ?? [
                    'label' => $status,
                    'class' => 'status-default',
                    'icon'  => 'fa-solid fa-clock',
                ];
            @endphp

            <div class="info-row">
                <span class="info-label">Status Pesanan</span>

                <span class="status-badge {{ $currentStatus['class'] }}">
                    <i class="{{ $currentStatus['icon'] }}" aria-hidden="true"></i>
                    {{ $currentStatus['label'] }}
                </span>
            </div>

            </div>

            {{-- KONFIRMASI WHATSAPP --}}
            <h2 class="section-title">
                <i class="fa-regular fa-message me-2"></i>
                Langkah Selanjutnya
            </h2>

            <a
                href="{{ $whatsappUrl }}"
                class="btn-whatsapp"
                target="_blank"
                rel="noopener noreferrer">

                <i class="fa-brands fa-whatsapp"></i>
                Konfirmasi via WhatsApp
            </a>

            <p class="whatsapp-note">
                Tekan tombol di atas untuk melanjutkan
                konfirmasi pesanan melalui WhatsApp.
            </p>

        </section>

        <footer class="payment-success-footer">
            Terima kasih telah memesan bersama kami.
        </footer>

    </div>
</main>

<div
    class="payment-toast"
    id="toastMessage"
    role="status"
    aria-live="polite">
    Nomor invoice berhasil disalin.
</div>

<script>
    async function copyInvoice() {
        const invoiceElement = document.getElementById('invoiceNumber');

        if (!invoiceElement) {
            showToast('Nomor invoice tidak ditemukan.');
            return;
        }

        const invoice = invoiceElement.textContent.trim();

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(invoice);
            } else {
                const textarea = document.createElement('textarea');

                textarea.value = invoice;
                textarea.setAttribute('readonly', '');
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';

                document.body.appendChild(textarea);
                textarea.select();

                const copied = document.execCommand('copy');
                textarea.remove();

                if (!copied) {
                    throw new Error('Copy gagal');
                }
            }

            showToast('Nomor invoice berhasil disalin.');
        } catch (error) {
            showToast('Gagal menyalin. Silakan salin manual.');
        }
    }

    let toastTimeout;

    function showToast(message) {
        const toast = document.getElementById('toastMessage');

        if (!toast) return;

        toast.textContent = message;
        toast.style.display = 'block';

        clearTimeout(toastTimeout);

        toastTimeout = setTimeout(function () {
            toast.style.display = 'none';
        }, 2500);
    }
</script>

@endsection