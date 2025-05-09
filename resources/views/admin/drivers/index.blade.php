@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Drivers Management</h1>
        <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Driver
        </a>
    </div>

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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Drivers List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="driversTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Vehicle</th>
                            <th>Active Deliveries</th>
                            <th>Completed Deliveries</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $driver)
                            <tr>
                                <td>{{ $driver->id }}</td>
                                <td>{{ $driver->user->name }}</td>
                                <td>{{ $driver->user->email }}</td>
                                <td>{{ $driver->phone_number }}</td>
                                <td>{{ ucfirst($driver->vehicle_type) }} ({{ $driver->vehicle_plate }})</td>
                                <td>{{ $driver->active_deliveries_count }}</td>
                                <td>{{ $driver->completed_deliveries_count }}</td>
                                <td>
                                    @if($driver->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-info btn-sm" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-btn" 
                                                title="Delete"
                                                data-delete-url="{{ route('admin.drivers.destroy', $driver) }}"
                                                data-type="driver">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No drivers found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $drivers->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Include the delete confirmation modal -->
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
    .alert {
        border-radius: 0.35rem;
        margin-bottom: 1.5rem;
    }
    .alert .btn-close {
        padding: 1.25rem;
    }
</style>
@endpush 