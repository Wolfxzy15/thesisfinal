<?php 
session_start();
include 'db.php'; // Connect to the database

// Default coordinates for Brgy. Tabuc Suba, Jaro, Iloilo City
$defaultLat = 10.7344;
$defaultLong = 122.5580;

// SQL query to get evacuation center details and their status
$sql = "
SELECT evac.evacID, evac.evacName, evac.max_capacity, 
    IFNULL(SUM(fam.num_members), 0) AS current_capacity, 
    CASE 
        WHEN IFNULL(SUM(fam.num_members), 0) >= evac.max_capacity THEN 'Full'
        WHEN IFNULL(SUM(fam.num_members), 0) >= (evac.max_capacity * 0.8) THEN 'Almost Full'
        ELSE 'Available'
    END AS status,
    evac.latitude, evac.longitude, evac.height, evac.width
FROM tbl_evac_centers evac
LEFT JOIN tbl_families fam ON evac.evacID = fam.evacID
GROUP BY evac.evacID";

$result = mysqli_query($conn, $sql);

$evacData = []; // Store evacuation centers data
while ($row = mysqli_fetch_assoc($result)) {
    $evacData[] = $row;
}

// Handle update evacuation center request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $evacID = $_POST['evacID'];
    $evacName = $_POST['evacName'];
    $evacHeight = $_POST['evacHeight'];
    $evacWidth = $_POST['evacWidth'];

    // Calculate the max capacity based on the given formula
    $maxCapacity = round((($evacWidth * 39.3701) / 50) * (($evacHeight * 39.3701) / 105));


    $updateSql = "UPDATE tbl_evac_centers SET evacName = ?, height = ?, width = ?, max_capacity = ? WHERE evacID = ?";
    if ($stmt = mysqli_prepare($conn, $updateSql)) {
        mysqli_stmt_bind_param($stmt, 'ssiii', $evacName, $evacHeight, $evacWidth, $maxCapacity, $evacID);
        if (mysqli_stmt_execute($stmt)) {
            echo "";
        } else {
            echo "";
        }
    }
}

// Handle delete evacuation center request
// Handle delete evacuation center request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $evacID = $_POST['evacID'];

    // First, update all families with the given evacID to set evacID to 0
    $updateFamiliesSql = "UPDATE tbl_families SET evacID = 0 WHERE evacID = $evacID";
    if (mysqli_query($conn, $updateFamiliesSql)) {
        // After updating families, delete the evacuation center
        $deleteSql = "DELETE FROM tbl_evac_centers WHERE evacID = $evacID";
        if (mysqli_query($conn, $deleteSql)) {
            echo "<script>alert('Evacuation Center and related family assignments deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting Evacuation Center');</script>";
        }
    } else {
        echo "<script>alert('Error updating families');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Map Evacuation Center</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-Df2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<main>
    <div class="container1">
        <h2>Evacuation Centers in Iloilo City</h2>
        <div id='map' style="height: 600px;"></div>
    </div>

    <br>
    <table class="table table-light">
        <thead>
            <tr>
                <th scope="col">LEGEND</th>
                <th scope="col">Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <th scope="row">
                <img src="images/building-solid-green.svg" alt="Building Icon" style="width: 20px; height: 20px;">
            </th>
                <td>Available</td>
            </tr>
            <tr>
            <th scope="row">
                <img src="images/building-solid-orange.svg" alt="Building Icon" style="width: 20px; height: 20px;">
            </th>
                <td>Almost Full</td>
            </tr>
            <tr>
            <th scope="row">
                <img src="images/building-solid-red.svg" alt="Building Icon" style="width: 20px; height: 20px;">
            </th>
                <td>Full</td>
            </tr>
        </tbody>
    </table>

    <div class="container1">
        <h2>Evacuation Site Status</h2>
        <?php if ($result): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Evacuation Center</th>
                        <th>Max Capacity</th>
                        <th>Current Capacity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evacData as $row): ?>
                        <tr>
                            <td><?= $row['evacName']; ?></td>
                            <td><?= $row['max_capacity']; ?></td>
                            <td><?= $row['current_capacity']; ?></td>
                            <td>
                                <?php 
                                if ($row['status'] === 'Full') {
                                    echo '<span style="color:red">Full</span>';
                                } elseif ($row['status'] === 'Almost Full') {
                                    echo '<span style="color:orange">Almost Full</span>';
                                } else {
                                    echo '<span style="color:green">Available</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <!-- Update Form -->
                                <button class="btn btn-warning" data-toggle="modal" data-target="#updateModal" onclick="populateUpdateForm(<?= $row['evacID']; ?>, '<?= $row['evacName']; ?>', <?= $row['height']; ?>, <?= $row['width']; ?>)">Update</button>
                                <!-- Delete Form -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="evacID" value="<?= $row['evacID']; ?>">
                                    <button class="btn btn-danger" type="submit" name="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No evacuation centers found.</p>
        <?php endif; ?>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Update Evacuation Center</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="evacName">Evacuation Center Name</label>
                            <input type="text" class="form-control" id="evacName" name="evacName" required>
                        </div>
                        <div class="form-group">
                            <label for="evacHeight">Height</label>
                            <input type="number" class="form-control" id="evacHeight" name="evacHeight" required>
                        </div>
                        <div class="form-group">
                            <label for="evacWidth">Width</label>
                            <input type="number" class="form-control" id="evacWidth" name="evacWidth" required>
                        </div>
                        <input type="hidden" id="evacID" name="evacID">
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="geojson.js"></script>
<script>
var map = L.map('map').setView([<?= $defaultLat ?>, <?= $defaultLong ?>], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);


        // Function to get the appropriate icon URL based on the status
        function getStatusIcon(status) {
            if (status === 'Full') {
                return 'images/building-solid-red.svg'; // Red icon for full
            } else if (status === 'Almost Full') {
                return 'images/building-solid-orange.svg'; // Orange icon for almost full
            } else {
                return 'images/building-solid-green.svg'; // Green icon for available
            }
        }

        // Add markers for each evacuation center
        <?php foreach ($evacData as $evac): ?>
            var statusIcon = getStatusIcon("<?= $evac['status']; ?>");

            L.marker([<?= $evac['latitude']; ?>, <?= $evac['longitude']; ?>], {
                icon: L.icon({
                    iconUrl: statusIcon,
                    iconSize: [35, 35], // Size of the marker
                    iconAnchor: [17, 35], // Position of the marker
                    popupAnchor: [0, -35], // Position of the popup
                    shadowSize: [0, 0], // Remove shadow
                })
            }).addTo(map)
              .bindPopup("<b><?= $evac['evacName']; ?></b><br>Capacity: <?= $evac['max_capacity']; ?>");

        <?php endforeach; ?>

        // Function to populate the update form
        function populateUpdateForm(evacID, evacName, evacHeight, evacWidth) {
            document.getElementById('evacID').value = evacID;
            document.getElementById('evacName').value = evacName;
            document.getElementById('evacHeight').value = evacHeight;
            document.getElementById('evacWidth').value = evacWidth;
        }
    </script>
</main>
</body>
</html>
