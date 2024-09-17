<?php
// Include the database connection
require 'db.php';

// Start session and check if the user is logged in as HOD
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) { // Assuming HOD's role ID is 2
    die('Access denied.');
}

$hod_id = $_SESSION['user_id'];
$error = '';

// Fetch all staff in the HOD's department
try {
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, role_id
        FROM users
        WHERE role_id = 3 AND department_id = (
            SELECT department_id FROM users WHERE id = ?
        )
    ");
    $stmt->execute([$hod_id]);
    $staffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching staff: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-5">
        <h2>Staff in Your Department</h2>
        <!-- Back Button -->
        <a href="hod_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Display staff in a table format -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($staffs)): ?>
                    <?php foreach ($staffs as $staff): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($staff['id']); ?></td>
                            <td><?php echo htmlspecialchars($staff['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($staff['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($staff['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No staff members found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
