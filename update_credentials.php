<?php
session_start();
require 'db.php';

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the student's data
$student_id = $_SESSION['student_id'];
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $email = htmlspecialchars($_POST['email']);
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Update email and password in the database
    $update_stmt = $pdo->prepare("UPDATE students SET email = ?, password = ?, is_first_login = FALSE WHERE id = ?");
    if ($update_stmt->execute([$email, $new_password, $student_id])) {
        echo "Credentials updated successfully!";
        header("Location: student_dashboard.php");
        exit();
    } else {
        echo "Error updating credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Credentials</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="container">
    <h1 class="mt-5">Update Your Credentials</h1>
    <form method="post" action="">
        <div class="form-group">
            <label for="email">New Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</body>
</html>
