<?php
// Include the database connection
require 'db.php';

// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    die('User not logged in.');
}

$file = isset($_GET['file']) ? $_GET['file'] : '';
$file_path = 'uploads/' . $file;

if (file_exists($file_path)) {
    // Send headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_path));
    
    // Read the file and send it to the user
    readfile($file_path);
    exit;
} else {
    die('File not found.');
}
