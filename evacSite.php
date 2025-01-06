<?php
include 'db.php'; // Connect to the database

// Function to calculate distance using Haversine Formula
function haversine($lat1, $lon1, $lat2, $lon2)
{
    $earth_radius = 6371; // Earth radius in kilometers

    // Convert latitude and longitude from degrees to radians
    $lat1 = (float) $lat1;
    $lon1 = (float) $lon1;
    $lat2 = (float) $lat2;
    $lon2 = (float) $lon2;

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earth_radius * $c; // Distance in kilometers
}

$sql = "SELECT evac.evacID, evac.evacName, evac.max_capacity, 
               COUNT(res.residentID) AS current_capacity, 
               (evac.max_capacity <= COUNT(res.residentID)) AS is_full, 
               GROUP_CONCAT(fam.family_id) AS family_ids,
               COUNT(res.residentID) AS member_count
        FROM tbl_evac_centers evac
        LEFT JOIN tbl_families fam ON evac.evacID = fam.evacID
        LEFT JOIN tbl_residents res ON fam.family_id = res.family_id
        GROUP BY evac.evacID";


$result = mysqli_query($conn, $sql);


// Check if the family data is received
if (isset($_GET['family_id']) && isset($_GET['latitude']) && isset($_GET['longitude'])) {
    $familyID = $_GET['family_id'];
    $familyLat = (float) $_GET['latitude'];
    $familyLong = (float) $_GET['longitude'];

    // Query evacuation centers
    $sql = "SELECT evacID, evacName, latitude, longitude, max_capacity, current_capacity FROM tbl_evac_centers";
    $evacCenters = mysqli_query($conn, $sql);

    $evacData = []; // Store evacuation centers data
    $nearestEvac = null; // Track the nearest evacuation center
    $minDistance = PHP_FLOAT_MAX; // Initialize the minimum distance to a very large number

    while ($row = mysqli_fetch_assoc($evacCenters)) {
        $evacLat = (float) $row['latitude'];
        $evacLong = (float) $row['longitude'];
        $distance = haversine($familyLat, $familyLong, $evacLat, $evacLong);

        // Store evacuation data with calculated distance
        $evacData[] = array_merge($row, ['distance' => $distance]);

        // Find the nearest evacuation center that is not full
        if ($distance < $minDistance && $row['current_capacity'] < $row['max_capacity']) {
            $minDistance = $distance;
            $nearestEvac = $row;
        }
    }

    // Check if the nearest evacuation center is full
    if (!$nearestEvac) {
        $evacData = array_filter($evacData, function ($evac) {
            return $evac['current_capacity'] < $evac['max_capacity'];
        });

        usort($evacData, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $nearestEvac = $evacData[0] ?? null;
    }

    // Check if family is already registered in any evacuation center
    $checkRegistrationQuery = "SELECT evacID FROM tbl_families WHERE family_id = '$familyID' AND evacID > 0";
    $checkRegistrationResult = mysqli_query($conn, $checkRegistrationQuery);
    $isRegistered = mysqli_num_rows($checkRegistrationResult) > 0;

    if ($isRegistered) {
        // Fetch the assigned evacuation center if already registered
        $assignedEvacQuery = "SELECT evacID, evacName, latitude, longitude FROM tbl_evac_centers WHERE evacID = (SELECT evacID FROM tbl_families WHERE family_id = '$familyID')";
        $assignedEvacResult = mysqli_query($conn, $assignedEvacQuery);
        $assignedEvac = mysqli_fetch_assoc($assignedEvacResult);
    }

    if (isset($_POST['register']) && isset($_POST['evac_id'])) {
        $newEvacID = $_POST['evac_id'];

        // Get the number of residents already in the selected evacuation center
        $current_capacity_query = "SELECT current_capacity, max_capacity FROM tbl_evac_centers WHERE evacID = '$newEvacID'";
        $current_capacity_result = mysqli_query($conn, $current_capacity_query);
        $current_capacity_row = mysqli_fetch_assoc($current_capacity_result);

        // Get the current number of residents in the evacuation center
        $current_capacity = $current_capacity_row['current_capacity'];
        $max_capacity = $current_capacity_row['max_capacity'];

        // Get the number of residents in the family being registered
        $resident_count_query = "SELECT COUNT(residentID) AS family_member_count FROM tbl_residents WHERE family_id = '$familyID'";
        $resident_count_result = mysqli_query($conn, $resident_count_query);
        $resident_count_row = mysqli_fetch_assoc($resident_count_result);
        $family_member_count = $resident_count_row['family_member_count'];

        // Calculate the total capacity after registration
        $total_capacity = $current_capacity + $family_member_count;

        // Check if the registration would exceed the maximum capacity
        if ($total_capacity <= $max_capacity) {
            // Update the family's evacID to the new center
            $update_family_sql = "UPDATE tbl_families SET evacID = '$newEvacID' WHERE family_id = '$familyID'";
            if (mysqli_query($conn, $update_family_sql)) {
                // Increase the capacity of the new evacuation center
                $add_capacity_sql = "UPDATE tbl_evac_centers SET current_capacity = current_capacity + $family_member_count WHERE evacID = '$newEvacID'";
                mysqli_query($conn, $add_capacity_sql);
                $success = true;
            } else {
                echo "Error updating the family: " . mysqli_error($conn);
                $success = false;
            }
        } else {
            $success = false;
        }
    }
} else {
    echo "Family data not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0JLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    
</head>

<body>
    <?php include 'include/sidebar.php'; ?>
    <main>
        <div class="container1">

            <h2>Evacuation Site Family Registration</h2>

            <!-- Show the nearest evacuation center and distance -->
            <p>Nearest Evacuation Center: <strong><?= $nearestEvac['evacName']; ?></strong></p>
            <p>Distance: <strong><?= round($minDistance, 2); ?> km</strong></p>

            <div id='map'></div>
            <br>

            <!-- Disable register button if already registered -->
            <form method="POST">
                <input type="hidden" name="family_id" value="<?= $familyID; ?>">
                <select name="evac_id" id="evacSelect" style="pointer-events: none;" readonly>
                    <option value="<?= $nearestEvac['evacID']; ?>">Nearest Evacuation Center (<?= round($minDistance, 2); ?> km)</option>
                    <?php foreach ($evacData as $evac) { ?>
                        <option value="<?= $evac['evacID']; ?>"><?= $evac['evacName']; ?> (<?= round($evac['distance'], 2); ?> km)</option>
                    <?php } ?>
                </select>
                <button type="submit" name="register" <?= $isRegistered ? 'disabled' : ''; ?>>
                    <?= $isRegistered ? 'Already Registered' : 'Register'; ?>
                </button>
            </form>
        </div><br>
        <div class="container1">
            <h2>Evacuation Site Status</h2>

            <?php if ($result): ?>
                <table class="table ">
                    <thead>
                        <tr>
                            <th>Evacuation Center</th>
                            <th>Max Capacity</th>
                            <th>Current Capacity -Families</th>
                            <th>Status</th>
                            <th>Total No. of Residents</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($evac = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $evac['evacName']; ?></td>
                                <td><?= $evac['max_capacity']; ?></td>
                                <td><?= $evac['current_capacity']; ?></td>
                                <td class="<?= $evac['is_full'] ? 'text-danger' : 'text-success'; ?>">
                                    <?= $evac['is_full'] ? 'Full' : 'Available'; ?>
                                </td>
                                <td><?= $evac['member_count']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No evacuation centers found.</p>
            <?php endif; ?>

        </div>
    </main>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
    // Family and evacuation center coordinates
    var familyLat = <?= $familyLat; ?>;
    var familyLong = <?= $familyLong; ?>;
    var evacLat = <?= isset($assignedEvac) ? $assignedEvac['latitude'] : $nearestEvac['latitude']; ?>;
    var evacLong = <?= isset($assignedEvac) ? $assignedEvac['longitude'] : $nearestEvac['longitude']; ?>;
    var evacName = "<?= isset($assignedEvac) ? $assignedEvac['evacName'] : $nearestEvac['evacName']; ?>";
    // Initialize the map
    var map = L.map('map').setView([familyLat, familyLong], 18);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // Family marker
    var familyIcon = L.icon({
        iconUrl: "images/shelter.svg", 
        iconSize: [32, 32]
    });
    L.marker([familyLat, familyLong], { icon: familyIcon }).addTo(map)
        .bindPopup("Family Location")
        .openPopup();

    // Evacuation center marker
    var evacIcon = L.icon({
        iconUrl: "images/building-solid.svg", 
        iconSize: [32, 32]
    });
    L.marker([evacLat, evacLong], { icon: evacIcon }).addTo(map)
        .bindTooltip(evacName);

    // Draw route using Leaflet Routing Machine
    try {
        L.Routing.control({
            waypoints: [
                L.latLng(familyLat, familyLong),
                L.latLng(evacLat, evacLong)
            ],
            routeWhileDragging: true,
            createMarker: function (i, waypoint, n) {
                return L.marker(waypoint.latLng, {
                    icon: i === 0 ? familyIcon : evacIcon
                });
            },
            lineOptions: {
                styles: [{ color: 'green', weight: 5 }]
            }
        }).addTo(map);
    } catch (error) {
        console.error('Error with Leaflet Routing Machine:', error);
        alert('Routing Machine failed to load. Please check your configuration.');
    }
    </script>

    <script>
    // Add SweetAlert notifications for registration success or failure
    <?php if (isset($success)): ?>
        <?php if ($success): ?>
            Swal.fire({
                title: 'Registration Successful!',
                text: 'You have been successfully registered to the evacuation center.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'displayFamilies.php';
            });
        <?php else: ?>
            Swal.fire({
                title: 'Registration Failed!',
                text: 'There was an issue registering your family due to capacity limits.',
                icon: 'error',
                confirmButtonText: 'Try Again'
            });
        <?php endif; ?>
    <?php endif; ?>
    </script>
</body>

</html>
