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
    </div>

    @if ($googleMapsApiKey)
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

            function resetFilters() {
                document.getElementById('filter-state').value = '';
                document.getElementById('filter-country').value = '';
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
    @else
        <div class="p-6 text-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <p class="text-lg font-medium">Google Maps API key is not configured.</p>
            <p class="mt-1">Add <code>GOOGLE_MAPS_API_KEY</code> to your <code>.env</code> file to enable the map.</p>
        </div>
    @endif
</x-filament-panels::page>
