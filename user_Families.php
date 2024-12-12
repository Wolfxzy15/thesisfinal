<!DOCTYPE html>
<html lang="en">

<head>
    <title>Families</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">

    <style>
    </style>
</head>

<body>

    <?php include 'include/user_Sidebar.php'; ?>
    <main>
        
        <div class="table-container">
            <form class="form-inline mx-auto" method="GET" action="">
                <label><span style="color:black; font-size: 20px">Search Family ID#</span> </label>
                <input class="form-control mr-sm-2 ml-2" type="search" name="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-light mr-sm-2" type="submit" style="background-color: #E1D7B7; color: black;">Search</button>
                <select name="sort" class="form-control mr-sm-2">
                    <option value="">Sort By</option>
                    <option value="family_members">Number of Family Members</option>
                    <option value="pwd_status">PWD Status</option>
                    <option value="Not Evacuated">Not Evacuated</option>
                    <option value="Evacuated">Evacuated Families</option>
                    <option value="Needs Assistance">Needs Assistance</option>
                </select>
                <button class="btn btn-light" type="submit" style="background-color: #E1D7B7; color: black;">Sort</button>
            </form>
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
                                <th scope="col">Choose Status</th>
                                <th scope="col">Status</th>
                                <th scope="col">View Family</th>
                                <th scope="col">Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'db.php';

                            if (isset($_POST['update_status'])) {
                                foreach ($_POST['evacStatus'] as $family_id => $status) {
                                    // Update each family's status in the database
                                    $sql = "UPDATE tbl_families SET evacStatus = '$status' WHERE family_id = '$family_id'";
                                    mysqli_query($conn, $sql);
                                    
                                }
                            }

                            $search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
                            $sort_column = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : '';
                            $sort_order = 'DESC';
                            
                            $sql = "
                            SELECT 
                                f.family_id,
                                f.presentAddress,
                                f.latitude,
                                f.longitude,
                                COUNT(r.residentID) AS num_members,
                                SUM(CASE WHEN r.PWD = 'YES' THEN 1 ELSE 0 END) AS num_pwd,
                                f.evacID,
                                f.evacStatus
                            FROM tbl_families f
                            LEFT JOIN tbl_residents r ON f.family_id = r.family_id
                            WHERE 1
                        ";
                        
                        if (is_numeric($search_query)) {
                            $sql .= " AND f.family_id = '$search_query'";
                        } elseif (!empty($search_query)) {
                            $sql .= " AND (f.family_id LIKE '%$search_query%'
                                        OR f.presentAddress LIKE '%$search_query%' 
                                        OR r.lastName LIKE '%$search_query%' 
                                        OR r.firstName LIKE '%$search_query%')";
                        }
                        
                        $sql .= " GROUP BY f.family_id";

                            if ($sort_column) {
                                if ($sort_column == 'family_members') {
                                    $sql .= " ORDER BY num_members $sort_order";
                                } elseif ($sort_column == 'pwd_status') {
                                    $sql .= " ORDER BY num_pwd $sort_order";
                                } elseif ($sort_column == 'Evacuated') {
                                    $sql .= " ORDER BY (CASE WHEN f.evacStatus = 'Evacuated' THEN 1 ELSE 0 END) DESC, num_members $sort_order";
                                } elseif ($sort_column == 'Not Evacuated') {
                                    $sql .= " ORDER BY (CASE WHEN f.evacStatus = 'Not Evacuated' THEN 1 ELSE 0 END) DESC, num_members $sort_order";
                                } elseif ($sort_column == 'Needs Assistance') {
                                    $sql .= " ORDER BY (CASE WHEN f.evacStatus = 'Needs Assistance' THEN 1 ELSE 0 END) DESC, num_members $sort_order";
                                }
                            }
                            

                            $result = mysqli_query($conn, $sql);

                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $family_id = $row['family_id'];
                                    $presentAddress = $row['presentAddress'];
                                    $num_members = $row['num_members'];
                                    $num_pwd = $row['num_pwd'];
                                    $latitude = $row['latitude'];
                                    $longitude = $row['longitude'];
                                    $evacID = $row['evacID'];
                                    $status = $row['evacStatus']; // Added status field

                                    // Options for family status dropdown
                                    $status_options = "
                                        <option value='Evacuated' class='status-evacuated' " . ($status == 'Evacuated' ? 'selected' : '') . ">Evacuated</option>
                                        <option value='Not Evacuated' class='status-not-evacuated' " . ($status == 'Not Evacuated' ? 'selected' : '') . ">Not Evacuated</option>
                                        <option value='Needs Assistance' class='status-needs-assistance' " . ($status == 'Needs Assistance' ? 'selected' : '') . ">Needs Assistance</option>
                                    ";

                                    // Determine text class based on status
                                    $status_class = '';
                                    if ($status == 'Evacuated') {
                                        $status_class = 'status-evacuated';
                                    } elseif ($status == 'Not Evacuated') {
                                        $status_class = 'status-not-evacuated';
                                    } elseif ($status == 'Needs Assistance') {
                                        $status_class = 'status-needs-assistance';
                                    }

                                    echo '<tr>
                                        <th scope="row">' . $family_id . '</th>
                                        <td>' . $presentAddress . '</td>
                                        <td>' . $num_members . '</td>
                                        <td>' . $num_pwd . '</td>
                                        <td>' . $evacID . '</td>
                                        <td>
                                            <select class="form-control" name="evacStatus[' . $family_id . ']">
                                                ' . $status_options . '
                                            </select>
                                        </td>
                                        <td class="' . $status_class . '">' . $status . '</td>
                                        <td>
                                            <button class="btn btn-success">
                                                <a href="user_Residents.php?family_id=' . $family_id . '" class="text-light">VIEW</a>
                                            </button>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning" type="submit" name="update_status">Update Status</button>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo "<tr><td colspan='10' class='text-center'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

</body>

</html>
