<?php
session_start();
$barangay = $_SESSION['username']; // User's barangay or admin

// Barangay mapping
$barangay_map = [
    'tabuc suba' => 1,
    'cubay' => 2,
    'san isidro' => 3,
    'quintin salas' => 4
];

// Check if the user is an admin
$is_admin = ($barangay === 'admin');

// Determine barangay_num based on user session or dropdown
if ($is_admin && isset($_GET['barangay_filter'])) {
    // For admin, get barangay_num from dropdown
    $selected_barangay = $_GET['barangay_filter'];
    $barangay_num = $barangay_map[$selected_barangay] ?? null;
} else {
    // For regular users, use session username
    $barangay_num = $barangay_map[$barangay] ?? null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Families</title>
    <!-- Include your CSS/JS libraries here (Bootstrap, Leaflet, etc.) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
    <?php include 'include/sidebar.php'; ?>
    <main>
        <div class="table-container">
            <!-- Admin Filter Dropdown -->
            <?php if ($is_admin): ?>
                <form class="form-inline mx-auto" method="GET" action="">
                    <label><span style="color:black; font-size: 20px">Select Barangay:</span></label>
                    <select class="form-control ml-2 mr-2" name="barangay_filter" onchange="this.form.submit()">
                        <option value="">All Barangays</option>
                        <?php foreach ($barangay_map as $name => $id): ?>
                            <option value="<?php echo $name; ?>" <?php echo (isset($_GET['barangay_filter']) && $_GET['barangay_filter'] == $name) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php endif; ?>

            <br>
            <div class="table-wrapper">
                <form method="POST">
                    <table class="table table-hover table-bordered table-light">
                        <thead>
                            <tr>
                                <th scope="col">Family ID#</th>
                                <th scope="col">Present Address</th>
                                <th scope="col">Number of Members</th>
                                <th scope="col">Number of PWD</th>
                                <th scope="col">Evacuation Center #</th>
                                <th scope="col">Status</th>
                                <th scope="col">View Family</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'db.php';

                            // Dynamic query based on barangay_num and search
                            $sql = "SELECT f.family_id, f.presentAddress, f.latitude, f.longitude,
                                            COUNT(r.residentID) AS num_members,
                                            SUM(CASE WHEN r.PWD = 'YES' THEN 1 ELSE 0 END) AS num_pwd,
                                            f.evacID, 
                                            SUM(CASE WHEN r.evacStatus = 'Evacuated' THEN 1 ELSE 0 END) AS num_evacuated,
                                            SUM(CASE WHEN r.evacStatus = 'Not Evacuated' THEN 1 ELSE 0 END) AS num_not_evacuated,
                                            SUM(CASE WHEN r.evacStatus = 'Needs Assistance' THEN 1 ELSE 0 END) AS num_needs_assistance
                                    FROM tbl_families f
                                    LEFT JOIN tbl_residents r ON f.family_id = r.family_id";

                            // Filter by barangay ID
                            if ($barangay_num !== null) {
                                $sql .= " WHERE f.barangay_id = '$barangay_num'";
                            }

                            $sql .= " GROUP BY f.family_id";

                            $result = mysqli_query($conn, $sql);

                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $family_id = $row['family_id'];
                                    $presentAddress = $row['presentAddress'];
                                    $num_members = $row['num_members'];
                                    $num_pwd = $row['num_pwd'];
                                    $evacID = $row['evacID'];
                                    $num_evacuated = $row['num_evacuated'];
                                    $num_not_evacuated = $row['num_not_evacuated'];
                                    $num_needs_assistance = $row['num_needs_assistance'];

                                    echo '<tr>
                                        <th scope="row">' . $family_id . '</th>
                                        <td>' . $presentAddress . '</td>
                                        <td>' . $num_members . '</td>
                                        <td>' . $num_pwd . '</td>
                                        <td>' . $evacID . '</td>
                                        <td>
                                            <span class="badge badge-success">Evacuated: ' . $num_evacuated . '</span><br>
                                            <span class="badge badge-danger">Not Evacuated: ' . $num_not_evacuated . '</span><br>
                                            <span class="badge badge-warning">Needs Assistance: ' . $num_needs_assistance . '</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-success">
                                                <a href="viewFamily.php?family_id=' . $family_id . '" class="text-light">VIEW</a>
                                            </button>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
