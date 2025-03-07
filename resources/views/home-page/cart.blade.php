@extends('home-page.layouts.app-home')

@section('content')

<style>
    /* Pastikan tabel responsif */
    .table-responsive {
        overflow-x: auto; /* Izinkan scroll horizontal jika diperlukan */
    }

    .table {
        width: 100%; /* Pastikan tabel mengambil seluruh lebar kontainer */
        table-layout: auto; /* Biarkan kolom menyesuaikan ukuran konten */
    }

    .table-image {
        max-width: 60px;
        height: auto;
    }

    .font-heading {
        font-size: 16px;
    }

    .font-isi-nama {
        font-size: 14px;
    }

    .font-isi-harga {
        font-size: 14px;
    }

    .font-isi-jumlah {
        font-size: 14px;
    }

    .font-isi-total {
        font-size: 14px;
    }
    .font-isi-grandtotal {
        font-size: 16px;
    }
    /* Ukuran tombol default */
    .btn-small {
        font-size: 14px; /* Ukuran font lebih kecil */
        padding: 4px 8px; /* Kurangi padding */
    }

/* Gaya responsif untuk layar kecil */
    @media (max-width: 768px) {

    .table-responsive {
    overflow-x: hidden; /* Izinkan scroll horizontal jika diperlukan */
    }

    .table {
        width: 100%; /* Pastikan tabel mengambil seluruh lebar kontainer */
        table-layout: fixed; /* Biarkan kolom menyesuaikan ukuran konten */
    }

    .table th, .table td {
        padding: 2px; /* Kurangi padding */
    }

    .table-image {
        display: none; /* Hilangkan gambar */
    }

    

    .input-group .btn {
        font-size: 8px; /* Sesuaikan ukuran tombol */
    }

    .input-group input {
        width: 40px; /* Sesuaikan lebar input */
        font-size: 8px; /* Sesuaikan ukuran teks input */
    }

    .font-heading {
        font-size: 9px;
    }

    .font-isi-nama {
        word-break: break-word;
        font-size: 10px;
    }

    .font-isi-harga {
        font-size: 10px;
    }

    .font-isi-jumlah {
        font-size: 10px;
    }

    .font-isi-total {
        font-size: 10px;
    }
    .font-isi-grandtotal {
        font-size: 11px;
    }
    .btn-small {
        font-size: 10px; /* Ukuran font lebih kecil */
        padding: 2px 6px; /* Kurangi padding */
    }
}

</style>

<!-- Header -->
<div class="d-flex flex-wrap justify-content-center" style="gap: 1rem;">
    <div class="card" style="min-width: 150px;">
        <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Favorit</div>
        <img class="card-img-top" src="{{ asset('img/' . $merchant->image) }}" alt="{{ $merchant->nama }}" />
        <div class="card-body d-flex flex-column justify-content-between text-center">
            <div>
                <h4 class="fw-bolder mb-1">{{ $merchant->nama }}</h4>
                <small class="text-muted d-block mb-2">{{ $merchant->deskripsi }}</small>
            </div>
        </div>
    </div>
</div>

<section class="py-4">

    <div class="container px-4 px-lg-5 mt-0">
        <h3 class="text-center mb-4">Keranjang Belanja</h3>
        <div class="card shadow-lg">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th colspan="2" class="font-heading">Nama Produk</th>
                                <th colspan="2" class="font-heading">Harga</th>
                                <th colspan="3" class="font-heading">Jumlah</th>
                                <th colspan="2" class="font-heading">Total</th>
                                <th colspan="2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach ($cart as $id => $item)
                            @php $total = $item['price'] * $item['quantity']; @endphp
                            <tr>
                                <td colspan="2" class="font-isi-nama">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $item['image'] ?? asset('img/default-img.jpeg') }}" alt="{{ $item['name'] }}" class="img-fluid me-3 table-image">
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                </td>
                                <td colspan="2" class="font-isi-harga">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                <td colspan="3" class="font-isi-jumlah">
                                    <div class="input-group">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity({{ $id }}, -1)">-</button>
                                        <input type="text" class="form-control text-center jml-input" value="{{ $item['quantity'] }}" readonly id="quantity-{{ $id }}">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="updateQuantity({{ $id }}, 1)">+</button>
                                    </div>
                                </td>
                                <td colspan="2" class="font-isi-total" id="total-{{ $id }}">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                <td colspan="2"> 
                                    <form action="{{ route('cart.remove', $id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <!-- <i class="bi-trash"></i> -->
                                        <button type="submit" class="bi-trash"></button>
                                    </form>
                                </td>
                            </tr>
                            @php $grandTotal += $total; @endphp
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="8" class="text-end font-isi-grandtotal">Total Keseluruhan</th>
                                <th colspan="3"id="grand-total" class="font-isi-grandtotal">Rp {{ number_format($grandTotal, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <hr class="my-4">
                <form id="checkout-form" action="/checkout" method="POST">
                    @csrf
                
                    <h3 class="mb-4 text-center">🛒 Info Pemesanan</h3>
                    
                    <div class="card border-0 shadow-sm p-4">
                        <!-- Nama Pemesan -->
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-bold">
                                <i class="bi bi-person-circle me-2"></i> Nama Pemesan
                            </label>
                            <input type="text" class="form-control py-2" id="nama" name="nama" placeholder="Masukkan nama pemesan" required>
                        </div>
                
                        <!-- Nomor Meja -->
                        <div class="mb-3">
                            <label for="meja" class="form-label fw-bold">
                                <i class="bi bi-grid-3x3-gap me-2"></i> Nomor Meja
                            </label>
                            <select class="form-select py-2" id="meja" name="meja" required>
                                <option value="" selected disabled>Pilih Nomor Meja</option>
                                @for ($i = 1; $i <= 29; $i++) 
                                    <option value="{{ $i }}">Meja {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                
                        <!-- Metode Pembayaran -->
                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label fw-bold">
                                <i class="bi bi-credit-card-2-front me-2"></i> Metode Pembayaran
                            </label>
                            <select class="form-select py-2" id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="" selected disabled>Pilih Metode Pembayaran</option>
                                <option value="cash">CASH</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>
                    </div>
                
                    <input type="hidden" name="total" id="total-tagihan">
                    <input type="hidden" name="details" id="order-details">
                
                    <div class="card-footer text-center mt-3">
                        <a href="/" class="btn btn-outline-secondary me-2" style="margin-bottom: 10px;">Lanjut Pilih Menu</a>
                        <button type="submit" id="checkout-button" class="btn btn-primary" style="margin-bottom: 10px;">Proses</button>
                    </div>
                </form>
            
            </div>
        </div>
    </div>

</section>

@if(session('error'))
<script>
    alert('{{ session('error') }}');
</script>
@endif

<script>
    function updateQuantity(itemId, change) {
        const quantityInput = document.getElementById(`quantity-${itemId}`);
        let currentQuantity = parseInt(quantityInput.value);

        if (currentQuantity + change > 0) {
            currentQuantity += change;
            quantityInput.value = currentQuantity;

            fetch(`/update-cart/${itemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        quantity: currentQuantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                        const totalElement = document.getElementById(`total-${itemId}`);
                        if (totalElement) {
                            const total = data.itemTotal;
                            totalElement.textContent = `Rp ${total}`;
                        }

                        document.getElementById('grand-total').textContent = `Rp ${data.grandTotal}`;
                    } else {
                        alert('Gagal memperbarui jumlah item.');
                    }
                });
        }
    }
</script>

<script>
    document.getElementById('checkout-button').addEventListener('click', function(event) {
        var nama = document.getElementById('nama');
        var meja = document.getElementById('meja');
        var metodePembayaran = document.getElementById('metode_pembayaran');

        if (!nama || nama.value.trim() === "") {
            alert("Nama harus diisi!");
            event.preventDefault(); // Cegah submit form
            return false;
        }

        if (!meja || meja.value.trim() === "") {
            alert("Silakan pilih nomor meja!");
            event.preventDefault();
            return false;
        }

        if (!metodePembayaran || metodePembayaran.value.trim() === "") {
            alert("Silakan pilih metode pembayaran!");
            event.preventDefault();
            return false;
        }

        var daftarProduk = [];
        var totalTagihan = 0;
        @foreach ($cart as $item)
            daftarProduk.push({
                menu_id: "{{ $item['name'] }}",
                note: "-",
                quantity: {{ $item['quantity'] }},
                price: {{ $item['price'] }}
            });
            totalTagihan += {{ $item['price'] * $item['quantity'] }};
        @endforeach

        // Simpan data ke input hidden
        document.getElementById('total-tagihan').value = totalTagihan;
        document.getElementById('order-details').value = JSON.stringify(daftarProduk);
    });
</script>


@endsection