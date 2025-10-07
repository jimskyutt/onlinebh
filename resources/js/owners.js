document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.editable, .editable-password').forEach(initEditable);
});

function initEditable(element) {
    const isPassword = element.classList.contains('editable-password');
    const originalValue = element.getAttribute('data-original-value');
    const originalDisplay = isPassword ? element.getAttribute('data-original-display') : originalValue;
    const field = element.getAttribute('data-field');
    const userId = element.getAttribute('data-user-id');
    
    element.addEventListener('dblclick', function(e) {
        e.stopPropagation();
        const currentValue = isPassword ? '' : originalValue;
        const input = document.createElement('input');
        input.type = isPassword ? 'password' : 'text';
        input.className = 'editable-input';
        input.value = currentValue;
        input.placeholder = isPassword ? 'Enter new password' : '';
        
        let isSaving = false;
        
        const save = () => {
            if (isSaving) return;
            
            const newValue = input.value.trim();
            if (newValue !== originalValue) {
                isSaving = true;
                updateUser(userId, field, newValue, element, isPassword)
                    .finally(() => {
                        isSaving = false;
                    });
            } else {
                element.textContent = isPassword ? originalDisplay : originalValue;
            }
        };
        
        const handleKeyDown = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            } else if (e.key === 'Escape') {
                element.textContent = isPassword ? originalDisplay : originalValue;
                input.blur();
            }
        };
        
        input.addEventListener('keydown', handleKeyDown);
        
        input.addEventListener('blur', save, { once: true });
        
        element.textContent = '';
        element.appendChild(input);
        input.focus();
    });
}

function updateUser(userId, field, value, element, isPassword) {
    console.log('updateUser called with:', { userId, field, value, element, isPassword });
    
    if (!userId) {
        const errorMsg = 'Error: User ID is missing or invalid';
        console.error(errorMsg);
        showToast('error', errorMsg);
        return Promise.reject(errorMsg);
    }
    const elementUserId = element.getAttribute('data-user-id');
    const finalUserId = userId || elementUserId;
    
    if (!finalUserId) {
        const errorMsg = 'Error: Could not determine user ID';
        console.error(errorMsg, { userId, elementUserId });
        showToast('error', errorMsg);
        return Promise.reject(errorMsg);
    }
    const url = `${window.location.origin}/admin/owners/${finalUserId}`;
    const data = new FormData();
    data.append('_method', 'PUT');
    data.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
    data.append(field, value);
    data.append('user_id', finalUserId);
    
    return fetch(url, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(async response => {
        const responseText = await response.text();
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}, body: ${responseText}`);
        }
        
        try {
            return JSON.parse(responseText);
        } catch (e) {
            console.error('Failed to parse JSON:', e);
            throw new Error('Invalid JSON response from server');
        }
    })
    .then(data => {
        if (data.success) {
            if (isPassword) {
                element.setAttribute('data-original-value', value);
                element.setAttribute('data-original-display', '*************');
                element.textContent = '*************';
            } else {
                element.setAttribute('data-original-value', value);
                element.textContent = value;
            }
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.textContent = data.message || 'Update successful';
            
            document.querySelectorAll('.alert.alert-success').forEach(el => el.remove());
            
            const header = document.querySelector('.d-sm-flex.justify-content-between.align-items-center.mb-4');
            if (header) {
                header.insertAdjacentElement('afterend', alertDiv);
            }
            
            setTimeout(() => {
                alertDiv.remove();
            }, 2000);
        } else {
            showToast('error', data.message || 'Update failed');
            element.textContent = isPassword ? element.getAttribute('data-original-display') : element.getAttribute('data-original-value');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'An error occurred. Please try again.');
        element.textContent = isPassword ? element.getAttribute('data-original-display') : element.getAttribute('data-original-value');
    });
}

function showToast(type, message) {
    alert(message); 
}
