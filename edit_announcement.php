<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: login.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
$announcement_id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_announcement'])) {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $course_id = intval($_POST['course_id']);
    $class_id = intval($_POST['class_id']);
    
    $file_path = null;
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($_FILES["file"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            exit();
        }

        if ($fileType != "pdf" && $fileType != "doc" && $fileType != "docx" && $fileType != "jpg" && $fileType != "png" && $fileType != "jpeg") {
            echo "Sorry, only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.";
            exit();
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE announcements
            SET title = ?, content = ?, file_path = ?, course_id = ?, class_id = ?
            WHERE id = ? AND staff_id = ?
        ");
        $stmt->execute([$title, $content, $file_path, $course_id, $class_id, $announcement_id, $staff_id]);

        echo "Announcement updated successfully!";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    try {
        $stmt = $pdo->prepare("
            SELECT title, content, file_path, course_id, class_id
            FROM announcements
            WHERE id = ? AND staff_id = ?
        ");
        $stmt->execute([$announcement_id, $staff_id]);
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Announcement</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="edit_announcement" value="1">

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required><br><br>

        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($announcement['content']); ?></textarea><br><br>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <?php
            $stmt = $pdo->query("SELECT id, name FROM courses");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = $row['id'] == $announcement['course_id'] ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($row['id']) . "\" $selected>" . htmlspecialchars($row['name']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="class_id">Class:</label>
        <select id="class_id" name="class_id" required>
            <?php
            $stmt = $pdo->query("SELECT id, class_name FROM class");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = $row['id'] == $announcement['class_id'] ? 'selected' : '';
                echo "<option value=\"" . htmlspecialchars($row['id']) . "\" $selected>" . htmlspecialchars($row['class_name']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="file">Attach File (optional):</label>
        <input type="file" id="file" name="file"><br><br>

        <input type="submit" value="Update Announcement">
    </form>
</body>
</html>
