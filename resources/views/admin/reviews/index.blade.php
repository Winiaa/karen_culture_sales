@extends('layouts.admin')

@section('title', 'Reviews')
@section('subtitle', 'Manage customer reviews')

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" 
                           placeholder="Search by customer or product...">
                </div>
                <div class="col-md-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select class="form-select" id="rating" name="rating">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-karen w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Reviews List</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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
                                    @if($review->product->image)
                                    <img src="{{ Storage::url($review->product->image) }}" alt="{{ $review->product->title }}" 
                                         class="rounded" width="40" height="40" style="object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-box text-muted"></i>
                                    </div>
                                    @endif
                                    <div class="ms-3">
                                        <h6 class="mb-0">{{ $review->product->title }}</h6>
                                        <small class="text-muted">ID: {{ $review->product->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>{{ $review->user->name }}</div>
                                <small class="text-muted">{{ $review->user->email }}</small>
                            </td>
                            <td>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 300px;">
                                    {{ $review->comment }}
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
                                            data-bs-toggle="modal" data-bs-target="#reviewModal{{ $review->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($review->status === 'pending')
                                    <button type="button" class="btn btn-sm btn-outline-success approve-review" 
                                            data-review-id="{{ $review->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger reject-review" 
                                            data-review-id="{{ $review->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-delete-url="{{ route('admin.reviews.destroy', $review) }}"
                                            data-type="review">
                                        <i class="fas fa-trash"></i>
                                    </button>
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

<!-- Review Detail Modals -->
@foreach($reviews as $review)
<div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            @if($review->product->image)
                            <img src="{{ Storage::url($review->product->image) }}" alt="{{ $review->product->title }}" 
                                 class="img-fluid rounded">
                            @else
                            <div class="bg-light rounded p-4 d-flex align-items-center justify-content-center">
                                <i class="fas fa-box fa-3x text-muted"></i>
                            </div>
                            @endif
                            <h6 class="mt-2">{{ $review->product->title }}</h6>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <h6>Customer Information</h6>
                            <p class="mb-1">{{ $review->user->name }}</p>
                            <p class="text-muted mb-0">{{ $review->user->email }}</p>
                        </div>
                        <div class="mb-3">
                            <h6>Rating</h6>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6>Review</h6>
                            <p class="mb-0">{{ $review->comment }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Include all confirmation modals -->
<x-approve-confirmation-modal />
<x-reject-confirmation-modal />
<x-delete-confirmation-modal />

@endsection

@push('styles')
<style>
    .btn-group .btn {
        margin-right: 2px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    .badge {
        padding: 0.5em 0.75em;
    }
</style>
@endpush 