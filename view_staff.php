<?php
require 'db.php'; // Include database connection

// Fetch staff data
$sql = "SELECT users.id, users.first_name, users.last_name, users.email, departments.name as department_name
        FROM users
        LEFT JOIN departments ON users.department_id = departments.id
        WHERE users.role_id = 3"; // Only fetch staff members
$stmt = $pdo->query($sql);
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Staff</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Staff List</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staff as $member): ?>
            <tr>
                <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                <td><?php echo htmlspecialchars($member['email']); ?></td>
                <td><?php echo htmlspecialchars($member['department_name']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
</body>
</html>
