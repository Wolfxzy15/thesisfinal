`<?php
session_start();
$residentID = $_SESSION['residentID'];
$fname = $_SESSION['fName'];
$lastName = $_SESSION['lastName'];
include 'include/db.php';

echo $fname;
echo $lastName;

$sql = "SELECT latitude, longitude FROM tbl_residents WHERE residentID = $residentID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $latitude = $row["latitude"];
    $longitude = $row["longitude"];

    // Add this line for debugging
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {position: absolute; top: 50px; bottom: 50px; left: 50%; right: 50px;}
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id='map'>
    <script>
        var x = <?php echo json_encode($latitude); ?>;
        var y = <?php echo json_encode($longitude); ?>;
        var evac1_long = 10.730668;
        var evac1_lat = 122.560212;
        var evac2_long =10.733978;
        var evac2_lat = 122.557465;
        var evac3_long = 10.733936;
        var evac3_lat = 122.561885;
        var evac1_distance, evac2_distance, evac3_distance;



        var map = L.map('map').setView([10.731019, 122.558467], 15);
        L.tileLayer('https://api.maptiler.com/maps/streets-v2/256/{z}/{x}/{y}.png?key=ngrPpvE2X0m7KBaoLLex', {
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTile  r</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
        }).addTo(map);

        // Red marker icon
        var redIcon = L.icon({
            iconUrl: 'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        // Add red marker
        var redMarker = L.marker([x, y], {icon: redIcon}).addTo(map);

        // Other markers
        var markers = [
            {coords: [evac1_long, evac1_lat], name: 'Marker 1'},
            {coords: [evac2_long, evac2_lat], name: 'Marker 2'},
            {coords: [evac3_long, evac3_lat], name: 'Marker 3'}
        ];

        // Calculate distance between red marker and other markers
        markers.forEach(function(markerData) {
            var marker = L.marker(markerData.coords).addTo(map);
            var distance = redMarker.getLatLng().distanceTo(marker.getLatLng());
            marker.bindPopup('Distance to ' + markerData.name + ': ' + distance.toFixed(2) + ' meters').openPopup();

            if (markerData.name === 'Marker 1') {
                evac1_distance = parseFloat(distance.toFixed(2));
            } else if (markerData.name === 'Marker 2') {
                evac2_distance = parseFloat(distance.toFixed(2));
            } else if (markerData.name === 'Marker 3') {
                evac3_distance = parseFloat(distance.toFixed(2));
            }
        });
    </script>
    </div>
    <form method="post">
    <div style="display: inline-block;" name="evac">
    
    <table class="table" >
  <thead>
    <tr>
      <th scope="col">Evacuation Center</th>
      <th scope="col">Distance</th>
    
    </tr>
    
  </thead>
  
    <tr>
      <th scope="row">1</th>
      <td><input type="text" id="evac1" name="evac1" class="readonly-input" readonly></td>
      <script>
    document.getElementById('evac1').value = evac1_distance;
  </script>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td><input type="text" id="evac2" name="evac2" class="readonly-input" readonly></td>
      <script>
    document.getElementById('evac2').value = evac2_distance;
  </script>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td><input type="text" id="evac3" name="evac3" class="readonly-input" readonly></td>
      <script>
    document.getElementById('evac3').value = evac3_distance;
  </script>
    </tr>   
  </tbody>
</table>
<button class="btn btn-success" type="submit" name="mainregister">Register!</button>
</form>
        <script>
            document.write('The nearest evacuation is ' + Math.min(evac1_distance, evac2_distance, evac3_distance) + ' meters away');
        </script><br>   
    </div>
    
    
</body>
</html>

<?php


if (!isset($_SESSION['residentID'])) {
    die("Session residentID not set.");
}

$residentID = $_SESSION['residentID'];
include 'include/db.php';

$sql = "SELECT COUNT(*) AS row_count from evac1";
$sql = "SELECT COUNT(*) AS row_count FROM evac2;";
$sql = "SELECT COUNT(*) AS row_count from evac3";

function getRowCount($conn, $tableName) {
    $sql = "SELECT COUNT(*) AS row_count FROM $tableName";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["row_count"];
    }
    return 0;
}

// Get row counts from the tables
$evac1_count = getRowCount($conn, 'evac1');
$evac2_count = getRowCount($conn, 'evac2');
$evac3_count = getRowCount($conn, 'evac3');

echo "Evacuation Center 1 Capacity: " . $evac1_count . "/5<br>";
echo "Evacuation Center 2 Capacity: " . $evac2_count . "/5<br>";
echo "Evacuation Center 3 Capacity: " . $evac3_count . "/5<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables
    $evacuation1 = isset($_POST['evac1']) ? $_POST['evac1'] : null;
    $evacuation2 = isset($_POST['evac2']) ? $_POST['evac2'] : null;
    $evacuation3 = isset($_POST['evac3']) ? $_POST['evac3'] : null;

    // Check if the form was submitted for registration
    if (isset($_POST['mainregister'])) {
        // Determine which evacuation center is nearest
        $nevacuation = array($evacuation1, $evacuation2, $evacuation3);
        sort($nevacuation);


        // Directly insert values into SQL query
        if ($nevacuation[0] == $evacuation1) {
            if ($evac1_count < 5) {
                $sql = "INSERT INTO evac1 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
            } else if ($nevacuation[1] == $evacuation2){
                if ($evac2_count < 5){
                    $sql = "INSERT INTO evac2 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                } else if ($nevacuation[2] == $evacuation3){
                    if ($evac3_count < 5){
                        $sql = "INSERT INTO evac3 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                    }
                }
            } else if ($nevacuation[1] == $evacuation3){
                if ($evac3_count < 5){
                    $sql = "INSERT INTO evac3 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                } else if ($nevacuation[2] == $evacuation2){
                    if ($evac2_count < 5){
                        $sql = "INSERT INTO evac2 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                    }
                }
            } 
            
        } else if ($nevacuation[0] == $evacuation2) {
            if ($evac2_count < 5) {
                $sql = "INSERT INTO evac2 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
            } else if ($nevacuation[1] == $evacuation1){
                if ($evac1_count < 5){
                    $sql = "INSERT INTO evac1 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                } else if ($nevacuation[2] == $evacuation3){
                    if ($evac3_count < 5){
                        $sql = "INSERT INTO evac3 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                    }
                }
            } else if ($nevacuation[1] == $evacuation3){
                if ($evac3_count < 5){
                    $sql = "INSERT INTO evac3 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                } else if ($nevacuation[2] == $evacuation1){
                    if ($evac1_count < 5){
                        $sql = "INSERT INTO evac1 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                    }
                }
            } 
        } else if ($nevacuation[0] == $evacuation3) {
            if ($evac3_count < 5) {
                $sql = "INSERT INTO evac3 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
            } else if ($nevacuation[1] == $evacuation1){
                if ($evac1_count < 5){
                    $sql = "INSERT INTO evac1 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                } else if ($nevacuation[2] == $evacuation2){
                    if ($evac2_count < 5){
                        $sql = "INSERT INTO evac2 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                    }
                }
            } else if ($nevacuation[1] == $evacuation2){
                if ($evac2_count < 5){
                    $sql = "INSERT INTO evac2 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                } else if ($nevacuation[2] == $evacuation1){
                    if ($evac1_count < 5){
                        $sql = "INSERT INTO evac1 (residentID, fname, lastName) VALUES ('$residentID', '$fname', '$lastName')";
                    }
                }
            } 
        } else {
            echo "All evacuation centers are full";
        }

        // Check if SQL statement is valid
        if ($sql !== "") {
            if ($conn->query($sql) === TRUE) {
                echo "Registration successful.";
            } else {
                echo "Error executing SQL: " . $conn->error;
            }
        } else {
            echo "No valid SQL statement found.";
        }
    }
} else {
    echo "Not yet registered.";
}

$sql1 = "SELECT residentid, fname, lastname FROM evac1";
$sql2 = "SELECT residentid, fname, lastname FROM evac2";
$sql3 = "SELECT residentid, fname, lastname FROM evac3";

// Execute SQL queries
$result1 = $conn->query($sql1);
$result2 = $conn->query($sql2);
$result3 = $conn->query($sql3);

// Function to generate table output
function generateTable($result, $tableId) {
    if ($result->num_rows > 0) {
        echo '<table id="' . $tableId . '" class="table table-sm">';
        echo '<thead>
                <tr>
                    <th scope="col">Resident ID</th>
                    <th scope="col">First Name</th>
                    <th scope="col">Last Name</th>
                </tr>
            </thead>
            <tbody>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                    <td>' . $row["residentid"] . '</td>
                    <td>' . $row["fname"] . '</td>
                    <td>' . $row["lastname"] . '</td>
                </tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No results for ' . $tableId . '</p>';
    }
}

// Output each table vertically aligned to the left
echo '<div class="container-fluid">';
echo '<div class="row">';
echo '<div class="col-md-4">';
generateTable($result1, "evac1");
echo '</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-md-4">';
generateTable($result2, "evac2");
echo '</div>';
echo '</div>';

echo '<div class="row">';
echo '<div class="col-md-4">';
generateTable($result3, "evac3");
echo '</div>';
echo '</div>';
echo '</div>';


$conn->close();
?>
