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
    <!-- Page Heading -->
    <div class="card-header py-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0 text-gray-800">Data Master Promo</h1>
            <button
                id="toggleButton"
                class="btn btn-success"
                data-toggle="modal"
                data-target="#addModal">
                Add New Promotions
            </button>
        </div>  
    </div>  

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Code</th>
                        <th>Nama Diskon</th>
                        <th>Deskripsi</th>
                        <th>Tipe</th>
                        <th>Value</th>
                        <th>Periode</th>
                        <th>Scope</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($promotions->isNotEmpty())
                    @foreach ($promotions as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ $item->value }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->start_at)->format('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($item->end_at)->format('H:i') }}
                        </td>
                        <td>{{ $item->scope }}</td>
                        <div class="button-group">
                            <td>
                                @if($item->is_active == 1) 
                                <form method="POST" action="/activedPromotions" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="row_id" value="{{ $item->id }}">
                                    <button type="submit" name="proses" value="not_actived" class="btn btn-success mb-2">Aktif</button>
                                </form> 
                                @else
                                <form method="POST" action="/activedPromotions" style="display: inline;">
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
                                    data-item='@json($item)'>Edit</button>
                                
                                <form method="POST" action="/editPromotions" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="id_promo" value="{{ $item->id }}">
                                    <button type="submit" name="proses" value="delete" class="btn btn-danger mb-2">Delete</button>
                                </form> 
                        </td>
                                                               
                        </div>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <hr>

    <br>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"> Tambah Promo Baru </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <form id="addForm" method="POST" action="/addPromotions">
                        @csrf
                        <input type="hidden" name="proses" value="add">
                        {{-- CODE --}}
                        <div class="form-group"> 
                            <label for="addcode"> <b>CODE</b> </label>
                            <input type="text" name="code" id="addcode" class="form-control" required>
                        </div>

                        {{-- NAMA --}}
                        <div class="form-group">
                            <label for="addname"> <b>Nama Promo</b> </label>
                            <input type="text" name="name" id="addname" class="form-control" required>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="form-group">
                            <label for="adddescription"> <b>Deskripsi Promo</b> </label>
                            <textarea name="description" id="adddescription" class="form-control" rows="2" required></textarea>
                        </div>

                        {{-- TIPE + NILAI --}}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="addPromoType"> <b>Tipe Promo</b> </label>
                                <select class="form-control" name="promo_type" id="addPromoType" required>
                                    <option value="percentage">  Persentase (%) </option>
                                    <option value="fixed">  Nominal (Rp) </option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="addpromo_value"> <b>Nilai Promo</b> </label>
                                <input type="number" name="promo_value" id="addpromo_value" class="form-control" min="0"  step="0.01" required>
                            </div>
                        </div>

                        {{-- SCOPE --}}
                        <div class="form-group">
                            <label for="addScope"> <b>Berlaku Untuk</b> </label>
                            <select class="form-control" name="scope" id="addScope" required>
                                <option value="all"> Semua Produk </option>
                                <option value="category"> Kategori Tertentu </option>
                                <option value="product"> Produk Tertentu </option>
                            </select>
                        </div>


                        {{-- CATEGORY --}}
                        <div class="form-group" id="categoryScope" style="display:none;">
                            <label>
                                <b>Pilih Kategori</b>
                            </label>
                            <select name="category_ids[]" id="addCategoryIds" class="form-control" multiple>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                @endforeach
                            </select>

                            <small class="form-text text-muted">
                                Bisa memilih lebih dari satu kategori.
                            </small>
                        </div>


                        {{-- PRODUCT --}}
                        <div class="form-group" id="productScope" style="display:none;">
                            <label> <b>Pilih Produk</b> </label>
                            <select name="product_ids[]" id="addProductIds" class="form-control"  multiple style="width: 100%;">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                        - Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="form-text text-muted">
                                Bisa memilih lebih dari satu produk.
                            </small>
                        </div>


                        {{-- JAM --}}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="addstart_time"> <b>Jam Mulai</b> </label>
                                <input type="time" name="start_time" id="addstart_time" class="form-control" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="addend_time"> <b>Jam Selesai</b> </label>
                                <input type="time" name="end_time" id="addend_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> Close </button>
                            <button type="submit" class="btn btn-primary"> Save changes </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"> Edit Promo </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <form id="addForm" method="POST" action="/editPromotions">
                        @csrf
                        <input type="hidden" name="proses" value="edit">
                        <input type="hidden" name="id_promo" id="editRowid">
                        {{-- CODE --}}
                        <div class="form-group"> 
                            <label for="editcode"> <b>CODE</b> </label>
                            <input type="text" name="code" id="editcode" class="form-control" required>
                        </div>

                        {{-- NAMA --}}
                        <div class="form-group">
                            <label for="editname"> <b>Nama Promo</b> </label>
                            <input type="text" name="name" id="editname" class="form-control" required>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="form-group">
                            <label for="editdescription"> <b>Deskripsi Promo</b> </label>
                            <textarea name="description" id="editdescription" class="form-control" rows="2" required></textarea>
                        </div>

                        {{-- TIPE + NILAI --}}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="editPromoType"> <b>Tipe Promo</b> </label>
                                <select class="form-control" name="promo_type" id="editPromoType" required>
                                    <option value="percentage">  Persentase (%) </option>
                                    <option value="fixed">  Nominal (Rp) </option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="editpromo_value"> <b>Nilai Promo</b> </label>
                                <input type="number" name="promo_value" id="editpromo_value" class="form-control" min="0"  step="0.01" required>
                            </div>
                        </div>

                        {{-- SCOPE --}}
                        <div class="form-group">
                            <label for="editScope"> <b>Berlaku Untuk</b> </label>
                            <select class="form-control" name="scope" id="editScope" required>
                                <option value="all"> Semua Produk </option>
                                <option value="category"> Kategori Tertentu </option>
                                <option value="product"> Produk Tertentu </option>
                            </select>
                        </div>


                        {{-- CATEGORY --}}
                        <div class="form-group" id="editCategoryScope" style="display:none;">
                            <label>
                                <b>Pilih Kategori</b>
                            </label>
                            <select name="category_ids[]" id="editCategoryIds" class="form-control" multiple>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                @endforeach
                            </select>

                            <small class="form-text text-muted">
                                Bisa memilih lebih dari satu kategori.
                            </small>
                        </div>


                        {{-- PRODUCT --}}
                        <div class="form-group" id="editProductScope" style="display:none;">
                            <label> <b>Pilih Produk</b> </label>
                            <select name="product_ids[]" id="editProductIds" class="form-control"  multiple style="width: 100%;">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $promotions->products->contains('product_id', $product->id) ? 'selected' : '' }}>
                                        {{ $product->name }}
                                        - Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>

                            <small class="form-text text-muted">
                                Bisa memilih lebih dari satu produk.
                            </small>
                        </div>


                        {{-- JAM --}}
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label for="editstart_time"> <b>Jam Mulai</b> </label>
                                <input type="time" name="start_time" id="editstart_time" class="form-control" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="editend_time"> <b>Jam Selesai</b> </label>
                                <input type="time" name="end_time" id="editend_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"> Close </button>
                            <button type="submit" class="btn btn-primary"> Save changes </button>
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
            var data = $(this).data('item');


            $('#editRowid').val(data.id);
            $('#editcode').val(data.code);
            $('#editname').val(data.name);
            $('#editdescription').val(data.description);
            $('#editPromoType').val(data.type);
            $('#editpromo_value').val(data.value);
            $('#editScope').val(data.scope);
            $('#editstart_time').val(data.start_at);
            $('#editend_time').val(data.end_at);
            updateScopeUI();
            $('#editModal').modal('show');
        });
    });
</script>

<script>
    $('#addScope').on('change', function () {

        let scope = $(this).val();
        // Sembunyikan semuanya terlebih dahulu
        $('#categoryScope').hide();
        $('#productScope').hide();

        // Hapus required
        $('#addCategoryIds').prop('required', false);
        $('#addProductIds').prop('required', false);

        if (scope === 'category') {
            $('#categoryScope').show();
            $('#addCategoryIds').prop('required', true);
        } else if (scope === 'product') {
            $('#productScope').show();
            $('#addProductIds').prop('required', true);
        }
    });

</script>

<script>
    function updateScopeUI() {

        let scope = $('#editScope').val();

        $('#editCategoryScope').hide();
        $('#editProductScope').hide();

        $('#editCategoryIds').prop('required', false);
        $('#editProductIds').prop('required', false);

        if (scope === 'category') {

            $('#editCategoryScope').show();
            $('#editCategoryIds').prop('required', true);

        } else if (scope === 'product') {

            $('#editProductScope').show();
            $('#editProductIds').prop('required', true);
        }
    }

    $('#editScope').on('change', function () {
        updateScopeUI();
    });



</script>





@endsection