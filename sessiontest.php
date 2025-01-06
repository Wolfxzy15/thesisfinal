<?php
session_start();
require 'db.php';
echo "Session username: " . $_SESSION['username'];
$usernames = $_SESSION['username'];   // Debugging line
$sql = "SELECT userType FROM tbl_register WHERE username = '$usernames'";
$result = mysqli_query($conn, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $userType = $row['userType'];  // Get the user type (admin or user)
} else {
    // Handle error if the query fails or user not found
    echo "Error fetching user type or user not found.";
    exit();
}

echo $userType;
?>