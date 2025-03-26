<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmationMessage">Are you sure you want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const modal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    const deleteForm = document.getElementById('deleteForm');
    const deleteMessage = document.getElementById('deleteConfirmationMessage');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const deleteUrl = this.getAttribute('data-delete-url');
            deleteForm.action = deleteUrl;

            // Determine the type of item being deleted based on the URL
            if (deleteUrl.includes('/categories/')) {
                deleteMessage.textContent = 'Are you sure you want to delete this category? This action cannot be undone.';
            } else if (deleteUrl.includes('/products/')) {
                deleteMessage.textContent = 'Are you sure you want to delete this product? This action cannot be undone.';
            } else if (deleteUrl.includes('/drivers/')) {
                deleteMessage.textContent = 'Are you sure you want to delete this driver? This action cannot be undone.';
            } else if (deleteUrl.includes('/users/')) {
                deleteMessage.textContent = 'Are you sure you want to delete this user? This action cannot be undone.';
            } else {
                deleteMessage.textContent = 'Are you sure you want to delete this item? This action cannot be undone.';
            }

            modal.show();
        });
    });
});
</script>
@endpush 