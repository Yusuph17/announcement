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
$error = '';
$success = '';

// Fetch the current profile information
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error fetching profile: ' . $e->getMessage());
}

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'All fields are required.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $email, $student_id]);
            $success = 'Profile updated successfully!';
        } catch (PDOException $e) {
            $error = 'Error updating profile: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">

    <!-- Dashboard Header with Buttons -->
    <div class="d-flex justify-content-between align-items-center">
        <h2>View Profile</h2>
        <a href="student_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Display Errors or Success Messages -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <!-- Profile Form -->
    <form action="" method="post" class="mt-4">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
</body>
</html>
