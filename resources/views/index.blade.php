@extends('layouts.app')

@section('title', 'Online BH Finder')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            z-index: 1;
        }
        .map-legend {
            position: absolute;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 10px 15px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .legend-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .legend-item { 
            display: flex; 
            align-items: center; 
            margin-bottom: 5px;
            font-size: 13px;
        }
        .legend-color { 
            width: 18px; 
            height: 18px; 
            margin-right: 8px; 
            border-radius: 3px;
            border: 1px solid #ddd;
        }
        .search-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 10px 20px;
            display: flex;
            justify-content: end;
            align-items: center;
        }

        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-input {
            padding: 8px 40px 8px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 250px;
            outline: none;
            transition: all 0.3s;
        }
        .search-input:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 5px rgba(74, 144, 226, 0.5);
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .pulse-marker {
            animation: pulsemark 1.5s infinite;
        }

        .voice-search-btn {
            position: absolute;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
            background: transparent;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .voice-search-btn:hover {
            color: #4a90e2;
        }

        .voice-search-btn.listening {
            color: #e74c3c;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.5); }
            100% { transform: scale(1); }
        }
        @keyframes pulsemark {
            0% { transform: scale(1.2); }
            50% { transform: scale(1.5); }
            100% { transform: scale(1.2); }
        }
    </style>
@endpush
@section('content')
<div class="d-flex">
    <div class="search-container">
        <div class="search-wrapper">
            <input type="text" id="searchInput" class="search-input" placeholder="Search by name or address...">
            <button id="voiceSearchBtn" class="voice-search-btn" title="Voice Search">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                    <line x1="12" y1="19" x2="12" y2="23"></line>
                    <line x1="8" y1="23" x2="16" y2="23"></line>
                </svg>
            </button>
        </div>
    </div>
    <div id="map"></div>
</div>

                    
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const voiceSearchBtn = document.getElementById('voiceSearchBtn');
            const searchInput = document.getElementById('searchInput');
            
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            
            if (!SpeechRecognition) {
                voiceSearchBtn.style.display = 'none';
                console.warn('Speech recognition not supported in this browser');
                return;
            }
            
            const recognition = new SpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';
            
            voiceSearchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (voiceSearchBtn.classList.contains('listening')) {
                    recognition.stop();
                    voiceSearchBtn.classList.remove('listening');
                    return;
                }
                
                try {
                    recognition.start();
                    voiceSearchBtn.classList.add('listening');
                    searchInput.placeholder = 'Listening...';
                } catch (err) {
                    console.error('Error starting voice recognition:', err);
                    searchInput.placeholder = 'Error: Could not start voice recognition';
                    setTimeout(() => {
                        searchInput.placeholder = 'Search by name or address...';
                    }, 3000);
                }
            });
            
            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                searchInput.value = transcript;
                
                if (typeof performSearch === 'function') {
                    performSearch(transcript);
                }
                
                const inputEvent = new Event('input', { bubbles: true });
                searchInput.dispatchEvent(inputEvent);
            };
            
            recognition.onerror = function(event) {
                console.error('Speech recognition error:', event.error);
                searchInput.placeholder = 'Error: ' + event.error;
                setTimeout(() => {
                    searchInput.placeholder = 'Search by name or address...';
                }, 3000);
            };
            
            recognition.onend = function() {
                voiceSearchBtn.classList.remove('listening');
                if (searchInput.placeholder === 'Listening...') {
                    searchInput.placeholder = 'Search by name or address...';
                }
            };
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([11.2258, 122.9278], 15);
            
            L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                minZoom: 15,
                maxZoom: 18,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                attribution: '&copy; Google'
            }).addTo(map);

            const statusColors = {
                'Available': '#28a745',
                'Under Maintenance': '#ffc107',
                'Full': '#dc3545'
            };

            let markers = [];
            let allBoardingHouses = [];
            let currentSearchTerm = '';

            function updateMapMarkers(filteredHouses) {
                markers.forEach(marker => map.removeLayer(marker));
                markers = [];

                filteredHouses.forEach(bh => {
                    const isMatch = currentSearchTerm !== '' && 
                        (bh.name.toLowerCase().includes(currentSearchTerm) || 
                         bh.address.toLowerCase().includes(currentSearchTerm));
                    
                    const marker = L.circleMarker(
                        [bh.lat, bh.lng],
                        {
                            radius: isMatch ? 12 : 8,
                            fillColor: statusColors[bh.status] || '#6c757d',
                            color: '#fff',
                            weight: isMatch ? 5 : 1,
                            opacity: 1,
                            fillOpacity: isMatch ? 0.9 : 0.8
                            
                        }
                    ).addTo(map);
                    
                    if (isMatch) {
                        marker.bringToFront();
                        marker.setStyle({
                            className: 'pulse-marker'
                        });
                    }
                    
                    marker.bindPopup(bh.popup);
                    markers.push(marker);
                });

                if (filteredHouses.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            }

            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                currentSearchTerm = this.value.toLowerCase().trim();
                if (currentSearchTerm === '') {
                    updateMapMarkers(allBoardingHouses);
                    return;
                }
                
                const filtered = allBoardingHouses.filter(bh => 
                    bh.name.toLowerCase().includes(currentSearchTerm) || 
                    bh.address.toLowerCase().includes(currentSearchTerm)
                );
                updateMapMarkers(filtered);
            });

            fetch('{{ route("public.map-data") }}')
            .then(response => response.json())
            .then(boardingHouses => {
                allBoardingHouses = boardingHouses;
                updateMapMarkers(boardingHouses);
                boardingHouses.forEach(bh => {
                    const marker = L.circleMarker(
                        [bh.lat, bh.lng],
                        {
                            radius: 8,
                            fillColor: statusColors[bh.status] || '#6c757d',
                            color: '#fff',
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        }
                    ).addTo(map);
                    marker.bindPopup(bh.popup);
                });
            });
        });
    </script>
@endpush