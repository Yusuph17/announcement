<?php
// Include database connection
require 'db.php';

// Query to select all students from users table and display in student table
$query = "
    SELECT 
        users.id AS user_id, 
        users.first_name, 
        users.last_name, 
        users.email, 
        courses.name AS course_name 
    FROM users 
    JOIN courses ON users.course_id = courses.id
    WHERE users.role_id = 4";
$statement = $pdo->query($query);
$students = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1>Student List</h1>

    <?php if (count($students) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Course</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['user_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['course_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">
            No students have signed up yet.
        </div>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn btn-primary">Back to Admin Dashboard</a>
</div>

</body>
</html>
