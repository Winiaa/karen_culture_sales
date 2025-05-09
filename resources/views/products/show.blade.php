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

        /* Stock level badge */
        .stock-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            display: inline-block;
            margin: 1rem 0;
        }

        .stock-badge.low {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .stock-badge.out {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stock-badge.in {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
            flex-wrap: wrap;
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

        /* Reviews section styling */
        .reviews-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }

        .review-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .review-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .review-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .review-author img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .review-rating {
            color: #ffc107;
        }

        .review-date {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .review-content {
            color: #212529;
            line-height: 1.6;
        }

        /* Review form styling */
        .review-form {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .review-stars {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .review-star {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ffc107;
            transition: all 0.2s ease;
        }

        .review-star:hover {
            transform: scale(1.1);
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

    <div class="container py-5">
        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6">
                <div class="product-image-slider">
                    @if($product->image)
                        <div class="slide active">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="main-product-image" onclick="openLightbox(0)">
                        </div>
                    @endif
                    @if(!empty($product->additional_images) && is_array($product->additional_images))
                        @foreach($product->additional_images as $index => $image)
                            <div class="slide">
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->title }}" class="main-product-image" onclick="openLightbox({{ $index + 1 }})">
                            </div>
                        @endforeach
                    @endif
                </div>
                @if(!empty($product->additional_images) && is_array($product->additional_images) && count($product->additional_images) > 0)
                    <div class="thumbnail-nav mt-3">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="thumbnail active" data-index="0">
                        @endif
                        @foreach($product->additional_images as $index => $image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->title }}" class="thumbnail" data-index="{{ $index + 1}}">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <h1 class="mb-3">{{ $product->title }}</h1>
                <p class="text-muted mb-3">Category: {{ $product->category->category_name }}</p>

                <!-- Stock Level Display -->
                @php
                    $stockClass = 'in';
                    $stockText = $product->quantity . ' in stock';
                    
                    if ($product->quantity <= 0) {
                        $stockClass = 'out';
                        $stockText = 'Out of Stock';
                    } elseif ($product->quantity <= 5) {
                        $stockClass = 'low';
                        $stockText = 'Only ' . $product->quantity . ' left in stock';
                    }
                @endphp
                <div class="stock-badge {{ $stockClass }}">
                    <i class="fas fa-box me-2"></i>{{ $stockText }}
                </div>

                <!-- Price -->
                <div class="mb-4">
                    @if($product->discount_price)
                        <span class="text-muted text-decoration-line-through h4">@baht($product->price)</span>
                        <span class="text-danger h3 ms-2">@baht($product->discount_price)</span>
                    @else
                        <span class="h3">@baht($product->price)</span>
                    @endif
                </div>

                <!-- Add to Cart Form -->
                <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-3 align-items-center">
                        <div class="col-auto">
                            <label for="quantity" class="col-form-label">Quantity:</label>
                        </div>
                        <div class="col-auto">
                            <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="{{ $product->quantity }}" {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-karen btn-lg" {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                                <i class="fas fa-cart-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Description -->
                <div class="mb-4">
                    <h4>Description</h4>
                    <p>{{ $product->description }}</p>
                </div>
            </div>
        </div>

        @if($relatedProducts->count())
            <div class="related-products my-5">
                <h3>Related Products</h3>
                <div class="row">
                    @foreach($relatedProducts as $related)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <x-product-card :product="$related" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Reviews Section -->
        <div class="reviews-section">
            <h3 class="mb-4">Customer Reviews</h3>
            
            <!-- Review Form -->
            @auth
                <div class="review-form mb-4">
                    <h4 class="mb-3">Write a Review</h4>
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
                            </div>
                            <input type="hidden" name="rating" id="rating-input" required>
                            @error('rating')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Your Review</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            @error('comment')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-karen">Submit Review</button>
                    </form>
                </div>
            @endauth

            <!-- Reviews List -->
            <div class="reviews-list">
                @forelse($product->reviews as $review)
                    @if($review->status === 'approved' || (auth()->check() && $review->user_id === auth()->id()))
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-author">
                                    <img src="{{ $review->user->profile_picture_url }}" alt="{{ $review->user->name }}" class="rounded-circle">
                                    <div>
                                        <h5 class="mb-0">{{ $review->user->name }}</h5>
                                        <div class="review-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $review->rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="review-date">
                                    {{ $review->created_at->diffForHumans() }}
                                    @if($review->status !== 'approved')
                                        <span class="badge bg-{{ $review->status === 'pending' ? 'warning' : 'danger' }} ms-2">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="review-content">
                                {{ $review->comment }}
                            </div>
                            @if(auth()->check() && $review->canBeEditedBy(auth()->user()))
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" 
                                            onclick="openEditModal({{ $review->id }}, '{{ addslashes($review->comment) }}', {{ $review->rating }})">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                @empty
                    <div class="alert alert-info">
                        No reviews yet. Be the first to review this product!
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReviewModalLabel">Edit Your Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-review-form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div class="rating-input">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="rating-star pe-1" data-rating="{{ $i }}"
                                          onclick="setEditRating({{ $i }}, this); document.getElementById('edit_rating').value = {{ $i }}; return false;">
                                        <i class="far fa-star text-muted"></i>
                                    </span>
                                @endfor
                                <span class="rating-text"></span>
                            </div>
                            <input type="hidden" name="rating" id="edit_rating" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_content" class="form-label">Your Review</label>
                            <textarea class="form-control" id="edit_content" name="comment" rows="3" required></textarea>
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
            const slides = document.querySelectorAll('.product-image-slider .slide');
            const thumbnails = document.querySelectorAll('.thumbnail-nav .thumbnail');
            
            if (slides.length > 1) {
                // Add navigation controls
                const sliderControls = document.createElement('div');
                sliderControls.className = 'slider-controls';
                sliderControls.innerHTML = `
                    <button type="button" class="slider-control prev" onclick="prevSlide()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="slider-control next" onclick="nextSlide()">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                `;
                document.querySelector('.product-image-slider').appendChild(sliderControls);
            }

            // Initialize with first slide
            if (slides.length > 0) {
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

        // Global slider functions
        window.currentSlideIndex = 0;

        function showSlide(index) {
            const slides = document.querySelectorAll('.product-image-slider .slide');
            const thumbnails = document.querySelectorAll('.thumbnail-nav .thumbnail');
            
            if (slides.length === 0) return;
            
            // Update current index
            window.currentSlideIndex = index;
            
            // Hide all slides
            slides.forEach(slide => {
                slide.classList.remove('active');
            });
            
            // Show selected slide
            slides[index].classList.add('active');
            
            // Update thumbnails
            thumbnails.forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            const slides = document.querySelectorAll('.product-image-slider .slide');
            const nextIndex = (window.currentSlideIndex + 1) % slides.length;
            showSlide(nextIndex);
        }

        function prevSlide() {
            const slides = document.querySelectorAll('.product-image-slider .slide');
            const prevIndex = (window.currentSlideIndex - 1 + slides.length) % slides.length;
            showSlide(prevIndex);
        }
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

    <script>
        /**
         * Open the lightbox modal with the specified image
         * @param {number} index - Index of the image to show
         */
        window.openLightbox = function(index) {
            window.selectedImageIndex = index;
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        };
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
