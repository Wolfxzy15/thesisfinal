<?php
session_start();
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <main>
        <div class="table-container">
            <?php
            include 'db.php';
            if (isset($_GET['family_id'])) {
                $family_id = $_GET['family_id'];
            } else {
                echo "Family ID not provided!";
                exit;
            }
            $query = "SELECT latitude, longitude FROM tbl_families WHERE family_id = '$family_id'";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $latitude = $row['latitude'];
                $longitude = $row['longitude'];
            } else {
                echo "Coordinates not found for the family!";
            }

            ?>
            <h4 style="color: aliceblue">Family ID: <?php echo $family_id; ?></h4>
            <div class="container-4">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFamilyMemberModal"><i class="fa-solid fa-user-plus pr-2"></i>Add Family Member</button>
                <form action="evacSite.php" method="GET">
                    <input type="hidden" name="family_id" value="<?php echo $family_id; ?>">
                    <input type="hidden" name="latitude" value="<?php echo $latitude; ?>">
                    <input type="hidden" name="longitude" value="<?php echo $longitude; ?>">
                    <button class="btn btn-success ml-4" type="submit"><i class="fa-solid fa-building pr-2"></i>Evacuation Center</button>
                </form>
            </div>
            <br>
            <div class="table-wrapper">
                <form>
                    <table class="table table-hover table-bordered table-light">
                        <thead>
                            <tr>
                                <th scope="col">Family ID</th>
                                <th scope="col">Resident ID</th>
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
    include 'db.php';

    // Get family_id and search parameters
    $family_id = isset($_GET['family_id']) ? intval($_GET['family_id']) : 0;
    $search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $sort_column = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : '';
    $sort_order = 'ASC';
    $presentAddress = '';

    // Build the base query
    $sql = "SELECT r.*, f.presentAddress, f.latitude, f.longitude, f.evacID  
            FROM tbl_residents r 
            LEFT JOIN tbl_families f ON r.family_id = f.family_id 
            WHERE 1"; // Always true, allowing further conditions to be appended

    // If a specific family_id is provided, add it to the query
    if ($family_id) {
        $sql .= " AND r.family_id = '$family_id'";
    }
    if (is_numeric($search_query)) {
        $sql .= " AND r.residentID = '$search_query'";
    }
    // If a search query is provided, add it to the query
    if (!empty($search_query)) {
        $sql .= " AND (r.residentID LIKE '%$search_query%' OR 
                        r.kinship LIKE '%$search_query%' OR 
                        r.lastName LIKE '%$search_query%' OR 
                        r.firstName LIKE '%$search_query%' OR 
                        r.age LIKE '%$search_query%' OR 
                        r.sex LIKE '%$search_query%' OR   
                        r.pwd LIKE '%$search_query%')";
    }

    // Sorting logic
    if ($sort_column) {
        if ($sort_column == 'pwd') {
            // Special sorting for PWD status
            $sql .= " ORDER BY r.pwd = 'NO', r.pwd $sort_order";
        } else {
            $sql .= " ORDER BY $sort_column $sort_order";
        }
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
            $evacID = $row['evacID'];  // Evacuation ID

            // Handle different evacStatus cases and check for evacID
            echo '<tr>
                <th scope="row">' . $family_id . '</th>
                <td>' . $id . '</td>
                <td>' . $lastName . '</td>
                <td>' . $fName . '</td>
                <td>' . $age . '</td>
                <td>' . $sex . '</td>
                <td>' . $pwd . '</td>
                <td>';
            
            // Lock the status dropdown if evacID is 0
            if ($evacID == 0) {
                echo '<select class="form-control evac-status-dropdown" disabled>
                        <option value="Not Evacuated"' . ($evacStatus === 'Not Evacuated' ? ' selected' : '') . '>Not Evacuated</option>
                        <option value="Evacuated"' . ($evacStatus === 'Evacuated' ? ' selected' : '') . '>Evacuated</option>
                        <option value="Needs Assistance"' . ($evacStatus === 'Needs Assistance' ? ' selected' : '') . '>Needs Assistance</option>
                    </select>';
            } else {
                echo '<select class="form-control evac-status-dropdown" onchange="changeBackgroundColor(this)" data-resident-id="' . $id . '">
                        <option value="Not Evacuated"' . ($evacStatus === 'Not Evacuated' ? ' selected' : '') . '>Not Evacuated</option>
                        <option value="Evacuated"' . ($evacStatus === 'Evacuated' ? ' selected' : '') . '>Evacuated</option>
                        <option value="Needs Assistance"' . ($evacStatus === 'Needs Assistance' ? ' selected' : '') . '>Needs Assistance</option>
                    </select>';
            }
            echo '</td>
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
            <?php
            $family_query = "SELECT f.presentAddress, f.latitude, f.longitude, e.evacName, e.latitude AS evacLat, e.longitude AS evacLon
            FROM tbl_families f
            LEFT JOIN tbl_evac_centers e ON f.evacID = e.evacID
            WHERE f.family_id = '$family_id'";
            $family_result = mysqli_query($conn, $family_query);
            $family_row = mysqli_fetch_assoc($family_result);
            $presentAddress = $family_row['presentAddress'];
            $familyLat = $family_row['latitude'];
            $familyLon = $family_row['longitude'];
            $evacName = $family_row['evacName'];
            $evacLat = $family_row['evacLat'];
            $evacLon = $family_row['evacLon'];
            ?>


            <div class="container">
                <label for="presentAdd">Present Address:</label>
                <input type="text" class="form-control" value="<?php echo $family_row['presentAddress']; ?>" placeholder="Present Address" id="presentAddress" name="presentAddress" readonly>
                <div id="map" style="height: 400px;"></div>
                <input type="hidden" id="latitude" name="latitude" value="<?php echo $family_row['latitude']; ?>">
                <input type="hidden" id="longitude" name="longitude" value="<?php echo $family_row['longitude']; ?>"><br>
                <button id="updateAddress" class="btn btn-primary">Update Address</button>
            </div>

           
            <div class="modal fade" id="addFamilyMemberModal" tabindex="-1" role="dialog" aria-labelledby="addFamilyMemberModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addFamilyMemberModalLabel">Add Family Member</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="addFamilyMemberForm" method="POST" value="addFmemberFunction.php">
                            <div class="modal-body">
                                <input type="hidden" name="family_id" value="<?php echo isset($_GET['family_id']) ? intval($_GET['family_id']) : ''; ?>" />
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" required>
                                </div>
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" required>
                                </div>
                                <div class="form-group">
                                    <label for="middleName">Middle Name</label>
                                    <input type="text" class="form-control" id="middleName" name="middleName">
                                </div>
                                <div class="form-group">
                                    <label for="dateOfBirth">Date of Birth</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" onchange="calculateAge()" required>
                                </div>
                                <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="text" class="form-control" id="age" name="age" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="sex">Sex:</label>
                                    <div>
                                        <input type="radio" value="Female" id="female" name="sex" required>
                                        <label for="female">Female</label>
                                        <input type="radio" value="Male" id="male" name="sex" required>
                                        <label for="male">Male</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="kinship">Kinship Position:</label>
                                    <select id="kinship" name="kinship" class="form-control" required>
                                        <option value="">--Position--</option>
                                        <option value="Head of Family">Head of Family</option>
                                        <option value="Spouse">Spouse</option>
                                        <option value="Solo Parent">Solo Parent</option>
                                        <option value="Solo Living">Solo Living</option>
                                        <option value="Dependent">Dependent</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="civilStatus">Civil Status:</label>
                                    <select id="civilStatus" name="civilStatus" class="form-control" required>
                                        <option value="">--Select--</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Separated">Separated</option>
                                        <option value="Divorced">Divorced</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="placeOfBirth">Place of Birth</label>
                                    <input type="text" class="form-control" id="placeOfBirth" name="placeOfBirth" required>
                                </div>
                                <div class="form-group">
                                    <label for="height">Height (cm)</label>
                                    <input type="number" class="form-control" id="height" name="height" required>
                                </div>
                                <div class="form-group">
                                    <label for="weight">Weight (kg)</label>
                                    <input type="number" class="form-control" id="weight" name="weight" required>
                                </div>
                                <div class="form-group">
                                    <label for="contactNo">Contact Number</label>
                                    <input type="text" class="form-control" id="contactNo" name="contactNo">
                                </div>
                                <div class="form-group">
                                    <label for="religion">Religion</label>
                                    <input type="text" class="form-control" id="religion" name="religion">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email">
                                </div>
                                <div class="form-group">
                                    <label for="pwd">PWD:</label>
                                    <div>
                                        <input type="radio" value="YES" id="yes" name="pwd" required> YES
                                        <input type="radio" value="NO" id="no" name="pwd" required> NO
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="occupation">Occupation</label>
                                    <input type="text" class="form-control" id="occupation" name="occupation">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" onclick="submitData();" name="submit" class="btn btn-primary">Add Member</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php require 'memberScript.php'; ?>

            </form>
        </div>
        </div>
        </div>
        </div>
        <?php require 'addressScript.php'; ?>
        </div>
    </main>
    <script>
        function calculateAge() {
            const dobInput = document.getElementById('dateOfBirth');
            const ageInput = document.getElementById('age');

            const dob = new Date(dobInput.value);
            const today = new Date();
            let age = today.getFullYear() - dob.getFullYear();
            const monthDifference = today.getMonth() - dob.getMonth();

            // Adjust age if the birthday hasn't occurred yet this year
            if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dob.getDate())) {
                age--;
            }

            ageInput.value = age;
        }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    </script>
    <script src="family_map.js"></script>
    <script>

        var iconUrl = "images/building-solid.svg";

        var evacMarker = L.icon({
        iconUrl: iconUrl, 
        iconSize: [35, 35], 
        iconAnchor: [17, 35], 
        popupAnchor: [0, -35], 
    });

        if (<?php echo json_encode($evacLat); ?> && <?php echo json_encode($evacLon); ?>) {
            var evacMarker = L.marker([<?php echo $evacLat; ?>, <?php echo $evacLon; ?>], {icon: evacMarker}).addTo(map)
                .bindPopup("Evacuation Center: <?php echo $evacName; ?>");
        } else {
            console.log("Evacuation center not registered for this family.");
        }
    </script>
    <script>
    function changeBackgroundColor(selectElement) {
        
        selectElement.style.backgroundColor = 'white';

        
        var selectedValue = selectElement.value;

        if (selectedValue === 'Evacuated') {
            selectElement.style.backgroundColor = 'green';
            selectElement.style.color = 'white'; 
        } else if (selectedValue === 'Not Evacuated') {
            selectElement.style.backgroundColor = 'red';
            selectElement.style.color = 'white'; 
        } else if (selectedValue === 'Needs Assistance') {
            selectElement.style.backgroundColor = 'yellow';
            selectElement.style.color = 'black'; 
        }
    }

    
    document.addEventListener('DOMContentLoaded', function() {
        var selectElements = document.querySelectorAll('.evac-status-dropdown');
        selectElements.forEach(function(selectElement) {
            changeBackgroundColor(selectElement); 
        });
    });
    
    $(document).on('change', '.evac-status-dropdown', function () {
    var selectedStatus = $(this).val();
    var residentID = $(this).data('resident-id');

    $.ajax({
        url: 'updateEvacStatus.php',
        type: 'POST',
        data: { evacStatus: selectedStatus, residentID: residentID },
        success: function (response) {
            console.log('Evacuation status updated successfully!');
        },
        error: function (xhr, status, error) {
            console.error('Error updating evacuation status:', error);
        }
    });
});

</script>

</body>

</html>