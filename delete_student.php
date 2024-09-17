<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}
include 'db/database.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    header('Location: view_students.php');
    exit;
}
?>
