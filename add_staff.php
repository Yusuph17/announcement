<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Staff</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
    <h1>Add New Staff</h1>
    <form action="process.php" method="POST">
        <input type="hidden" name="add_staff" value="1">

        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number"><br><br>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <option value="">Select Course</option>
            <?php
            require 'db.php';
            $stmt = $pdo->query("SELECT id, name, department_id FROM courses");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value=\"" . htmlspecialchars($row['id']) . "\" data-department=\"" . htmlspecialchars($row['department_id']) . "\">" . htmlspecialchars($row['name']) . "</option>";
            }
            ?>
        </select><br><br>

        <label for="department_id">Department:</label>
        <select id="department_id" name="department_id" required readonly>
            <option value="">Select a course first</option>
        </select><br><br>

        <input type="submit" value="Add Staff">
    </form>

    <script>
        // Automatically assign department when course is selected
        $('#course_id').on('change', function() {
            var departmentId = $(this).find('option:selected').data('department');
            $('#department_id').html('<option value="' + departmentId + '">Department ' + departmentId + '</option>');
        });
    </script>
</body>
</html>
