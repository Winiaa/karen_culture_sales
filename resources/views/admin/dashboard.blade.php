@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Overview of your store performance')

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-shopping-bag fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-0">Total Products</h6>
                        <h2 class="mb-0">{{ $totalProducts }}</h2>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.products.index') }}" class="text-decoration-none text-primary">Manage Products <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-accent bg-opacity-10 p-3 rounded-3 me-3" style="background-color: var(--accent-light)">
                        <i class="fas fa-sitemap fa-2x" style="color: var(--accent-color)"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-0">Total Categories</h6>
                        <h2 class="mb-0">{{ $totalCategories }}</h2>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.categories.index') }}" class="text-decoration-none text-primary">Manage Categories <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-shopping-cart fa-2x text-warning"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-0">Total Orders</h6>
                        <h2 class="mb-0">{{ $totalOrders }}</h2>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">Manage Orders <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                    <div>
                        <h6 class="card-title text-muted mb-0">Total Users</h6>
                        <h2 class="mb-0">{{ $totalUsers }}</h2>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none">Manage Users <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Overview -->
<div class="row mt-4">
    <div class="col-md-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">Revenue Overview</h5>
            </div>
            <div class="card-body">
                <div class="stat-card">
                    <div class="stat-card-header text-secondary fw-light">Monthly Revenue</div>
                    <h3 class="mb-1">@baht($monthlyRevenue)</h3>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 75%"></div>
                    </div>
                </div>

                <!-- Today's Orders with Revenue -->
                <div class="stat-card">
                    <div class="stat-card-header text-secondary fw-light">Today's Revenue</div>
                    <h3 class="mb-1">@baht($todayRevenue ?? 0)</h3>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 60%"></div>
                    </div>
                </div>

                <!-- Average Order Value -->
                <div class="stat-card">
                    <div class="stat-card-header text-secondary fw-light">Average Order Value</div>
                    <h3 class="mb-1">@baht($averageOrderValue ?? 0)</h3>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 45%"></div>
                    </div>
                </div>

                <!-- Total Yearly Revenue -->
                <div class="stat-card">
                    <div class="stat-card-header text-secondary fw-light">Yearly Revenue</div>
                    <h3 class="mb-1">@baht($yearlyRevenue ?? 0)</h3>
                    <div class="progress mt-2" style="height: 5px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 80%"></div>
                    </div>
                </div>
                
                <!-- Revenue Chart -->
                <div class="revenue-chart mt-4">
                    <h5 class="mb-3">Daily Sales</h5>
                    <div class="d-flex justify-content-between">
                        @foreach($salesData as $day)
                        <div class="revenue-bar">
                            <div class="day-label">{{ $day['date'] }}</div>
                            <div class="bar-container">
                                <div class="bar" style="height: {{ $day['sales'] > 0 ? min($day['sales'] * 10, 100) : 5 }}%"></div>
                            </div>
                            <div class="text-dark fw-bold mb-1 small" style="height: 20px; font-size: 0.8rem;">@baht($day['sales'] * 100)</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-md-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestOrders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user->name ?? 'Unknown User' }}</td>
                                <td>@baht($order->total_amount)</td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="row mt-0">
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Top Products</h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Sales</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" 
                                             class="rounded" width="40" height="40" style="object-fit: cover;">
                                        @else
                                        <div class="bg-light rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-box text-muted"></i>
                                        </div>
                                        @endif
                                        <div class="ms-3">
                                            <h6 class="mb-0">{{ $product->title }}</h6>
                                            <small class="text-muted">ID: {{ $product->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>@baht($product->price)</td>
                                <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                                <td>{{ $product->order_items_count ?? 0 }} orders</td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No products found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 