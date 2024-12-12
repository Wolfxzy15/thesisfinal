<?php
session_start();
require 'db.php'; // Assuming this file contains your database connection logic

// Query to select latitude from tbl_residents
$sql = "SELECT latitude FROM tbl_residents";

// Execute the query
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Latitude Data</h1>
    <table>
        <tr>
            <th>Latitude</th>
        </tr>
        <?php
        // Check if there are rows returned from the query
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>".$row["latitude"]."</td></tr>";
            }
        } else {
            echo "<tr><td>No data found</td></tr>";
        }
        ?>
    </table>
</body>
</html>
