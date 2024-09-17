<?php
// Include the database connection
require 'db.php';

// Get the department ID from the request (assumed to be sent via GET)
$department_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

// Prepare the query to fetch courses for the specified department
$query = "SELECT id, name FROM courses WHERE department_id = :department_id";
$statement = $pdo->prepare($query);
$statement->bindParam(':department_id', $department_id, PDO::PARAM_INT);

// Execute the query
$statement->execute();

// Fetch all courses
$courses = $statement->fetchAll(PDO::FETCH_ASSOC);

// Set the header to indicate the response is JSON
header('Content-Type: application/json');

// Output the courses as JSON
echo json_encode($courses);
?>
