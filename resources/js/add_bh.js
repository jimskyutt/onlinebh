// Make the function available globally
window.previewImage = function(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePlaceholder');
    
    if (input.files && input.files[0]) {
        // Validate file type
        const fileType = input.files[0].type;
        if (!fileType.match('image.*')) {
            alert('Please select a valid image file (JPG, PNG, JPEG)');
            input.value = '';
            return;
        }

        // Validate file size (2MB max)
        if (input.files[0].size > 2 * 1024 * 1024) {
            alert('Image size must be less than 2MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        
        reader.onerror = function() {
            alert('Failed to load image');
            input.value = '';
            if (placeholder) placeholder.style.display = 'block';
            preview.style.display = 'none';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
    }
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('bh_image');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            window.previewImage(this);
        });
    }
});


const contactNumber = document.getElementById('contact_number');
contactNumber.addEventListener('keydown', function(e) {
    if (e.key === 'Backspace' && this.selectionStart <= 2) {
        e.preventDefault();
        return false;
    }        
});

contactNumber.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
            
    if (!value.startsWith('09')) {
        value = '09' + value.replace(/^09/, '');
    }
            
    if (value.length > 11) {
        value = value.substring(0, 11);
    }
            
    this.value = value;
});

contactNumber.addEventListener('paste', function(e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text');
    const numbers = text.replace(/\D/g, '');
    const currentValue = this.value;
    const newValue = currentValue.substring(0, this.selectionStart) + numbers + currentValue.substring(this.selectionEnd);
            
    let finalValue = '09' + newValue.replace(/^09/, '').replace(/\D/g, '');
    if (finalValue.length > 11) {
        finalValue = finalValue.substring(0, 11);
    }
            
    this.value = finalValue;
});