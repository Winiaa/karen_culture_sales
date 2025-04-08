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
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only select buttons with the delete-btn class
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const modal = document.getElementById('deleteConfirmationModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteMessage = document.getElementById('deleteConfirmationMessage');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Only proceed if this is a delete button
            if (!this.classList.contains('delete-btn')) {
                return;
            }

            const deleteUrl = this.getAttribute('data-delete-url');
            if (!deleteUrl) {
                return;
            }

            // Set the form action
            deleteForm.action = deleteUrl;

            // Update the confirmation message based on the type
            const type = this.getAttribute('data-type') || 'item';
            let message = 'Are you sure you want to delete this ';
            
            switch(type) {
                case 'categories':
                    message += 'category?';
                    break;
                case 'products':
                    message += 'product?';
                    break;
                case 'drivers':
                    message += 'driver?';
                    break;
                case 'users':
                    message += 'user?';
                    break;
                default:
                    message += 'item?';
            }
            
            deleteMessage.textContent = message;

            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        });
    });
});
</script> 