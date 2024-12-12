<?php

$servername = "localhost"; // Change to your database server name
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "thesis"; // Change to your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $presentAddress = $conn->real_escape_string($_POST['presentAddress']);
    $latitude = $conn->real_escape_string($_POST['latitude']);
    $longitude = $conn->real_escape_string($_POST['longitude']);


    $num_members = count($_POST['lastName']);
    $num_pwd = 0;


    for ($i = 0; $i < count($_POST['lastName']); $i++) {
        if ($_POST['pwd' . ($i + 1)] == 'YES') {
            $num_pwd++;
        }
    }


    $sql_family = "INSERT INTO tbl_families (presentAddress, latitude, longitude, num_members, num_pwd, evacID, evacStatus) VALUES ('$presentAddress', '$latitude', '$longitude','$num_members', '$num_pwd', 0, 'Not Evacuated')";
    if ($conn->query($sql_family) === TRUE) {
        $family_id = $conn->insert_id;
    } else {
        die("Error: " . $sql_family . "<br>" . $conn->error);
    }


    $lastNames = $_POST['lastName'];
    $firstNames = $_POST['fName'];
    $middleNames = $_POST['mName'];
    $ages = $_POST['age'];
    $kinships = $_POST['kinship'];
    $civilStats = $_POST['civilStat'];
    $dateOfBirths = $_POST['dateOfBirth'];
    $placeOfBirths = $_POST['placeOfBirth'];
    $heights = $_POST['height'];
    $weights = $_POST['weight'];
    $contactNos = $_POST['contactNo'];
    $religions = $_POST['religion'];
    $emailAdds = $_POST['emailAdd'];
    $occupations = $_POST['occupation'];

    for ($i = 0; $i < count($firstNames); $i++) {
        $lname = $lastNames[$i];
        $fname = $firstNames[$i];
        $mname = $middleNames[$i];
        $age = $ages[$i];
        $kin = $kinships[$i];
        $sex = $_POST["sex" . ($i + 1)];
        $civil = $civilStats[$i];
        $dob = $dateOfBirths[$i];
        $pob = $placeOfBirths[$i];
        $h = $heights[$i];
        $w = $weights[$i];
        $contact = $contactNos[$i];
        $rel = $religions[$i];
        $email = $emailAdds[$i];
        $pwd = $_POST["pwd" . ($i + 1)];
        $occupation = $occupations[$i];

        if (
            empty($kin) || empty($lname) || empty($fname) || empty($mname) || empty($age) || empty($civil) ||
            empty($dob) || empty($pob) || empty($h) || empty($w) || empty($contact) || empty($rel) ||
            empty($email) || empty($pwd)
        ) {
            $message = 'incomplete';
            break;
        } else {
            $sql_resident = "INSERT INTO tbl_residents 
            (family_id, lastName, firstName, middleName, age, kinship, sex, civilStatus, 
            dateOfBirth, placeOfBirth, height, weight, contactNo, religion, email, PWD, occupation) 
            VALUES ('$family_id', '$lname', '$fname', '$mname', '$age', '$kin', '$sex', '$civil', 
            '$dob', '$pob', '$h', '$w', '$contact', '$rel', '$email', '$pwd', '$occupation')";

            $result = mysqli_query($conn, $sql_resident);
            if (!$result) {
                $message = 'error';
                break;
            }
        }
    }

    if ($message !== 'incomplete') {
        $message = 'success';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Register Resident</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="main.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <style>
        .form-container {
            position: relative;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: aliceblue;
        }

        .form-container h3 {
            margin-top: 0;
        }

        .nav-arrows {
            position: absolute;
            top: 10px;
            /* Adjust this value to move buttons up/down */
            right: 10px;
            /* Adjust this value to move buttons left/right */
            display: flex;
            gap: 10px;
            /* Space between the buttons */
        }

        .nav-arrows button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .nav-arrows button.disabled {
            background-color: #d6d6d6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <?php include 'include/user_Sidebar.php'; ?>
    <main>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let formCount = 0;
                const formContainer = document.getElementById('familyForm');
                let currentFormIndex = 0;

                function addForm() {
                    formCount++;
                    const formWrapper = document.createElement('div');
                    formWrapper.className = 'form-wrapper';
                    formWrapper.id = `form-${formCount}`;
                    formWrapper.innerHTML = `
            <div class="form-container">
                <h3>Family Member ${formCount}</h3>
                <div class="row row-cols-2 mb-3">
                    <div class="col-md-4 mb-2">
                        <label for="lastName${formCount}">Lastname:</label>
                        <input type="text" class="form-control" placeholder="Last name" id="lastName${formCount}" name="lastName[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="fName${formCount}">Firstname:</label>
                        <input type="text" class="form-control" placeholder="First name" id="fName${formCount}" name="fName[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="mName${formCount}">Middlename:</label>
                        <input type="text" class="form-control" placeholder="Middle name" id="mName${formCount}" name="mName[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="age${formCount}">Age:</label>
                        <input type="text" class="form-control" placeholder="" id="age${formCount}" name="age[]" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kinship${formCount}">Kinship Position:</label>
                        <select id="kinship${formCount}" name="kinship[]" class="form-control">
                            <option value="">--Position--</option>
                            <option value="Head of Family">Head of Family</option>
                            <option value="Spouse">Spouse</option>
                            <option value="Solo Parent">Solo Parent</option>
                            <option value="Solo Living">Solo Living</option>
                            <option value="Dependent">Dependent</option>
                        </select>
                    </div>
                </div>
                <div class="row row-cols-2 mb-3">
                    <div class="col-md-2 mb-2">
                        <label for="sex${formCount}">Sex:</label>
                        <div>
                            <input type="radio" value="Female" id="female${formCount}" name="sex${formCount}" required>
                            <label for="female${formCount}">Female</label>
                            <input type="radio" value="Male" id="male${formCount}" name="sex${formCount}" required>
                            <label for="male${formCount}">Male</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="civilStat${formCount}">Civil Status:</label>
                        <select id="civilStat${formCount}" name="civilStat[]" class="form-control">
                            <option value="">--Select--</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Divorced">Divorced</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="dateOfBirth${formCount}">Date of Birth:</label>
                        <input type="date" class="form-control" id="dateOfBirth${formCount}" onchange="calculateAge(${formCount})" name="dateOfBirth[]">
                    </div>
                    <div class="col-md-4">
                        <label for="placeOfBirth${formCount}">Place of Birth:</label>
                        <input type="text" class="form-control" id="placeOfBirth${formCount}" name="placeOfBirth[]">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="height${formCount}">Height:</label>
                        <input type="text" class="form-control" id="height${formCount}" name="height[]">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label for="weight${formCount}">Weight:</label>
                        <input type="text" class="form-control" id="weight${formCount}" name="weight[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="contactNo${formCount}">Contact Number:</label>
                        <input type="text" class="form-control" id="contactNo${formCount}" name="contactNo[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="religion${formCount}">Religion:</label>
                        <input type="text" class="form-control" id="religion${formCount}" name="religion[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="emailAdd${formCount}">Email Address:</label>
                        <input type="email" class="form-control" id="emailAdd${formCount}" name="emailAdd[]">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="occupation${formCount}">Occupation:</label>
                        <input type="text" class="form-control" id="occupation${formCount}" name="occupation[]">
                    </div>
                    <div class="col-md-4">
                        <label for="pwd${formCount}">Are you a person with disability?</label>
                        <div>
                            <input type="radio" value="YES" id="yes${formCount}" name="pwd${formCount}" required>YES
                            <input type="radio" value="NO" id="no${formCount}" name="pwd${formCount}" required>NO
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger delete-btn" onclick="deleteForm(${formCount})">&#10006; Delete</button>
                <div class="nav-arrows">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="prevForm()">&#10094;</button>
                    <button type="button" class="btn btn-secondary" id="nextBtn" onclick="nextForm()">&#10095;</button>
                </div>
            </div>`;

                    formContainer.appendChild(formWrapper);
                    currentFormIndex = formCount - 1; 
                    updateNavigation();
                }

                
                addForm();
                //Calculate age from date Form

                function updateNavigation() {
                    const forms = document.querySelectorAll('.form-wrapper');
                    forms.forEach((form, index) => {
                        form.style.display = index === currentFormIndex ? 'block' : 'none';
                    });
                    document.getElementById('prevBtn').style.display = currentFormIndex === 0 ? 'none' : 'block';
                    document.getElementById('nextBtn').style.display = currentFormIndex === forms.length - 1 ? 'none' : 'block';
                }

                window.prevForm = function() {
                    if (currentFormIndex > 0) {
                        currentFormIndex--;
                        updateNavigation();
                    }
                }

                window.nextForm = function() {
                    const forms = document.querySelectorAll('.form-wrapper');
                    if (currentFormIndex < forms.length - 1) {
                        currentFormIndex++;
                        updateNavigation();
                    }
                }

                window.deleteForm = function(formNumber) {
                    console.log(`Attempting to delete form-${formNumber}`); // Debugging line
                    const formToDelete = document.getElementById(`form-${formNumber}`);
                    if (formToDelete) {
                        console.log(`Deleting form-${formNumber}`); // Debugging line
                        formToDelete.remove();
                        formCount--;
                        // Reindex forms
                        reindexForms();
                        updateNavigation();
                    } else {
                        console.log(`Form-${formNumber} not found`); // Debugging line
                    }
                }

                function reindexForms() {
                    const forms = document.querySelectorAll('.form-wrapper');
                    formCount = forms.length;
                    forms.forEach((form, index) => {
                        form.id = `form-${index + 1}`;
                        form.querySelector('.delete-btn').setAttribute('onclick', `deleteForm(${index + 1})`);
                        // Update the family member number in the header
                        form.querySelector('h3').textContent = `Family Member ${index + 1}`;
                    });
                    currentFormIndex = Math.min(currentFormIndex, formCount - 1);
                }

                document.querySelector('button[onclick="addForm()"]').addEventListener('click', addForm);
            });
        </script>


        <div class="container">
            <form id="familyForm" method="post">
                <h1>Family Registration Form</h1><br>
                <button type="submit" class="btn btn-success" form="familyForm" name="submit">Submit All</button><br><br>
                <label for="presentAddress"><b>Present Address:</b></label><br>
                <input type="text" class="form-control" placeholder="Choose from the map" id="presentAddress" name="presentAddress" required readonly>
                <div id="map"></div>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude"><br>
                <hr>
                <button type="button" class="btn btn-primary" onclick="addForm()"><i class="fa-solid fa-user-plus pr-2"></i>Add Family Member</button><br><br>
            </form>
        </div>
    </main>
</body>
<script>
    <?php if ($message == 'incomplete'): ?>
        Swal.fire({
            icon: 'warning',
            title: 'Form Incomplete',
            text: 'Please fill up the form completely!',
            confirmButtonText: 'OK'
        });
    <?php elseif ($message == 'success'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Family Registered successfully!',
            confirmButtonText: 'OK'
        });
    <?php elseif ($message == 'error'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error registering resident information.',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
</script>
<script src="script.js"></script>

</html>