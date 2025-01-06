<?php
session_start();

// Check if the user is logged in by ensuring the session variable 'username' is set
if (!isset($_SESSION['username'])) {
    // Redirect to login page if the user is not logged in
    header("Location: index.php");
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

// Barangay mapping (assumes these values are predefined)
$barangay_map = [
    'tabuc suba' => 1,
    'cubay' => 2,
    'san isidro' => 3,
    'quintin salas' => 4
];

// Initialize barangay_num variable
$barangay_num = null;

// Handle Barangay Filtering
$selected_barangay = isset($_GET['barangay_filter']) ? $_GET['barangay_filter'] : '';

// If the user is an admin and a barangay filter is selected, set the barangay_num based on the dropdown
if ($is_admin && array_key_exists($selected_barangay, $barangay_map)) {
    $barangay_num = $barangay_map[$selected_barangay];
} elseif (!$is_admin && array_key_exists($usernames, $barangay_map)) {
    // For regular users, use the username as the barangay key
    $barangay_num = $barangay_map[$usernames];
}

// Initialize search query
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// SQL query to fetch residents based on barangay and search filter
$sql = "SELECT r.*, r.evacStatus 
        FROM tbl_residents r 
        LEFT JOIN tbl_families f ON r.family_id = f.family_id 
        WHERE 1"; // Default to showing all residents

// Apply barangay filter if applicable
if ($barangay_num) {
    $sql .= " AND r.barangay_id = '$barangay_num'";
}

// Apply search filter if provided
if (!empty($search_query)) {
    $sql .= " AND (r.residentID LIKE '%$search_query%' OR 
                    r.kinship LIKE '%$search_query%' OR 
                    r.lastName LIKE '%$search_query%' OR 
                    r.firstName LIKE '%$search_query%' OR 
                    r.age LIKE '%$search_query%' OR 
                    r.sex LIKE '%$search_query%' OR   
                    r.pwd LIKE '%$search_query%')";
}

// Execute the query
$result = mysqli_query($conn, $sql);

// If query execution is successful, fetch and display the results
if ($result) {
    // Code to display the results in the table goes here
} else {
    echo "Error executing query.";
    exit();
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
        <div class="d-flex align-items-center">
            <?php if ($is_admin): ?>
                <form class="form-inline mr-3" method="GET" action="">
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

            <form class="form-inline" method="GET" action="">
                <input type="text" name="search" class="form-control" placeholder="Search by Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit" class="btn btn-primary ml-2">Search</button>
            </form>
        </div>

        <br>

        <div class="table-wrapper">
            <form method="POST">
                <table class="table table-hover table-bordered table-light">
                    <thead>
                        <tr>
                            <th scope="col">Family ID#</th>
                            <th scope="col">Members</th>
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
                        // Get the search term from the GET request if it exists
                        $search = isset($_GET['search']) ? $_GET['search'] : '';

                        // Dynamic query based on barangay_num and search
                        // Start building the query
$sql = "SELECT f.family_id, f.presentAddress, f.latitude, f.longitude,
COUNT(r.residentID) AS num_members,
SUM(CASE WHEN r.PWD = 'YES' THEN 1 ELSE 0 END) AS num_pwd,
f.evacID, 
SUM(CASE WHEN r.evacStatus = 'Evacuated' THEN 1 ELSE 0 END) AS num_evacuated,
SUM(CASE WHEN r.evacStatus = 'Not Evacuated' THEN 1 ELSE 0 END) AS num_not_evacuated,
SUM(CASE WHEN r.evacStatus = 'Needs Assistance' THEN 1 ELSE 0 END) AS num_needs_assistance
FROM tbl_families f
LEFT JOIN tbl_residents r ON f.family_id = r.family_id";

// Initialize an array to hold conditions
$conditions = [];

// Filter by barangay ID
if ($barangay_num !== null) {
$conditions[] = "f.barangay_id = '$barangay_num'";
}

// Add search filter if search term exists
if ($search) {
$conditions[] = "(r.firstName LIKE '%$search%' OR r.lastName LIKE '%$search%')";
}

// Combine the conditions with 'AND' if any conditions exist
if (!empty($conditions)) {
$sql .= " WHERE " . implode(' AND ', $conditions);
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

                                // Query to get the first and last names of the family members
                                $members_sql = "SELECT firstName, lastName FROM tbl_residents WHERE family_id = '$family_id'";
                                if ($search) {
                                    $members_sql .= " AND (firstName LIKE '%$search%' OR lastName LIKE '%$search%')";
                                }
                                $members_result = mysqli_query($conn, $members_sql);
                                $family_members = [];
                                if ($members_result) {
                                    while ($member = mysqli_fetch_assoc($members_result)) {
                                        $family_members[] = $member['firstName'] . ' ' . $member['lastName'];
                                    }
                                }

                                // Create a string of all member names, each in a new line
                                $members_list = implode('<br>', $family_members);

                                echo '<tr>
                                    <th scope="row">' . $family_id . '</th>
                                    <td>' . $members_list . '</td>
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
    </main>
</body>
</html>
