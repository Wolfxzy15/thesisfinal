<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barangay1";  // Change to your actual DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$total_area = 0;
$restrooms = 0;
$kitchens = 0;
$water_supply = 0;
$food_supply = 0;
$total_people = 0;
$total_pwd = 0;
$final_capacity = 0;

// Standards (fixed)
$space_per_person = 3;  // sq meters per person
$water_per_person = 3;  // liters per person per day
$meals_per_person = 3;  // meals per person per day
$people_per_restroom = 50;
$people_per_kitchen = 100;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input from form
    $total_area = isset($_POST['total_area']) ? floatval($_POST['total_area']) : 0;
    $restrooms = isset($_POST['restrooms']) ? intval($_POST['restrooms']) : 0;
    $kitchens = isset($_POST['kitchens']) ? intval($_POST['kitchens']) : 0;
    $water_supply = isset($_POST['water_supply']) ? floatval($_POST['water_supply']) : 0;
    $food_supply = isset($_POST['food_supply']) ? floatval($_POST['food_supply']) : 0;

    // Retrieve families and resident data
    $sql_families = "
        SELECT 
            f.family_id, 
            COUNT(r.residentID) AS num_members, 
            SUM(CASE WHEN r.PWD = 'YES' THEN 1 ELSE 0 END) AS num_pwd
        FROM tbl_families f
        LEFT JOIN tbl_residents r ON f.family_id = r.family_id
        GROUP BY f.family_id";

    $result_families = $conn->query($sql_families);

    if ($result_families->num_rows > 0) {
        $total_people = 0;
        $total_pwd = 0;

        // Loop through each family
        while ($row = $result_families->fetch_assoc()) {
            $total_people += $row['num_members'];
            $total_pwd += $row['num_pwd'];
        }
    } else {
        die("No families found");
    }

    // Calculate capacities based on user input
    $capacity_by_area = floor($total_area / $space_per_person);
    $capacity_by_restrooms = $restrooms * $people_per_restroom;
    $capacity_by_kitchens = $kitchens * $people_per_kitchen;
    $capacity_by_water = floor($water_supply / $water_per_person);
    $capacity_by_food = floor($food_supply / $meals_per_person);

    // Adjust area capacity for PWDs (PWDs require extra space)
    $pwd_adjustment = $total_pwd * 1.5;  // Assuming PWDs need 1.5x space
    $adjusted_capacity_by_area = floor(($total_area - $pwd_adjustment) / $space_per_person);

    // Final capacity based on the limiting factor
    $final_capacity = min(
        $adjusted_capacity_by_area,
        $capacity_by_restrooms,
        $capacity_by_kitchens,
        $capacity_by_water,
        $capacity_by_food
    );

    // Check for overcrowding
    $overcrowding_warning = $total_people > $final_capacity
        ? "<p style='color:red;'>Warning: Overcrowding! The current number of people exceeds the site's capacity.</p>"
        : "<p style='color:green;'>The site can accommodate the current number of people.</p>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Evacuation Site Capacity Assessment</title>
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
</head>
<body>
<?php include 'include/sidebar.php'; ?>
<main> 
    <div class="container">
        <h2>Evacuation Site Capacity Assessment</h2>
        <form action="esca.php" method="POST">
            <div class="form-group">
                <label for="total_area">Total Area (in square meters):</label>
                <input type="number" class="form-control" id="total_area" name="total_area" required>
            </div>
            <div class="form-group">
                <label for="restrooms">Number of Restrooms:</label>
                <input type="number" class="form-control" id="restrooms" name="restrooms" required>
            </div>
            <div class="form-group">
                <label for="kitchens">Number of Kitchens:</label>
                <input type="number" class="form-control" id="kitchens" name="kitchens" required>
            </div>
            <div class="form-group">
                <label for="water_supply">Water Supply (liters per day):</label>
                <input type="number" class="form-control" id="water_supply" name="water_supply" required>
            </div>
            <div class="form-group">
                <label for="food_supply">Food Supply (meals available):</label>
                <input type="number" class="form-control" id="food_supply" name="food_supply" required>
            </div>
            <button type="submit" class="btn btn-primary">Calculate Capacity</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <h2>Evacuation Site Capacity Assessment Results</h2>
            <p>Total people: <?php echo $total_people; ?></p>
            <p>Final capacity: <?php echo $final_capacity; ?></p>
            <?php echo $overcrowding_warning; ?>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
