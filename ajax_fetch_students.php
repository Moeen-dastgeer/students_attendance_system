<?php
session_start();
include 'db.php';

$campus_id = $_SESSION['campus_id'] ?? null;
$role = $_SESSION['role'] ?? '';
$search = $_POST['search'] ?? '';
$course_id = $_POST['course_id'] ?? '';
$shift_id = $_POST['shift_id'] ?? '';
$campus_filter = $_POST['campus_id'] ?? '';

$query = "SELECT s.*, c.course_name, sh.shift_name 
          FROM students s
          LEFT JOIN courses c ON s.course_id = c.id
          LEFT JOIN shifts sh ON s.shift_id = sh.id
          WHERE 1=1";

if ($role == 'admin' && $campus_id) {
  $query .= " AND s.campus_id = $campus_id";
} elseif ($campus_filter) {
  $query .= " AND s.campus_id = $campus_filter";
}

if ($course_id) {
  $query .= " AND s.course_id = $course_id";
}
if ($shift_id) {
  $query .= " AND s.shift_id = $shift_id";
}
if ($search) {
  $search = $conn->real_escape_string($search);
  $query .= " AND s.name LIKE '%$search%'";
}

$query .= " ORDER BY s.name ASC";
$res = $conn->query($query);
?>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>Image</th>
      <th>Name</th>
      <th>Course</th>
      <th>Shift</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $res->fetch_assoc()): ?>
    <tr>
      <td>
        <?php if ($row['image']): ?>
          <img src="uploads/<?= $row['image'] ?>" width="50" class="img-thumbnail">
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
      <td><?= $row['name'] ?></td>
      <td><?= $row['course_name'] ?></td>
      <td><?= $row['shift_name'] ?></td>
      <td>
        <select class="form-select form-select-sm" onchange="updateStatus(<?= $row['id'] ?>, this.value)">
          <option value="active" <?= $row['status'] == 'active' ? 'selected' : '' ?>>Active</option>
          <option value="passout" <?= $row['status'] == 'passout' ? 'selected' : '' ?>>Passout</option>
          <option value="blocked" <?= $row['status'] == 'blocked' ? 'selected' : '' ?>>Blocked</option>
        </select>
      </td>
      <td>
        <a href="student_profile.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">👤 View</a>
        <a href="edit_student.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mx-1">✏️ Edit</a>
        <a href="delete_student.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this student?')">🗑️ Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
