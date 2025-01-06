<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $residentID = isset($_POST['residentID']) ? intval($_POST['residentID']) : 0;
    $evacStatus = isset($_POST['evacStatus']) ? mysqli_real_escape_string($conn, $_POST['evacStatus']) : '';

    if ($residentID && $evacStatus) {
        $query = "UPDATE tbl_residents SET evacStatus = '$evacStatus' WHERE residentID = $residentID";
        $result = mysqli_query($conn, $query);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database update failed.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
?>
