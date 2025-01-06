<?php

// Mock database connection (simulated data)
$mockResidents = [
    ['family_id' => 2, 'evacStatus' => 'Evacuated'],
    ['family_id' => 2, 'evacStatus' => 'Evacuated'],
    ['family_id' => 2, 'evacStatus' => 'Not Evacuated'],
    ['family_id' => 2, 'evacStatus' => 'Needs Assistance'],
    ['family_id' => 2, 'evacStatus' => 'Needs Assistance'],

    ['family_id' => 4, 'evacStatus' => 'Not Evacuated'],
    ['family_id' => 4, 'evacStatus' => 'Not Evacuated'],
    ['family_id' => 4, 'evacStatus' => 'Not Evacuated'],

    ['family_id' => 6, 'evacStatus' => 'Evacuated'],
    ['family_id' => 6, 'evacStatus' => 'Evacuated'],
    ['family_id' => 6, 'evacStatus' => 'Evacuated'],
];

// Process data to calculate status
$familyStatus = [];
foreach ($mockResidents as $resident) {
    $family_id = $resident['family_id'];
    $status = $resident['evacStatus'];

    if (!isset($familyStatus[$family_id])) {
        $familyStatus[$family_id] = ['Evacuated' => 0, 'Needs Assistance' => 0, 'Not Evacuated' => 0];
    }

    $familyStatus[$family_id][$status]++;
}

// Generate status string for each family
foreach ($familyStatus as $family_id => $statuses) {
    $statusString = [];
    if ($statuses['Evacuated'] > 0) {
        $statusString[] = "{$statuses['Evacuated']} Evacuated";
    }
    if ($statuses['Needs Assistance'] > 0) {
        $statusString[] = "{$statuses['Needs Assistance']} Needs Assistance";
    }
    if ($statuses['Not Evacuated'] > 0) {
        $statusString[] = "{$statuses['Not Evacuated']} Not Evacuated";
    }
    $familyStatus[$family_id] = implode(", ", $statusString);
}

// Output the results
?>

<!DOCTYPE html>
<html>
<head>
    <title>Family Status Test</title>
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Family Status Test</h1>
    <table>
        <thead>
            <tr>
                <th>Family ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($familyStatus as $family_id => $status): ?>
                <tr>
                    <td><?php echo $family_id; ?></td>
                    <td><?php echo $status; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
