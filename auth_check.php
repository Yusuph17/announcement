<?php
session_start();
require 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT role_id FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$role_id = $stmt->fetchColumn();

if (!$role_id) {
    header('Location: login.php');
    exit();
}

$_SESSION['role_id'] = $role_id;
?>
