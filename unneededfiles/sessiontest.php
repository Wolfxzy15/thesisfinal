<?php
session_start();
$user = $_SESSION['residentID'];
echo $user;
?>
<?php
session_start();

// Include database configuration
include 'include/db.php';

// Check if residentID is set in session
if(isset($_SESSION['residentID'])) {
    $residentID = $_SESSION['residentID'];

    // Fetch resident's location data using residentID from session
    $sql = "SELECT latitude, longitude FROM tbl_residents WHERE residentID = 1";
    $result = $conn->query($sql);

    // Check if query executed successfully
    if ($result) {
        // Fetch resident's location data
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $latitude = $row["latitude"];
            $longitude = $row["longitude"];
        } else {
            // Handle case where resident data is not found
            echo "Error: Resident data not found.";
            exit; // Stop further execution
        }
    } else {
        // Handle case where query execution failed
        echo "Error: Unable to fetch resident data.";
        exit; // Stop further execution
    }

    $result->close();
} else {
    // Handle case where residentID is not set in session
    echo "Error: ResidentID not set.";
    exit; // Stop further execution
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evacuation Centers</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Include Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {position: absolute; top: 50px; bottom: 50px; left: 25%; right: 50px;}
    </style>
</head>
<body>
    <!-- Map Container -->
    <div id='map'></div>

    <!-- Include Bootstrap and Leaflet JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Inline JavaScript -->
    <script>
        // Resident's location coordinates
        var latitude = <?php echo json_encode($latitude); ?>;
        var longitude = <?php echo json_encode($longitude); ?>;
        
        // Evacuation centers coordinates
        var evacCenters = [
            { name: 'Evacuation Center 1', coords: [10.730668, 122.560212] },
            { name: 'Evacuation Center 2', coords: [10.733978, 122.557465] },
            { name: 'Evacuation Center 3', coords: [10.733936, 122.561885] }
        ];

        // Initialize Leaflet map
        var map = L.map('map').setView([latitude, longitude], 15);
        L.tileLayer('https://api.maptiler.com/maps/streets-v2/256/{z}/{x}/{y}.png?key=ngrPpvE2X0m7KBaoLLex', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
        }).addTo(map);

        // Add resident's marker
        L.marker([latitude, longitude]).addTo(map);

        // Add evacuation centers markers and calculate distances
        evacCenters.forEach(function(center) {
            var marker = L.marker(center.coords).addTo(map);
            var distance = L.latLng(latitude, longitude).distanceTo(marker.getLatLng());
            marker.bindPopup('Distance to ' + center.name + ': ' + distance.toFixed(2) + ' meters').openPopup();
        });
    </script>
</body>
</html>
