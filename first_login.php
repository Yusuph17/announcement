<?php
// Include the database connection
require 'db.php';

// Start the session only if it’s not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT email, is_first_login FROM users WHERE id = :user_id";
$statement = $pdo->prepare($query);
$statement->execute(['user_id' => $user_id]);
$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    // Validate input
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Update user details
        $update_query = "UPDATE users SET email = :email, password = :password, is_first_login = 0 WHERE id = :user_id";
        $update_statement = $pdo->prepare($update_query);
        $update_statement->execute([
            'email' => $new_email,
            'password' => password_hash($new_password, PASSWORD_DEFAULT),
            'user_id' => $user_id
        ]);

        // Redirect to appropriate dashboard after update
        $role_query = "SELECT ur.role_name FROM users u JOIN user_roles ur ON u.role_id = ur.id WHERE u.id = :user_id";
        $role_statement = $pdo->prepare($role_query);
        $role_statement->execute(['user_id' => $user_id]);
        $role = $role_statement->fetch(PDO::FETCH_ASSOC);

        switch ($role['role_name']) {
            case 'Admin':
                header('Location: admin_dashboard.php');
                break;
            case 'HOD':
                header('Location: hod_dashboard.php');
                break;
            case 'Staff':
                header('Location: staff_dashboard.php');
                break;
            case 'Student':
                header('Location: student_dashboard.php');
                break;
            default:
                echo "Role not recognized.";
                exit();
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First Login - Update Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Update Your Details</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Details</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
