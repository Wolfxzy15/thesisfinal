<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['registerEvacCenter'])) {
        include 'db.php'; // Ensure the database connection is included

        // Retrieve input data and validate
        $evacName = !empty($_POST['evacName']) ? mysqli_real_escape_string($conn, $_POST['evacName']) : null;
        $height = !empty($_POST['height']) ? (float)$_POST['height'] : null;
        $width = !empty($_POST['width']) ? (float)$_POST['width'] : null;
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        $evacAddress = !empty($_POST['evacAddress']) ? mysqli_real_escape_string($conn, $_POST['evacAddress']) : null;
        $landmark = !empty($_POST['landmark']) ? mysqli_real_escape_string($conn, $_POST['landmark']) : null;
        $max = round((($width * 39.3701) / 50) * (($height * 39.3701) / 105));

        if ($evacName && $height && $width && $latitude && $longitude) {
            // Insert new evacuation center into tbl_evac_centers
            $sql_site = "INSERT INTO tbl_evac_centers (evacName, height, width, latitude, longitude, evacAddress, max_capacity, landmark)
                    VALUES ('$evacName', $height, $width, $latitude, $longitude, '$evacAddress', '$max', '$landmark')";
            
            if (mysqli_query($conn, $sql_site)) {
                $message = 'success';
            } else {
                $message = 'error'; // Error saving into tbl_evac_centers
            }
        } else {
            $message = 'incomplete';
        }

        mysqli_close($conn); // Ensure the database connection is closed
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evacuation Center</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <main>
        <div class="container2">
        <h2>Register New Evacuation Center</h2>
        <div id="map" class="map-container2" style="height: 570px">
    
        </div>
<br>
        <!-- Form for registering new evacuation centers -->
        <form action="" method="post">
            <div class="form-group">
                <label for="evacName">Evacuation Center Name:</label>
                <input type="text" id="evacName" name="evacName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="height">Height (meters):</label>
                <input type="number" id="height" name="height" placeholder="meters" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="width">Width (meters):</label>
                <input type="number" id="width" name="width" placeholder="meters" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="landmark">Landmark:</label>
                <input type="text" id="landmark" name="landmark" class="form-control">
            </div>
            <div class="form-group">
                <label for="latitude">Latitude:</label>
                <input type="number" id="latitude" name="latitude" class="form-control" readonly required>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude:</label>
                <input type="number" id="longitude" name="longitude" class="form-control" readonly required>
            </div>
            <div class="form-group">
                <label for="evacAddress">Address:</label>
                <input type="text" id="evacAddress" name="evacAddress" class="form-control" readonly required>
            </div>
            <button type="submit" class="btn btn-primary" name="registerEvacCenter">Register Evacuation Center</button>
        </form>
        </div>
        <!-- Existing Form for distances and view location -->

        <?php include 'mapFunction2.php'; ?>
    </main>
</body>
<script>
        <?php if (isset($message) && $message == 'incomplete'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Form Incomplete',
                text: 'Please fill up the form completely!',
                confirmButtonText: 'OK'
            });
        <?php elseif (isset($message) && $message == 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Evacuation Center Registered successfully!',
                confirmButtonText: 'OK'
            });
        <?php elseif (isset($message) && $message == 'error'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'There was an error registering the evacuation center.',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</html>
