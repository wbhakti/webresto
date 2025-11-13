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
            <h1 class="h3 mb-4 text-gray-800">Data Transaksi</h1>
        </div>
    
        <form method="GET" action="/dashboard/transaction">
            <div class="row justify-content-center">
                <div class="col-sm-4">
                    <div class="mb-3">
                        <label for="date_start" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="date_start" name="date_start" 
                            value="{{ isset($date_start) ? $date_start : old('date_start') }}" required>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="mb-3">
                        <label for="date_end" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="date_end" name="date_end" 
                            value="{{ isset($date_end) ? $date_end : old('date_end') }}" required>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success" name="action" value="report">Tampilkan Data</button>
            </div>
        </form>
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
                    <p><b>Status:</b> <span id="detailStatus"></span></p>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="btnLunas">LUNAS</button>
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
    $('#detailStatus').text(status);

    if (bukti) {
        $('#detailBuktiLink').attr('href', "{{ url('forestmenu/public/invoice') }}" + "/" + bukti);
        $('#detailBuktiImg').attr('src', "{{ url('forestmenu/public/invoice') }}" + "/" + bukti).show();
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

    let btnLunas = $('#btnLunas');
    btnLunas.data('id', id);
    if (status === "KONFIRMASI") {
        btnLunas.show().prop('disabled', false);
    }  else {
        btnLunas.hide();
    }

    $('#detailModal').modal('show');
});

$('#btnLunas').on('click', function() {
    var transaksiId = $(this).data('id');

    if (!transaksiId) {
        alert("ID transaksi tidak ditemukan!");
        return;
    }

    $.ajax({
        url: "{{ route('updatestatus') }}",
        type: "POST",
        data: {
            id: transaksiId,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            alert(response.message);
            $('#detailStatus').text("LUNAS");
            $('#btnLunas').prop('disabled', true);
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

@endsection
