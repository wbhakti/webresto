@extends('home-page.layouts.app-home')

@section('content')

<!-- Header -->
<header class="bg-dark py-5">
    <div class="text-center text-white">
        <h1 class="display-4 fw-bolder">Konfirmasi Pembayaran</h1>
    </div>
</header>

<section class="py-5">
    <div class="container text-center">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">{{ $head }}</h4>
            <p>{{ $body }}</p>
        </div>
    </div>

    <div class="container">
        <div class="card p-4 shadow-sm">
            <h5 class="fw-bold">📝 Info Pemesanan</h5>
            <hr>

            <!-- Info Pemesanan -->
            <p><strong>ID Transaksi:</strong> {{ $idtransaksi }}</p>
            <p><strong>Nama:</strong> {{ $nama }}</p>
            <p><strong>Nomor Meja:</strong> {{ $meja }}</p>
            <p><strong>Metode Pembayaran:</strong> {{ $metodePembayaran }}</p>
            <p><strong>Sub Total Pemesanan:</strong> Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
            <p><strong>Diskon:</strong> Rp {{ number_format($discount, 0, ',', '.') }}</p>
            <p><strong>Total Pembayaran:</strong> Rp {{ number_format(($totalTagihan-$discount), 0, ',', '.') }}</p>
            
            <hr>

            <!-- Detail Pesanan -->
            <ul class="list-group">
                @foreach ($details as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="text-start">
                            <strong>Menu : {{ $item['menu_id'] }}</strong> <br>
                            {{ $item['quantity'] }}x @Rp {{ number_format($item['price'], 0, ',', '.') }} 
                            <br><small class="text-muted">Catatan: {{ $item['note'] }}</small>
                        </div>
                        <span class="fw-bold">Rp {{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>

            <!-- QRIS Payment -->
            @if ($isQRIS)
                <div class="mt-4">
                    <p class="fw-bold">🔍 Scan QRIS untuk pembayaran:</p>
                    <img src="{{ $qrisImage }}" alt="QRIS Payment" class="img-fluid" style="max-width: 300px;">
                    <div class="text-left">
                        <p class="fw-bold"><br>Panduan Bayar QRIS</p>
                        <p class="text-muted mt-2">1. Scan <b>QRIS</b> di atas <br>2. Lakukan input pembayaran sesuai <b>Total Tagihan</b> <br>3. <b><i>Upload</i></b> bukti pembayaran <br>4. Setelah <b><i>Upload</i></b> berhasil akan di arahkan ke aplikasi <b><i>Whatsapp</i></b><br>5. Lanjutkan proses dengan mengirim pesan pada aplikasi <b><i>Whatsapp</i></b></p>
                    </div>

                    <!-- Form Upload Bukti Pembayaran -->
                    <div class="text-center mt-4">
                        <label class="fw-bold">📸 Upload Bukti Pembayaran:</label>
                        <form id="uploadBuktiForm" enctype="multipart/form-data">
                            @csrf
                            <input type="text" name="idtransaksi" value="{{ $idtransaksi }}" hidden>
                            <input type="text" name="nama" value="{{ $nama }}" hidden>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control mt-2" accept="image/*" required>
                            <button type="submit" class="btn btn-success mt-2">Upload</button>
                        </form>

                        <!-- Tampilkan Bukti Pembayaran Setelah Upload -->
                        <div id="buktiPembayaranPreview" class="mt-3 d-none">
                            <p class="fw-bold">✅ Upload Bukti Pembayaran Berhasil!</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="text-center mt-4">
            <a href="/" class="btn btn-primary">Kembali ke Menu</a>
        </div>
    </div>
</section>

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
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="sendWaQris">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let isQRIS = {{ $isQRIS ? 'true' : 'false' }};

        if (!isQRIS) {
            let modal = new bootstrap.Modal(document.getElementById("successModal"));
            modal.show();

            document.getElementById("sendWa").addEventListener("click", function() {
                // Data Pesanan
                let phoneWa = "{{ $phone_wa }}";
                let idTransaksi = "{{ $idtransaksi }}";
                let nama = "{{ $nama }}";
                let meja = "{{ $meja }}";
                let metodePembayaran = "{{ $metodePembayaran }}";
                let totalDiscount = "Rp {{ number_format($discount, 0, ',', '.') }}";
                let totalTagihan = "Rp {{ number_format($totalTagihan, 0, ',', '.') }}";
                let totalPembayaran = "Rp {{ number_format(($totalTagihan-$discount), 0, ',', '.') }}";

                // Detail Pesanan
                let pesanDetail = "";
                @foreach ($details as $item)
                    pesanDetail += "{{ $item['menu_id'] }} - {{ $item['quantity'] }}x @Rp {{ number_format($item['price'], 0, ',', '.') }}%0A";
                @endforeach

                // Format Pesan WhatsApp
                let waMessage = `Hallo, saya ingin mengkonfirmasi pesanan saya.%0A%0A` +
                                `ID Transaksi: ${idTransaksi}%0A` +
                                `Nama: ${nama}%0A` +
                                `Nomor Meja: ${meja}%0A` +
                                `Metode Pembayaran: ${metodePembayaran}%0A` +
                                `Sub Total Pemesanan: ${totalTagihan}%0A` +
                                `Diskon: ${totalDiscount}%0A` +
                                `Total Pembayaran: ${totalPembayaran}%0A%0A` +
                                `*Detail Pesanan:*%0A` + pesanDetail;

                let waLink = `https://wa.me/${phoneWa}?text=${waMessage}`;

                window.open(waLink, '_blank');
                modal.hide();
            });
        }
    });
</script>

<script>
    document.getElementById('uploadBuktiForm').addEventListener('submit', function (event) {
        event.preventDefault();

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

                let modal = new bootstrap.Modal(document.getElementById("successModalQris"));
                modal.show();

                document.getElementById("sendWaQris").addEventListener("click", function() {
                    let imageUrl = data.imageUrl;
                    // Data Pesanan
                    let phoneWa = "{{ $phone_wa }}";
                    let idTransaksi = "{{ $idtransaksi }}";
                    let nama = "{{ $nama }}";
                    let meja = "{{ $meja }}";
                    let metodePembayaran = "{{ $metodePembayaran }}";
                    let totalDiscount = "Rp {{ number_format($discount, 0, ',', '.') }}";
                    let totalTagihan = "Rp {{ number_format($totalTagihan, 0, ',', '.') }}";
                    let totalPembayaran = "Rp {{ number_format(($totalTagihan-$discount), 0, ',', '.') }}";

                    // Detail Pesanan
                    let pesanDetail = "";
                    @foreach ($details as $item)
                        pesanDetail += "{{ $item['menu_id'] }} - {{ $item['quantity'] }}x @Rp {{ number_format($item['price'], 0, ',', '.') }}%0A";
                    @endforeach

                    // Format Pesan WhatsApp
                    let waMessage = `Hallo, saya ingin mengkonfirmasi pesanan saya.%0A%0A` +
                                `ID Transaksi: ${idTransaksi}%0A` +
                                `Nama: ${nama}%0A` +
                                `Nomor Meja: ${meja}%0A` +
                                `Metode Pembayaran: ${metodePembayaran}%0A` +
                                `Sub Total Pemesanan: ${totalTagihan}%0A` +
                                `Diskon: ${totalDiscount}%0A` +
                                `Total Pembayaran: ${totalPembayaran}%0A%0A` +
                                `*Detail Pesanan:*%0A` + pesanDetail;

                    let waLink = `https://wa.me/${phoneWa}?text=${waMessage}`;

                    document.getElementById('buktiPembayaranPreview').classList.remove('d-none');

                    // Buka WhatsApp
                    window.open(waLink, '_blank');
                    modal.hide();
                });

            } else {
                alert("Upload gagal! Silakan coba lagi.");
            }
        })
        .catch(error => console.error('Error:', error));
    });
</script>


@endsection
