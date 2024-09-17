<?php
// Include the database connection
require 'db.php';

// Start session and check if user is logged in as staff
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    die('Access denied.');
}

if (isset($_GET['id'])) {
    $announcement_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Check if the announcement belongs to the logged-in staff member
        $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ? AND posted_by = ?");
        $stmt->execute([$announcement_id, $user_id]);
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($announcement) {
            // Delete the announcement
            $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ? AND posted_by = ?");
            $stmt->execute([$announcement_id, $user_id]);
            header('Location: staff_dashboard.php');
            exit;
        } else {
            die('Announcement not found or you do not have permission to delete it.');
        }
    } catch (PDOException $e) {
        die('Error: ' . $e->getMessage());
    }
} else {
    die('Invalid request.');
}
