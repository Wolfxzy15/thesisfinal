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
    $maxCapacity = ceil(($evacWidth * 39.3701) / 50) * ceil(($evacHeight * 39.3701) / 105);

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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $evacID = $_POST['evacID'];
    $deleteSql = "DELETE FROM tbl_evac_centers WHERE evacID = ?";
    if ($stmt = mysqli_prepare($conn, $deleteSql)) {
        mysqli_stmt_bind_param($stmt, 'i', $evacID);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Evacuation Center Deleted Successfully');</script>";
        } else {
            echo "<script>alert('Error deleting Evacuation Center');</script>";
        }
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
<?php include 'include/sidebar.php'; ?>

<main>
    <div class="container1">
        <h2>Evacuation Centers in Brgy. <?php echo $_SESSION['username']?>, Jaro, Iloilo City</h2>
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
                <th scope="row"><i class="fa-solid fa-building"></i></th>
                <td>- Evacuation Centers</td>
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

    <script>
        // Initialize Leaflet map
        // var map = L.map('map').setView([], 13); // Default to Brgy. Tabuc Suba

    //     // Add OpenStreetMap tile layer
    //     L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //         attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    //     }).addTo(map);

    //     var tabucSubaBoundary = [
    //     [10.743164, 122.553640], [10.739382, 122.559256],[10.738431, 122.558515],[10.737611, 122.559689],[10.737120, 122.559314],
    //     [10.735866, 122.561052],[10.732595, 122.559950],[10.732244, 122.560343], [10.731820, 122.560315],[10.728212, 122.564755],
    //     [10.724658, 122.562600], [10.724254, 122.561318],[10.725630, 122.559709],[10.729595, 122.558438],[10.731196, 122.557518], 
    //     [10.731354, 122.556414],[10.732155, 122.553148],[10.732805, 122.551697],
    //     [10.743150, 122.553617] 
    // ];

// Initialize Leaflet map with default center coordinates
var map = L.map('map').setView([<?= $defaultLat ?>, <?= $defaultLong ?>], 13);

// Add OpenStreetMap tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);


    fetch('geojson/tabucsuba.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        L.geoJSON(geojsonData, {
            style: function (feature) {
                return { color: "#ff0000", weight: 2 };
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup(feature.properties.name);
                }
            }
        }).addTo(map);
    })

    fetch('geojson/cubay.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        L.geoJSON(geojsonData, {
            style: function (feature) {
                return { color: "#ff0000", weight: 2 };
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup(feature.properties.name);
                }
            }
        }).addTo(map);
    })

    fetch('geojson/sanisidro.geojson')
    .then(response => response.json())
    .then(geojsonData => {
        L.geoJSON(geojsonData, {
            style: function (feature) {
                return { color: "#ff0000", weight: 2 };
            },
            onEachFeature: function (feature, layer) {
                if (feature.properties && feature.properties.name) {
                    layer.bindPopup(feature.properties.name);
                }
            }
        }).addTo(map);
    })




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
