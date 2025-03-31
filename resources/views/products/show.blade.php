@extends('layouts.app')

@section('styles')
    <style>
        /* Rating stars styling */
        .rating-input {
            margin-bottom: 10px;
        }

        .rating-star {
            cursor: pointer;
            font-size: 1.5rem;
            transition: transform 0.2s;
        }

        .rating-star:hover {
            transform: scale(1.1);
        }

        .rating-star i {
            transition: all 0.2s ease;
        }

        .rating-text {
            margin-left: 10px;
            font-weight: 500;
        }

        /* Product image styling */
        .main-product-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            border: 1px solid #eee;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Slider styling */
        .product-image-slider {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .product-image-slider .slide {
            display: none;
            width: 100%;
        }

        .product-image-slider .slide.active {
            display: block !important;
        }

        .thumbnail-nav {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            transform: translateY(-3px);
        }

        .thumbnail.active {
            border-color: #38603f;
        }

        .slider-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            z-index: 900;
            pointer-events: none;
        }

        .slider-control {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            height: 50px;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin: 0 10px;
            transition: all 0.3s ease;
            pointer-events: auto;
            z-index: 901;
            color: #38603f;
            font-size: 1.2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .slider-control:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        /* Product details box */
        .product-details-box {
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
        }

        /* Lightbox styling */
        .lightbox-slider {
            position: relative;
            width: 100%;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-slide {
            display: none;
            width: 100%;
            height: 100%;
            text-align: center;
        }

        .lightbox-slide.active {
            display: block !important;
        }

        .lightbox-slide img {
            max-height: 80vh;
            max-width: 100%;
            object-fit: contain;
        }

        .lightbox-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            z-index: 9999;
            pointer-events: none;
            padding: 0 20px;
        }

        .lightbox-control {
            background: rgba(255, 255, 255, 0.4);
            border: none;
            border-radius: 50%;
            height: 80px;
            width: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin: 0 20px;
            transition: all 0.3s ease;
            pointer-events: auto;
            color: white;
            font-size: 2rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            opacity: 0.9;
        }

        .lightbox-control:hover {
            background: rgba(255, 255, 255, 0.6);
            transform: scale(1.1);
            color: #38603f;
            opacity: 1;
        }

        /* Make product images clickable */
        .product-image-slider .slide img {
            cursor: zoom-in;
        }
    </style>
@endsection

@section('content')
    <script>
        /**
         * Set star rating for product reviews
         * @param {number} rating - Rating value (1-5)
         * @param {HTMLElement} starElement - The star element clicked
         */
        function setRating(rating, starElement) {
            try {
                const container = starElement.closest('.rating-input');
                const form = starElement.closest('form');
                const input = form.querySelector('input[name="rating"]');

                // Update hidden input value
                input.value = rating;

                // Update star appearances
                const stars = container.querySelectorAll('.rating-star i');
                for (let i = 0; i < stars.length; i++) {
                    if (i < rating) {
                        // Filled star
                        stars[i].className = 'fas fa-star text-warning fs-4';
                        stars[i].style.color = '#ffc107';
                    } else {
                        // Empty star
                        stars[i].className = 'far fa-star text-muted fs-4';
                        stars[i].style.color = '#6c757d';
                    }
                }

                // Update rating text description
                const textElement = container.querySelector('.rating-text');
                if (textElement) {
                    let ratingText = '';
                    switch(rating) {
                        case 1: ratingText = 'Poor'; break;
                        case 2: ratingText = 'Fair'; break;
                        case 3: ratingText = 'Good'; break;
                        case 4: ratingText = 'Very Good'; break;
                        case 5: ratingText = 'Excellent'; break;
                    }
                    textElement.textContent = ratingText;
                }
            } catch(e) {
                console.error('Error in setRating:', e);
            }
        }

        /**
         * Set star rating for edit review form
         * @param {number} rating - Rating value (1-5)
         * @param {HTMLElement} starElement - The star element clicked
         */
        function setEditRating(rating, starElement) {
            try {
                const container = starElement.closest('.rating-input');
                const form = starElement.closest('form');
                const input = form.querySelector('input[name="rating"]');

                // Update hidden input value
                if (input) {
                    input.value = rating;
                }

                // Update stars directly
                const stars = container.querySelectorAll('.rating-star i');
                for (let i = 0; i < stars.length; i++) {
                    if (i < rating) {
                        stars[i].className = 'fas fa-star text-warning';
                        stars[i].style.color = '#ffc107';
                    } else {
                        stars[i].className = 'far fa-star text-muted';
                        stars[i].style.color = '#6c757d';
                    }
                }

                // Update text
                const textElement = container.querySelector('.rating-text');
                if (textElement) {
                    let ratingText = '';
                    switch(rating) {
                        case 1: ratingText = 'Poor'; break;
                        case 2: ratingText = 'Fair'; break;
                        case 3: ratingText = 'Good'; break;
                        case 4: ratingText = 'Very Good'; break;
                        case 5: ratingText = 'Excellent'; break;
                    }
                    textElement.textContent = ratingText;
                }
            } catch(e) {
                console.error('Error in setEditRating:', e);
            }
        }
    </script>

    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categories.show', $product->category) }}">{{ $product->category->category_name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->title }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6 mb-4">
                <div class="product-image-gallery">
                    <div class="product-image-slider">
                        <!-- Main image as first slide -->
                        <div class="slide active">
                            <img src="{{ Storage::url($product->image) }}" class="main-product-image d-block w-100" alt="{{ $product->title }}">
                        </div>

                        <!-- Additional images as additional slides -->
                        @if(!empty($product->additional_images) && is_array($product->additional_images))
                            @foreach($product->additional_images as $index => $imagePath)
                                <div class="slide">
                                    <img src="{{ Storage::url($imagePath) }}" class="main-product-image d-block w-100" alt="{{ $product->title }}">
                                </div>
                            @endforeach
                        @endif

                        <!-- Slider Controls - show only if there are additional images -->
                        @if(!empty($product->additional_images) && is_array($product->additional_images) && count($product->additional_images) > 0)
                            <div class="slider-controls">
                                <button type="button" class="slider-control prev" id="prev-slide" onclick="prevSlide()" aria-label="Previous image">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="slider-control next" id="next-slide" onclick="nextSlide()" aria-label="Next image">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>

                            <!-- Inline script to ensure navigation functions are available immediately -->
                            <script>
                                // Immediate navigation functions
                                function prevSlide() {
                                    const slides = document.querySelectorAll('.product-image-slider .slide');
                                    const thumbnails = document.querySelectorAll('.thumbnail-nav .thumbnail');
                                    let currentIndex = 0;

                                    // Find the current active slide
                                    for (let i = 0; i < slides.length; i++) {
                                        if (slides[i].classList.contains('active')) {
                                            currentIndex = i;
                                            break;
                                        }
                                    }

                                    // Calculate new index
                                    let newIndex = currentIndex - 1;
                                    if (newIndex < 0) newIndex = slides.length - 1;

                                    // Update slides
                                    for (let i = 0; i < slides.length; i++) {
                                        slides[i].style.display = 'none';
                                        slides[i].classList.remove('active');
                                        if (thumbnails[i]) thumbnails[i].classList.remove('active');
                                    }

                                    // Show new slide
                                    slides[newIndex].style.display = 'block';
                                    slides[newIndex].classList.add('active');
                                    if (thumbnails[newIndex]) thumbnails[newIndex].classList.add('active');
                                }

                                function nextSlide() {
                                    const slides = document.querySelectorAll('.product-image-slider .slide');
                                    const thumbnails = document.querySelectorAll('.thumbnail-nav .thumbnail');
                                    let currentIndex = 0;

                                    // Find the current active slide
                                    for (let i = 0; i < slides.length; i++) {
                                        if (slides[i].classList.contains('active')) {
                                            currentIndex = i;
                                            break;
                                        }
                                    }

                                    // Calculate new index
                                    let newIndex = currentIndex + 1;
                                    if (newIndex >= slides.length) newIndex = 0;

                                    // Update slides
                                    for (let i = 0; i < slides.length; i++) {
                                        slides[i].style.display = 'none';
                                        slides[i].classList.remove('active');
                                        if (thumbnails[i]) thumbnails[i].classList.remove('active');
                                    }

                                    // Show new slide
                                    slides[newIndex].style.display = 'block';
                                    slides[newIndex].classList.add('active');
                                    if (thumbnails[newIndex]) thumbnails[newIndex].classList.add('active');
                                }
                            </script>
                        @endif
                    </div>

                    <!-- Thumbnail Navigation - show only if there are additional images -->
                    @if(!empty($product->additional_images) && is_array($product->additional_images) && count($product->additional_images) > 0)
                        <div class="thumbnail-nav">
                            <!-- Main image thumbnail -->
                            <img src="{{ Storage::url($product->image) }}" class="thumbnail active" alt="{{ $product->title }}" data-index="0" onclick="showThumbnail(0)">

                            <!-- Additional image thumbnails -->
                            @foreach($product->additional_images as $index => $imagePath)
                                <img src="{{ Storage::url($imagePath) }}" class="thumbnail" alt="{{ $product->title }}" data-index="{{ $index + 1 }}" onclick="showThumbnail({{ $index + 1 }})">
                            @endforeach
                        </div>

                        <!-- Thumbnail click function -->
                        <script>
                            function showThumbnail(index) {
                                const slides = document.querySelectorAll('.product-image-slider .slide');
                                const thumbnails = document.querySelectorAll('.thumbnail-nav .thumbnail');

                                // Hide all slides
                                for (let i = 0; i < slides.length; i++) {
                                    slides[i].style.display = 'none';
                                    slides[i].classList.remove('active');
                                    thumbnails[i].classList.remove('active');
                                }

                                // Show selected slide
                                slides[index].style.display = 'block';
                                slides[index].classList.add('active');
                                thumbnails[index].classList.add('active');
                            }
                        </script>
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-md-6 mb-4">
                <h1 class="mb-3">{{ $product->title }}</h1>

                <div class="mb-3">
                    <span class="badge bg-secondary">{{ $product->category->category_name }}</span>
                    <div class="rating d-inline-block ms-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="{{ $i <= $product->average_rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                        @endfor
                        <span class="ms-2">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews->count() }} reviews)</span>
                    </div>
                </div>

                <div class="mb-3">
                    @if($product->discount_price)
                        <h3>
                            <span class="text-danger">@baht($product->discount_price)</span>
                            <small class="text-muted text-decoration-line-through">@baht($product->price)</small>
                        </h3>
                    @else
                        <h3>@baht($product->price)</h3>
                    @endif
                </div>

                <div class="mb-4">
                    <p class="mb-2">Availability:
                        @if($product->in_stock)
                            <span class="text-success">In Stock ({{ $product->quantity }} available)</span>
                        @else
                            <span class="text-danger">Out of Stock</span>
                        @endif
                    </p>
                </div>

                @if($product->in_stock)
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="form-label">Quantity:</label>
                            </div>
                            <div class="col-auto">
                                <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="{{ $product->quantity }}">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-karen" onclick="this.innerHTML = '<i class=\'fas fa-check me-1\'></i>Alright!'; this.classList.add('added'); return true;">
                                    <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                <div class="mb-4">
                    <h4>Product Description</h4>
                    <p>{{ $product->description }}</p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Customer Reviews</h3>

                @auth
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Write a Review</h5>
                            <form action="{{ route('reviews.store', $product) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="rating-input">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="rating-star pe-1" data-rating="{{ $i }}"
                                                  onclick="setRating({{ $i }}, this); document.getElementById('rating-input').value = {{ $i }}; return false;">
                                    <i class="far fa-star text-muted fs-4"></i>
                                </span>
                                        @endfor
                                        <span class="rating-text"></span>
                                        <div class="rating-debug small text-muted mt-1"></div>
                                    </div>
                                    <input type="hidden" name="rating" id="rating-input" required>
                                    @error('rating')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comment</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-karen">Submit Review</button>
                            </form>
                        </div>
                    </div>
                @endauth

                <div class="row">
                    @forelse($product->reviews as $review)
                        @if($review->status === 'approved' || (auth()->check() && $review->user_id === auth()->id()))
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $review->rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                @endfor
                                            </div>
                                            <div>
                                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                                @if($review->status !== 'approved')
                                                    <span class="badge bg-{{ $review->status === 'pending' ? 'warning' : 'danger' }} ms-2">
                                                        {{ ucfirst($review->status) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="{{ $review->user->profile_picture_url }}" alt="{{ $review->user->name }}" 
                                                 class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            <h6 class="card-subtitle mb-0 text-muted">{{ $review->user->name }}</h6>
                                        </div>
                                        <p class="card-text">{{ $review->comment }}</p>

                                        @if(auth()->check() && $review->canBeEditedBy(auth()->user()))
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editReview{{ $review->id }}">
                                                    <i class="fas fa-edit me-1"></i> Edit
                                                </button>
                                                <form action="{{ route('reviews.destroy', $review) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash me-1"></i> Delete</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                No reviews yet. Be the first to review this product!
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Review Modals - Placed outside of the main review content -->
        @foreach($product->reviews as $review)
            @if(auth()->check() && $review->canBeEditedBy(auth()->user()))
                <!-- Edit Review Modal for {{ $review->id }} -->
                <div class="modal fade" id="editReview{{ $review->id }}" tabindex="-1" aria-labelledby="editReviewLabel{{ $review->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editReviewLabel{{ $review->id }}">Edit Your Review</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('reviews.update', $review) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Your Rating</label>
                                        <div class="rating-input">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="rating-star pe-1" data-rating="{{ $i }}"
                                                      onclick="setEditRating({{ $i }}, this); document.getElementById('editRating{{ $review->id }}').value = {{ $i }}; return false;">
                                        <i class="{{ $i <= $review->rating ? 'fas fa-star text-warning' : 'far fa-star text-muted' }}"></i>
                                    </span>
                                            @endfor
                                            <span class="rating-text">
                                        @php
                                            $ratingText = '';
                                            switch($review->rating) {
                                                case 1: $ratingText = 'Poor'; break;
                                                case 2: $ratingText = 'Fair'; break;
                                                case 3: $ratingText = 'Average'; break;
                                                case 4: $ratingText = 'Good'; break;
                                                case 5: $ratingText = 'Excellent'; break;
                                            }
                                        @endphp
                                                {{ $ratingText }}
                                    </span>
                                        </div>
                                        <input type="hidden" name="rating" id="editRating{{ $review->id }}" value="{{ $review->rating }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editComment{{ $review->id }}" class="form-label">Your Review</label>
                                        <textarea class="form-control" id="editComment{{ $review->id }}" name="comment" rows="4" required>{{ $review->comment }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-karen">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <!-- Related Products -->
        @if(isset($relatedProducts) && is_object($relatedProducts) && method_exists($relatedProducts, 'isNotEmpty') && $relatedProducts->isNotEmpty())
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-4">Related Products</h3>
                    <div class="row">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="col-md-3 mb-4">
                                <div class="card product-card h-100">
                                    @php
                                        $relatedImageUrl = asset('images/placeholder.jpg');
                                        try {
                                            if(isset($relatedProduct->image) && $relatedProduct->image) {
                                                $relatedImageUrl = Storage::url($relatedProduct->image);
                                            }
                                        } catch(\Exception $e) {
                                            \Log::error('Error displaying related product image: ' . $e->getMessage());
                                        }
                                    @endphp
                                    <img src="{{ $relatedImageUrl }}" class="card-img-top product-image" alt="{{ $relatedProduct->title }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $relatedProduct->title }}</h5>
                                        <p class="card-text text-muted">{{ $relatedProduct->category->category_name }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                @if($relatedProduct->discount_price)
                                                    <span class="text-muted text-decoration-line-through">@baht($relatedProduct->price)</span>
                                                    <span class="text-danger ms-2">@baht($relatedProduct->discount_price)</span>
                                                @else
                                                    <span class="text-dark">@baht($relatedProduct->price)</span>
                                                @endif
                                            </div>
                                            <div class="rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="{{ $i <= $relatedProduct->average_rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                @endfor
                                                @if($relatedProduct->reviews->count() > 0)
                                                    <span class="ms-1 small">{{ number_format($relatedProduct->average_rating, 1) }} ({{ $relatedProduct->reviews->count() }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-top-0">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('products.show', $relatedProduct) }}" class="btn btn-outline-primary flex-grow-1">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <form action="{{ route('cart.add', $relatedProduct) }}" method="POST" class="flex-grow-1">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-karen w-100" onclick="this.innerHTML = '<i class=\'fas fa-check me-1\'></i>Alright!'; this.classList.add('added'); return true;">
                                                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Lightbox Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="lightbox-slider">
                        <!-- Main image as first slide -->
                        <div class="lightbox-slide active">
                            <img src="{{ Storage::url($product->image) }}" class="d-block mx-auto" alt="{{ $product->title }}">
                        </div>

                        <!-- Additional images as additional slides -->
                        @if(!empty($product->additional_images) && is_array($product->additional_images))
                            @foreach($product->additional_images as $index => $imagePath)
                                <div class="lightbox-slide">
                                    <img src="{{ Storage::url($imagePath) }}" class="d-block mx-auto" alt="{{ $product->title }}">
                                </div>
                            @endforeach
                        @endif

                        <!-- Lightbox Controls -->
                        <div class="lightbox-controls">
                            <button type="button" class="lightbox-control prev" id="lightbox-prev" aria-label="Previous image" onclick="prevLightboxImage()">
                                <i class="fas fa-chevron-left fa-2x"></i>
                            </button>
                            <button type="button" class="lightbox-control next" id="lightbox-next" aria-label="Next image" onclick="nextLightboxImage()">
                                <i class="fas fa-chevron-right fa-2x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add data attributes for image lightbox to the slides -->
    <script>
        /**
         * Setup image lightbox functionality
         * Sets up modal triggers on product images
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Get all product images
            const productImages = document.querySelectorAll('.product-image-slider .slide img');

            // Add data attributes and click handlers
            productImages.forEach((img, index) => {
                img.style.cursor = 'zoom-in';
                img.setAttribute('data-bs-toggle', 'modal');
                img.setAttribute('data-bs-target', '#imageModal');
                img.setAttribute('data-slide-index', index.toString());

                img.addEventListener('click', function() {
                    // Store the index to use when modal is shown
                    window.selectedImageIndex = index;
                });
            });
        });
    </script>

    <!-- Lightbox functionality -->
    <script>
        /**
         * Lightbox navigation controls and functionality
         * Provides next/prev navigation and keyboard controls
         */

        // Global variable to track current lightbox slide
        window.currentLightboxIndex = 0;

        /**
         * Navigate to previous lightbox image
         */
        window.prevLightboxImage = function() {
            const lightboxSlides = document.querySelectorAll('.lightbox-slide');
            let newIndex = window.currentLightboxIndex - 1;
            if (newIndex < 0) newIndex = lightboxSlides.length - 1;

            updateLightboxImage(newIndex);
        };

        /**
         * Navigate to next lightbox image
         */
        window.nextLightboxImage = function() {
            const lightboxSlides = document.querySelectorAll('.lightbox-slide');
            let newIndex = window.currentLightboxIndex + 1;
            if (newIndex >= lightboxSlides.length) newIndex = 0;

            updateLightboxImage(newIndex);
        };

        /**
         * Update the displayed lightbox image
         * @param {number} index - Index of slide to display
         */
        function updateLightboxImage(index) {
            const lightboxSlides = document.querySelectorAll('.lightbox-slide');

            // Hide all slides
            for (let i = 0; i < lightboxSlides.length; i++) {
                lightboxSlides[i].style.display = 'none';
                lightboxSlides[i].classList.remove('active');
            }

            // Show the current slide
            if (lightboxSlides[index]) {
                lightboxSlides[index].style.display = 'block';
                lightboxSlides[index].classList.add('active');
                window.currentLightboxIndex = index;
            }
        }

        // Initialize the lightbox when the modal is shown
        document.addEventListener('DOMContentLoaded', function() {
            const imageModal = document.getElementById('imageModal');
            if (imageModal) {
                imageModal.addEventListener('shown.bs.modal', function() {
                    // Initialize with first slide or selected slide
                    const index = (window.selectedImageIndex !== undefined) ? window.selectedImageIndex : 0;
                    updateLightboxImage(index);
                });
            }

            // Set up keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (!imageModal || !imageModal.classList.contains('show')) return;

                if (e.key === 'ArrowLeft') {
                    window.prevLightboxImage();
                } else if (e.key === 'ArrowRight') {
                    window.nextLightboxImage();
                }
            });
        });
    </script>

    <!-- Review Submission JavaScript -->
    <script>
        /**
         * Review Rating Functionality
         * Handles star rating selection and submission
         */
        document.addEventListener('DOMContentLoaded', function() {
            /**
             * Set the rating value and update star visualization
             * @param {number} rating - Rating value (1-5)
             * @param {HTMLElement} starElement - The star element clicked
             */
            window.setRating = function(rating, starElement) {
                try {
                    const container = starElement.closest('.rating-input');
                    const form = starElement.closest('form');
                    const input = form.querySelector('input[name="rating"]');

                    // Update hidden input value
                    input.value = rating;

                    // Update star appearances
                    const stars = container.querySelectorAll('.rating-star i');
                    for (let i = 0; i < stars.length; i++) {
                        if (i < rating) {
                            // Filled star
                            stars[i].className = 'fas fa-star text-warning fs-4';
                            stars[i].style.color = '#ffc107';
                        } else {
                            // Empty star
                            stars[i].className = 'far fa-star text-muted fs-4';
                            stars[i].style.color = '#6c757d';
                        }
                    }

                    // Update rating text description
                    const textElement = container.querySelector('.rating-text');
                    if (textElement) {
                        let ratingText = '';
                        switch(rating) {
                            case 1: ratingText = 'Poor'; break;
                            case 2: ratingText = 'Fair'; break;
                            case 3: ratingText = 'Good'; break;
                            case 4: ratingText = 'Very Good'; break;
                            case 5: ratingText = 'Excellent'; break;
                        }
                        textElement.textContent = ratingText;
                    }
                } catch(e) {
                    console.error('Error in setRating:', e);
                }
            };

            /**
             * Set star rating for edit review form
             * @param {number} rating - Rating value (1-5)
             * @param {HTMLElement} starElement - The star element clicked
             */
            window.setEditRating = function(rating, starElement) {
                try {
                    const container = starElement.closest('.rating-input');
                    const form = starElement.closest('form');
                    const input = form.querySelector('input[name="rating"]');

                    // Update hidden input value
                    if (input) {
                        input.value = rating;
                    }

                    // Update stars directly
                    const stars = container.querySelectorAll('.rating-star i');
                    for (let i = 0; i < stars.length; i++) {
                        if (i < rating) {
                            stars[i].className = 'fas fa-star text-warning';
                            stars[i].style.color = '#ffc107';
                        } else {
                            stars[i].className = 'far fa-star text-muted';
                            stars[i].style.color = '#6c757d';
                        }
                    }

                    // Update text
                    const textElement = container.querySelector('.rating-text');
                    if (textElement) {
                        let ratingText = '';
                        switch(rating) {
                            case 1: ratingText = 'Poor'; break;
                            case 2: ratingText = 'Fair'; break;
                            case 3: ratingText = 'Good'; break;
                            case 4: ratingText = 'Very Good'; break;
                            case 5: ratingText = 'Excellent'; break;
                        }
                        textElement.textContent = ratingText;
                    }
                } catch(e) {
                    console.error('Error in setEditRating:', e);
                }
            };
        });
    </script>

    <!-- Review Edit Modal Functionality -->
    <script>
        /**
         * Review Edit Modal Functionality
         * Handles populating and showing the edit review modal
         */
        document.addEventListener('DOMContentLoaded', function() {
            /**
             * Open the review edit modal and populate fields
             * @param {number} reviewId - ID of the review to edit
             * @param {string} content - Content of the review
             * @param {number} rating - Rating value of the review
             */
            window.openEditModal = function(reviewId, content, rating) {
                const form = document.getElementById('edit-review-form');
                const contentField = document.getElementById('edit_content');

                // Set form action URL
                if (form) {
                    const url = "{{ route('reviews.update', ['review' => ':review_id']) }}";
                    form.action = url.replace(':review_id', reviewId);
                }

                // Set content field value
                if (contentField) {
                    contentField.value = content;
                }

                // Set rating stars
                window.setEditRating(rating);

                // Show the modal
                const editModal = new bootstrap.Modal(document.getElementById('editReviewModal'));
                editModal.show();
            };
        });
    </script>
@endsection

@section('scripts')
    <script>
        // Global slider functions - these must be global to work with onclick
        window.currentSlideIndex = 0;

        /**
         * Show a specific slide
         */
        window.showSlide = function(index) {
            // Get all slides and thumbnails
            const slides = document.querySelectorAll('.product-image-slider .slide');
            const thumbnails = document.querySelectorAll('.thumbnail-nav .thumbnail');

            if (slides.length === 0) {
                console.error('No slides found!');
                return;
            }

            // Adjust index if needed
            if (index < 0) index = slides.length - 1;
            if (index >= slides.length) index = 0;

            // Hide all slides
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = 'none';
                slides[i].classList.remove('active');
                if (thumbnails[i]) thumbnails[i].classList.remove('active');
            }

            // Show selected slide
            slides[index].style.display = 'block';
            slides[index].classList.add('active');
            if (thumbnails[index]) thumbnails[index].classList.add('active');

            // Store current index globally
            window.currentSlideIndex = index;
        }

        /**
         * Go to next slide
         */
        window.nextSlide = function() {
            const slides = document.querySelectorAll('.product-image-slider .slide');
            showSlide(window.currentSlideIndex + 1);
        }

        /**
         * Go to previous slide
         */
        window.prevSlide = function() {
            const slides = document.querySelectorAll('.product-image-slider .slide');
            showSlide(window.currentSlideIndex - 1);
        }

        // Initialize slider when DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.product-image-slider .slide');
            const thumbnails = document.querySelectorAll('.thumbnail-nav .thumbnail');

            // Initialize with first slide
            if (slides.length > 0) {
                window.currentSlideIndex = 0;
                showSlide(0);
            }

            // Set up thumbnail clicks
            thumbnails.forEach((thumb) => {
                thumb.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index')) || 0;
                    showSlide(index);
                });
            });
        });

        // Cart form handling with jQuery
        $(document).on('submit', 'form[action*="cart.add"]', function(e) {
            e.preventDefault();

            const form = $(this);
            const button = form.find('button[type="submit"]');
            const originalText = button.html();

            // Show loading state
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Adding...');

            // Send the AJAX request
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    // Show success state
                    button.removeClass('btn-karen').addClass('btn-karen added')
                        .html('<i class="fas fa-check me-1"></i> Added!');

                    // Update cart count in navigation
                    if (response.cart_count) {
                        $('#cart-count').text(response.cart_count);
                    }

                    // Flash notification
                    if (response.message) {
                        const notification = $('<div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 1050;" role="alert">' +
                            '<i class="fas fa-thumbs-up me-2"></i>' + response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>').appendTo('body');

                        setTimeout(function() {
                            notification.fadeOut(function() { $(this).remove(); });
                        }, 3000);
                    }

                    // Restore button after delay
                    setTimeout(function() {
                        button.prop('disabled', false).html(originalText).removeClass('added');
                    }, 2000);
                },
                error: function(xhr) {
                    // Show error state
                    button.removeClass('btn-karen').addClass('btn-danger')
                        .html('<i class="fas fa-times me-1"></i> Failed');

                    // Show error message
                    const errorMessage = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Failed to add to cart';

                    const notification = $('<div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 1050;" role="alert">' +
                        errorMessage +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>').appendTo('body');

                    setTimeout(function() {
                        notification.fadeOut(function() { $(this).remove(); });
                    }, 3000);

                    // Restore button after delay
                    setTimeout(function() {
                        button.prop('disabled', false).html(originalText)
                            .removeClass('btn-danger').addClass('btn-karen');
                    }, 2000);
                }
            });
        });

        // Handle modal triggers with Bootstrap
        const modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                const targetId = this.getAttribute('data-bs-target');
                const modalElement = document.querySelector(targetId);
                if (modalElement) {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            });
        });
    </script>
@endsection
