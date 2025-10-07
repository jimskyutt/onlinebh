let map;
let marker;
let selectedLocation = null;

const mapModal = document.getElementById('mapModal');
mapModal.addEventListener('shown.bs.modal', function() {
    if (!map) {
    map = L.map('map').setView([11.2258, 122.9278], 15);
    
    const googleHybrid = L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        zoomControl: true,
        minZoom: 16,
        maxZoom: 18,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google'
    }).addTo(map);

    map.on('click', function(e) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker(e.latlng).addTo(map);
        selectedLocation = e.latlng;
    });
}
setTimeout(function() {
    map.invalidateSize();
}, 100);
});
document.getElementById('confirmLocation').addEventListener('click', function() {
if (selectedLocation) {
    const confirmBtn = this;
    const originalText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Getting address...';
    
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${selectedLocation.lat}&lon=${selectedLocation.lng}&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            let address = '';
            if (data.address) {
                const addr = data.address;
                address = [
                    addr.road,
                    addr.hamlet || addr.village || addr.town || addr.city,
                    addr.state,
                    addr.postcode
                ].filter(Boolean).join(', ');
            }
            
            document.getElementById('address').value = address || 'Address not found';
            document.getElementById('location').value = 
                `${selectedLocation.lat.toFixed(6)}, ${selectedLocation.lng.toFixed(6)}`;
            
            const modal = bootstrap.Modal.getInstance(mapModal);
            modal.hide();
        })
        .catch(error => {
            console.error('Error getting address:', error);
            alert('Error getting address. Please try again.');
        })
        .finally(() => {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        });
} else {
    alert('Please select a location on the map');
}
});

document.getElementById('addressSearch').addEventListener('click', function() {
const modal = new bootstrap.Modal(document.getElementById('mapModal'));
modal.show();
});