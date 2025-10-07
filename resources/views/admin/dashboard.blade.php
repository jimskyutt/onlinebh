@extends('layouts.admin')

@section('title', 'Online BH Finder')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map { height: 435px; width: 100%; border-radius: 8px; }
        .map-legend { background: white; padding: 10px; border-radius: 5px; box-shadow: 0 1px 5px rgba(0,0,0,0.4); }
        .legend-item { display: flex; align-items: center; margin-bottom: 5px; }
        .legend-color { width: 20px; height: 20px; margin-right: 8px; border-radius: 3px; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
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

            fetch('{{ route("admin.dashboard.map-data") }}')
                .then(response => response.json())
                .then(boardingHouses => {
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