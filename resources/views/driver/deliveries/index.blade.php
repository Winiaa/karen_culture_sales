@extends('layouts.driver')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $title }}</h2>
        <div>
            <span class="badge bg-primary">Total: {{ $deliveries->total() }}</span>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search by order number...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Delivery Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="picked_up" {{ request('status') == 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                        <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <select class="form-select" id="date" name="date">
                        <option value="">All Dates</option>
                        <option value="today" {{ request('date') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('date') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="this_week" {{ request('date') == 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('date') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary px-4 w-100">
                        <i class="fas fa-filter me-2"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Deliveries List -->
    @if($deliveries->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Address</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveries as $delivery)
                        <tr>
                            <td>
                                <strong>{{ $delivery->order->id }}</strong>
                                @if($delivery->order->payment && $delivery->order->payment->payment_method === 'cash_on_delivery')
                                    <span class="badge bg-info ms-1">COD</span>
                                @endif
                            </td>
                            <td>{{ $delivery->recipient_name }}</td>
                            <td class="text-truncate" style="max-width: 200px;">
                                {{ $delivery->recipient_address }}
                            </td>
                            <td>
                                <span class="badge bg-{{ 
                                    $delivery->delivery_status === 'assigned' ? 'warning' : 
                                    ($delivery->delivery_status === 'picked_up' ? 'info' : 
                                    ($delivery->delivery_status === 'out_for_delivery' ? 'primary' : 
                                    ($delivery->delivery_status === 'delivered' ? 'success' : 
                                    ($delivery->delivery_status === 'failed' ? 'danger' : 'secondary')))) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                                </span>
                                @if($delivery->is_confirmed_by_customer)
                                    <span class="badge bg-info ms-1">Confirmed</span>
                                @endif
                            </td>
                            <td>
                                @if($delivery->delivered_at)
                                    {{ $delivery->delivered_at->format('M d, Y H:i') }}
                                @else
                                    {{ $delivery->updated_at->format('M d, Y H:i') }}
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('driver.deliveries.show', $delivery) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $deliveries->withQueryString()->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> No deliveries found matching your criteria.
        </div>
    @endif
</div>
@endsection 