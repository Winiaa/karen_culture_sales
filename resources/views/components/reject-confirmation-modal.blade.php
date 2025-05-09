<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectConfirmationModal" tabindex="-1" aria-labelledby="rejectConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectConfirmationModalLabel">Confirm Rejection</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="rejectConfirmationMessage">Are you sure you want to reject this review?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="rejectForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectButtons = document.querySelectorAll('.reject-review');
    const modal = document.getElementById('rejectConfirmationModal');
    const rejectForm = document.getElementById('rejectForm');

    rejectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const reviewId = this.getAttribute('data-review-id');
            if (!reviewId) return;

            // Set the form action
            rejectForm.action = `/admin/reviews/${reviewId}/reject`;

            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        });
    });
});
</script> 