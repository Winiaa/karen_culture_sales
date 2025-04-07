@props(['product'])

<div class="card product-card h-100">
    @if($product->image)
        <img src="{{ Storage::url($product->image) }}" class="card-img-top product-image" alt="{{ $product->title }}">
    @else
        <div class="card-img-top bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px;">
            <i class="fas fa-image fa-3x text-muted"></i>
        </div>
    @endif
    <div class="card-body">
        <h5 class="card-title text-truncate">{{ $product->title }}</h5>
        <p class="card-text text-muted small">{{ $product->category->category_name }}</p>
        
        <!-- Rating -->
        <div class="rating mb-2">
            @for($i = 1; $i <= 5; $i++)
                <i class="{{ $i <= $product->average_rating ? 'fas fa-star' : 'far fa-star' }}"></i>
            @endfor
            @if($product->reviews->count() > 0)
                <span class="ms-1 small">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews->count() }})</span>
            @endif
        </div>
        
        <!-- Price and Stock Level -->
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
            <div class="price-container">
                @if($product->discount_price)
                    <div class="d-flex flex-column">
                        <span class="text-muted text-decoration-line-through small">@baht($product->price)</span>
                        <span class="text-danger fw-bold">@baht($product->discount_price)</span>
                    </div>
                @else
                    <span class="text-dark fw-bold">@baht($product->price)</span>
                @endif
            </div>
            
            <!-- Stock Level Indicator -->
            @php
                $stockColor = 'success';
                $stockText = $product->quantity . ' in stock';
                
                if ($product->quantity <= 0) {
                    $stockColor = 'danger';
                    $stockText = 'Out of Stock';
                } elseif ($product->quantity <= 5) {
                    $stockColor = 'warning';
                    $stockText = 'Only ' . $product->quantity . ' left';
                }
            @endphp
            <span class="badge bg-{{ $stockColor }} mt-1 mt-sm-0">{{ $stockText }}</span>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0">
        <div class="d-flex gap-2 flex-column flex-sm-row">
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary">
                <i class="fas fa-eye me-1"></i> View
            </a>
            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-grow-1">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-karen w-100" {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                </button>
            </form>
        </div>
    </div>
</div> 