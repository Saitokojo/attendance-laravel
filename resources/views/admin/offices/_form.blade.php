@php
    $inputClass = 'w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500';
    $errorClass = 'mt-1 text-xs text-red-600';
    $labelClass = 'block text-sm font-medium text-gray-700 mb-1';
    $initLat = old('latitude', $office?->latitude);
    $initLng = old('longitude', $office?->longitude);
@endphp

<div class="space-y-5">

    <div>
        <label class="{{ $labelClass }}">Name</label>
        <input type="text" name="name"
               value="{{ old('name', $office?->name) }}"
               class="{{ $inputClass }} @error('name') border-red-400 @enderror"
               required>
        @error('name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

    {{-- Location --}}
    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="{{ $labelClass }} mb-0">Location</label>
            <button type="button" id="btn-get-location"
                    class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5
                           bg-indigo-600 text-white rounded-md hover:bg-indigo-700
                           disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span id="btn-location-label">Use My Location</span>
            </button>
        </div>

        <p id="location-status" class="text-xs text-gray-500 mb-2 hidden"></p>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="{{ $labelClass }}">Latitude</label>
                <input type="number" id="input-lat" name="latitude" step="0.0000001"
                       value="{{ $initLat }}"
                       placeholder="-90 to 90"
                       class="{{ $inputClass }} @error('latitude') border-red-400 @enderror"
                       required>
                @error('latitude') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $labelClass }}">Longitude</label>
                <input type="number" id="input-lng" name="longitude" step="0.0000001"
                       value="{{ $initLng }}"
                       placeholder="-180 to 180"
                       class="{{ $inputClass }} @error('longitude') border-red-400 @enderror"
                       required>
                @error('longitude') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Map preview --}}
        <div id="map-wrapper" class="{{ ($initLat && $initLng) ? '' : 'hidden' }} mt-3">
            <div id="office-map" class="w-full rounded-lg border border-gray-200 overflow-hidden" style="height:260px;z-index:0;"></div>
            <p class="mt-1 text-xs text-gray-400">Drag the pin to fine-tune the exact location.</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="{{ $labelClass }}">Radius (meters)</label>
            <input type="number" name="radius_meters" min="1"
                   value="{{ old('radius_meters', $office?->radius_meters) }}"
                   placeholder="e.g. 100"
                   class="{{ $inputClass }} @error('radius_meters') border-red-400 @enderror"
                   required>
            @error('radius_meters') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="{{ $labelClass }}">Late After (time)</label>
            <input type="time" name="late_after"
                   value="{{ old('late_after', $office ? substr($office->late_after, 0, 5) : '') }}"
                   class="{{ $inputClass }} @error('late_after') border-red-400 @enderror"
                   required>
            @error('late_after') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="{{ $labelClass }}">Timezone</label>
        <select name="timezone"
                class="{{ $inputClass }} @error('timezone') border-red-400 @enderror"
                required>
            <option value="">— select timezone —</option>
            @foreach ($timezones as $tz)
                <option value="{{ $tz }}" @selected(old('timezone', $office?->timezone) === $tz)>
                    {{ $tz }}
                </option>
            @endforeach
        </select>
        @error('timezone') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

</div>

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const latInput  = document.getElementById('input-lat');
    const lngInput  = document.getElementById('input-lng');
    const btn       = document.getElementById('btn-get-location');
    const btnLabel  = document.getElementById('btn-location-label');
    const status    = document.getElementById('location-status');
    const wrapper   = document.getElementById('map-wrapper');

    let map    = null;
    let marker = null;

    function showStatus(msg, color) {
        status.textContent = msg;
        status.className   = `text-xs mb-2 ${color}`;
        status.classList.remove('hidden');
    }

    function initMap(lat, lng) {
        wrapper.classList.remove('hidden');

        if (map) {
            map.setView([lat, lng], 16);
            marker.setLatLng([lat, lng]);
            return;
        }

        map = L.map('office-map').setView([lat, lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        marker.on('dragend', function () {
            const pos = marker.getLatLng();
            latInput.value = pos.lat.toFixed(7);
            lngInput.value = pos.lng.toFixed(7);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            latInput.value = e.latlng.lat.toFixed(7);
            lngInput.value = e.latlng.lng.toFixed(7);
        });
    }

    // Sync manual input changes → move the pin
    function syncInputToMap() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng) && map && marker) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], map.getZoom());
        }
    }
    latInput.addEventListener('change', syncInputToMap);
    lngInput.addEventListener('change', syncInputToMap);

    // "Use My Location" button
    btn.addEventListener('click', function () {
        if (!navigator.geolocation) {
            showStatus('Geolocation is not supported by your browser.', 'text-red-500');
            return;
        }

        btn.disabled = true;
        btnLabel.textContent = 'Getting location…';
        showStatus('Requesting your location…', 'text-gray-500');

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;

                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);

                initMap(lat, lng);
                showStatus('Location set. Drag the pin or click the map to adjust.', 'text-green-600');

                btn.disabled  = false;
                btnLabel.textContent = 'Update Location';
            },
            function (err) {
                const msgs = {
                    1: 'Permission denied. Please allow location access in your browser.',
                    2: 'Position unavailable. Try again or enter coordinates manually.',
                    3: 'Request timed out. Try again.',
                };
                showStatus(msgs[err.code] || 'Could not get location.', 'text-red-500');
                btn.disabled  = false;
                btnLabel.textContent = 'Use My Location';
            },
            { timeout: 10000 }
        );
    });

    // If editing with existing coords, show map on load
    const existingLat = parseFloat(latInput.value);
    const existingLng = parseFloat(lngInput.value);
    if (!isNaN(existingLat) && !isNaN(existingLng)) {
        // defer so Leaflet can measure the container
        setTimeout(function () { initMap(existingLat, existingLng); }, 100);
    }
})();
</script>
@endpush
