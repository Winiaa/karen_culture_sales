@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Driver Details</h1>
        <div class="btn-group">
            <a href="{{ route('admin.drivers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Driver
            </a>
            <form action="{{ route('admin.drivers.toggle-active', $driver) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn {{ $driver->is_active ? 'btn-warning' : 'btn-success' }}">
                    <i class="fas {{ $driver->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i> 
                    {{ $driver->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Driver Information -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Driver Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle" 
                            src="https://ui-avatars.com/api/?name={{ urlencode($driver->user->name) }}&background=random&size=128" 
                            alt="{{ $driver->user->name }}" style="max-width: 128px;">
                        <h5 class="mt-3">{{ $driver->user->name }}</h5>
                        <div class="badge {{ $driver->is_active ? 'badge-success' : 'badge-secondary' }}">
                            {{ $driver->is_active ? 'Active' : 'Inactive' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-gray-600 font-weight-bold">Email:</h6>
                        <p>{{ $driver->user->email }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-gray-600 font-weight-bold">Phone Number:</h6>
                        <p>{{ $driver->phone_number }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-gray-600 font-weight-bold">Vehicle:</h6>
                        <p>{{ ucfirst($driver->vehicle_type) }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-gray-600 font-weight-bold">License Number:</h6>
                        <p>{{ $driver->license_number }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-gray-600 font-weight-bold">Vehicle Plate:</h6>
                        <p>{{ $driver->vehicle_plate }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-gray-600 font-weight-bold">Joined:</h6>
                        <p>{{ $driver->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Statistics -->
        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Delivery Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <!-- Total Deliveries -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Deliveries</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $deliveryStats['total'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Deliveries -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Active Deliveries</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $deliveryStats['active'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-truck-loading fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Completed Deliveries -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Completed</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $deliveryStats['completed'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Failed Deliveries -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Failed</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $deliveryStats['failed'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Deliveries -->
                    <h5 class="mb-3">Recent Deliveries</h5>
                    @if($recentDeliveries->isEmpty())
                        <div class="alert alert-info">No deliveries found for this driver.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Delivery Date</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDeliveries as $delivery)
                                        <tr>
                                            <td>{{ $delivery->order->id }}</td>
                                            <td>
                                                @if($delivery->delivered_at)
                                                    {{ $delivery->delivered_at->format('M d, Y') }}
                                                @else
                                                    {{ $delivery->updated_at->format('M d, Y') }}
                                                @endif
                                            </td>
                                            <td>{{ $delivery->recipient_name }}</td>
                                            <td>
                                                <span class="badge badge-{{ 
                                                    $delivery->delivery_status === 'assigned' ? 'warning' : 
                                                    ($delivery->delivery_status === 'picked_up' ? 'info' : 
                                                    ($delivery->delivery_status === 'out_for_delivery' ? 'primary' : 
                                                    ($delivery->delivery_status === 'delivered' ? 'success' : 
                                                    ($delivery->delivery_status === 'failed' ? 'danger' : 'secondary')))) 
                                                }}">
                                                    {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.orders.show', $delivery->order) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 