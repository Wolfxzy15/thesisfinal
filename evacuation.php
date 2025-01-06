<?php
session_start();

// Check if the user is logged in by ensuring the session variable 'username' is set
if (!isset($_SESSION['username'])) {
    // Redirect to login page if the user is not logged in
    header("Location: login.php");
    exit();
}

$usernames = $_SESSION['username']; // User's username from session

// Include the database connection file
include 'db.php';

// Initialize the variable for userType
$userType = '';

// Query to get the user type from the database
$sql = "SELECT userType FROM tbl_register WHERE username = '$usernames'";
$result = mysqli_query($conn, $sql);

// Check if the query is successful and if a row is returned
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userType = $row['userType'];  // Get the user type (admin or user)
} else {
    // Handle error if the query fails or user not found
    echo "Error fetching user type or user not found.";
    exit();
}

// Check if the user is an admin
$is_admin = ($userType === 'admin');

// Fetch evacuation centers
$evacuation_centers = [];
$sql_evac = "SELECT evacID, evacName FROM tbl_evac_centers";
$result_evac = mysqli_query($conn, $sql_evac);
while ($row = mysqli_fetch_assoc($result_evac)) {
    $evacuation_centers[$row['evacName']] = $row['evacID'];
}

// Determine evacID based on admin filter or default
$evac_filter = isset($_GET['evac_filter']) ? $_GET['evac_filter'] : null;
$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : null;

// Handle reset button action
if (isset($_POST['reset_evac_status'])) {
    // Update all residents' evacuation status to 'Not Evacuated'
    $update_sql = "UPDATE tbl_residents SET evacStatus = 'Not Evacuated'";
    $update_result = mysqli_query($conn, $update_sql);

    if ($update_result) {
        // Redirect to the same page to see the updated table
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // Handle error if the query fails
        echo "Error updating evacuation status.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Families</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main>
        <div class="table-container">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
    <!-- Admin Filter Dropdown -->

        <form class="form-inline" method="GET" action="">
            <label><span style="color:black; font-size: 20px">Select Evacuation Center:</span></label>
            <select class="form-control ml-2 mr-2" name="evac_filter" onchange="this.form.submit()">
                <option value="">All Centers</option>
                <?php foreach ($evacuation_centers as $name => $id): ?>
                    <option value="<?php echo $id; ?>" <?php echo ($evac_filter == $id) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="text" class="form-control ml-2" name="search_query" placeholder="Search Name" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-primary ml-2">Search</button>
        </form>
    

    <?php if ($is_admin): ?>
    <form class="form-inline" method="POST" action="">
        <button type="submit" name="reset_evac_status" class="btn btn-danger ml-2">Reset Evacuation Status</button>
    </form>
    <?php endif; ?>
</div>

            <br>
            <?php
            // Dynamic query based on evacID and search query
            $sql = "SELECT f.family_id, 
                            f.evacID,
                            COALESCE(e.evacName, 'Unregistered') AS evacName,
                            GROUP_CONCAT(CONCAT(r.firstName, ' ', r.lastName, ' (', r.evacStatus, ')') ORDER BY r.firstName ASC SEPARATOR ', ') AS member_names,
                            GROUP_CONCAT(CONCAT(r.firstName, ' ', r.lastName, '|', r.evacStatus) ORDER BY r.firstName ASC SEPARATOR ', ') AS member_details,
                            COUNT(r.residentID) AS num_members,
                            SUM(CASE WHEN r.PWD = 'YES' THEN 1 ELSE 0 END) AS num_pwd,
                            SUM(CASE WHEN r.evacStatus = 'Evacuated' THEN 1 ELSE 0 END) AS num_evacuated,
                            SUM(CASE WHEN r.evacStatus = 'Not Evacuated' THEN 1 ELSE 0 END) AS num_not_evacuated,
                            SUM(CASE WHEN r.evacStatus = 'Needs Assistance' THEN 1 ELSE 0 END) AS num_needs_assistance
                    FROM tbl_families f
                    LEFT JOIN tbl_residents r ON f.family_id = r.family_id
                    LEFT JOIN tbl_evac_centers e ON f.evacID = e.evacID";

            $conditions = [];
            if ($evac_filter) {
                $conditions[] = "f.evacID = '$evac_filter'";
            }
            if ($search_query) {
                $conditions[] = "(r.firstName LIKE '%$search_query%' OR r.lastName LIKE '%$search_query%')";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " GROUP BY f.family_id, f.evacID ORDER BY f.evacID";
            $result = mysqli_query($conn, $sql);

            $current_evacID = null;
            while ($row = mysqli_fetch_assoc($result)) {
                if ($current_evacID !== $row['evacID']) {
                    if ($current_evacID !== null) {
                        echo '</tbody></table><br>';
                    }
                    $current_evacID = $row['evacID'];
echo '<table class="table table-hover table-bordered table-light">
        <thead>
            <tr>
                <th colspan="6" class="text-center" style="font-size: 20px; background-color:rgb(20, 6, 85); font-weight: bold;">' . $row['evacName'] . '</th>
            </tr>
            <tr>
                <th scope="col">Family ID#</th>
                <th scope="col">Present Members</th>
                <th scope="col">Number of Members</th>
                <th scope="col">Number of PWD</th>
                <th scope="col">Evacuation Status</th>
                <th scope="col">View Family</th>
            </tr>
        </thead>
        <tbody>';}

                $member_details = explode(', ', $row['member_details']);
                $colored_names = [];
                foreach ($member_details as $detail) {
                    $parts = explode('|', $detail);
                    if (count($parts) === 2) {
                        list($name, $status) = $parts;
                        $color = ($status === 'Evacuated') ? 'green' : (($status === 'Not Evacuated') ? 'red' : 'orange');
                        $colored_names[] = "<span style='color: $color;'>$name</span>";
                    } else {
                        $colored_names[] = "<span style='color: black;'>$detail</span>";
                    }
                }
                $colored_names_str = implode(', ', $colored_names);

                echo '<tr>
                        <th scope="row">' . $row['family_id'] . '</th>
                        <td>' . $colored_names_str . '</td>
                        <td>' . $row['num_members'] . '</td>
                        <td>' . $row['num_pwd'] . '</td>
                        <td>
                            <span class="badge badge-success">Evacuated: ' . $row['num_evacuated'] . '</span><br>
                            <span class="badge badge-danger">Not Evacuated: ' . $row['num_not_evacuated'] . '</span><br>
                            <span class="badge badge-warning">Needs Assistance: ' . $row['num_needs_assistance'] . '</span>
                        </td>
                        <td>
                            <button class="btn btn-success">
                                <a href="viewFamilyEvac.php?family_id=' . $row['family_id'] . '" class="text-light">VIEW</a>
                            </button>
                        </td>
                    </tr>';
            }
            if ($current_evacID !== null) {
                echo '</tbody></table>';
            }
            ?>
        </div>
    </main>
</body>
</html>
