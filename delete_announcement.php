<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
$announcement_id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("
        DELETE FROM announcements
        WHERE id = ? AND staff_id = ?
    ");
    $stmt->execute([$announcement_id, $staff_id]);

    echo "Announcement deleted successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
