<?php
// Include the database connection file with the correct path
include('db.php');

// Check if the form was submitted and the action field is set
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_staff'])) {
    // Fetch and sanitize form data
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $course_id = intval($_POST['course_id']);
    $department_id = intval($_POST['department_id']);
    
    // Hash the default password ('12345')
    $password_hash = password_hash('12345', PASSWORD_DEFAULT);
    
    // Begin database transaction
    $pdo->beginTransaction();
    
    try {
        // Insert staff into the users table
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, role_id, course_id, department_id, is_first_login) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $role_id = 3; // Assuming role_id for staff is 3
        $stmt->execute([
            $first_name, 
            $last_name, 
            $email, 
            $password_hash, 
            $role_id, 
            $course_id, 
            $department_id, 
            true // Set is_first_login to true (forcing password change later)
        ]);

        // Commit the transaction
        $pdo->commit();

        echo "Staff added successfully!";
    } catch (PDOException $e) {
        // Rollback transaction if something goes wrong
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request!";
}
?>
