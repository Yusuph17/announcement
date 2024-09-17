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

// Fetch all students in the HOD's department
try {
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email
        FROM users
        WHERE role_id = 4 AND department_id = (
            SELECT department_id FROM users WHERE id = ?
        )
    ");
    $stmt->execute([$hod_id]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Error fetching students: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mt-5">
        <h2>Students in Your Department</h2>
        <!-- Back Button -->
        <a href="hod_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <!-- Display students in a table format -->
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
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['id']); ?></td>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
