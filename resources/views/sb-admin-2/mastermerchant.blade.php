@extends('sb-admin-2.layouts.app')

@section('content')

<!-- DataTables CSS -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

<style>
    .card-header {
        background-color: #fff;
    }

    .page-title {
        margin-bottom: 5px;
        font-weight: 700;
    }

    .page-description {
        color: #858796;
        margin-bottom: 0;
    }

    .merchant-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 5px;
        background-color: #fff;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .operational-time {
        font-weight: 600;
        color: #5a5c69;
    }

    .button-group {
        display: flex;
        justify-content: center;
        gap: 5px;
    }

    .table td {
        vertical-align: middle;
    }

    .modal-logo {
        width: 100px;
        height: 100px;
        object-fit: contain;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 5px;
        background-color: #fff;
    }
</style>


<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">

    <div>
        <h1 class="h3 text-gray-800 page-title">
            Master Merchant
        </h1>

        <p class="page-description">
            Kelola informasi merchant dan jam operasional.
        </p>
    </div>

</div>


<!-- Merchant Card -->
<div class="card shadow mb-4">

    <!-- Card Header -->
    <div class="card-header py-3 d-flex align-items-center justify-content-between">

        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-store mr-2"></i>
            Data Merchant
        </h6>

    </div>


    <!-- Card Body -->
    <div class="card-body">

        <div class="table-responsive">

            <table
                class="table table-bordered"
                id="dataTable"
                width="100%"
                cellspacing="0"
            >

                <thead>

                    <tr>

                        <th width="5%" class="text-center">
                            No
                        </th>

                        <th>
                            Nama Merchant
                        </th>

                        <th width="20%" class="text-center">
                            Jam Operasional
                        </th>

                        <th width="15%" class="text-center">
                            Logo
                        </th>

                        <th width="12%" class="text-center">
                            Status
                        </th>

                        <th width="10%" class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @if($data->isNotEmpty())

                        @foreach ($data as $item)

                            <tr>

                                <!-- No -->
                                <td class="text-center">
                                    {{ $loop->iteration }}
                                </td>


                                <!-- Merchant -->
                                <td>

                                    <strong>
                                        {{ $item->name }}
                                    </strong>

                                </td>


                                <!-- Jam Operasional -->
                                <td class="text-center">

                                    <span class="operational-time">

                                        <i class="fas fa-clock mr-1 text-primary"></i>

                                        {{ \Carbon\Carbon::parse($item->open)->format('H:i') }}

                                        <span class="mx-1">
                                            -
                                        </span>

                                        {{ \Carbon\Carbon::parse($item->closed)->format('H:i') }}

                                    </span>

                                </td>


                                <!-- Logo -->
                                <td class="text-center">

                                    @if($item->logo)

                                        <img
                                            src="{{ asset('img/' . $item->logo) }}"
                                            alt="{{ $item->name }}"
                                            class="merchant-logo"
                                        >

                                    @else

                                        <span class="text-muted">
                                            Tidak ada logo
                                        </span>

                                    @endif

                                </td>


                                <!-- Status -->
                                <td class="text-center">

                                    @if(isset($item->is_active))

                                        @if($item->is_active == 1)

                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                AKTIF
                                            </span>

                                        @else

                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                NON AKTIF
                                            </span>

                                        @endif

                                    @else

                                        <span class="status-badge status-active">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            AKTIF
                                        </span>

                                    @endif

                                </td>


                                <!-- Action -->
                                <td class="text-center">

                                    <div class="button-group">

                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm btn-edit"
                                            data-item='@json($item)'
                                            title="Edit Merchant"
                                        >

                                            <i class="fas fa-edit"></i>

                                            Edit

                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    @else

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-4"
                            >

                                <i class="fas fa-store-slash fa-2x text-gray-400 mb-2"></i>

                                <br>

                                <span class="text-muted">
                                    Data merchant tidak ditemukan.
                                </span>

                            </td>

                        </tr>

                    @endif

                </tbody>

            </table>

        </div>

    </div>

</div>



<!-- ===================================================== -->
<!-- EDIT MERCHANT MODAL -->
<!-- ===================================================== -->

<div
    class="modal fade"
    id="editModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="editModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">


            <!-- Modal Header -->
            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="editModalLabel"
                >

                    <i class="fas fa-store mr-2 text-primary"></i>

                    Edit Merchant

                </h5>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close"
                >

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>


            <!-- Modal Body -->
            <div class="modal-body">

                <form
                    id="editForm"
                    method="POST"
                    action="{{ url('/postmerchant') }}"
                    enctype="multipart/form-data"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="proses"
                        value="edit"
                    >

                    <input
                        type="hidden"
                        name="merchant_id"
                        id="editRowid"
                    >


                    <!-- Nama Merchant -->
                    <div class="form-group">

                        <label for="editName">
                            <b>Nama Merchant</b>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="editName"
                            class="form-control"
                            placeholder="Nama merchant"
                            required
                        >

                    </div>


                    <!-- Jam Operasional -->
                    <div class="row">

                        <div class="form-group col-md-6">

                            <label for="editopen_time">
                                <b>Jam Buka</b>
                            </label>

                            <div class="input-group">

                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                </div>

                                <input
                                    type="time"
                                    name="open_time"
                                    id="editopen_time"
                                    class="form-control"
                                    required
                                >

                            </div>

                        </div>


                        <div class="form-group col-md-6">

                            <label for="editclosed_time">
                                <b>Jam Tutup</b>
                            </label>

                            <div class="input-group">

                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                </div>

                                <input
                                    type="time"
                                    name="closed_time"
                                    id="editclosed_time"
                                    class="form-control"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    <!-- Status -->
                    @if(isset($item->is_active))

                    <div class="form-group">

                        <label for="editStatus">
                            <b>Status Merchant</b>
                        </label>

                        <select
                            name="is_active"
                            id="editStatus"
                            class="form-control"
                        >

                            <option value="1">
                                AKTIF
                            </option>

                            <option value="0">
                                NON AKTIF
                            </option>

                        </select>

                    </div>

                    @endif


                    <!-- Logo -->
                    <div class="form-group">

                        <label for="img_merchant">
                            <b>Logo Merchant</b>
                        </label>

                        <input
                            type="file"
                            name="img_merchant"
                            id="img_merchant"
                            class="form-control"
                            accept="image/*"
                        >

                        <small class="form-text text-muted">
                            Kosongkan jika tidak ingin mengganti logo.
                        </small>

                    </div>


                    <!-- Current Logo -->
                    <div class="form-group">

                        <label>
                            <b>Logo Saat Ini</b>
                        </label>

                        <div>

                            <img
                                id="currentimage"
                                src=""
                                alt="Current Logo"
                                class="modal-logo"
                            >

                        </div>

                    </div>


                    <!-- Modal Footer -->
                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                        >

                            <i class="fas fa-times mr-1"></i>

                            Batal

                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >

                            <i class="fas fa-save mr-1"></i>

                            Simpan Perubahan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>



<!-- ===================================================== -->
<!-- SESSION MESSAGE -->
<!-- ===================================================== -->

@if(session('success'))

<script>

    alert(@json(session('success')));

</script>

@endif


@if(session('error'))

<script>

    alert(@json(session('error')));

</script>

@endif



<!-- ===================================================== -->
<!-- DATATABLE -->
<!-- ===================================================== -->

<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>

<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>


<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    $('#dataTable').DataTable({

        lengthMenu: [
            [10, 20, 50, 100],
            [10, 20, 50, 100]
        ],

        pageLength: 10,

        searching: true,

        ordering: true,

        info: true

    });


    /*
    |--------------------------------------------------------------------------
    | Edit Merchant
    |--------------------------------------------------------------------------
    */

    $(document).on('click', '.btn-edit', function () {

        const data = $(this).data('item');


        // ID
        $('#editRowid').val(data.id);


        // Nama Merchant
        $('#editName').val(data.name);


        // Jam Buka
        $('#editopen_time').val(
            data.open
                ? data.open.substring(0, 5)
                : ''
        );


        // Jam Tutup
        $('#editclosed_time').val(
            data.closed
                ? data.closed.substring(0, 5)
                : ''
        );


        // Status
        if (data.is_active !== undefined) {

            $('#editStatus').val(data.is_active);

        }


        // Logo
        if (data.logo) {

            $('#currentimage').attr(
                'src',
                "{{ asset('img') }}/" + data.logo
            );

        } else {

            $('#currentimage').attr('src', '');

        }


        // Show modal
        $('#editModal').modal('show');

    });

});

</script>

@endsection