<?php include 'admin_header.php'; ?>
<?php include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location:index.php');
    exit;
}

$campus_id = $_SESSION['campus_id'];
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="mb-5">
        <h3 class="card-title">📊 Attendance Summary (Present / Absent / Late / Leave)</h3>
      </div>

      <!-- Filter Form -->
      <form method="GET" class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label">From Date:</label>
          <input type="date" name="start_date" class="form-control" required value="<?= $_GET['start_date'] ?? '' ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">To Date:</label>
          <input type="date" name="end_date" class="form-control" required value="<?= $_GET['end_date'] ?? '' ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">Course:</label>
          <select name="course_id" class="form-select">
            <option value="">All Courses</option>
            <?php
            $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
            while ($c = $courses->fetch_assoc()) {
              $sel = ($_GET['course_id'] ?? '') == $c['id'] ? 'selected' : '';
              echo "<option value='{$c['id']}' $sel>{$c['course_name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Shift:</label>
          <select name="shift_id" class="form-select">
            <option value="">All Shifts</option>
            <?php
            $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
            while ($s = $shifts->fetch_assoc()) {
              $sel = ($_GET['shift_id'] ?? '') == $s['id'] ? 'selected' : '';
              echo "<option value='{$s['id']}' $sel>{$s['shift_name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-3 d-grid">
          <button type="submit" class="btn btn-primary">🔍 Filter</button>
        </div>
      </form>

      <?php
      if (isset($_GET['start_date'], $_GET['end_date'])) {
          $start = $_GET['start_date'];
          $end = $_GET['end_date'];
          $course_id = $_GET['course_id'] ?? '';
          $shift_id = $_GET['shift_id'] ?? '';

          $query = "SELECT * FROM students WHERE campus_id = $campus_id";
          if ($course_id) $query .= " AND course_id = $course_id";
          if ($shift_id) $query .= " AND shift_id = $shift_id";

          $students = $conn->query($query);

          echo "<div class='table-responsive mt-3'><table class='table table-bordered table-striped'>
                  <thead class='table-light'>
                    <tr>
                      <th>Image</th>
                      <th>Name</th>
                      <th>Course</th>
                      <th>Shift</th>
                      <th>Present</th>
                      <th>Absent</th>
                      <th>Late</th>
                      <th>Leave</th>
                    </tr>
                  </thead>
                  <tbody>";

          $labels = [];
          $present = [];
          $absent = [];
          $late = [];

          while ($student = $students->fetch_assoc()) {
              $sid = $student['id'];
              $counts = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0];

              $att = $conn->query("SELECT status FROM attendance 
                                   WHERE student_id = $sid AND date BETWEEN '$start' AND '$end'");
              while ($row = $att->fetch_assoc()) {
                  if (isset($counts[$row['status']])) {
                      $counts[$row['status']]++;
                  }
              }

              $img = $student['image'] ? "<img src='uploads/{$student['image']}' width='50' class='img-thumbnail'>" : "-";

              $course = $conn->query("SELECT course_name FROM courses WHERE id = {$student['course_id']}")->fetch_assoc()['course_name'] ?? '-';
              $shift = $conn->query("SELECT shift_name FROM shifts WHERE id = {$student['shift_id']}")->fetch_assoc()['shift_name'] ?? '-';

              echo "<tr>
                      <td>$img</td>
                      <td>{$student['name']}</td>
                      <td>$course</td>
                      <td>$shift</td>
                      <td>{$counts['present']}</td>
                      <td>{$counts['absent']}</td>
                      <td>{$counts['late']}</td>
                      <td>{$counts['leave']}</td>
                    </tr>";

              $labels[] = $student['name'];
              $present[] = $counts['present'];
              $absent[] = $counts['absent'];
              $late[] = $counts['late'];
          }

          echo "</tbody></table></div>";
      }
      ?>

      <?php if (!empty($labels)): ?>
        <canvas id="attendanceChart" style="max-width: 100%; height: 400px; margin-top: 40px;"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          const ctx = document.getElementById('attendanceChart').getContext('2d');
          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: <?= json_encode($labels); ?>,
              datasets: [
                {
                  label: 'Present',
                  data: <?= json_encode($present); ?>,
                  backgroundColor: '#198754'
                },
                {
                  label: 'Absent',
                  data: <?= json_encode($absent); ?>,
                  backgroundColor: '#dc3545'
                },
                {
                  label: 'Late',
                  data: <?= json_encode($late); ?>,
                  backgroundColor: '#fd7e14'
                }
              ]
            },
            options: {
              responsive: true,
              plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Attendance Summary Chart' }
              }
            }
          });
        </script>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
