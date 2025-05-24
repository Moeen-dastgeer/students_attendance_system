<?php
include 'db.php';

$campus_id = $_POST['campus_id'] ?? '';
$course_id = $_POST['course_id'] ?? '';
$shift_id = $_POST['shift_id'] ?? '';
$search = $_POST['search'] ?? '';

$where = "WHERE 1=1";
if ($campus_id) $where .= " AND s.campus_id = $campus_id";
if ($course_id) $where .= " AND s.course_id = $course_id";
if ($shift_id) $where .= " AND s.shift_id = $shift_id";
if ($search) $where .= " AND s.name LIKE '%$search%'";

$sql = "SELECT s.*, c.course_name, sh.shift_name, cam.name as campus_name
        FROM students s
        LEFT JOIN courses c ON s.course_id = c.id
        LEFT JOIN shifts sh ON s.shift_id = sh.id
        LEFT JOIN campuses cam ON s.campus_id = cam.id
        $where
        ORDER BY s.name";

$result = $conn->query($sql);

echo "<div class='table-responsive'><table class='table table-bordered table-striped'>
<thead class='table-dark'>
<tr>
  <th>Image</th>
  <th>Name</th>
  <th>Gender</th>
  <th>Phone</th>
  <th>Course</th>
  <th>Shift</th>
  <th>Campus</th>
</tr>
</thead>
<tbody>";

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $img = $row['image'] ? "<img src='uploads/{$row['image']}' width='40' class='img-thumbnail'>" : "-";
    echo "<tr>
            <td>$img</td>
            <td>{$row['name']}</td>
            <td>{$row['gender']}</td>
            <td>{$row['student_phone']}</td>
            <td>{$row['course_name']}</td>
            <td>{$row['shift_name']}</td>
            <td>{$row['campus_name']}</td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='7' class='text-center'>No students found.</td></tr>";
}
echo "</tbody></table></div>";
?>
