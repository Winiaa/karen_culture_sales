@extends('layouts.admin')

@section('title', 'Categories')
@section('subtitle', 'Manage your product categories')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Categories</h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Add New Category
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-3">
                                    <i class="fas fa-folder text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $category->name }}</h6>
                                    <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">
                                {{ $category->products_count }} products
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}">
                                {{ $category->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-category-btn" 
                                        data-category-id="{{ $category->id }}" data-category-name="{{ $category->name }}"
                                        data-products-count="{{ $category->products_count }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center py-5">
                                <i class="fas fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                                <h5>No Categories Found</h5>
                                <p class="text-muted">Get started by adding your first category</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-1"></i> Add New Category
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Category Modal Template -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <p>Are you sure you want to delete the category <strong id="categoryNamePlaceholder"></strong>?</p>
                <p class="text-danger products-warning d-none">
                    <i class="fas fa-exclamation-triangle me-1"></i> <span id="productsCountMessage"></span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCategoryForm" action="" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Category</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure modals are moved to the end of the body for proper stacking
    document.querySelectorAll('.modal').forEach(modal => {
        document.body.appendChild(modal);
    });
    
    // Get modal element
    const modalElement = document.getElementById('deleteCategoryModal');
    const deleteModal = new bootstrap.Modal(modalElement);
    
    // Add click handlers for delete buttons
    document.querySelectorAll('.delete-category-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const categoryId = this.getAttribute('data-category-id');
            const categoryName = this.getAttribute('data-category-name');
            const productsCount = parseInt(this.getAttribute('data-products-count'));
            
            // Update modal content
            document.getElementById('categoryNamePlaceholder').textContent = categoryName;
            
            // Show warning about products if needed
            const productsWarning = document.querySelector('.products-warning');
            if (productsCount > 0) {
                document.getElementById('productsCountMessage').textContent = 
                    `This category has ${productsCount} products. Deleting it may cause issues.`;
                productsWarning.classList.remove('d-none');
            } else {
                productsWarning.classList.add('d-none');
            }
            
            // Set the form action
            document.getElementById('deleteCategoryForm').action = 
                "{{ route('admin.categories.destroy', '') }}/" + categoryId;
            
            // Show the modal
            deleteModal.show();
        });
    });
    
    // Ensure modal can be closed with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalElement.classList.contains('show')) {
            deleteModal.hide();
        }
    });
    
    // Ensure clicking outside modal closes it
    modalElement.addEventListener('click', function(e) {
        if (e.target === modalElement) {
            deleteModal.hide();
        }
    });
});
</script>
@endpush 