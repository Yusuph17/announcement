<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
include 'db/database.php';

if (isset($_GET['id'])) {
    $staff_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'staff'");
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();

    header('Location: view_staff.php');
    exit;
}
?>
