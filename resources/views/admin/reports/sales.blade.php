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

    <!-- Report Filters -->
    <div class="card mb-4">
        <div class="card-header bg-karen text-dark">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Report Filters</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3">
                <!-- Date Range Filter -->
                <div class="col-md-3 mb-3">
                    <label for="date_range" class="form-label">Date Range</label>
                    <select class="form-select" id="date_range" name="date_range" onchange="toggleCustomDateRange(this.value)">
                        <option value="">All Time</option>
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
                
                <!-- Custom Date Range (conditionally displayed) -->
                <div class="col-md-3 mb-3 custom-date-range" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                
                <div class="col-md-3 mb-3 custom-date-range" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                
                <!-- Payment Status Filter -->
                <div class="col-md-3 mb-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="" {{ request('payment_status', '') == '' ? 'selected' : '' }}>All Statuses</option>
                        <option value="completed" {{ request('payment_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                
                <!-- Order Status Filter -->
                <div class="col-md-3 mb-3">
                    <label for="order_status" class="form-label">Order Status</label>
                    <select class="form-select" id="order_status" name="order_status">
                        <option value="">All Statuses</option>
                        <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div class="col-md-3 mb-3">
                    <label for="category_id" class="form-label">Product Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filter Buttons -->
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-karen me-2">
                        <i class="fas fa-search me-2"></i>Generate Report
                    </button>
                    <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
                
                <!-- Export Button -->
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <a href="{{ route('admin.reports.sales.export', request()->all()) }}" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i>Export to CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
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
        <div class="col-md-3">
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
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-white-50">Average Order Value</h6>
                            <h2 class="mb-0">@baht($averageOrderValue)</h2>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-white-50">Cancellations & Refunds</h6>
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

@push('scripts')
<script>
    function toggleCustomDateRange(value) {
        const customDateRangeFields = document.querySelectorAll('.custom-date-range');
        if (value === 'custom') {
            customDateRangeFields.forEach(field => field.style.display = 'block');
        } else {
            customDateRangeFields.forEach(field => field.style.display = 'none');
        }
    }
    
    // Initialize the display on page load
    document.addEventListener('DOMContentLoaded', function() {
        const dateRangeSelect = document.getElementById('date_range');
        if (dateRangeSelect) {
            toggleCustomDateRange(dateRangeSelect.value);
        }
    });
</script>
@endpush
@endsection 