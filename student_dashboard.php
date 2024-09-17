<?php
// Include the database connection
require 'db.php';

// Start session
session_start();

// Check if the user is logged in as Student
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 4) { // Assuming role_id 4 is for Student
    die('Access denied.');
}

$student_id = $_SESSION['user_id'];

// Fetch recent announcements for the student's department
try {
    $stmt = $pdo->prepare("
        SELECT a.id, a.title, a.description, a.created_at, u.first_name, u.last_name
        FROM announcements a
        JOIN users u ON a.posted_by = u.id
        WHERE u.department_id = (
            SELECT department_id FROM users WHERE id = ?
        )
        ORDER BY a.created_at DESC
        LIMIT 5
    ");
    $stmt->execute([$student_id]);
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error fetching announcements: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .header-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logout-btn {
            margin-left: auto;
        }
    </style>
</head>
<body>
<div class="container mt-5">

    <!-- Dashboard Header with Buttons -->
    <div class="header-buttons">
        <h2>Student Dashboard</h2>
        <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
    </div>

    <!-- Manage Announcements and Profile -->
    <div class="mt-4">
        <a href="view_announcements.php" class="btn btn-primary">View Announcements</a>
        <a href="view_profile.php" class="btn btn-secondary">View Profile</a>
    </div>

    <!-- Display Recent Announcements -->
    <h3>Recent Announcements</h3>
    <?php if (count($announcements) > 0): ?>
        <ul class="list-group">
            <?php foreach ($announcements as $announcement): ?>
                <li class="list-group-item">
                    <h5><?php echo htmlspecialchars($announcement['title']); ?></h5>
                    <p class="mb-1">
                        <?php echo htmlspecialchars(substr($announcement['description'], 0, 100)) . '...'; ?>
                    </p>
                    <small>Posted by: <?php echo htmlspecialchars($announcement['first_name'] . ' ' . $announcement['last_name']); ?> on <?php echo htmlspecialchars($announcement['created_at']); ?></small>
                    <div class="mt-2">
                        <a href="view_announcement.php?id=<?php echo $announcement['id']; ?>" class="btn btn-primary btn-sm">Read More</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No announcements found.</p>
    <?php endif; ?>
</div>
</body>
</html>
