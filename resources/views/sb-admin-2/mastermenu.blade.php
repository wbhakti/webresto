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
            <h1 class="h3 mb-0 text-gray-800">Data Master Produk</h1>
            <button
                id="toggleButton"
                class="btn btn-success"
                data-toggle="modal"
                data-target="#addModal">
                Tambah Produk Baru
            </button>
        </div>
        
    </div>   

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>SKU</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($products->isNotEmpty())
                    @foreach ($products as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->category_id }}</td>
                        <div class="button-group">
                            <td>
                                @if($item->is_active == 1) 
                                <form method="POST" action="/activedProducts" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="row_id" value="{{ $item->id }}">
                                    <button type="submit" name="proses" value="not_actived" class="btn btn-success mb-2">Aktif</button>
                                </form> 
                                @else
                                <form method="POST" action="/activedProducts" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="row_id" value="{{ $item->id }}">
                                    <button type="submit" name="proses" value="actived" class="btn btn-warning mb-2">Non Aktif</button>
                                </form> 
                                @endif
                            </td>
                        </div>
                        <div class="button-group">
                        <td>
                            <button type="button" class="btn btn-primary mb-2 btn-edit"
                                    data-rowid="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-description="{{ $item->description }}"
                                    data-price="{{ $item->price }}"
                                    data-cost_price="{{ $item->cost_price }}"
                                    data-category_id="{{ $item->category_id }}"
                                    data-image="{{ $item->image }}">Edit</button>
                                
                                <form method="POST" action="/editProducts" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="row_id" value="{{ $item->id }}">
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="/editProducts" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="proses" value="edit">
                        <input type="hidden" name="row_id" id="editRowid">
                        <div class="form-group">
                            <label for="editName"><b>Nama Produk</b></label>
                            <input type="text" name="name" id="editName" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="editdescription"><b>Deskripsi Produk</b></label>
                            <input type="text" name="description" id="editDescription" class="form-control" required />
                        </div>
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="editPrice"><b>Harga Produk</b></label>
                                <input type="text" name="price" id="editPrice" class="form-control" required />
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="editCost_price"><b>Harga Modal</b></label>
                                <input type="text" name="cost_price" id="editCost_price" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editkategori"><b>Kategori Menu</b></label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach ($catagories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ $cat->id == $item->category_id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image"><b>Image</b></label>
                            <input type="file" name="img_menu" class="form-control" accept="image/*" />
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Tambah Produk Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="/addProducts" enctype="multipart/form-data">
                        @csrf
                        
                        <input type="hidden" name="proses" value="add">
                        <div class="form-group">
                            <label for="addsku"><b>SKU</b></label>
                            <input type="text" name="sku" id="addsku" class="form-control" required />
                        </div>
                        
                        <div class="form-group">
                            <label for="addname"><b>Nama Produk</b></label>
                            <input type="text" name="name" id="addname" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="adddescription"><b>Deskripsi Produk</b></label>
                            <input type="text" name="description" id="adddescription" class="form-control" required />
                        </div>
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="addprice"><b>Harga Produk</b></label>
                                <input type="text" name="price" id="addprice" class="form-control" required />
                            </div>
                            <div class="form-group col-sm-6">
                                <label for="addcost_price"><b>Harga Modal</b></label>
                                <input type="text" name="cost_price" id="addcost_price" class="form-control" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editkategori"><b>Kategori Menu</b></label>
                            <select class="form-control" id="category_id" name="category_id" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                @foreach ($catagories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image"><b>Image</b></label>
                            <input type="file" name="img_menu" class="form-control" accept="image/*" />
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
            var category_id = $(this).data('category_id');
            var name = $(this).data('name');
            var description = $(this).data('description');
            var price = $(this).data('price');
            var cost_price = $(this).data('cost_price');
            var image = $(this).data('image');

            $('#editRowid').val(rowid);
            $('#editName').val(name);
            $('#editDescription').val(description);
            $('#editPrice').val(price);
            $('#editCost_price').val(cost_price);
            $('#editCategory_id').val(category_id);
            $('#currentImage').attr('src', "{{ url('public/img/') }}" + "/" + image);

            $('#editModal').modal('show');
        });
    });
</script>


@endsection