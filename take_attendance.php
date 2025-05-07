<?php include 'admin_header.php'; ?>
<?php
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location:index.php');
    exit;
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="mb-5">
        <h3 class="card-title">📝 Take Attendance (Course + Shift)</h3>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label for="course_filter" class="form-label">Select Course</label>
          <select id="course_filter" class="form-select" onchange="loadStudentsByCourseShift()">
            <option value="">-- Select Course --</option>
            <?php
            $courses = $conn->query("SELECT * FROM courses ORDER BY course_name ASC");
            while ($c = $courses->fetch_assoc()) {
                echo "<option value='{$c['id']}'>{$c['course_name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-6">
          <label for="shift_filter" class="form-label">Select Shift</label>
          <select id="shift_filter" class="form-select" onchange="loadStudentsByCourseShift()">
            <option value="">-- Select Shift --</option>
            <?php
            $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name ASC");
            while ($s = $shifts->fetch_assoc()) {
                echo "<option value='{$s['id']}'>{$s['shift_name']}</option>";
            }
            ?>
          </select>
        </div>
      </div>

      <div id="student_table" class="table-responsive"></div>
    </div>
  </div>
</div>

<script src="attendance.js"></script>
<?php include 'admin_footer.php'; ?>
