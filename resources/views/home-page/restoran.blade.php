<!-- resources/views/home.blade.php -->
@extends('home-page.layouts.app-home')

@section('content')

<style>
    .categories-select {
        border: 1px solid #033800;
        border-radius: 5px;
        padding: 10px;
        font-size: 1.2rem;
        color: #033800;
        background-color: #f8f9fa;
    }

    .categories-select:focus {
        border-color: #033800;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    #searchMenu {
        border-color: #033800;
    }

    #searchMenu:focus {
        border-color: #033800;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* =========================================
    MERCHANT STATUS
    ========================================= */

    .merchant-status {
        display: flex;
        align-items: center;
        max-width: 700px;
        margin: 20px auto 10px;
        padding: 14px 16px;
        border-radius: 12px;
        border: 1px solid;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .merchant-status-icon {
        width: 44px;
        height: 44px;
        min-width: 44px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        color: #fff;
        font-size: 19px;

        margin-right: 13px;
    }

    .merchant-status-content {
        display: flex;
        flex-direction: column;
        line-height: 1.4;
    }

    .merchant-status-content strong {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .merchant-status-content span {
        font-size: 13px;
    }


    /* =========================================
    MERCHANT OPEN
    ========================================= */

    .merchant-open {
        background: #f0fff4;
        border-color: #badbcc;
        color: #146c43;
    }

    .merchant-open .merchant-status-icon {
        background: #198754;
    }

    .merchant-open .merchant-status-content span {
        color: #5f7568;
    }


    /* =========================================
    MERCHANT BEFORE OPEN
    ========================================= */

    .merchant-before-open {
        background: #fff8e6;
        border-color: #ffe69c;
        color: #856404;
    }

    .merchant-before-open .merchant-status-icon {
        background: #ffc107;
        color: #212529;
    }

    .merchant-before-open .merchant-status-content span {
        color: #756b4a;
    }


    /* =========================================
    MERCHANT CLOSED / INACTIVE
    ========================================= */

    .merchant-closed {
        background: #fff3f3;
        border-color: #f5c2c7;
        color: #842029;
    }

    .merchant-closed .merchant-status-icon {
        background: #dc3545;
    }

    .merchant-closed .merchant-status-content span {
        color: #765b5d;
    }


    /* =========================================
    DISABLED ADD TO CART
    ========================================= */

    .btn:disabled {
        cursor: not-allowed;
        opacity: 0.65;
    }


    /* =========================================
    MOBILE
    ========================================= */

    @media (max-width: 576px) {

        .merchant-status {
            margin: 15px 10px 10px;
            padding: 12px 13px;
            border-radius: 10px;
        }

        .merchant-status-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;

            font-size: 17px;
            margin-right: 10px;
        }

        .merchant-status-content strong {
            font-size: 14px;
        }

        .merchant-status-content span {
            font-size: 12px;
        }
    }


    /* =========================================
   PRODUCT MODAL
    ========================================= */

    .product-modal {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }


    /* =========================================
    HEADER
    ========================================= */

    .product-modal-header {
        padding: 16px 20px;
        border-bottom: 1px solid #eeeeee;
        background: #ffffff;
    }

    .product-modal-header .modal-title {
        color: #222;
        font-size: 18px;
    }


    /* =========================================
    BODY
    ========================================= */

    .product-modal-body {
        padding: 20px;
    }


    /* =========================================
    IMAGE
    ========================================= */

    .product-modal-image {
        width: 100%;
        height: 280px;
        overflow: hidden;
        border-radius: 12px;
        background: #f5f5f5;
        margin-bottom: 20px;
    }

    .product-modal-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }


    /* =========================================
    PRODUCT INFO
    ========================================= */

    .product-modal-info {
        text-align: center;
    }

    .product-modal-name {
        font-size: 20px;
        font-weight: 700;
        color: #222;
        margin-bottom: 8px;
    }

    .product-modal-price {
        font-size: 20px;
        font-weight: 700;
        color: #033800;
        margin-bottom: 14px;
    }


    /* OPEN */

    .status-open {
        background: #e9f8ef;
        color: #198754;
    }


    /* CLOSED */

    .status-closed {
        background: #f1f1f1;
        color: #6c757d;
    }


    /* =========================================
    FOOTER
    ========================================= */

    .product-modal-footer {
        display: flex;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid #eeeeee;
        background: #ffffff;
    }


    /* =========================================
    BUTTON
    ========================================= */

    .product-close-btn {
        flex: 0 0 auto;

        padding: 10px 18px;

        border: 1px solid #dee2e6;
        border-radius: 8px;

        font-size: 14px;
    }

    .product-add-btn {
        flex: 1;

        padding: 10px 18px;

        border-radius: 8px;

        font-size: 14px;
        font-weight: 600;
    }


    /* =========================================
    MOBILE
    ========================================= */

    @media (max-width: 576px) {

        .product-modal {
            margin: 10px;
            border-radius: 14px;
        }

        .product-modal-body {
            padding: 15px;
        }

        .product-modal-image {
            height: 230px;
            margin-bottom: 16px;
        }

        .product-modal-name {
            font-size: 18px;
        }

        .product-modal-price {
            font-size: 18px;
        }

        .product-modal-footer {
            padding: 12px 15px;
        }

        .product-close-btn {
            padding: 10px 14px;
        }

        .product-add-btn {
            padding: 10px 12px;
            font-size: 13px;
        }
    }

</style>

<div class="d-flex flex-wrap justify-content-center" style="gap: 1rem;">
    <div class="card" style="min-width: 150px;">
        <img class="card-img-top" src="{{ asset('img/' . $merchant->logo) }}" alt="{{ $merchant->name }}" />
        <div class="card-body d-flex flex-column justify-content-between text-center">
            <div>
                <h4 class="fw-bolder mb-1">{{ $merchant->name }}</h4>
                <small class="text-muted d-block mb-2">{{ $merchant->address }}</small>
            </div>
        </div>
    </div>
</div>

@if($merchantStatus === 'inactive')

    <div class="merchant-status merchant-closed">
        <div class="merchant-status-icon">
            <i class="bi bi-shop-window"></i>
        </div>

        <div class="merchant-status-content">
            <strong>Maaf sedang tidak menerima pesanan</strong>
            <span>
                Saat ini kami belum menerima pesanan.
                Silakan kembali lagi nanti.
            </span>
        </div>
    </div>

@elseif($merchantStatus === 'before_open')

    <div class="merchant-status merchant-before-open">
        <div class="merchant-status-icon">
            <i class="bi bi-clock"></i>
        </div>

        <div class="merchant-status-content">
            <strong>Maaf belum buka</strong>
            <span>
                Kami mulai menerima pesanan pukul
                {{ \Carbon\Carbon::parse($merchant->open)->format('H:i') }}.
            </span>
        </div>
    </div>

@elseif($merchantStatus === 'after_close')

    <div class="merchant-status merchant-closed">
        <div class="merchant-status-icon">
            <i class="bi bi-clock-history"></i>
        </div>

        <div class="merchant-status-content">
            <strong>Maaf sudah tutup</strong>
            <span>
                Kami menerima pesanan sampai pukul
                {{ \Carbon\Carbon::parse($merchant->closed)->format('H:i') }}.
            </span>
        </div>
    </div>

@else


@endif

<section class="py-4">
    <div class="container px-4 px-lg-5 mt-0">

        <!-- Pilihan Kategori -->
        <div class="text-center mb-4">
            <h4 class="fw-bolder">Kategori Menu</h4>
        </div>
        <div class="form-group">
            <select class="form-select form-select-lg categories-select" id="selectedCategories">
                <option value="all" data-categories="all" selected>SEMUA MENU</option>
                @foreach ($categories as $item)
                    <option value="{{ $item->id }}" data-categories="{{ $item->id }}" {{ request()->query('categories') == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>            
        </div>

        <div class="mb-4 mt-3">
            <div class="input-group">
                <input type="text"
                    id="searchMenu"
                    class="form-control form-control-lg"
                    placeholder="Cari menu..."
                    autocomplete="off">

                <button class="btn btn-outline-secondary"
                        type="button"
                        id="clearSearch">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- List makanan -->
        <div class="text-center">
            <h4 class="fw-bolder mt-5 mb-4">Pilihan Menu</h4>
        </div>

        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
            @if($products->isNotEmpty())
                @foreach ($products as $item)
                <div class="col mb-5 product-item"
                    data-name="{{ strtolower($item->name) }}">
                    <div class="card h-100">
                        <img class="card-img-top" data-bs-toggle="modal" data-bs-target="#modal{{ $item->id }}" src="{{ asset('img/' . $item->image) }}" alt="..." onerror="this.onerror=null;this.src='{{ asset('img/default-img.jpeg') }}';" style="width: 100%; height: 150px; object-fit: cover;"/>
                        @if($item->is_favorite)
                            <span class="badge bg-warning text-dark position-absolute"
                                style="top: 10px; left: 10px;">
                                <i class="bi bi-star-fill"></i> NEW
                            </span>
                        @endif
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name with modal trigger-->
                                <h5 class="fw-bolder" style="font-size: 14px;" >{{ $item->name }}</h5>
                                <!-- Product price-->
                                Rp {{ number_format($item->price , 0, ',', '.') }}
                            </div>
                        </div>
                        <!-- Product actions-->
                        @if($merchantStatus === 'open')
                            <a class="btn btn-outline-dark mt-auto btn-add-to-cart"
                                href="javascript:void(0)"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                                data-price="{{ $item->price }}"
                                data-idmerchant="{{ $merchant->id }}"
                                data-img="{{ $item->image }}">
                                <i class="bi bi-cart-plus"></i>
                                Add to Cart
                            </a>
                        @else
                            <button type="button"
                                class="btn btn-secondary mt-auto w-100"
                                disabled>
                                <i class="bi bi-cart-x"></i>
                                Tidak Bisa Order
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade"
                    id="modal{{ $item->id }}"
                    tabindex="-1"
                    aria-labelledby="modalLabel{{ $item->id }}"
                    aria-hidden="true">

                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content product-modal">

                            <!-- Header -->
                            <div class="modal-header product-modal-header">
                                <h5 class="modal-title fw-bold"
                                    id="modalLabel{{ $item->id }}">
                                    {{ $item->name }}
                                </h5>

                                <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close">
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="modal-body product-modal-body">

                                <!-- Product Image -->
                                <div class="product-modal-image">
                                    <img src="{{ asset('img/' . $item->image) }}"
                                        alt="{{ $item->name }}"
                                        onerror="this.onerror=null;this.src='{{ asset('img/default-img.jpeg') }}';">
                                </div>

                                <!-- Product Info -->
                                <div class="product-modal-info">

                                    <h4 class="product-modal-name">
                                        {{ $item->name }}
                                    </h4>

                                    <div class="product-modal-price">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </div>

                                </div>

                            </div>

                            <!-- Footer -->
                            <div class="modal-footer product-modal-footer">

                                <button type="button"
                                        class="btn btn-light product-close-btn"
                                        data-bs-dismiss="modal">
                                    Tutup
                                </button>

                                @if($merchantStatus === 'open')

                                    <a href="javascript:void(0)"
                                    class="btn btn-primary product-add-btn btn-add-to-cart"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-price="{{ $item->price }}"
                                    data-idmerchant="{{ $merchant->id }}"
                                    data-img="{{ $item->image }}">

                                        <i class="bi bi-cart-plus me-1"></i>
                                        Tambah ke Keranjang
                                    </a>

                                @else

                                    <button type="button"
                                            class="btn btn-secondary product-add-btn"
                                            disabled>

                                        <i class="bi bi-cart-x me-1"></i>
                                        Tidak Bisa Order

                                    </button>

                                @endif

                            </div>

                        </div>
                    </div>
                </div>
                @endforeach

                <div id="noSearchResult"
                    class="d-none text-center w-100 py-5">

                    <h5 class="fw-bold text-muted">
                        Menu tidak ditemukan
                    </h5>

                    <p class="text-muted mb-0">
                        Coba gunakan kata kunci lain.
                    </p>

                </div>
                
            @else

            <div class="d-flex flex-column align-items-center justify-content-center">
                <h5 class="fw-bold text-muted">Oops! Menu belum tersedia coba kategori menu lain.</h5>
            </div>            
                
            @endif
        </div>

        <!-- modal promo -->
        @if(!empty($promo->description))
        <div class="modal fade" id="popupPromo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel{{ $promo->description }}">{{ $promo->description }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="{{ url('public/img/' .$promo->value) }}" alt="{{ url('public/img/' .$promo->value) }}" class="img-fluid" onerror="this.onerror=null;this.src='{{ asset('img/default-img.jpeg') }}';"/>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
    </div>
</section>

@if(session('error'))
<script>
    alert('{{ session('error') }}');
</script>
@endif

<script>
    $(document).on('click', '.btn-add-to-cart', function () {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = $(this).data('price');
        const merchant = $(this).data('idmerchant');
        const productImage = $(this).data('img');
        const mDiscount = $(this).data('discount');

        $.ajax({
            url: "{{ route('cart.add') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1, // Default
                merchantId: merchant,
                isDiscount: mDiscount,
                productImage: productImage,
            },
            success: function (response) {
                alert(response.message);
                $('#cart-badge').text(Object.keys(response.cart).length);
            },
            error: function (xhr) {
                alert('Terjadi kesalahan. Silakan coba lagi.');
            }
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectedCat = document.getElementById('selectedCategories');
        selectedCat.addEventListener('change', function () {
            const mcat = this.options[this.selectedIndex].getAttribute('data-categories');
            if (mcat) {
                window.location.href = `/restoran/?categories=${encodeURIComponent(mcat)}`;
            }
        });
    });
</script>

<script type="text/javascript">
    window.onload = () => {
        const myModal = new bootstrap.Modal('#popupPromo');
        myModal.show();
    }
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchMenu');
    const clearButton = document.getElementById('clearSearch');
    const products = document.querySelectorAll('.product-item');
    const noResult = document.getElementById('noSearchResult');

    searchInput.addEventListener('input', function () {

        const keyword = this.value
            .toLowerCase()
            .trim();

        let found = 0;

        products.forEach(function (product) {

            const name = product.dataset.name || '';
            const sku = product.dataset.sku || '';

            const match =
                name.includes(keyword) ||
                sku.includes(keyword);

            product.style.display = match ? '' : 'none';

            if (match) {
                found++;
            }

        });

        if (found === 0) {
            noResult.classList.remove('d-none');
        } else {
            noResult.classList.add('d-none');
        }

    });

    clearButton.addEventListener('click', function () {

        searchInput.value = '';

        products.forEach(function (product) {
            product.style.display = '';
        });

        noResult.classList.add('d-none');

        searchInput.focus();
    });

    });
</script>



@endsection