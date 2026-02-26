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
        <div wire:ignore id="retailer-map" style="height: 600px; width: 100%;" class="rounded-xl border border-gray-200 dark:border-gray-700"></div>

        {{-- Retailer Count --}}
        <p id="retailer-count" class="text-sm text-gray-500 dark:text-gray-400"></p>

        @if (! $googleMapsApiKey)
            <div class="p-6 text-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <p class="text-lg font-medium">Google Maps API key is not configured.</p>
                <p class="mt-1">Add <code>GOOGLE_MAPS_API_KEY</code> to your <code>.env</code> file to enable the map.</p>
            </div>
        @endif

        {{-- Collapsible Retailer List --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
            <button
                wire:click="toggleList"
                class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    <svg class="inline-block w-4 h-4 mr-1 transition-transform {{ $this->listExpanded ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Retailer List ({{ $retailerList['total'] }})
                </span>
            </button>

            @if ($this->listExpanded)
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="px-4 py-3">
                        <div class="flex gap-2 max-w-sm">
                            <input
                                type="text"
                                wire:model="listSearch"
                                wire:keydown.enter="searchList"
                                placeholder="Search by name..."
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm placeholder-gray-400 dark:placeholder-gray-500"
                            >
                            <button
                                wire:click="searchList"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-colors"
                                title="Search"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                            <button
                                wire:click="resetListSearch"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
                                title="Reset"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">State</th>
                                    <th class="px-4 py-3">Country</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($retailerList['items'] as $retailer)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $retailer->name ?? 'Unnamed' }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $retailer->state }}</td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $retailer->country }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <button
                                                wire:click="mountAction('editRetailerLocation', { id: {{ $retailer->id }} })"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Edit
                                            </button>
                                            <button
                                                wire:click="mountAction('deleteRetailerLocation', { id: {{ $retailer->id }} })"
                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-danger-600 dark:text-danger-400 bg-danger-50 dark:bg-danger-900/20 rounded-lg hover:bg-danger-100 dark:hover:bg-danger-900/40 transition-colors"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No retailer locations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($retailerList['hasMore'])
                        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                            <button
                                wire:click="loadMore"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-medium text-primary-600 bg-primary-50 dark:bg-primary-900/20 dark:text-primary-400 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/40 transition-colors disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="loadMore">
                                    Load More ({{ $retailerList['total'] - $retailerList['items']->count() }} remaining)
                                </span>
                                <span wire:loading wire:target="loadMore">
                                    Loading...
                                </span>
                            </button>
                        </div>
                    @else
                        @if ($retailerList['total'] > 0)
                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 text-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Showing all {{ $retailerList['total'] }} retailers</span>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>

@if ($googleMapsApiKey)
    @push('scripts')
        <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>
        <script>
            let map;
            let markers = [];
            let markerCluster = null;
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
                // Clear existing markers and cluster
                markers.forEach(m => m.setMap(null));
                markers = [];
                if (markerCluster) {
                    markerCluster.clearMarkers();
                    markerCluster = null;
                }

                const bounds = new google.maps.LatLngBounds();

                data.forEach(retailer => {
                    const position = { lat: retailer.latitude, lng: retailer.longitude };
                    const marker = new google.maps.Marker({
                        position: position,
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

                // Create marker cluster
                if (markers.length > 0) {
                    markerCluster = new markerClusterer.MarkerClusterer({
                        map,
                        markers,
                    });

                    map.fitBounds(bounds);
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
