@extends('layouts.admin')

@section('title', 'Orders')
@section('subtitle', 'Manage all orders')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note:</strong> Credit card orders will only appear here after successful payment. Cash on delivery orders are visible immediately.
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Orders List</h4>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by customer name or email...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Order Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">All Payment Statuses</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-karen w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                <div>{{ $order->user->name }}</div>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @foreach($order->orderItems->take(3) as $item)
                                    <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->title }}" class="rounded" style="width: 30px; height: 30px; object-fit: cover; margin-right: -10px;">
                                    @endforeach
                                    @if($order->orderItems->count() > 3)
                                    <span class="ms-3 badge bg-secondary">+{{ $order->orderItems->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td>@baht($order->total_amount)</td>
                            <td>
                                <div class="fw-bold text-secondary text-uppercase small">
                                    Payment Method
                                </div>
                                <div>{{ $order->payment->payment_method === 'stripe' ? 'Credit Card' : 'Cash on Delivery' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->order_status === 'delivered' ? 'success' : ($order->order_status === 'cancelled' ? 'danger' : 'primary') }}">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $order->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-order-btn" data-modal-target="#updateStatus{{ $order->id }}">
                                    <i class="fas fa-edit"></i>
                                    </button>
                                </div>

                                <!-- Update Status Modal -->
                                <div class="modal fade" id="updateStatus{{ $order->id }}" tabindex="-1" aria-labelledby="updateStatusLabel{{ $order->id }}" aria-hidden="true" style="z-index: 1050;">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Update Order Status</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="order_status" class="form-label">Order Status</label>
                                                        <select class="form-select" id="order_status" name="order_status" required>
                                                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                            <option value="shipped" {{ $order->order_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                            <option value="delivered" {{ $order->order_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="payment_status" class="form-label">Payment Status</label>
                                                        <select class="form-select" id="payment_status" name="payment_status" required>
                                                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="completed" {{ $order->payment_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-karen">Update Status</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">No orders found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
/* Fix modal flickering */
.modal.fade {
    -webkit-transition: none;
    -moz-transition: none;
    -ms-transition: none;
    -o-transition: none;
    transition: none;
}

.modal.fade .modal-dialog {
    -webkit-transform: none;
    -ms-transform: none;
    -o-transform: none;
    transform: none;
}

/* Pagination customization */
.page-item.active .page-link {
    background-color: #8D6E63;
    border-color: #8D6E63;
}

.page-link {
    padding: 0.375rem 0.75rem;
}

.page-link:hover {
    color: #8D6E63;
    border-color: #8D6E63;
}

.page-link .fa-sm {
    font-size: 0.875rem;
    line-height: 1.25;
}
</style>

<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection

@push('scripts')
<script>
// Fix for modal issues
window.addEventListener('DOMContentLoaded', function() {
    // Move all modals to the end of the body
    document.querySelectorAll('.modal').forEach(function(modal) {
        // Remove the modal from its current position and append to body
        document.body.appendChild(modal);
    });
    
    // Fix for modal backdrop issues
    document.querySelectorAll('.edit-order-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            // Prevent default behavior
            e.preventDefault();
            e.stopPropagation();
            
            // Get the modal ID
            const modalId = this.getAttribute('data-modal-target');
            if (!modalId) return;
            
            // Get the modal element
            const modal = document.querySelector(modalId);
            if (!modal) return;
            
            // Ensure the modal is properly positioned
            modal.style.position = 'fixed';
            modal.style.zIndex = '1050';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.right = '0';
            modal.style.bottom = '0';
            
            // Display modal and add backdrop
            setTimeout(function() {
                // Ensure any stale backdrops are removed first
                document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                    backdrop.remove();
                });
                
                // Create fresh backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.style.position = 'fixed';
                backdrop.style.top = '0';
                backdrop.style.left = '0';
                backdrop.style.right = '0'; 
                backdrop.style.bottom = '0';
                backdrop.style.zIndex = '1040';
                backdrop.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                document.body.appendChild(backdrop);
                
                // Show modal
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
            }, 10);
        });
    });
    
    // Ensure close buttons work properly
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(closeBtn) {
        closeBtn.addEventListener('click', function() {
            // Find the modal this button belongs to
            const modal = this.closest('.modal');
            if (modal) {
                // Hide modal manually
                modal.classList.remove('show');
                modal.style.display = 'none';
                
                // Remove backdrop
                document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                    backdrop.remove();
                });
                
                // Reset body styles
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        });
    });
    
    // Add Escape key listener to close any open modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            const openModals = document.querySelectorAll('.modal.show');
            if (openModals.length > 0) {
                openModals.forEach(function(modal) {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                });
                
                document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                    backdrop.remove();
                });
                
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        }
    });
    
    // Add click handler on modal backdrops
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-backdrop') || e.target.classList.contains('modal')) {
            document.querySelectorAll('.modal.show').forEach(function(modal) {
                modal.classList.remove('show');
                modal.style.display = 'none';
            });
            
            document.querySelectorAll('.modal-backdrop').forEach(function(backdrop) {
                backdrop.remove();
            });
            
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }
    });
});
</script>
@endpush

@endsection 