<?php
// Include the database connection
require 'db.php';

// Start session and check if user is logged in as staff
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die('Access denied.');
}

$user_id = $_SESSION['user_id'];
$error = '';

// Get the announcement to edit
if (isset($_GET['id'])) {
    $announcement_id = $_GET['id'];

    try {
        // Fetch the announcement if it belongs to the logged-in staff
        $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ? AND posted_by = ?");
        $stmt->execute([$announcement_id, $user_id]);
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$announcement) {
            die('Announcement not found or access denied.');
        }
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
} else {
    die('Invalid request.');
}

// Handle form submission for editing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '';
    $description = isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '';
    $file_path = $announcement['file_path'];

    // Check if title and description are set
    if (empty($title) || empty($description)) {
        $error = 'Both title and description are required.';
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file']['name']);
        $upload_dir = 'uploads/';
        $file_path = $upload_dir . $file_name;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            $error = 'Failed to upload file.';
        }
    }

    // Update the announcement in the database
    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE announcements 
                SET title = ?, description = ?, file_path = ? 
                WHERE id = ? AND posted_by = ?
            ");
            $stmt->execute([$title, $description, $file_path, $announcement_id, $user_id]);
            echo '<div class="alert alert-success">Announcement updated successfully.</div>';
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
    <title>Edit Announcement</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Edit Announcement</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($announcement['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="file">Attach File (optional):</label>
            <input type="file" id="file" name="file" class="form-control-file">
        </div>
        <button type="submit" class="btn btn-primary">Update Announcement</button>
    </form>
</div>
</body>
</html>
