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
        <h1 class="h3 mb-2 text-gray-800">Data Master Promo</h1>
        
    </div>   

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Promo</th>
                        <th>Image</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->isNotEmpty())
                    @foreach ($data as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->value }}</td>
                        <div class="button-group">
                        <td>
                            <button type="button" class="btn btn-primary mb-2 btn-edit"
                                    data-rowid="{{ $item->id }}"
                                    data-description="{{ $item->description }}"
                                    data-value="{{ $item->value }}">Edit</button>
                                
                                <form method="POST" action="/postPopupPromo" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="id_promo" value="{{ $item->id }}">
                                    <button type="submit" name="proses" value="delete" class="btn btn-danger mb-2">Delete</button>
                                </form> 
                        </td>
                                                               
                        </div>
                    </tr>
                    @endforeach
                    @else
                        <div align="center">
                            <button id="toggleButton" class="btn btn-success">Add New Promo</button>
                        </div>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <hr>

    <br>
    <div id="myForm" style="display: none;">
        <div class="col-xl-8 col-lg-7 mx-auto">
            <!-- Project Card Example -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="POST" action="/postPopupPromo" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="description"><b>Nama Promo</b></label>
                                <input type="text" name="description" class="form-control" required />
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="img_promo"><b>Image Promo (900x400)</b></label>
                                <input type="file" name="img_promo" class="form-control" accept="image/*" required />
                            </div>
                        </div>
                        <br />
                        <div align="center">
                            <button type="submit" name="proses" value="save" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Promo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="/postPopupPromo" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="proses" value="edit">
                        <input type="hidden" name="id_promo" id="editRowid">
                        <div class="form-group">
                            <label for="editTitle"><b>Nama Promo</b></label>
                            <input type="text" name="editTitle" id="editTitle" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="image"><b>Image</b></label>
                            <input type="file" name="img_promo" class="form-control" accept="image/*" />
                        </div>
                        <div class="form-group">
                            <label><b>Current Image</b></label>
                            <img id="currentImage" src="" alt="Current Image" style="max-width: 100px; max-height: 100px;">
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
        "pageLength": 100,
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
            var title = $(this).data('description');
            var img = $(this).data('value');

            $('#editRowid').val(rowid);
            $('#editTitle').val(title);
            $('#currentImage').attr('src', "{{ url('public/img/') }}" + "/" + img);

            $('#editModal').modal('show');
        });
    });
</script>


@endsection