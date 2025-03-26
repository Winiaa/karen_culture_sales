@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Reviews</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Search by customer or product...">
                </div>
                <div class="col-md-2">
                    <label for="rating" class="form-label">Rating</label>
                    <select class="form-select" id="rating" name="rating">
                        <option value="">All Ratings</option>
                        @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} {{ Str::plural('Star', $i) }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Highest Rating</option>
                        <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Lowest Rating</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-karen w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ Storage::url($review->product->image) }}" 
                                         alt="{{ $review->product->title }}" 
                                         class="rounded me-2" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <a href="{{ route('products.show', $review->product) }}" 
                                           class="text-decoration-none">
                                            {{ $review->product->title }}
                                        </a>
                                        <div class="text-muted small">
                                            {{ $review->product->category->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $review->user->profile_picture_url }}" 
                                         alt="{{ $review->user->name }}" 
                                         class="rounded-circle me-2" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                    <div>
                                        <div>{{ $review->user->name }}</div>
                                        <small class="text-muted">{{ $review->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted">{{ $review->rating }}/5</small>
                            </td>
                            <td>
                                <div style="max-width: 300px;">
                                    <div class="mb-1">{{ Str::limit($review->title, 50) }}</div>
                                    <small class="text-muted">{{ Str::limit($review->content, 100) }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $review->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $review->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewReview{{ $review->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($review->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="updateReviewStatus({{ $review->id }}, 'approved')">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="updateReviewStatus({{ $review->id }}, 'rejected')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteReview({{ $review->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- View Review Modal -->
                                <div class="modal fade" id="viewReview{{ $review->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Review Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-4">
                                                    <h6>Product Information</h6>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ Storage::url($review->product->image) }}" 
                                                             alt="{{ $review->product->title }}" 
                                                             class="rounded me-2" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                        <div>
                                                            <div class="fw-bold">{{ $review->product->title }}</div>
                                                            <div class="text-muted">{{ $review->product->category->name }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <h6>Customer Information</h6>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <img src="{{ $review->user->profile_picture_url }}" 
                                                             alt="{{ $review->user->name }}" 
                                                             class="rounded-circle me-3" 
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                        <div>
                                                            <p class="mb-1 fw-bold">{{ $review->user->name }}</p>
                                                            <p class="mb-1 text-muted">{{ $review->user->email }}</p>
                                                            <p class="mb-0 small">Joined: {{ $review->user->created_at->format('M d, Y') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <h6>Review</h6>
                                                    <div class="text-warning mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="{{ $i <= $review->rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                                                        @endfor
                                                        <span class="text-muted ms-2">{{ $review->rating }}/5</span>
                                                    </div>
                                                    <h5>{{ $review->title }}</h5>
                                                    <p class="text-muted">{{ $review->content }}</p>
                                                </div>
                                                @if($review->images)
                                                <div class="mb-4">
                                                    <h6>Review Images</h6>
                                                    <div class="row g-2">
                                                        @foreach(json_decode($review->images) as $image)
                                                        <div class="col-4">
                                                            <img src="{{ Storage::url($image) }}" 
                                                                 alt="Review image" 
                                                                 class="img-fluid rounded">
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endif
                                                <div>
                                                    <h6>Review Status</h6>
                                                    <p class="mb-0">
                                                        Current Status: 
                                                        <span class="badge bg-{{ $review->status === 'approved' ? 'success' : ($review->status === 'rejected' ? 'danger' : 'warning') }}">
                                                            {{ ucfirst($review->status) }}
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                @if($review->status === 'pending')
                                                <button type="button" class="btn btn-success" 
                                                        onclick="updateReviewStatus({{ $review->id }}, 'approved')" 
                                                        data-bs-dismiss="modal">
                                                    Approve Review
                                                </button>
                                                <button type="button" class="btn btn-danger" 
                                                        onclick="updateReviewStatus({{ $review->id }}, 'rejected')" 
                                                        data-bs-dismiss="modal">
                                                    Reject Review
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">No reviews found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $reviews->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateReviewStatus(reviewId, status) {
    fetch(`/admin/reviews/${reviewId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            toastr.success(`Review ${status} successfully`);
            window.location.reload();
        } else {
            toastr.error(data.message || `Failed to ${status} review`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error(`An error occurred while ${status}ing the review`);
    });
}

function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
        fetch(`/admin/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Review deleted successfully');
                window.location.reload();
            } else {
                toastr.error(data.message || 'Failed to delete review');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An error occurred while deleting the review');
        });
    }
}
</script>
@endpush
@endsection 