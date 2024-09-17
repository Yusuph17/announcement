<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
session_start();

$email = $password = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    try {
        // Prepare and execute the query to fetch user details
        $stmt = $pdo->prepare("
            SELECT id, password, is_first_login, role_id
            FROM users
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['is_first_login'] = $user['is_first_login'];

            if ($user['is_first_login'] == 1) {
                header('Location: first_login.php');
                exit();
            }

            // Redirect based on role_id
            switch ($user['role_id']) {
                case 1:
                    header('Location: admin_dashboard.php');
                    exit();
                case 2:
                    header('Location: hod_dashboard.php');
                    exit();
                case 3:
                    header('Location: staff_dashboard.php');
                    exit();
                case 4:
                    header('Location: student_dashboard.php');
                    exit();
                default:
                    $error = 'Role not recognized.';
                    break;
            }
        } else {
            $error = 'Invalid email or password.';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
