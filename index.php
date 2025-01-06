<?php
session_start();

// Include the database connection file
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials using db.php
    $query = "SELECT * FROM tbl WHERE username = ? AND password = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username, $password]);

    // Check if a user with the provided username and password exists
    if ($stmt->rowCount() > 0) {
        // Credentials are valid
        $_SESSION['username'] = $username; // Store username in session
        header('Location: testing.php'); // Redirect to the welcome page
        exit();
    } else {
        // Handle invalid credentials
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login Page</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
  body {
    background-image: url('images/calle_real.jpg');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;
  }

  body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: -1;
  }

  .center-image {
    display: flex;
    justify-content: center;
  }

  .navbar {
    background-color: #1E2A5E;
  }

  .navbar a {
    color: aliceblue;
    font-size: 25px;
  }

  .navbar li {
    font-size: 15px;
  }

  .card {
    background-color: #93c7f0;
    background-image: linear-gradient(135deg, #93c7f0 0%, #bbc9c9 100%);
    color: black;
    border-radius: 15px;
  }

  .card-header {
    text-align: center;
  }

  /* Adjust form input styles */
  .form-control {
    background-color: aliceblue;
    border: 1px solid #555;
    color: black;
  }

  .form-control::placeholder {
    color: #333;
  }

  .btn-success {
    background-color: #28a745;
    border-color: #28a745;
  }

  .label {
    background-color: #000000;
  }
</style>
</head>

<body>
  
  <div class="container mt-5 pt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
            <div class="center-image mt-4">
              <img src="images/citylogo.png"
                width="90" height="90" alt="">
            </div>
            <h2 style="text-align: center;">Login</h2>
          <div class="card-body">
            <form autocomplete="off" action="login.php" method="post">
              <input type="hidden" id="action" value="loginPage">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" placeholder="username" name="username" required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" placeholder="password" name="password" required>
              </div>
              <button type="button" onclick="submitData();" class="btn btn-success" style="width: 100%">Login</button>
              <br><br>
              <p class="mb-0">Don't have an account? <a href="register.php"><span style="color:aliceblue">Register here.</a></span></p>
            </form>

            <?php require 'loginScript.php'; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js">
  </script>


</body>

</html>