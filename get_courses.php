<?php
require 'db.php';

$department_id = $_GET['department_id'] ?? null;

if ($department_id) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE department_id = ?");
    $stmt->execute([$department_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($courses);
}
