<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Registration Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .form-container {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-container h3 {
            margin-top: 0;
        }

        button {
            margin: 10px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div>
        <h1>Family Registration Form</h1>
        <button onclick="addForm()">Add Family Member</button>
        <form id="familyForm" method="post">
            <button type="submit" form="familyForm" name="submit">Submit All</button>
        </form>
    </div>

    <script>
        let formCount = 0;

        function addForm() {
            formCount++;
            const formContainer = document.createElement('div');
            formContainer.className = 'form-container';
            formContainer.innerHTML = `
                
                <h3>Family Member ${formCount}</h3>
                <label for="firstName${formCount}">First Name:</label><br>
                <input type="text" id="firstName${formCount}" name="firstName[]"><br><br>
                
                <label for="lastName${formCount}">Last Name:</label><br>
                <input type="text" id="lastName${formCount}" name="lastName[]"><br><br>
                
                <label for="age${formCount}">Age:</label><br>
                <input type="number" id="age${formCount}" name="age[]"><br><br>
                
                <label for="relationship${formCount}">Relationship:</label><br>
                <input type="text" id="relationship${formCount}" name="relationship[]"><br><br>
            `;
            document.getElementById('familyForm').appendChild(formContainer);
        }
    </script>
</body>
</html>

<?php
$servername = "localhost"; // Change to your database server name
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "family"; // Change to your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $firstNames = $_POST['firstName'];
    $lastNames = $_POST['lastName'];
    $ages = $_POST['age'];
    $relationships = $_POST['relationship'];

    for ($i = 0; $i < count($firstNames); $i++) {
        $fname = $firstNames[$i];
        $lname = $lastNames[$i];
        $age = $ages[$i];
        $rel = $relationships[$i];

        $sql = "INSERT INTO resident (fname, lname, age, relationship) VALUES ('$fname', '$lname', '$age', '$rel')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully for Family Member $i<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
