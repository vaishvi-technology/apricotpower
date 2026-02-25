<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Filters --}}
        <div class="flex flex-wrap gap-4">
            <div class="w-64">
                <label for="filter-state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">State</label>
                <select id="filter-state" onchange="filterMarkers()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                    <option value="">All States</option>
                    @foreach ($states as $state)
                        <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-64">
                <label for="filter-country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Country</label>
                <select id="filter-country" onchange="filterMarkers()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                    <option value="">All Countries</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country }}">{{ $country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-64">
                <label for="filter-zip" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zip Code</label>
                <div class="flex gap-2">
                    <input type="text" id="filter-zip" onkeydown="if(event.key==='Enter') searchByZip()" placeholder="Enter zip code" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                    <button onclick="searchByZip()" class="px-3 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700">
                        Search
                    </button>
                </div>
            </div>
            <div class="flex items-end">
                <button onclick="resetFilters()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    Reset Filters
                </button>
            </div>
        </div>

        {{-- Map Container --}}
        <div id="retailer-map" style="height: 600px; width: 100%;" class="rounded-xl border border-gray-200 dark:border-gray-700"></div>

        {{-- Retailer Count --}}
        <p id="retailer-count" class="text-sm text-gray-500 dark:text-gray-400"></p>

        @if (! $googleMapsApiKey)
            <div class="p-6 text-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <p class="text-lg font-medium">Google Maps API key is not configured.</p>
                <p class="mt-1">Add <code>GOOGLE_MAPS_API_KEY</code> to your <code>.env</code> file to enable the map.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>

@if ($googleMapsApiKey)
    @push('scripts')
        <script>
            let map;
            let markers = [];
            let infoWindow;
            const retailers = @json($retailers);

            function initMap() {
                const center = { lat: 39.8283, lng: -98.5795 }; // Center of US

                map = new google.maps.Map(document.getElementById('retailer-map'), {
                    zoom: 4,
                    center: center,
                    mapTypeControl: true,
                    streetViewControl: false,
                });

                infoWindow = new google.maps.InfoWindow();

                addMarkers(retailers);
                updateCount(retailers.length);
            }

            function addMarkers(data) {
                // Clear existing markers
                markers.forEach(m => m.setMap(null));
                markers = [];

                const bounds = new google.maps.LatLngBounds();

                data.forEach(retailer => {
                    const position = { lat: retailer.latitude, lng: retailer.longitude };
                    const marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: retailer.name,
                    });

                    marker.addListener('click', () => {
                        const addressParts = [retailer.street, retailer.city, retailer.state, retailer.country].filter(Boolean);
                        const address = addressParts.join(', ');

                        let content = `<div style="max-width: 280px; padding: 4px;">`;
                        content += `<h3 style="font-weight: bold; font-size: 14px; margin-bottom: 6px;">${retailer.name}</h3>`;
                        if (address) content += `<p style="margin: 2px 0; font-size: 13px;">${address}</p>`;
                        if (retailer.phone) content += `<p style="margin: 2px 0; font-size: 13px;">Phone: ${retailer.phone}</p>`;
                        if (retailer.website) {
                            let url = retailer.website;
                            if (!url.startsWith('http')) url = 'https://' + url;
                            content += `<p style="margin: 2px 0; font-size: 13px;"><a href="${url}" target="_blank" style="color: #1d4ed8;">${retailer.website}</a></p>`;
                        }
                        content += `</div>`;

                        infoWindow.setContent(content);
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                    bounds.extend(position);
                });

                if (data.length > 0) {
                    map.fitBounds(bounds);
                    // Don't zoom in too far for a single marker
                    if (data.length === 1) {
                        map.setZoom(12);
                    }
                }
            }

            let zipCircle = null;
            const ZIP_RADIUS_KM = 50; // Show retailers within 50km of the zip code

            function getDistanceKm(lat1, lng1, lat2, lng2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLng = (lng2 - lng1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLng / 2) * Math.sin(dLng / 2);
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            }

            function filterMarkers() {
                const stateFilter = document.getElementById('filter-state').value;
                const countryFilter = document.getElementById('filter-country').value;

                const filtered = retailers.filter(r => {
                    if (stateFilter && r.state !== stateFilter) return false;
                    if (countryFilter && r.country !== countryFilter) return false;
                    return true;
                });

                addMarkers(filtered);
                updateCount(filtered.length);
            }

            function searchByZip() {
                const zip = document.getElementById('filter-zip').value.trim();
                if (!zip) {
                    // Clear zip filter, show all with other filters applied
                    if (zipCircle) { zipCircle.setMap(null); zipCircle = null; }
                    filterMarkers();
                    return;
                }

                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({ address: zip }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        const location = results[0].geometry.location;
                        const lat = location.lat();
                        const lng = location.lng();

                        // Apply state/country filters too
                        const stateFilter = document.getElementById('filter-state').value;
                        const countryFilter = document.getElementById('filter-country').value;

                        const nearby = retailers.filter(r => {
                            if (stateFilter && r.state !== stateFilter) return false;
                            if (countryFilter && r.country !== countryFilter) return false;
                            return getDistanceKm(lat, lng, r.latitude, r.longitude) <= ZIP_RADIUS_KM;
                        });

                        // Draw a circle to show search area
                        if (zipCircle) zipCircle.setMap(null);
                        zipCircle = new google.maps.Circle({
                            strokeColor: '#4F46E5',
                            strokeOpacity: 0.3,
                            strokeWeight: 2,
                            fillColor: '#4F46E5',
                            fillOpacity: 0.08,
                            map: map,
                            center: { lat, lng },
                            radius: ZIP_RADIUS_KM * 1000,
                        });

                        addMarkers(nearby);
                        updateCount(nearby.length);

                        if (nearby.length === 0) {
                            // Center on the zip code location even if no retailers found
                            map.setCenter({ lat, lng });
                            map.setZoom(10);
                        }
                    } else {
                        alert('Could not find location for zip code: ' + zip);
                    }
                });
            }

            function resetFilters() {
                document.getElementById('filter-state').value = '';
                document.getElementById('filter-country').value = '';
                document.getElementById('filter-zip').value = '';
                if (zipCircle) { zipCircle.setMap(null); zipCircle = null; }
                addMarkers(retailers);
                updateCount(retailers.length);
            }

            function updateCount(count) {
                document.getElementById('retailer-count').textContent =
                    `Showing ${count} retailer${count !== 1 ? 's' : ''} on map`;
            }
        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap">
        </script>
    @endpush
@endif
