<?php
// Include the database connection
require 'db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $posted_by = $_POST['posted_by'];  // Assuming logged in staff ID
    $file_path = null;
    $send_to_entire_college = isset($_POST['send_to_entire_college']);
    $send_to_entire_department = isset($_POST['send_to_entire_department']);
    $department_id = $_POST['department_id'] ?? null;
    $course_id = $send_to_entire_department ? null : ($_POST['course_id'] ?? null);

    // Handle file uploads
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = time() . '-' . $_FILES['attachment']['name'];  // Unique file name
        $file_path = 'uploads/' . $file_name;
        move_uploaded_file($file_tmp, $file_path);
    }

    // Validate inputs
    if (empty($title) || empty($description)) {
        $errors[] = "Title and description are required.";
    } elseif (!$send_to_entire_college && !$department_id) {
        $errors[] = "Please select a department or choose 'Send to Entire College'.";
    }

    // Insert the announcement if no errors
    if (empty($errors)) {
        $query = "
            INSERT INTO announcements (title, description, department_id, course_id, posted_by, file_path)
            VALUES (:title, :description, :department_id, :course_id, :posted_by, :file_path)
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'department_id' => $send_to_entire_college ? null : $department_id,
            'course_id' => $send_to_entire_college || $send_to_entire_department ? null : $course_id,
            'posted_by' => $posted_by,
            'file_path' => $file_path
        ]);

        $success = true;
    }
}

// Fetch departments for dropdown
$departments = $pdo->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Post New Announcement</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Announcement posted successfully!</div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="post_announcement.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
        </div>

        <div class="form-group">
            <label for="department_id">Department</label>
            <select name="department_id" id="department_id" class="form-control" onchange="fetchCourses()">
                <option value="">-- Select Department --</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?php echo $department['id']; ?>"><?php echo $department['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="course_id">Course</label>
            <select name="course_id" id="course_id" class="form-control">
                <option value="">-- Select Course --</option>
                <!-- Courses will be dynamically loaded here -->
            </select>
        </div>

        <div class="form-group">
            <label for="attachment">Attachment (optional)</label>
            <input type="file" name="attachment" id="attachment" class="form-control-file">
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="send_to_entire_department" name="send_to_entire_department" onclick="toggleCourse()">
            <label class="form-check-label" for="send_to_entire_department">Send to Entire Department</label>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="send_to_entire_college" name="send_to_entire_college" onclick="toggleDepartment()">
            <label class="form-check-label" for="send_to_entire_college">Send to Entire College</label>
        </div>

        <input type="hidden" name="posted_by" value="1"> <!-- Logged in staff ID -->

        <button type="submit" class="btn btn-primary mt-3">Post Announcement</button>
    </form>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- AJAX to dynamically load courses based on department selection -->
<script>
    function fetchCourses() {
        const departmentId = document.getElementById('department_id').value;
        const courseSelect = document.getElementById('course_id');
        
        if (!departmentId) {
            courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
            return;
        }

        $.ajax({
            url: 'get_courses.php',
            type: 'GET',
            data: { department_id: departmentId },
            success: function(data) {
                const courses = JSON.parse(data);
                let options = '<option value="">-- Select Course --</option>';
                courses.forEach(function(course) {
                    options += `<option value="${course.id}">${course.name}</option>`;
                });
                courseSelect.innerHTML = options;
            }
        });
    }

    function toggleCourse() {
        const sendToDepartment = document.getElementById('send_to_entire_department').checked;
        document.getElementById('course_id').disabled = sendToDepartment;
    }

    function toggleDepartment() {
        const sendToCollege = document.getElementById('send_to_entire_college').checked;
        document.getElementById('department_id').disabled = sendToCollege;
        document.getElementById('course_id').disabled = sendToCollege || document.getElementById('send_to_entire_department').checked;
    }
</script>

</body>
</html>
