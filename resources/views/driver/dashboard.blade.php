@extends('layouts.driver')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Driver Dashboard</h2>
        <div>
            @if($driver->is_active)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Inactive</span>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Total Deliveries</h6>
                            <h3 class="mb-0">{{ $stats['total_deliveries'] }}</h3>
                        </div>
                        <div class="bg-light p-3 rounded-circle">
                            <i class="fas fa-truck fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Active Deliveries</h6>
                            <h3 class="mb-0">{{ $stats['active_deliveries'] }}</h3>
                        </div>
                        <div class="bg-light p-3 rounded-circle">
                            <i class="fas fa-truck-loading fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Completed Today</h6>
                            <h3 class="mb-0">{{ $stats['completed_today'] }}</h3>
                        </div>
                        <div class="bg-light p-3 rounded-circle">
                            <i class="fas fa-calendar-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Rating</h6>
                            <h3 class="mb-0">{{ number_format($stats['rating'], 1) }}</h3>
                        </div>
                        <div class="bg-light p-3 rounded-circle">
                            <i class="fas fa-star fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Deliveries -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Active Deliveries</h4>
            <a href="{{ route('driver.deliveries.assigned') }}" class="btn btn-sm btn-outline-primary">
                View All
            </a>
        </div>
        
        @if($activeDeliveries->count() > 0)
            <div class="list-group">
                @foreach($activeDeliveries as $delivery)
                    <div class="list-group-item p-3 delivery-card {{ $delivery->delivery_status }}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-0">Order #{{ $delivery->order->id }}</h6>
                                <small class="text-muted">{{ $delivery->order->created_at->format('M d, Y') }}</small>
                            </div>
                            <div class="col-md-3">
                                <span class="badge bg-{{ 
                                    $delivery->delivery_status === 'assigned' ? 'warning' : 
                                    ($delivery->delivery_status === 'picked_up' ? 'info' : 
                                    ($delivery->delivery_status === 'out_for_delivery' ? 'primary' : 'secondary')) 
                                }}">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                                </span>
                            </div>
                            <div class="col-md-4">
                                <small class="d-block">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> 
                                    {{ $delivery->recipient_address }}
                                </small>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="{{ route('driver.deliveries.show', $delivery) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                No active deliveries at the moment.
            </div>
        @endif
    </div>

    <!-- Recent Completed Deliveries -->
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Recent Completed Deliveries</h4>
            <a href="{{ route('driver.deliveries.completed') }}" class="btn btn-sm btn-outline-success">
                View All
            </a>
        </div>
        
        @if($completedDeliveries->count() > 0)
            <div class="list-group">
                @foreach($completedDeliveries as $delivery)
                    <div class="list-group-item p-3 delivery-card delivered">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-0">Order #{{ $delivery->order->id }}</h6>
                                <small class="text-muted">{{ $delivery->delivered_at->format('M d, Y H:i') }}</small>
                            </div>
                            <div class="col-md-3">
                                <span class="badge bg-success">Delivered</span>
                                @if($delivery->is_confirmed_by_customer)
                                    <span class="badge bg-info">Confirmed</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <small class="d-block">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> 
                                    {{ $delivery->recipient_address }}
                                </small>
                            </div>
                            <div class="col-md-2 text-end">
                                <a href="{{ route('driver.deliveries.show', $delivery) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info">
                No completed deliveries yet.
            </div>
        @endif
    </div>
</div>
@endsection 