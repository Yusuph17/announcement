<?php
session_start();
if (!in_array($_SESSION['role'], ['admin', 'hod', 'staff'])) {
    header('Location: login.php');
    exit;
}
include 'db/database.php';

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$query = "SELECT a.id, a.title, a.content, a.posted_by, a.department_id, a.course_id, u.name AS posted_by_name
          FROM announcements a
          JOIN users u ON a.posted_by = u.id
          WHERE (a.department_id = (SELECT department_id FROM users WHERE id = ?) OR a.department_id IS NULL)";

if ($role == 'admin') {
    $query .= "";
} else if ($role == 'hod') {
    $query .= " AND a.department_id = (SELECT department_id FROM users WHERE id = ?)";
} else if ($role == 'staff') {
    $query .= " AND a.posted_by = ?";
}

$stmt = $conn->prepare($query);
if ($role == 'admin') {
    $stmt->bind_param("i", $user_id);
} else if ($role == 'hod') {
    $stmt->bind_param("ii", $user_id, $user_id);
} else if ($role == 'staff') {
    $stmt->bind_param("ii", $user_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Manage Announcements</h1>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Posted By</th>
                <th>Department</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['content']); ?></td>
                    <td><?php echo htmlspecialchars($row['posted_by_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['department_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['course_id']); ?></td>
                    <td>
                        <a href="edit_announcement.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_announcement.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
