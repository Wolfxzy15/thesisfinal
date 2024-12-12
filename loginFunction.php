<?php
session_start();

include 'db.php';

// IF
if (isset($_POST["action"])) {
    if ($_POST["action"] == "register") {
        registerPage();
    } else if ($_POST["action"] == "loginPage") {
        loginPage();
    }
}

// REGISTER ADMIN
function registerPage() {
    global $conn;

    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $emailAdd = $_POST["emailAdd"];
    $userType = $_POST["userType"];

    if (empty($firstName) || empty($lastName) || empty($cpassword) || empty($username) || empty($password) || empty($emailAdd) || empty($userType)) {
        echo "Please Fill Out The Form!";
        exit;
    }

    if ($password !== $cpassword) {
        echo "Passwords do not match!";
        exit;
    }

    $user = mysqli_query($conn, "SELECT * FROM tbl_register WHERE username = '$username'");
    if (mysqli_num_rows($user) > 0) {
        echo "Username Has Already Taken";
        exit;
    }

    $query = "INSERT INTO tbl_register (firstName, lastName, username, password, emailAdd, userType) 
    VALUES('$firstName', '$lastName', '$username', '$password',  '$emailAdd', '$userType')";
    mysqli_query($conn, $query);
    echo "Registration Successful";
}

// LOGIN ADMIN
function loginPage() {
    global $conn;

    $username = $_POST["username"];
    $password = $_POST["password"];

    $user = mysqli_query($conn, "SELECT * FROM tbl_register WHERE username = '$username'");

    if (mysqli_num_rows($user) > 0) {
        $row = mysqli_fetch_assoc($user);

        // Check if the password matches
        if ($password == $row['password']) {
            // Check the user type and respond accordingly
            if ($row['userType'] == 'admin') {
                echo "Admin Login Successful";
                $_SESSION["login"] = true;
                $_SESSION["reg_id"] = $row["reg_id"];
                $_SESSION['username'] = $username; // Session set here
            } else if ($row['userType'] == 'user') {
                echo "User Login Successful";
                $_SESSION["login"] = true;
                $_SESSION["reg_id"] = $row["reg_id"];
                $_SESSION['username'] = $username; // Session set here
            }
        } else {
            echo "Wrong Password";
            exit;
        }
    } else {
        echo "Invalid User";
        exit;
    }
}
?>
