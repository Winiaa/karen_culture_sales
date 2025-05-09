<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveConfirmationModal" tabindex="-1" aria-labelledby="approveConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveConfirmationModalLabel">Confirm Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="approveConfirmationMessage">Are you sure you want to approve this review?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="approveForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveButtons = document.querySelectorAll('.approve-review');
    const modal = document.getElementById('approveConfirmationModal');
    const approveForm = document.getElementById('approveForm');

    approveButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const reviewId = this.getAttribute('data-review-id');
            if (!reviewId) return;

            // Set the form action
            approveForm.action = `/admin/reviews/${reviewId}/approve`;

            // Show the modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        });
    });
});
</script> 