<?php include 'teacher_header.php'; ?>
<?php
include 'db.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>❌ Student ID not provided.</div>";
    include 'teacher_footer.php'; exit;
}

$id = intval($_GET['id']);
$student = $conn->query("SELECT s.*, c.course_name, sh.shift_name 
    FROM students s
    JOIN courses c ON s.course_id = c.id
    JOIN shifts sh ON s.shift_id = sh.id
    WHERE s.id = $id")->fetch_assoc();

if (!$student) {
    echo "<div class='alert alert-danger'>❌ Student not found.</div>";
    include 'teacher_footer.php'; exit;
}

// Validate: is student assigned to this teacher?
$combo = $student['course_id'] . '-' . $student['shift_id'];
$assigned = explode(',', $_SESSION['assigned_class']);

if (!in_array($combo, array_map('trim', $assigned))) {
    echo "<div class='alert alert-danger'>❌ You are not assigned to this student's course-shift.</div>";
    include 'teacher_footer.php'; exit;
}

$image = $student['image'] ? "<img src='uploads/{$student['image']}' class='img-thumbnail' width='100'>" : "No Image";
$att = $conn->query("SELECT date, status FROM attendance WHERE student_id = $id ORDER BY date DESC");
?>

<div class="card shadow-sm">
  <div class="card-body">
    <h3 class="card-title mb-3">👤 Student Profile</h3>

    <div class="mb-3">
      <?= $image ?><br><br>
      <p><strong>Name:</strong> <?= $student['name']; ?></p>
      <p><strong>Roll No:</strong> <?= $student['roll']; ?></p>
      <p><strong>Course:</strong> <?= $student['course_name']; ?></p>
      <p><strong>Shift:</strong> <?= $student['shift_name']; ?></p>
    </div>

    <h5>📅 Attendance History:</h5>
    <div class="table-responsive">
      <table class="table table-sm table-bordered">
        <thead class="table-light">
          <tr>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $att->fetch_assoc()): ?>
            <tr>
              <td><?= $row['date']; ?></td>
              <td><?= ucfirst($row['status']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <a href="students_teacher.php" class="btn btn-secondary mt-3">← Back to Students</a>
  </div>
</div>

<?php include 'teacher_footer.php'; ?>
