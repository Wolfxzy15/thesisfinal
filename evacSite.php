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
    IFNULL(SUM(fam.num_members), 0) AS current_capacity, 
    CASE 
        WHEN IFNULL(SUM(fam.num_members), 0) >= evac.max_capacity THEN 'Full'
        WHEN IFNULL(SUM(fam.num_members), 0) >= (evac.max_capacity * 0.8) THEN 'Almost Full'
        ELSE 'Available'
    END AS status,
    CASE
        WHEN IFNULL(SUM(fam.num_members), 0) >= evac.max_capacity THEN 1
        ELSE 0
    END AS is_full,  -- Add this line
    evac.latitude, evac.longitude, evac.height, evac.width
FROM tbl_evac_centers evac
LEFT JOIN tbl_families fam ON evac.evacID = fam.evacID
GROUP BY evac.evacID;
";

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

    // Handle family registration
    if (isset($_POST['register']) && isset($_POST['evac_id'])) {
        $newEvacID = $_POST['evac_id'];
    
        // Get current evacuation center for the family
        $current_evac_query = "SELECT evacID FROM tbl_families WHERE family_id = '$familyID'";
        $current_evac_result = mysqli_query($conn, $current_evac_query);
        $current_evac_row = mysqli_fetch_assoc($current_evac_result);
        $currentEvacID = $current_evac_row['evacID'];
    
        // Fetch the family size
        $family_query = "SELECT num_members FROM tbl_families WHERE family_id = '$familyID'";
        $family_result = mysqli_query($conn, $family_query);
        $family_row = mysqli_fetch_assoc($family_result);
        $numMembers = $family_row['num_members'];
    
        // Subtract the family size from the current evacuation center's capacity
        if ($currentEvacID) {
            $subtract_capacity_sql = "UPDATE tbl_evac_centers SET current_capacity = current_capacity - $numMembers WHERE evacID = '$currentEvacID'";
            mysqli_query($conn, $subtract_capacity_sql);
        }
    
        // Update the family's evacID to the new center
        $update_family_sql = "UPDATE tbl_families SET evacID = '$newEvacID' WHERE family_id = '$familyID'";
        if (mysqli_query($conn, $update_family_sql)) {
            // Add the family size to the new evacuation center's capacity
            $add_capacity_sql = "UPDATE tbl_evac_centers SET current_capacity = current_capacity + $numMembers WHERE evacID = '$newEvacID'";
            mysqli_query($conn, $add_capacity_sql);
    
            $success = true;
        } else {
            echo "Error updating the family: " . mysqli_error($conn);
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
            <form method="POST">
                <input type="hidden" name="family_id" value="<?= $familyID; ?>">
                <select name="evac_id" id="evacSelect" required>
                    <option value="<?= $nearestEvac['evacID']; ?>">Nearest Evacuation Center (<?= round($minDistance, 2); ?> km)</option>
                    <?php foreach ($evacData as $evac) { ?>
                        <option value="<?= $evac['evacID']; ?>"><?= $evac['evacName']; ?> (<?= round($evac['distance'], 2); ?> km)</option>
                    <?php } ?>
                </select>
                <button type="submit" name="register">Register</button>
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
                            <th>Current Capacity</th>
                            <th>Status</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['evacName']; ?></td>
                                <td><?= $row['max_capacity']; ?></td>
                                <td><?= $row['current_capacity']; ?></td>
                                <td><?= $row['is_full'] ? '<span style=color:red>Full</span>' : '<span style=color:green>Available</span>'; ?></td>

                                
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No evacuation centers found.</p>
            <?php endif; ?>
        </div>

        <script>
            var familyLat = <?= $familyLat; ?>;
            var familyLong = <?= $familyLong; ?>;
            var evacCenters = <?= json_encode($evacData); ?>;
            var familyID = <?= json_encode($familyID); ?>;
            var success = <?= isset($success) && $success ? 'true' : 'false'; ?>;
            var familyIcon = L.icon({
                iconUrl: 'images/home2.svg', 
                iconSize: [32, 32],              
                iconAnchor: [16, 32],            
                popupAnchor: [0, -32]           
            });


            var map = L.map('map').setView([familyLat, familyLong], 18);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);


            L.marker([familyLat, familyLong], {icon: familyIcon}).addTo(map)
                .bindPopup("Family: " + familyID + " Location")
                .openPopup();

                var tabucSubaBoundary = [
                    [10.743164, 122.553640], [10.739382, 122.559256],[10.738431, 122.558515],[10.737611, 122.559689],[10.737120, 122.559314],
                    [10.735866, 122.561052],[10.732595, 122.559950],[10.732244, 122.560343], [10.731820, 122.560315],[10.728212, 122.564755],
                    [10.724658, 122.562600], [10.724254, 122.561318],[10.725630, 122.559709],[10.729595, 122.558438],[10.731196, 122.557518], 
                    [10.731354, 122.556414],[10.732155, 122.553148],[10.732805, 122.551697],
                    [10.743150, 122.553617] 
                ];

                var CubayBoundary = [
                [10.736462, 122.561303],[10.735294, 122.563511],[10.735716, 122.563897],[10.735427, 122.564427],[10.735695, 122.564720],
                [10.735431, 122.565215],[10.734709, 122.564683],[10.734431, 122.565041],[10.734149, 122.564873],[10.733706, 122.565522],
                [10.735877, 122.567353],[10.735615, 122.567721],[10.736652, 122.568487],[10.736349, 122.568867],[10.736151, 122.568721],
                [10.735383, 122.569882],[10.734911, 122.569978],[10.733453, 122.567136],[10.732947, 122.567606],[10.729133, 122.572549],
                [10.720910, 122.566068],[10.721580, 122.565784],[10.723093, 122.565627],[10.724181, 122.565713],[10.725195, 122.566019],
                [10.725655, 122.566191],[10.726274, 122.566544],[10.726828, 122.566764],[10.727179, 122.566801],[10.727732, 122.566711],
                [10.728251, 122.566642],[10.728645, 122.566417],[10.728974, 122.566064],[10.729103, 122.565565],[10.729047, 122.565377],
                [10.728768, 122.565133],[10.728221, 122.564758],[10.731838, 122.560306],[10.732259, 122.560373],[10.732591, 122.559947],
                [10.736452, 122.561302], 
                ];

                var sanIsidroBoundary = [
                [10.743134, 122.553613],[10.747129, 122.544828],[10.746827, 122.544916],[10.746575, 122.544881],[10.746411, 122.544781],
                [10.746267, 122.544720], [10.745939, 122.544459],[10.745661, 122.544015],[10.745529, 122.543830],[10.745003, 122.543386],
                [10.744801, 122.543143],[10.744640, 122.543002],[10.743781, 122.542519],[10.742031, 122.541926],[10.741630, 122.541872],
                [10.741354, 122.541916],[10.741158, 122.542053],[10.740701, 122.542223],[10.740578, 122.542416],[10.740549, 122.542814],
                [10.740664, 122.543767],[10.740606, 122.544104],[10.740624, 122.544352],[10.740494, 122.544739],[10.740290, 122.545248],
                [10.739925, 122.545701],[10.739885, 122.545613],[10.740288, 122.545050],[10.740506, 122.544554],[10.740303, 122.544960],
                [10.740096, 122.545273],[10.739868, 122.545480],[10.739618, 122.545616],[10.738867, 122.545753],[10.737806, 122.545870],
                [10.737603, 122.545945],[10.736627, 122.546545],[10.736451, 122.546694],[10.736346, 122.546815],[10.735666, 122.546363],
                [10.735950, 122.546055],[10.735517, 122.545680],[10.735335, 122.545889],[10.734779, 122.545405],[10.734962, 122.545211],
                [10.734602, 122.544899],[10.734520, 122.544987],[10.733106, 122.543822],[10.732993, 122.544049],[10.732823, 122.544119],
                [10.732725, 122.544123],[10.732493, 122.544013],[10.732374, 122.544038],[10.732314, 122.544235],[10.732300, 122.544438],
                [10.732159, 122.544719],[10.731872, 122.545037],[10.731645, 122.545329],[10.731576, 122.545451],[10.731496, 122.545698],
                [10.731410, 122.546335],[10.730700, 122.547108],[10.730600, 122.547137],[10.730440, 122.547127],[10.730102, 122.547046],
                [10.729816, 122.547120],[10.730305, 122.547644],[10.729700, 122.548353],[10.733105, 122.551156],[10.732804, 122.551688],
                [10.743167, 122.553604]

                ];

            var tabucSubaPolygon = L.polygon(tabucSubaBoundary, {
                color: "#3388ff",
                weight: 3,
                fill: true,
            }).addTo(map).bindTooltip("TABUC SUBA", { permanent: true, direction: "center",className: "no-box-label" });

            var cubayPolygon = L.polygon(CubayBoundary, {
            color: "#008000",
            weight: 3,
            fill: true,
            }).addTo(map).bindTooltip("CUBAY", { permanent: true, direction: "center", className: "no-box-label" });

            var sanIsidroPolygon = L.polygon(sanIsidroBoundary, {
                color: "#FFA500",
                weight: 3,
                fill: true,
            }).addTo(map).bindTooltip("SAN ISIDRO", { permanent: true, direction: "center", className: "no-box-label" });

            evacCenters.forEach(function(evac) {
                var evacLat = evac.latitude;
                var evacLong = evac.longitude;
                var evacName = evac.evacName;

                var evacIcon = L.icon({
                    iconUrl: "images/building-solid.svg",
                    iconSize: [32, 32]
                });
                L.marker([evacLat, evacLong], {
                        icon: evacIcon
                    }).addTo(map)
                    .bindTooltip(evacName)
                    .openTooltip();
            });


            if (success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Family has been successfully registered in the evacuation center.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'displayFamilies.php';
                });
            }
        </script>

    </main>
</body>

</html>