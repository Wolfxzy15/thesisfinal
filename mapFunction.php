<script>
        // Default coordinates (central map position)
        var defaultLat = 0;
        var defaultLong = 0;
        var evac1_distance = 0, evac2_distance = 0, evac3_distance = 0, evac4_distance = 0;

        // Latitude and longitude passed from PHP (POST request)
        var x = <?php echo json_encode($lat); ?> || defaultLat;
        var y = <?php echo json_encode($long); ?> || defaultLong;

        var map = L.map('map').setView([10.731019, 122.558467], 15);
        L.tileLayer('https://api.maptiler.com/maps/streets-v2/256/{z}/{x}/{y}.png?key=ngrPpvE2X0m7KBaoLLex', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
        }).addTo(map);

        if (x && y) {
            // Red marker icon
            var ClickIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-gold.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            });

            // Add red marker based on lat/long from the form
            var redMarker = L.marker([x, y], {icon: ClickIcon}).addTo(map);
        }

        // Static evacuation center markers
        var markers = [
            {coords: [10.730668, 122.560212], name: 'Marker 1'},
            {coords: [10.733978, 122.557465], name: 'Marker 2'},
            {coords: [10.733936, 122.561885], name: 'Marker 3'},
            // New evacuation center 4 coordinates
            {coords: [10.732251863393, 122.55615529758], name: 'Marker 4'}
        ];

        markers.forEach(function(markerData) {
            var marker = L.marker(markerData.coords).addTo(map);
            var distance = redMarker ? redMarker.getLatLng().distanceTo(marker.getLatLng()) : 0;
            marker.bindPopup('Distance to ' + markerData.name + ': ' + distance.toFixed(2) + ' meters<br>Coordinates: ' + markerData.coords).openPopup();

            if (markerData.name === 'Marker 1') {
                evac1_distance = parseFloat(distance.toFixed(2));
            } else if (markerData.name === 'Marker 2') {
                evac2_distance = parseFloat(distance.toFixed(2));
            } else if (markerData.name === 'Marker 3') {
                evac3_distance = parseFloat(distance.toFixed(2));
            } else if (markerData.name === 'Marker 4') {
                evac4_distance = parseFloat(distance.toFixed(2));
            }
        });

        var minDistance = Math.min(evac1_distance, evac2_distance, evac3_distance, evac4_distance);
        document.getElementById('mindistance').textContent = 'The nearest evacuation center is ' + minDistance + ' meters away';
        document.getElementById('evac1').value = evac1_distance;
        document.getElementById('evac2').value = evac2_distance;
        document.getElementById('evac3').value = evac3_distance;
        document.getElementById('evac4').value = evac4_distance;

    </script>
