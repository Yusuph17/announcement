<?php
require 'db.php'; // Include database connection

// Fetch HOD data
$sql = "SELECT users.id, users.first_name, users.last_name, users.email, departments.name as department_name
        FROM users
        LEFT JOIN departments ON users.department_id = departments.id
        WHERE users.role_id = 2"; // Only fetch HOD members
$stmt = $pdo->query($sql);
$hods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View HODs</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Head of Departments List</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hods as $hod): ?>
            <tr>
                <td><?php echo htmlspecialchars($hod['first_name'] . ' ' . $hod['last_name']); ?></td>
                <td><?php echo htmlspecialchars($hod['email']); ?></td>
                <td><?php echo htmlspecialchars($hod['department_name']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
</body>
</html>
