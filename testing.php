<!DOCTYPE html>
<html lang="en">
<head>
    <title>Display Residents</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            background-image: url('images/ilocity.jpg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .container1 {
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            padding: 30px;
            margin: 50px auto;
            width: 80%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h3 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #333;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .search-container form {
            display: flex;
            width: 60%;
        }

        .search-container input {
            flex: 1;
            margin-right: 10px;
            padding: 10px;
            font-size: 1.1em;
        }

        .search-container button {
            padding: 10px 15px;
            font-size: 1.1em;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #0056b3;
        }

        .table-wrapper {
            margin-top: 20px;
            overflow-x: auto;
        }

        .table {
            background-color: #343a40;
            color: #fff;
            font-size: 1.1em;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 15px;
        }

        .table th {
            background-color: #007bff;
        }

        .table td {
            background-color: #495057;
        }

        .table .no-data {
            text-align: center;
            font-size: 1.2em;
            color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container1">
        <h3>Residents and Evacuation Centers</h3>

        <div class="search-container">
            <form method="GET" action="">
                <input type="text" name="search" class="form-control" placeholder="Search by name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Evacuation Center</th>
                        <th scope="col">Resident Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'db.php';

                    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

                    $sql = "SELECT f.evacID, CONCAT(r.firstName, ' ', r.lastName) AS residentName
                            FROM tbl_residents r
                            LEFT JOIN tbl_families f ON r.family_id = f.family_id
                            WHERE f.evacID IS NOT NULL";

                    if (!empty($search)) {
                        $sql .= " AND (r.firstName LIKE '%$search%' OR r.lastName LIKE '%$search%')";
                    }

                    $result = mysqli_query($conn, $sql);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $evacID = $row['evacID'];
                            $residentName = $row['residentName'];
                            echo '<tr>
                                    <td>' . $evacID . '</td>
                                    <td>' . $residentName . '</td>
                                  </tr>';
                        }
                    } else {
                        echo "<tr><td colspan='2' class='no-data'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
