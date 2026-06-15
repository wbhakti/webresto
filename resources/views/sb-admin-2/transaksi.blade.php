@extends('sb-admin-2.layouts.app')

@section('content')
<!-- CSS custom -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css">

<style>
.card-header {
    background-color: #fff;
}
.mr-0 {
    margin-right: 0;
}
.ml-auto {
    margin-left: auto;
}
.d-block {
    display: block;
}
.button-group a {
    margin-bottom: 10px;
}
</style>

<!-- DataTales Example -->
<div class="card shadow mb-4 custom-card-header">
    <div class="card-header py-3">
        <div align="center">
            <h1 class="h3 mb-4 text-gray-800">Data Transaksi Hari Ini</h1>
        </div>
        <!-- <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}"> -->
        <button id="enable-notification">
            Aktifkan Notifikasi
        </button>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Transaksi</th>
                        <th>Tanggal Transaksi</th>
                        <th>Pembeli</th>
                        <th>STATUS</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->isNotEmpty())
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->id_transaksi }}</td>
                                <td>{{ $item->addtime }}</td>
                                <td>{{ $item->customer }}</td>
                                <td>{{ $item->status }}</td>
                                <td>
                                    <div class="button-group">
                                        <button type="button" class="btn btn-info mb-2 btn-detail"
                                                data-id="{{ $item->id_transaksi }}"
                                                data-nama="{{ $item->customer }}"
                                                data-total="{{ $item->total_bayar }}"
                                                data-metode="{{ $item->metode_bayar }}"
                                                data-meja="{{ $item->meja }}"
                                                data-bukti="{{ $item->bukti_bayar }}"
                                                data-tgl="{{ $item->addtime }}"
                                                data-status="{{ $item->status }}"
                                                data-menu='@json($item->details)'>Detail</button>                                
                                    </div>
                                </td>                                
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    <hr>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Transaksi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><b>Tgl Transaksi:</b> <span id="detailTgl"></span></p>
                    <p><b>ID Transaksi:</b> <span id="detailId"></span></p>
                    <p><b>Nama Pembeli:</b> <span id="detailNama"></span></p>
                    <p><b>Nomor Meja:</b> <span id="detailMeja"></span></p>
                    <p><b>Total Bayar:</b> <span id="detailTotal"></span></p>
                    <p><b>Metode Bayar:</b> <span id="detailMetode"></span></p>
                    <p><b>Status: <span id="detailStatus"></span> </b></p>
                    <p><b>Bukti Bayar:</b> 
                        <a id="detailBuktiLink" href="#" target="_blank">
                            <img id="detailBuktiImg" src="" alt="Bukti Bayar" style="max-width: 100px; max-height: 100px;">
                        </a>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Menu</th>
                                    <th>Catatan</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                            <tbody id="detailMenuTable">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnProses" class="btn btn-primary" data-id="detailId">PROSES</button>
                    <button type="submit" id="btnSelesai" class="btn btn-primary">SELESAI</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>    

</div>

@if(session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif
@if(session('error'))
<script>
    alert('{{ session('error') }}');
</script>
@endif

<!-- Page level plugins -->
<script src="{{ asset('vendor/jquery/jquery-3.3.1.min.js')}}"></script>
<script src="{{ asset('vendor/jquery/jquery.validate.min.js')}}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#dataTable').dataTable({
            "lengthMenu": [10, 20, 50, 100],
            "pageLength": 100,
            searching: true
        });
    });
</script>

<script>
    $(document).on('click', '.btn-detail', function() {
    var id = $(this).data('id');
    var nama = $(this).data('nama');
    var total = $(this).data('total');
    var metode = $(this).data('metode');
    var meja = $(this).data('meja');
    var bukti = $(this).data('bukti');
    var tgl = $(this).data('tgl');
    var menuData = $(this).data('menu'); // Ambil data menu
    var status = $(this).data('status');

    $('#detailId').text(id);
    $('#detailNama').text(nama);
    $('#detailTotal').text(total);
    $('#detailMetode').text(metode);
    $('#detailMeja').text(meja);
    $('#detailTgl').text(tgl);
    

    if (status == "") {
        $('#detailStatus').text('PENDING');
        $('#btnSelesai').hide();
        $('#btnProses').show();
    }
    else if (status == "DIPROSES"){
        $('#btnSelesai').show();
        $('#btnProses').hide();
        $('#detailStatus').text(status);
    } else if (status == "SELESAI"){
        $('#btnProses').hide();
        $('#btnSelesai').hide();
        $('#detailStatus').text(status);
    }

    

    if (bukti) {
        $('#detailBuktiLink').attr('href', "{{ url('webcubiq/public/invoice') }}" + "/" + bukti);
        $('#detailBuktiImg').attr('src', "{{ url('webcubiq/public/invoice') }}" + "/" + bukti).show();
    } else {
        $('#detailBuktiLink').attr('href', '#');
        $('#detailBuktiImg').hide();
    }

    try {
        var menuArray = JSON.parse(JSON.parse(menuData));
        var menuList = "";

        menuArray.forEach(function(menu) {
            menuList += `
                <tr>
                    <td>${menu.menu_id}</td>
                    <td>-</td>
                    <td>${menu.quantity}</td>
                    <td>Rp ${menu.price.toLocaleString()}</td>
                </tr>`;
        });

        $('#detailMenuTable').html(menuList);
    } catch (e) {
        console.error("Error parsing menu data:", e);
        $('#detailMenuTable').html("<tr><td colspan='4'>Format menu tidak valid.</td></tr>");
    }

    $('#detailModal').modal('show');
});

$('#btnSelesai').on('click', function() {
    var transaksiId = $('#detailId').text();

    if (!transaksiId) {
        alert("ID transaksi tidak ditemukan!");
        return;
    }

    $.ajax({
        url: "{{ route('updatestatus') }}",
        type: "POST",
        data: {
            id: transaksiId,
            status: "SELESAI",
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            alert(response.message);
            $('#detailStatus').text("SELESAI");
            $('#detailModal').modal('hide');
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error("Error updating status:", error);
            alert("Gagal memperbarui status transaksi. Coba lagi!");
        }
    });
});

$('#btnProses').on('click', function() {
    var transaksiId = $('#detailId').text();

    if (!transaksiId) {
        alert("ID transaksi tidak ditemukan!");
        return;
    }

    $.ajax({
        url: "{{ route('updatestatus') }}",
        type: "POST",
        data: {
            id: transaksiId,
            status: "DIPROSES",
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            alert(response.message);
            $('#detailStatus').text("DIPROSES");
            $('#detailModal').modal('hide');
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error("Error updating status:", error);
            alert("Gagal memperbarui status transaksi. Coba lagi!");
        }
    });
});


</script>    

<script>
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}
</script>

<script> 
    document.getElementById('enable-notification')
        .addEventListener('click', async () => {
    
        const vapidPublicKey = "{{ config('webpush.vapid.public_key') }}";
        const registration = await navigator.serviceWorker.ready;
        

        const subscription =
            await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
            });

        await fetch('{{ route('subscribe') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]'
                ).content
            },
            body: JSON.stringify(subscription)
        });

        alert('Notifikasi berhasil diaktifkan');
});
</script> 

<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.ready.then(function(registration) {

        navigator.serviceWorker.addEventListener('message', function(event) {

            console.log('Pesan diterima:', event.data);

            if (event.data.action === 'refresh') {

                alert('REFRESH');

                window.location.reload();
            }

        });

    });

}
</script>


@endsection
