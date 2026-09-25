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
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Data Master Merchant</h1>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Merchant</th>
                        <th>Lokasi Merchant</th>
                        <th>Image Merchant</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->isNotEmpty())
                        @foreach ($data as $item)
                        
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->address }}</td>
                            <td>
                                <img src="{{ asset('img/' . $item->logo) }}" alt="Thumbnail" style="max-width: 100px; max-height: 100px;">
                            </td>
                            <td>
                                <div class="button-group">
                                    <div class="button-group">
                                        <button type="button" class="btn btn-primary mb-2 btn-edit"
                                            data-rowid="{{ $item->id }}"
                                            data-name="{{ $item->name }}"
                                            data-address="{{ $item->address }}"
                                            data-logo="{{ $item->logo }}">Edit</button>
                                    </div>                                    
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">Data tidak ditemukan.</td>
                        </tr>
                    @endif
                </tbody>                
            </table>
        </div>
    </div>
    <hr>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Merchant</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="/postmerchant" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="proses" value="edit">
                        <input type="hidden" name="merchant_id" id="editRowid">
                        <div class="form-group">
                            <label for="editName"><b>Nama Merchant</b></label>
                            <input type="text" name="name" id="editName" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="editAddress"><b>Alamat</b></label>
                            <textarea name="address" id="editAddress" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="logo"><b>Logo</b></label>
                            <input type="file" name="logo" class="form-control" accept="image/*" />
                        </div>
                        <div class="form-group">
                            <label><b>Current Logo</b></label>
                            <img id="currentimage" src="" alt="Current Logo" style="max-width: 100px; max-height: 100px;">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
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
        "pageLength": 10,
        searching: true
    });
});
</script>

<script>
    $(document).ready(function() {
        $("#toggleButton").click(function() {
            $("#myForm").toggle();
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Edit button click event
        $(document).on('click', '.btn-edit', function() {
            var rowid = $(this).data('rowid');
            var name = $(this).data('name');
            var address = $(this).data('address');
            var logo = $(this).data('logo');
            
            $('#editRowid').val(rowid);
            $('#editName').val(name);
            $('#editAddress').val(address);
            $('#currentimage').attr('src', "{{ asset('img/') }}" + "/" + logo);

            $('#editModal').modal('show');
        });
    });
</script>


@endsection