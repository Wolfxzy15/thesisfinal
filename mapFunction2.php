<?php
// Connect to the database
include 'db.php';

$sql = "SELECT * FROM tbl_evac_centers";
$result = mysqli_query($conn, $sql);

$evacCenters = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $evacCenters[] = $row;
    }
}

mysqli_close($conn);
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([10.7332, 122.5585], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var evacIcon = L.icon({
        iconUrl: 'images/building-solid.svg', // Path to your building icon
        iconSize: [32, 32], // Size of the icon
        iconAnchor: [16, 32], // Anchor point of the icon (bottom center)
        popupAnchor: [0, -32] // Popup anchor point
    });

    // Load the geojson.js file that handles GeoJSON data loading
    var script = document.createElement('script');
    script.src = 'geojson.js'; // Replace with the correct path to geojson.js
    script.onload = function() {
        // After geojson.js is loaded, the GeoJSON layers will be added to the map
        Promise.all(geojsonPromises).then(function() {
            for (var barangay in barangays) {
                L.geoJSON(barangays[barangay]).addTo(map);
            }
        });
    };
    document.head.appendChild(script);

    // Add existing evacuation center markers
    var markers = <?php echo json_encode($evacCenters); ?>;
    markers.forEach(function(markerData) {
        L.marker([markerData.latitude, markerData.longitude], {icon: evacIcon}).addTo(map)
            .bindTooltip("<b>" + markerData.evacName + "</b><br>Height: " + markerData.height + " meters<br>Width: " + markerData.width + " meters")
            .openTooltip();
    });

    var currentMarker;  // Variable to hold the currently placed marker

    // Function to reverse geocode using Nominatim
    function getAddress(lat, lng) {
        var url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById('evacAddress').value = data.display_name; // Update address field
                    if (currentMarker) {
                        currentMarker.bindTooltip(`Address: ${data.display_name}<br>Latitude: ${lat}<br>Longitude: ${lng}`).openTooltip();
                    }
                } else {
                    document.getElementById('address').value = "Address not found";
                }
            })
            .catch(error => {
                console.error('Error fetching address:', error);
                document.getElementById('address').value = "Error fetching address";
            });
    }

    // Add a click event listener to the map
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // Update form fields with latitude and longitude
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Reverse geocode to get the address
        getAddress(lat, lng);

        // If there's an existing marker, remove it
        if (currentMarker) {
            map.removeLayer(currentMarker);
        }

        currentMarker = L.marker([lat, lng], {icon: evacIcon}).addTo(map)
            .bindTooltip("You clicked here:<br>Latitude: " + lat + "<br>Longitude: " + lng)
            .openTooltip();
    });
});
</script>
