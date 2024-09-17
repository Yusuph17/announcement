<?php
// Include the database connection
require 'db.php';

// Start session and check if user is logged in as staff
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die('Access denied.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $file_path = '';

    // Handle file upload if there's a file
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_name = basename($_FILES['file']['name']);
        $file_path = 'uploads/' . $file_name;

        if (!move_uploaded_file($file_tmp, $file_path)) {
            $error = 'Error uploading file.';
        }
    }

    if (empty($error)) {
        // Insert into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, description, file_path, posted_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $file_path, $_SESSION['user_id']]);
            header('Location: staff_dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Post a New Announcement</h2>

    <!-- Back to Dashboard Button -->
    <a href="staff_dashboard.php" class="btn btn-secondary mb-4">Back to Dashboard</a>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="staff_post_announcement.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="file">Attach a File (optional)</label>
            <input type="file" class="form-control-file" id="file" name="file">
        </div>

        <button type="submit" class="btn btn-primary">Post Announcement</button>
    </form>
</div>
</body>
</html>
