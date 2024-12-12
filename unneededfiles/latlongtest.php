<?php
include 'include/db.php';

$sql = "SELECT latitude FROM tbl_residents WHERE residentID = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $latitude = $row["latitude"];

    echo "Latitude fetched from database: " . $latitude; // Add this line for debugging
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <script>
    var latitude = <?php echo json_encode($latitude); ?>;
    console.log(latitude);
    </script>
</body>
</html>
