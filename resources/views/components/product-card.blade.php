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
        <h5 class="card-title">{{ $product->title }}</h5>
        <p class="card-text text-muted">{{ $product->category->category_name }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                @if($product->discount_price)
                <span class="text-muted text-decoration-line-through">@baht($product->price)</span>
                <span class="text-danger ms-2">@baht($product->discount_price)</span>
                @else
                <span class="text-dark">@baht($product->price)</span>
                @endif
            </div>
            <div class="rating">
                @for($i = 1; $i <= 5; $i++)
                    <i class="{{ $i <= $product->average_rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                @endfor
                @if($product->reviews->count() > 0)
                    <span class="ms-1 small">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews->count() }})</span>
                @endif
            </div>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0">
        <div class="d-flex gap-2">
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary flex-grow-1">
                <i class="fas fa-eye me-1"></i> View
            </a>
            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-grow-1">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn btn-karen w-100">
                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                </button>
            </form>
        </div>
    </div>
</div> 