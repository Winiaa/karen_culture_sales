@extends('layouts.driver')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Delivery #{{ $delivery->id }}</h2>
            <p class="text-muted mb-0">Order #{{ $delivery->order->id }}</p>
        </div>
        <div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Status and Actions Card -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Delivery Status</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ 
                            $delivery->delivery_status === 'assigned' ? 'warning' : 
                            ($delivery->delivery_status === 'picked_up' ? 'info' : 
                            ($delivery->delivery_status === 'out_for_delivery' ? 'primary' : 
                            ($delivery->delivery_status === 'delivered' ? 'success' : 
                            ($delivery->delivery_status === 'failed' ? 'danger' : 'secondary')))) 
                        }} p-2 me-3" style="font-size: 1rem;">
                            {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                        </span>
                        @if($delivery->is_confirmed_by_customer)
                            <span class="badge bg-success">Confirmed by Customer</span>
                        @endif
                    </div>
                    
                    @if($delivery->delivered_at)
                        <p class="text-muted mt-2 mb-0">
                            <i class="far fa-clock"></i> Delivered on: {{ $delivery->delivered_at->format('M d, Y h:i A') }}
                        </p>
                    @endif
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <!-- Status update buttons based on current status -->
                    @if($delivery->delivery_status === 'assigned')
                        <form action="{{ route('driver.deliveries.pickup', $delivery) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-box"></i> Mark as Picked Up
                            </button>
                        </form>
                    @elseif($delivery->delivery_status === 'picked_up')
                        <form action="{{ route('driver.deliveries.out-for-delivery', $delivery) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-truck"></i> Mark as Out for Delivery
                            </button>
                        </form>
                    @elseif($delivery->delivery_status === 'out_for_delivery')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#deliveredModal">
                            <i class="fas fa-check"></i> Mark as Delivered
                        </button>
                        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#failedModal">
                            <i class="fas fa-exclamation-triangle"></i> Mark as Failed
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery Confirmation Details (if already delivered) -->
    @if($delivery->delivery_status === 'delivered')
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Delivery Confirmation Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @if($delivery->delivery_photo)
                <div class="col-md-6 mb-3 mb-md-0">
                    <h6 class="text-muted mb-2">Recipient's Signature</h6>
                    <a href="{{ asset('storage/' . $delivery->delivery_photo) }}" target="_blank" class="d-block">
                        <img src="{{ asset('storage/' . $delivery->delivery_photo) }}" alt="Recipient's Signature" 
                            class="img-fluid img-thumbnail" style="max-height: 300px; background-color: #fff;">
                    </a>
                    <small class="text-muted d-block mt-1">Signed on: {{ $delivery->delivered_at->format('M d, Y h:i A') }}</small>
                </div>
                @endif
                
                @if($delivery->delivery_notes)
                <div class="col-md-{{ $delivery->delivery_photo ? '6' : '12' }}">
                    <h6 class="text-muted mb-2">Your Delivery Notes</h6>
                    <div class="border rounded bg-light p-3">
                        <div>{{ $delivery->delivery_notes }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Delivery Details -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Delivery Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted d-block">Recipient Name:</label>
                        <div class="fs-5">{{ $delivery->recipient_name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block">Contact Number:</label>
                        <div class="fs-5">
                            <a href="tel:{{ $delivery->recipient_phone }}" class="text-decoration-none">
                                {{ $delivery->recipient_phone }}
                            </a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block">Delivery Address:</label>
                        <div class="fs-5">{{ $delivery->recipient_address }}</div>
                        <a href="https://maps.google.com/?q={{ urlencode($delivery->recipient_address) }}" 
                            target="_blank" class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="fas fa-map-marker-alt"></i> View on Map
                        </a>
                    </div>
                    
                    @if($delivery->notes)
                        <div class="mb-0">
                            <label class="text-muted d-block">Special Instructions:</label>
                            <div class="alert alert-info mb-0">{{ $delivery->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Order Details -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted d-block">Order Number:</label>
                        <div class="fs-5">#{{ $delivery->order->id }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block">Order Date:</label>
                        <div class="fs-5">{{ $delivery->order->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted d-block">Payment Method:</label>
                        <div class="fs-5">
                            @if($delivery->order->payment)
                                {{ ucfirst(str_replace('_', ' ', $delivery->order->payment->payment_method)) }}
                                @if($delivery->order->payment->payment_method === 'cash_on_delivery')
                                    <span class="badge bg-warning">Collect Payment</span>
                                @endif
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted d-block">Total Amount:</label>
                        <div class="fs-4 fw-bold">{{ number_format($delivery->order->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Order Items</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($delivery->order->orderItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                alt="{{ $item->product->name }}" class="me-3"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="fas fa-box text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $item->product ? $item->product->name : 'Unknown Product' }}</h6>
                                            @if($item->product && $item->product->sku)
                                                <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->subtotal / $item->quantity, 2) }}</td>
                                <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-end">{{ number_format($delivery->order->total_amount, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delivered Modal -->
<div class="modal fade" id="deliveredModal" tabindex="-1" aria-labelledby="deliveredModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('driver.deliveries.deliver', $delivery) }}" method="POST" id="deliveryForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="deliveredModalLabel">Mark as Delivered</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($delivery->order->payment && $delivery->order->payment->payment_method === 'cash_on_delivery')
                    <div class="alert alert-warning mb-4">
                        <div class="d-flex align-items-center">
                            <div class="me-3 fs-3">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading mb-1">Cash Payment Collection</h5>
                                <p class="mb-0">This is a Cash on Delivery order. Please collect <strong>{{ number_format($delivery->order->total_amount, 2) }}</strong> from the customer before marking as delivered.</p>
                                <small class="d-block mt-2">Payment will be automatically marked as completed when you confirm delivery.</small>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label">Signature Confirmation</label>
                        <div class="border rounded p-3 bg-light">
                            <div class="text-center mb-2">
                                <p class="mb-1">Please have the recipient sign below:</p>
                                <button type="button" id="clearSignature" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eraser"></i> Clear Signature
                                </button>
                            </div>
                            <div class="signature-pad-container" id="signatureContainer">
                                <canvas id="signaturePad" class="signature-pad"></canvas>
                            </div>
                            <input type="hidden" id="signature_data" name="signature_data">
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Use your finger or mouse to sign</small>
                                <small id="signature-status" class="text-success" style="display: none;">
                                    <i class="fas fa-check-circle"></i> Signature captured
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="success_delivery_notes" class="form-label">Delivery Notes (optional)</label>
                        <textarea class="form-control" id="success_delivery_notes" name="delivery_notes" rows="3" placeholder="Add any notes about the delivery..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="confirmDeliveryBtn">
                        <i class="fas fa-check"></i> Confirm Delivery
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Failed Delivery Modal -->
<div class="modal fade" id="failedModal" tabindex="-1" aria-labelledby="failedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('driver.deliveries.fail', $delivery) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="failedModalLabel">Mark as Failed Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Please explain why this delivery failed:</p>
                    
                    <div class="mb-0">
                        <label for="failed_delivery_notes" class="form-label">Reason for Failed Delivery</label>
                        <textarea class="form-control" id="failed_delivery_notes" name="delivery_notes" rows="3" 
                            placeholder="Enter the reason why delivery failed..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Mark as Failed</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .signature-pad-container {
        position: relative;
        width: 100%;
        border: 1px solid #ced4da;
        background-color: #fff;
        margin-bottom: 8px;
        touch-action: none; /* Prevent scrolling when signing on touch devices */
    }
    
    .signature-pad {
        width: 100%;
        height: 200px;
        background-color: #fff;
        border-radius: 4px;
        cursor: crosshair;
        touch-action: none; /* Ensure touch events work properly */
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let signaturePad = null;
        
        // Initialize signature pad only when the modal is shown
        const deliveredModal = document.getElementById('deliveredModal');
        if (deliveredModal) {
            deliveredModal.addEventListener('shown.bs.modal', function() {
                initializeSignaturePad();
            });
            
            // Clean up when modal is hidden
            deliveredModal.addEventListener('hidden.bs.modal', function() {
                if (signaturePad) {
                    signaturePad.clear();
                    signaturePad = null;
                }
            });
        }

        function initializeSignaturePad() {
            try {
                const canvas = document.getElementById('signaturePad');
                const statusIndicator = document.getElementById('signature-status');
                const container = document.getElementById('signatureContainer');
                
                if (!canvas || !container) {
                    console.error('Required elements not found');
                    return;
                }
                
                // Set canvas dimensions
                canvas.width = container.clientWidth;
                canvas.height = 200;
                
                // Initialize signature pad
                signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    velocityFilterWeight: 0.7,
                    minWidth: 0.5,
                    maxWidth: 2.5,
                    throttle: 16
                });
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    if (deliveredModal.classList.contains('show') && signaturePad) {
                        const data = signaturePad.toData();
                        canvas.width = container.clientWidth;
                        canvas.height = 200;
                        signaturePad.clear();
                        if (data && data.length > 0) {
                            signaturePad.fromData(data);
                        }
                    }
                });
                
                // Handle signature events
                signaturePad.addEventListener("beginStroke", function() {
                    if (statusIndicator) statusIndicator.style.display = "none";
                });
                
                signaturePad.addEventListener("endStroke", function() {
                    if (statusIndicator) statusIndicator.style.display = "block";
                });
                
                // Clear signature button
                const clearButton = document.getElementById('clearSignature');
                if (clearButton) {
                    clearButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        signaturePad.clear();
                        if (statusIndicator) statusIndicator.style.display = "none";
                    });
                }
                
                // Prevent scrolling while signing
                canvas.addEventListener('touchstart', e => e.preventDefault(), { passive: false });
                canvas.addEventListener('touchmove', e => e.preventDefault(), { passive: false });
                canvas.addEventListener('touchend', e => e.preventDefault(), { passive: false });
                
                // Handle form submission
                const deliveryForm = document.getElementById('deliveryForm');
                if (deliveryForm) {
                    deliveryForm.addEventListener('submit', function(e) {
                        if (signaturePad && !signaturePad.isEmpty()) {
                            try {
                                document.getElementById('signature_data').value = signaturePad.toDataURL('image/png');
                            } catch (err) {
                                console.error('Error capturing signature:', err);
                                if (!confirm('Error capturing signature. Continue without signature?')) {
                                    e.preventDefault();
                                    return false;
                                }
                            }
                        } else if (!confirm('No signature provided. Continue without signature?')) {
                            e.preventDefault();
                            return false;
                        }
                        
                        const confirmBtn = document.getElementById('confirmDeliveryBtn');
                        if (confirmBtn) {
                            confirmBtn.disabled = true;
                            confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                        }
                    });
                }
            } catch (err) {
                console.error('Error setting up signature pad:', err);
            }
        }
    });
</script>
@endpush 