<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from POST request
    $family_id = mysqli_real_escape_string($conn, $_POST['family_id']);
    $lastName = mysqli_real_escape_string($conn, $_POST['lastName']);
    $firstName = mysqli_real_escape_string($conn, $_POST['firstName']);
    $middleName = mysqli_real_escape_string($conn, $_POST['middleName']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $kinship = mysqli_real_escape_string($conn, $_POST['kinship']);
    $sex = mysqli_real_escape_string($conn, $_POST['sex']);
    $civilStatus = mysqli_real_escape_string($conn, $_POST['civilStatus']);
    $dateOfBirth = mysqli_real_escape_string($conn, $_POST['dateOfBirth']);
    $placeOfBirth = mysqli_real_escape_string($conn, $_POST['placeOfBirth']);
    $height = mysqli_real_escape_string($conn, $_POST['height']);
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $contactNo = mysqli_real_escape_string($conn, $_POST['contactNo']);
    $religion = mysqli_real_escape_string($conn, $_POST['religion']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pwd = mysqli_real_escape_string($conn, $_POST['pwd']);
    $occupation = mysqli_real_escape_string($conn, $_POST['occupation']);

    // Check for incomplete fields
    if (empty($firstName) || empty($lastName) || empty($middleName) || empty($kinship) 
        || empty($sex) || empty($civilStatus) || empty($dateOfBirth) || empty($placeOfBirth) 
        || empty($height) || empty($weight) || empty($contactNo) || empty($religion) 
        || empty($email) || empty($pwd) || empty($occupation)) {
        echo "incomplete";
    } else {
        // Insert new family member into tbl_residents
        $sql = "INSERT INTO tbl_residents (family_id, lastName, firstName, middleName, age, kinship, sex, civilStatus, dateOfBirth, placeOfBirth, height, weight, contactNo, religion, email, pwd, occupation) 
        VALUES ('$family_id', '$lastName', '$firstName', '$middleName', '$age', '$kinship', '$sex', '$civilStatus', '$dateOfBirth', '$placeOfBirth', '$height', '$weight', '$contactNo', '$religion', '$email', '$pwd', '$occupation')";

        if (mysqli_query($conn, $sql)) {
            // Update family member count
            $update_sql = "UPDATE tbl_families SET num_members = num_members + 1 WHERE family_id = '$family_id'";
            mysqli_query($conn, $update_sql);
            
            if ($pwd === 'YES') {
                $update_pwd_sql = "UPDATE tbl_families SET num_pwd = num_pwd + 1 WHERE family_id = '$family_id'";
                mysqli_query($conn, $update_pwd_sql);
            }

            echo 'success';
        } else {
            echo 'error: ' . mysqli_error($conn);
        }
    }

    mysqli_close($conn);
}
?>
