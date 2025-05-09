// Newsletter subscription functionality
function setupNewsletterForm(formId, emailInputId, buttonId, messageDivId) {
    const button = document.getElementById(buttonId);
    if (!button) return;

    button.addEventListener('click', function() {
        const email = document.getElementById(emailInputId).value;
        const messageDiv = document.getElementById(messageDivId);
        
        if (!email) {
            messageDiv.className = 'mt-2 alert alert-danger';
            messageDiv.textContent = 'Please enter your email address.';
            return;
        }
        
        // Disable button
        button.disabled = true;
        
        fetch('/newsletter/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            messageDiv.className = 'mt-2 alert ' + (data.success ? 'alert-success' : 'alert-danger');
            messageDiv.textContent = data.message;
            
            if (data.success) {
                document.getElementById(emailInputId).value = '';
            }
        })
        .catch(error => {
            messageDiv.className = 'mt-2 alert alert-danger';
            messageDiv.textContent = 'An error occurred. Please try again.';
        })
        .finally(() => {
            button.disabled = false;
        });
    });
}

// Initialize newsletter forms when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    setupNewsletterForm('homeNewsletterForm', 'homeNewsletterEmail', 'homeNewsletterBtn', 'homeNewsletterMessage');
}); 