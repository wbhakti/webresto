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
                        <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                            <div class="text-center">
                                <a class="btn btn-outline-dark mt-auto btn-add-to-cart" href="javascript:void(0)" 
                                data-id="{{ $item->id }}" 
                                data-name="{{ $item->name }}" 
                                data-price="{{ $item->price }}"
                                data-idmerchant="{{ $merchant->id }}"
                                data-img="{{ $item->image }}">
                                Add to cart
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal{{ $item->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel{{ $item->id }}">{{ $item->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Harga:</strong> Rp {{ number_format($item->price , 0, ',', '.') }}</p>
                                <img src="{{ asset('img/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid" onerror="this.onerror=null;this.src='{{ asset('img/default-img.jpeg') }}';"/>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <a class="btn btn-primary btn-add-to-cart" href="javascript:void(0)" 
                                    data-id="{{ $item->id }}" 
                                    data-name="{{ $item->name }}" 
                                    data-price="{{ $item->price }}"
                                    data-idmerchant="{{ $merchant->id }}"
                                    data-img="{{ $item->image }}">
                                    Add to Cart
                                </a>
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