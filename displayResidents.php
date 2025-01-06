<?php
session_start();
$barangay = $_SESSION['username'];

// Database connection
include 'db.php';

// Query to get userType for the logged-in user
$sql_user = "SELECT userType FROM tbl_register WHERE username = '$barangay'";
$result_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($result_user);

if ($user && $user['userType'] === 'admin') {
    $is_admin = true; // User is admin
} else {
    $is_admin = false; // User is not admin
}

$barangay_map = [
    'tabuc suba' => 1,
    'cubay' => 2,
    'san isidro' => 3,
    'quintin salas' => 4
];

if (array_key_exists($barangay, $barangay_map)) {
    $barangay_num = $barangay_map[$barangay];
} else {
    $barangay_num = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Display Residents</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <main>
        
        <div class="table-container">
            <form class="form-inline mx-auto" method="GET" action="">
                
                <?php if ($is_admin): ?>
                    <label><span style="color:black; font-size: 20px">Select Barangay : </span> </label>
                    <select name="barangay_filter" class="form-control mr-sm-2" onchange="this.form.submit()">
                        <option value="">Select Barangay</option>
                        <option value="all" <?php echo (isset($_GET['barangay_filter']) && $_GET['barangay_filter'] == 'all') ? 'selected' : ''; ?>>All Barangays</option>
                        <?php foreach ($barangay_map as $name => $id): ?>
                            <option value="<?php echo $name; ?>" <?php echo (isset($_GET['barangay_filter']) && $_GET['barangay_filter'] == $name) ? 'selected' : ''; ?>>
                                <?php echo ucfirst($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="hidden" name="barangay_filter" value="<?php echo $barangay; ?>" />
                <?php endif; ?>

                <input class="form-control mr-sm-2 ml-2" type="search" name="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-primary ml-2" type="submit">Search</button>
            </form>
            <br>
            <div class="table-wrapper">
                <form>
                    <table class="table table-hover table-bordered table-light">
                        <thead>
                            <tr>
                                <th scope="col">Resident ID</th>
                                <th scope="col">Family ID</th>
                                <th scope="col">Lastname</th>
                                <th scope="col">Firstname</th>
                                <th scope="col">Age</th>
                                <th scope="col">Sex</th>
                                <th scope="col">PWD</th>
                                <th scope="col">Evacuation Status</th>
                                <th scope="col">EDIT Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Get barangay filter and search parameters
                            $barangay_filter = isset($_GET['barangay_filter']) ? $_GET['barangay_filter'] : '';
                            $search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

                            // Build the base query
                            $sql = "SELECT r.*, r.evacStatus 
                                FROM tbl_residents r 
                                LEFT JOIN tbl_families f ON r.family_id = f.family_id ";

                            // Filter by barangay if selected
                            if ($is_admin) {
                                if (!empty($barangay_filter) && $barangay_filter != 'all' && array_key_exists($barangay_filter, $barangay_map)) {
                                    $barangay_num = $barangay_map[$barangay_filter];
                                    $sql .= " WHERE r.barangay_id = '$barangay_num'";
                                } elseif (empty($barangay_filter) || $barangay_filter == 'all') {
                                    $sql .= " WHERE 1"; // This will show residents from all barangays
                                }
                            } else {
                                // If the user is not admin, filter by their barangay_id
                                $sql .= " WHERE r.barangay_id = '$barangay_num'";
                            }

                            // Add search query if provided
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

                            // Check if there are results and output them in the table
                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $id = $row['residentID'];
                                    $family_id = $row['family_id'];
                                    $lastName = $row['lastName'];
                                    $fName = $row['firstName'];
                                    $age = $row['age'];
                                    $sex = $row['sex'];
                                    $pwd = $row['pwd'];
                                    $evacStatus = $row['evacStatus'];

                                    // Handle different evacStatus cases
                                    $status_class = '';
                                    if ($evacStatus === 'Evacuated') {
                                        $status_class = 'status-evacuated';
                                    } elseif ($evacStatus === 'Needs Assistance') {
                                        $status_class = 'status-needs-assistance';
                                    } else {
                                        $status_class = 'status-not-evacuated';
                                    }

                                    echo '<tr>
                                        <th scope="row">' . $id . '</th>
                                        <td>' . $family_id . '</td>
                                        <td>' . $lastName . '</td>
                                        <td>' . $fName . '</td>
                                        <td>' . $age . '</td>
                                        <td>' . $sex . '</td>
                                        <td>' . $pwd . '</td>
                                       <td class="' . $status_class . '">' . $evacStatus . '</td>
                                        <td>
                                            <button class="btn btn-success">
                                                <a href="editResident.php?updateID=' . urlencode($id) . '" class="text-light">EDIT</a>
                                            </button>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
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
