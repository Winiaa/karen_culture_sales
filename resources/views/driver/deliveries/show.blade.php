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
                <div class="col-md-6 mb-3 mb-md-0">
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

                    @if($delivery->delivery_status === 'failed')
                        <div class="alert alert-warning mt-3 mb-0">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <p class="mb-0">{{ $delivery->delivery_notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
                        <!-- Status update buttons based on current status -->
                        @if($delivery->delivery_status === 'assigned')
                            <form action="{{ route('driver.deliveries.pickup', $delivery) }}" method="POST" class="d-grid d-md-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-info w-100 w-md-auto">
                                    <i class="fas fa-box me-2"></i>Mark as Picked Up
                                </button>
                            </form>
                        @elseif($delivery->delivery_status === 'picked_up')
                            <form action="{{ route('driver.deliveries.out-for-delivery', $delivery) }}" method="POST" class="d-grid d-md-block">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                    <i class="fas fa-truck me-2"></i>Mark as Out for Delivery
                                </button>
                            </form>
                        @elseif($delivery->delivery_status === 'out_for_delivery')
                            <div class="d-grid d-md-flex gap-2 justify-content-md-end">
                                <button type="button" class="btn btn-success w-100 w-md-auto order-md-2" data-bs-toggle="modal" data-bs-target="#deliveredModal">
                                    <i class="fas fa-check me-2"></i>Mark as Delivered
                                </button>
                                <button type="button" class="btn btn-danger w-100 w-md-auto order-md-1" data-bs-toggle="modal" data-bs-target="#failedModal">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Mark as Failed
                                </button>
                            </div>
                        @endif
                    </div>
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
                    <h5 class="modal-title" id="deliveredModalLabel">Confirm Delivery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Signature Section -->
                    <div class="mb-3">
                        <label class="form-label">Recipient's Signature</label>
                        <div class="border rounded p-3 bg-light">
                            <div class="text-center mb-2">
                                <p class="mb-1">Please have the recipient sign below:</p>
                                <button type="button" id="clearSignature" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eraser"></i> Clear Signature
                                </button>
                            </div>
                            <div class="signature-pad-container">
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

                    <!-- Delivery Notes -->
                    <div class="mb-0">
                        <label for="delivery_notes" class="form-label">Delivery Notes (optional)</label>
                        <textarea class="form-control" id="delivery_notes" name="delivery_notes" rows="3" 
                            placeholder="Add any notes about the delivery..."></textarea>
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

<!-- Payment Section -->
@if($delivery->order->payment && $delivery->order->payment->payment_method === 'cash_on_delivery')
<div class="card mb-4">
    <div class="card-header bg-light">
        <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
                <h5 class="mb-0">
                    <i class="fas fa-money-bill me-2"></i>Payment Details
                </h5>
            </div>
            @if($delivery->payment_status !== 'received' && $delivery->delivery_status === 'delivered')
                <div class="col-md-6">
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-success w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#cashPaymentModal">
                            <i class="fas fa-money-bill me-2"></i>Mark Cash as Received
                        </button>
                        <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#transferPaymentModal">
                            <i class="fas fa-university me-2"></i>Record Bank Transfer
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        <!-- Payment Status Alert -->
        <div class="alert {{ $delivery->payment_status === 'received' ? 'alert-success' : 'alert-warning' }} mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3 fs-3">
                    @if($delivery->payment_status === 'received')
                        <i class="fas fa-check-circle"></i>
                    @else
                        <i class="fas fa-exclamation-circle"></i>
                    @endif
                </div>
                <div>
                    <h5 class="alert-heading mb-1">
                        @if($delivery->payment_status === 'received')
                            Payment Collected
                        @else
                            Payment to Collect
                        @endif
                    </h5>
                    <p class="mb-0 fs-4">฿{{ number_format($delivery->order->total_amount, 2) }}</p>
                    @if($delivery->payment_status === 'received')
                        <small class="text-success">
                            @if($delivery->transfer_proof)
                                Paid by bank transfer
                            @else
                                Paid in cash
                            @endif
                            on {{ \Carbon\Carbon::parse($delivery->payment_received_at)->format('d M Y, h:i A') }}
                        </small>
                    @endif
                </div>
            </div>
        </div>

        @if($delivery->payment_status === 'received')
            <!-- Payment Details -->
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <!-- Payment Method -->
                    <div class="mb-4">
                        <h6 class="mb-2">Payment Method:</h6>
                        <div class="d-flex align-items-center">
                            @if($delivery->transfer_proof)
                                <span class="badge bg-primary">
                                    <i class="fas fa-university me-1"></i> Bank Transfer
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fas fa-money-bill me-1"></i> Cash
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Notes -->
                    @if($delivery->payment_notes)
                        <div class="mb-4">
                            <h6 class="mb-2">Payment Notes:</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $delivery->payment_notes }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Transfer Proof (if exists) -->
                @if($delivery->transfer_proof)
                    <div class="col-12 col-md-6">
                        <h6 class="mb-2">Transfer Slip:</h6>
                        <a href="{{ Storage::url($delivery->transfer_proof) }}" 
                           target="_blank" 
                           class="d-block">
                            <img src="{{ Storage::url($delivery->transfer_proof) }}" 
                                 alt="Transfer Proof" 
                                 class="img-fluid rounded shadow-sm"
                                 style="max-height: 200px; width: auto;">
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endif

<!-- Cash Payment Modal -->
<div class="modal fade" id="cashPaymentModal" tabindex="-1" aria-labelledby="cashPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('driver.deliveries.payment.update', $delivery) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cashPaymentModalLabel">Confirm Cash Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Payment Amount</h5>
                                <p class="mb-0">Please confirm you have received <strong>฿{{ number_format($delivery->order->total_amount, 2) }}</strong> in cash.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Collection Confirmation -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="cashConfirmation" name="cash_collected" required>
                            <label class="form-check-label" for="cashConfirmation">
                                I confirm that I have collected the full amount in cash
                            </label>
                        </div>
                    </div>

                    <!-- Payment Notes -->
                    <div class="mb-0">
                        <label for="cash_payment_notes" class="form-label">Payment Notes (Optional)</label>
                        <textarea class="form-control" id="cash_payment_notes" name="payment_notes" rows="3" 
                            placeholder="Add any notes about the cash payment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-secondary w-100 w-md-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success w-100 w-md-auto">
                        <i class="fas fa-check me-2"></i>Confirm Cash Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Payment Modal -->
<div class="modal fade" id="transferPaymentModal" tabindex="-1" aria-labelledby="transferPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('driver.deliveries.payment.update', $delivery) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="transferPaymentModalLabel">Record Bank Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Payment Amount</h5>
                                <p class="mb-0">Transfer amount should be <strong>฿{{ number_format($delivery->order->total_amount, 2) }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Proof Upload -->
                    <div class="mb-3">
                        <label class="form-label">Upload Transfer Slip</label>
                        <input type="file" class="form-control" name="transfer_proof" accept="image/*" required>
                        <div class="form-text">Take a photo or upload the transfer slip</div>
                    </div>

                    <!-- Payment Notes -->
                    <div class="mb-0">
                        <label for="transfer_payment_notes" class="form-label">Payment Notes (Optional)</label>
                        <textarea class="form-control" id="transfer_payment_notes" name="payment_notes" rows="3" 
                            placeholder="Add any notes about the transfer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-secondary w-100 w-md-auto" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary w-100 w-md-auto">
                        <i class="fas fa-check me-2"></i>Confirm Transfer
                    </button>
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
        touch-action: none;
    }
    
    .signature-pad {
        width: 100%;
        height: 200px;
        background-color: #fff;
        border-radius: 4px;
        cursor: crosshair;
        touch-action: none;
        border: 1px solid #e0e0e0;
    }

    .signature-pad canvas {
        border: 1px solid #e0e0e0;
        border-radius: 4px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    // IIFE to avoid global scope pollution
    (function() {
        // Configuration namespace
        const Config = {
            signature: {
                pad: {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    velocityFilterWeight: 0.7,
                    minWidth: 1,
                    maxWidth: 2.5,
                    throttle: 16,
                    minDistance: 1
                },
                canvas: {
                    height: 200,
                    maxWidth: 800,
                    imageQuality: 0.8
                }
            },
            selectors: {
                modal: '#deliveredModal',
                canvas: '#signaturePad',
                form: '#deliveryForm',
                submitBtn: '#confirmDeliveryBtn',
                clearBtn: '#clearSignature',
                statusIndicator: '#signature-status',
                signatureInput: '#signature_data'
            }
        };

        // State management
        const State = {
            signaturePad: null,
            isProcessing: false
        };

        // UI utilities
        const UI = {
            elements: {},
            
            // Initialize element references
            init() {
                Object.entries(Config.selectors).forEach(([key, selector]) => {
                    this.elements[key] = document.querySelector(selector);
                });
            },
            
            // Loading state management
            setLoading(isLoading) {
                if (!this.elements.submitBtn) return;
                
                State.isProcessing = isLoading;
                this.elements.submitBtn.disabled = isLoading;
                this.elements.submitBtn.innerHTML = isLoading ? 
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...' : 
                    '<i class="fas fa-check"></i> Confirm Delivery';
            },

            // Update signature status
            updateSignatureStatus(show) {
                if (this.elements.statusIndicator) {
                    this.elements.statusIndicator.style.display = show ? "block" : "none";
                }
            },

            // Show error message
            showError(message) {
                alert(message);
            }
        };

        // Canvas utilities
        const CanvasUtils = {
            setDimensions(canvas, container) {
                const ratio = window.devicePixelRatio || 1;
                const width = container.clientWidth;
                const height = Config.signature.canvas.height;

                canvas.width = width * ratio;
                canvas.height = height * ratio;
                canvas.style.width = width + 'px';
                canvas.style.height = height + 'px';
                canvas.getContext('2d').scale(ratio, ratio);
            },

            preventTouchEvents(element) {
                ['touchstart', 'touchmove', 'touchend'].forEach(eventName => {
                    element.addEventListener(eventName, e => e.preventDefault(), { passive: false });
                });
            },

            async compressImage(dataUrl) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let { width, height } = img;
                        const maxWidth = Config.signature.canvas.maxWidth;

                        if (width > maxWidth) {
                            height = (maxWidth * height) / width;
                            width = maxWidth;
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        resolve(canvas.toDataURL('image/jpeg', Config.signature.canvas.imageQuality));
                    };
                    img.onerror = reject;
                    img.src = dataUrl;
                });
            }
        };

        // Form handling
        const FormHandler = {
            async handleSubmit(e) {
                e.preventDefault();
                
                if (State.isProcessing) return;
                
                if (!State.signaturePad || State.signaturePad.isEmpty()) {
                    UI.showError('Please provide a signature before submitting.');
                    return;
                }

                try {
                    UI.setLoading(true);
                    
                    const originalSignature = State.signaturePad.toDataURL('image/png');
                    const compressedSignature = await CanvasUtils.compressImage(originalSignature);
                    
                    UI.elements.signatureInput.value = compressedSignature;
                    UI.elements.form.submit();
                } catch (error) {
                    console.error('Error processing signature:', error);
                    UI.setLoading(false);
                    UI.showError('Error saving signature. Please try again.');
                }
            }
        };

        // Signature pad management
        const SignaturePadManager = {
            init() {
                if (!UI.elements.canvas) {
                    console.error('Signature pad canvas not found');
                    return;
                }

                const container = UI.elements.canvas.parentElement;
                CanvasUtils.setDimensions(UI.elements.canvas, container);
                
                State.signaturePad = new SignaturePad(UI.elements.canvas, Config.signature.pad);
                
                this.setupEventListeners();
            },

            setupEventListeners() {
                // Signature events
                State.signaturePad.addEventListener("beginStroke", () => UI.updateSignatureStatus(false));
                State.signaturePad.addEventListener("endStroke", () => UI.updateSignatureStatus(true));
                
                // Clear button
                if (UI.elements.clearBtn) {
                    UI.elements.clearBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        State.signaturePad.clear();
                        UI.updateSignatureStatus(false);
                    });
                }
                
                // Prevent scrolling while signing
                CanvasUtils.preventTouchEvents(UI.elements.canvas);
                
                // Window resize
                window.addEventListener('resize', () => {
                    if (UI.elements.modal.classList.contains('show') && State.signaturePad) {
                        const data = State.signaturePad.toData();
                        CanvasUtils.setDimensions(UI.elements.canvas, UI.elements.canvas.parentElement);
                        State.signaturePad.clear();
                        if (data?.length) {
                            State.signaturePad.fromData(data);
                        }
                    }
                });
            },

            clear() {
                if (State.signaturePad) {
                    State.signaturePad.clear();
                    State.signaturePad = null;
                }
            }
        };

        // Initialize everything when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            UI.init();

            if (UI.elements.modal) {
                UI.elements.modal.addEventListener('shown.bs.modal', () => {
                    UI.setLoading(false);
                    SignaturePadManager.init();
                });

                UI.elements.modal.addEventListener('hidden.bs.modal', () => {
                    SignaturePadManager.clear();
                });
            }

            if (UI.elements.form) {
                UI.elements.form.addEventListener('submit', FormHandler.handleSubmit);
            }
        });
    })();
</script>
@endpush 