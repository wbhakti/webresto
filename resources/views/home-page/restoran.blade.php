<!-- resources/views/home.blade.php -->
@extends('home-page.layouts.app-home')

@section('content')

<style>
    .categories-select {
        border: 2px solid #033800;
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
</style>

<div class="d-flex flex-wrap justify-content-center" style="gap: 1rem;">
    <div class="card" style="min-width: 150px;">
        <!-- <div class="badge bg-dark text-white position-absolute" style="top: 0.5rem; right: 0.5rem">Favorit</div> -->
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
        
        <!-- List makanan -->
        <div class="text-center">
            <h4 class="fw-bolder mt-5 mb-4">Pilihan Menu</h4>
        </div>

        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
            @if($products->isNotEmpty())
                @foreach ($products as $item)
                <div class="col mb-5">
                    <div class="card h-100">
                        <img class="card-img-top" data-bs-toggle="modal" data-bs-target="#modal{{ $item->id }}" src="{{ url('public/img/' . $item->image) }}" alt="..." onerror="this.onerror=null;this.src='{{ asset('img/default-img.jpeg') }}';" style="width: 100%; height: 150px; object-fit: cover;"/>
                        <!-- Product details-->
                        <div class="card-body p-4">
                            <div class="text-center">
                                <!-- Product name with modal trigger-->
                                <h5 class="fw-bolder" style="font-size: 14px;" >{{ $item->name }}</h5>
                                <!-- Product reviews-->
                                <!-- <div class="d-flex justify-content-center small text-warning mb-2">
                                    <div class="bi-star-fill"></div>
                                    <div class="bi-star-fill"></div>
                                    <div class="bi-star-fill"></div>
                                    <div class="bi-star-fill"></div>
                                    <div class="bi-star-fill"></div>
                                </div> -->
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
                                <img src="{{ url('public/img/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid" onerror="this.onerror=null;this.src='{{ asset('img/default-img.jpeg') }}';"/>
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


@endsection