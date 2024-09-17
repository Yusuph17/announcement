<?php
require 'db.php'; // Include database connection

// Initialize variables
$first_name = '';
$last_name = '';
$email = '';
$course_id = '';
$class_id = '';
$department_id = ''; // New variable for department
$password_hash = password_hash('12345', PASSWORD_DEFAULT); // Default password hash

// Fetch courses, classes, and departments
try {
    $courses_query = $pdo->query("SELECT id, name, department_id FROM courses");
    $courses = $courses_query->fetchAll(PDO::FETCH_ASSOC);

    $classes_query = $pdo->query("SELECT id, class_name FROM class");
    $classes = $classes_query->fetchAll(PDO::FETCH_ASSOC);

    $departments_query = $pdo->query("SELECT id, name FROM departments");
    $departments = $departments_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database query error: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $course_id = intval($_POST['course_id']);
    $class_id = intval($_POST['class_id']);

    // Get the department_id based on selected course
    $stmt = $pdo->prepare("SELECT department_id FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC);
    $department_id = $department['department_id']; // Automatically assign the department

    // Generate email address
    $email = strtolower($first_name . '.' . $last_name . '@gmail.com');

    // Begin transaction
    $pdo->beginTransaction();

    try {
        // Insert into users table
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, role_id, course_id, class_id, department_id, is_first_login) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $role_id = 4; // Assuming role_id for students is 4
        $stmt->execute([
            $first_name, 
            $last_name, 
            $email, 
            $password_hash, 
            $role_id, 
            $course_id, 
            $class_id, 
            $department_id, // Assign department
            true // Set is_first_login to true
        ]);

        // Get the ID of the newly inserted user
        $user_id = $pdo->lastInsertId();

        // Insert into students table with additional details
        $stmt = $pdo->prepare("
            INSERT INTO students (user_id, first_name, last_name, email, password, course_id, class_id, department_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $first_name, $last_name, $email, $password_hash, $course_id, $class_id, $department_id]);

        // Commit transaction
        $pdo->commit();

        echo "Student added successfully!";
    } catch (PDOException $e) {
        // Rollback transaction if something fails
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add a Student</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="course_id">Course:</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">Select a course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['id']); ?>"><?php echo htmlspecialchars($course['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="class_id">Class:</label>
                <select id="class_id" name="class_id" class="form-control" required>
                    <option value="">Select a class</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?php echo htmlspecialchars($class['id']); ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Admin Dashboard</a>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
