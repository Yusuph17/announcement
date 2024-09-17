<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}

$announcement_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $pdo->prepare("
        SELECT a.title, a.description, a.file_path, u.first_name AS poster_first_name, u.last_name AS poster_last_name, r.role
        FROM announcements a
        JOIN users u ON a.posted_by = u.id
        JOIN user_roles r ON u.role_id = r.id
        WHERE a.id = ?
    ");
    $stmt->execute([$announcement_id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$announcement) {
        die('Announcement not found.');
    }

    // Automatically trigger the file download
    if (!empty($announcement['file_path'])) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($announcement['file_path']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($announcement['file_path']));
        flush(); // Flush system output buffer
        readfile($announcement['file_path']);
        exit;
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Announcement</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h1 class="mt-5">Announcement Details</h1>
    <h2><?php echo htmlspecialchars($announcement['title']); ?></h2>
    <p><?php echo htmlspecialchars($announcement['description']); ?></p>
    <p><strong>Posted by:</strong> <?php echo htmlspecialchars($announcement['poster_first_name']) . ' ' . htmlspecialchars($announcement['poster_last_name']); ?> (<?php echo htmlspecialchars($announcement['role']); ?>)</p>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
