@extends('layouts.admin')

@section('title', 'Sales Report')
@section('subtitle', 'View and analyze sales data')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Sales Report</li>
        </ol>
    </nav>

    <!-- Report Controls -->
    <div class="card mb-4">
        <div class="card-header bg-karen text-dark">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Report Filters</h5>
        </div>
        <div class="card-body sales-report-controls">
            <form action="{{ route('admin.reports.sales') }}" method="GET">
                <div class="row g-3">
                    <!-- Date Range Filter -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">Date Range</label>
                        <select name="date_range" class="form-select">
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="last_week" {{ request('date_range') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="last_month" {{ request('date_range') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                            <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <!-- Custom Date Range -->
                    <div class="col-12 col-md-6 col-lg-3" id="customDateRange" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                        <label class="form-label">Custom Range</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            <span class="input-group-text">to</span>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 col-md-6 col-lg-3 d-flex flex-column justify-content-end">
                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-karen">
                                <i class="fas fa-search me-1"></i><span class="d-none d-sm-inline">Generate</span>
                            </button>
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i><span class="d-none d-sm-inline">Reset</span>
                            </a>
                            <a href="{{ route('admin.reports.sales.export', request()->all()) }}" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i><span class="d-none d-sm-inline">Export</span>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-white-50">Total Revenue</h6>
                            <h2 class="mb-0">@baht($totalRevenue)</h2>
                        </div>
                        <div>
                            <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-white-50">Total Orders</h6>
                            <h2 class="mb-0">{{ $totalOrders }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-white-50">Average Order</h6>
                            <h2 class="mb-0">@baht($averageOrderValue)</h2>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-white-50">Cancellations</h6>
                            <h2 class="mb-0">{{ $orders->where('order_status', 'cancelled')->count() }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-undo fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="card mb-4">
        <div class="card-header bg-karen text-dark">
            <h5 class="mb-0"><i class="fas fa-trophy me-2"></i> Top Selling Products</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th class="text-end">Quantity Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.products.edit', $item['product']) }}">
                                        {{ $item['product']->title }}
                                    </a>
                                </td>
                                <td>{{ $item['product']->category->name }}</td>
                                <td class="text-end">{{ $item['total_quantity'] }}</td>
                                <td class="text-end">@baht($item['total_revenue'])</td>
                                <td class="text-end">@baht($item['product']->final_price)</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No products sold in this period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="card mb-4">
        <div class="card-header bg-karen text-dark">
            <h5 class="mb-0"><i class="fas fa-tags me-2"></i> Category Performance</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Items Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">% of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryPerformance as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $item['category']) }}">
                                        {{ $item['category']->name }}
                                    </a>
                                </td>
                                <td class="text-end">{{ $item['total_quantity'] }}</td>
                                <td class="text-end">@baht($item['total_revenue'])</td>
                                <td class="text-end">
                                    @if($totalRevenue > 0)
                                        {{ round(($item['total_revenue'] / $totalRevenue) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No categories sold in this period</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="card">
        <div class="card-header bg-karen text-dark">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Orders</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name }}</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>{{ $order->orderItems->sum('quantity') }}</td>
                                <td class="text-end">@baht($order->total_amount)</td>
                                <td>
                                    <span class="badge bg-{{ $order->order_status == 'delivered' ? 'success' : ($order->order_status == 'processing' ? 'warning' : ($order->order_status == 'cancelled' ? 'danger' : 'info')) }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->order_status == 'cancelled')
                                        @if($order->payment && $order->payment->payment_method === 'stripe')
                                            <span class="badge bg-info">
                                                Refunded
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Cancelled
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-{{ $order->payment_status == 'completed' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No orders found for the selected criteria</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style type="text/css">
/* Base styles */
.sales-report-controls .btn-group {
    width: 100%;
}

.sales-report-controls .form-label {
    font-weight: 500;
}

/* Mobile styles */
@media (max-width: 576px) {
    .sales-report-controls .btn-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    .sales-report-controls .btn-group > .btn {
        width: 100%;
        border-radius: 0.375rem !important;
    }
    .sales-report-controls .input-group {
        flex-direction: column;
    }
    .sales-report-controls .input-group > * {
        width: 100%;
        margin-top: 0.5rem;
    }
    .sales-report-controls .input-group > :first-child {
        margin-top: 0;
    }

    /* Mobile pagination fixes */
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.25rem;
    }
    
    .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        min-width: 2rem;
        text-align: center;
    }
    
    .page-item {
        margin: 0 1px;
    }
    
    /* Hide some pagination elements on very small screens */
    @media (max-width: 360px) {
        .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.disabled) {
            display: none;
        }
    }
    
    /* Responsive table adjustments */
    .table-responsive {
        margin-bottom: 1rem;
        border-radius: 0.375rem;
    }
}

/* Tablet/iPad specific optimizations */
@media (min-width: 577px) and (max-width: 991.98px) {
    /* Control buttons optimization */
    .sales-report-controls .btn-group {
        display: flex;
        gap: 0.5rem;
    }
    .sales-report-controls .btn-group > .btn {
        flex: 1;
        padding: 0.625rem 0.5rem;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Form controls optimization */
    .sales-report-controls .form-select,
    .sales-report-controls .form-control {
        font-size: 0.875rem;
        padding: 0.5rem;
    }
    
    /* Date range inputs */
    .sales-report-controls .input-group {
        gap: 0.5rem;
    }
    .sales-report-controls .input-group > * {
        min-width: auto;
    }
    .sales-report-controls .input-group-text {
        padding: 0.5rem;
        background: transparent;
        border: none;
    }
    
    /* Stats cards optimization */
    .card-body {
        padding: 1rem;
    }
    .card-body h2 {
        font-size: 1.5rem;
    }
    .card-body .fa-3x {
        font-size: 2em;
    }
    .card-body h6 {
        font-size: 0.75rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

/* Desktop/Laptop styles (restore original) */
@media (min-width: 992px) {
    .sales-report-controls .btn-group > .btn {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
    
    .sales-report-controls .form-select,
    .sales-report-controls .form-control {
        font-size: 1rem;
        padding: 0.5rem 1rem;
    }
    
    .sales-report-controls .input-group-text {
        padding: 0.5rem 1rem;
        background-color: #e9ecef;
        border: 1px solid #ced4da;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .card-body h2 {
        font-size: 2rem;
    }
    
    .card-body .fa-3x {
        font-size: 3em;
    }
    
    .card-body h6 {
        font-size: 0.875rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // Date range toggle functionality
    document.querySelector('select[name="date_range"]').addEventListener('change', function() {
        const customRange = document.getElementById('customDateRange');
        customRange.style.display = this.value === 'custom' ? 'block' : 'none';
    });
</script>
@endpush
@endsection 