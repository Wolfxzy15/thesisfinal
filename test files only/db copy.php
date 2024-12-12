<?php
$servername = "localhost"; // Change to your database server name
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "thesis"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
