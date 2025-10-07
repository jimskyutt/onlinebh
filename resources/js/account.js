document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Handle username update
    const usernameForm = document.getElementById('updateUsernameForm');
    if (usernameForm) {
        usernameForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('updateUsernameBtn');
            const spinner = btn.querySelector('.spinner-border');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btn.innerHTML = 'Updating...';
            
            fetch(updateUsernameUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    username: document.getElementById('username').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                } else {
                    showAlert('danger', data.message || 'Failed to update username');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while updating username');
            })
            .finally(() => {
                btn.disabled = false;
                spinner.classList.add('d-none');
                btn.innerHTML = originalText;
            });
        });
    }

    // Handle password update
    const passwordForm = document.getElementById('updatePasswordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('updatePasswordBtn');
            const spinner = btn.querySelector('.spinner-border');
            const originalText = btn.innerHTML;
            
            btn.disabled = true;
            spinner.classList.remove('d-none');
            btn.innerHTML = 'Updating...';
            
            const formData = new FormData(passwordForm);
            
            fetch(updatePasswordUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    passwordForm.reset();
                } else {
                    showAlert('danger', data.message || 'Failed to update password');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while updating password');
            })
            .finally(() => {
                btn.disabled = false;
                spinner.classList.add('d-none');
                btn.innerHTML = originalText;
            });
        });
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
        `;
        
        // Remove any existing alerts
        document.querySelectorAll('.alert-dismissible').forEach(el => el.remove());
        
        // Add the new alert after the page header
        const header = document.querySelector('.d-sm-flex.justify-content-between.align-items-center.mb-4');
        if (header) {
            header.insertAdjacentElement('afterend', alertDiv);
        } else {
            // Fallback to prepend to container if header not found
            document.querySelector('.container-fluid').prepend(alertDiv);
        }
        
        // Auto-remove after 2 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 2000);
    }
});
