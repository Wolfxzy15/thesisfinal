<?php
session_start(); // Start the session to track user login status

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Define the hardcoded username and password
    $valid_username = 'user';
    $valid_password = '12345';

    // Check if the entered username and password match the hardcoded values
    if ($username == $valid_username && $password == $valid_password) {
        // Store the username in the session
        $_SESSION['username'] = $username;

        // Redirect to the dashboard page
        header("Location: dashboard.php");
        exit();
    } else {
        // Display an error message if login failed
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 0 auto;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #218838;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <!-- Display error message if login failed -->
    <?php if (isset($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>

    <form action="testingloginpage.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
