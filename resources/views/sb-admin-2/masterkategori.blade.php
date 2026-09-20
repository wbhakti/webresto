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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0 text-gray-800">Data Master Kategori</h1>
            <button
                id="toggleButton"
                class="btn btn-success"
                data-toggle="modal"
                data-target="#addModal">
                Add New Kategori
            </button>
        </div>
    </div>    

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($data->isNotEmpty())
                    @foreach ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>
                            @if($item->is_active == 1)
                                <span class="badge badge-success">AKTIF</span>
                            @else
                                <span class="badge badge-secondary">NON AKTIF</span>
                            @endif
                        </td>
                        <div class="button-group">
                        <td>
                            <button type="button" class="btn btn-primary mb-2 btn-edit"
                                    data-rowid="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-description="{{ $item->description }}"
                                    data-is_active="{{ $item->is_active }}">Edit</button>
                                    
                            <form method="POST" action="/editCategories" style="display: inline;">
                                @csrf
                                <input type="hidden" name="categories_id" value="{{ $item->id }}">
                                <button type="submit" name="proses" value="delete" class="btn btn-danger mb-2">Delete</button>
                            </form> 
                        </td>
                                                               
                        </div>
                    </tr>
                    @endforeach
                    @else

                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    
    <br>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="/editCategories">
                        @csrf
                        <input type="hidden" name="proses" value="edit">
                        <input type="hidden" name="categories_id" id="editRowid">
                        <div class="form-group">
                            <label for="editName"><b>Nama Kategori</b></label>
                            <input type="text" name="name" id="editName" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="editDescription"><b>Deskripsi</b></label>
                            <input type="text" name="description" id="editDescription" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="editStatus"><b>Status</b></label>
                            <select class="form-control" id="editStatus" name="status" required>
                                <option value="1">AKTIF</option>
                                <option value="0">NON AKTIF</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="proses" value="edit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addForm" method="POST" action="/addCategories">
                        @csrf
                        <div class="form-group">
                            <label for="addName"><b>Nama Kategori</b></label>
                            <input type="text" name="name" id="addName" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="addDescription"><b>Deskripsi</b></label>
                            <input type="text" name="description" id="addDescription" class="form-control" required />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="proses" value="add" class="btn btn-primary">Save</button>
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
        $(document).on('click', '.btn-edit', function() {
            var rowid = $(this).data('rowid');
            var name = $(this).data('name');
            var description = $(this).data('description');
            var is_active = $(this).data('is_active');
            
            $('#editRowid').val(rowid);
            $('#editName').val(name);
            $('#editDescription').val(description);
            $('#editStatus').val(is_active);

            $('#editModal').modal('show');
        });
    });
</script>


@endsection