<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .dashboard {
            margin-top: 50px;
        }
        .btn-block {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container dashboard">
    <h1 class="text-center mb-4">Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-4">
            <a href="add_student.php" class="btn btn-primary btn-block">Add Student</a>
        </div>
        <div class="col-md-4">
            <a href="add_staff.php" class="btn btn-primary btn-block">Add Staff</a>
        </div>
        <div class="col-md-4">
            <a href="add_hod.php" class="btn btn-primary btn-block">Add HOD</a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <a href="view_student.php" class="btn btn-secondary btn-block">View Students</a>
        </div>
        <div class="col-md-4">
            <a href="view_staff.php" class="btn btn-secondary btn-block">View Staff</a>
        </div>
        <div class="col-md-4">
            <a href="view_hod.php" class="btn btn-secondary btn-block">View HODs</a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <a href="post_announcement.php" class="btn btn-success btn-block">Post Announcement</a>
        </div>
        <div class="col-md-6">
            <a href="manage_announcement.php" class="btn btn-warning btn-block">Manage Announcements</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
