<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $family_id = isset($_POST['family_id']) ? intval($_POST['family_id']) : 0;
    $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : 0;
    $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : 0;
    $presentAddress = isset($_POST['presentAddress']) ? mysqli_real_escape_string($conn, $_POST['presentAddress']) : '';

    if ($family_id && $latitude && $longitude) {
        $sql = "UPDATE tbl_families SET presentAddress = '$presentAddress', latitude = '$latitude', longitude = '$longitude' WHERE family_id = $family_id";
        if (mysqli_query($conn, $sql)) {
            echo 'success';
        } else {
            echo 'error', 'message' ;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    }
}
?>
