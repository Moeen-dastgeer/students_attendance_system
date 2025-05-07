<?php
include 'admin_header.php'; 
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location:index.php');
    exit;
}
?>


<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">📊 Attendance Summary (Present / Absent / Late)</h3>

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
          <label class="form-label">Class:</label>
          <select name="class" class="form-select">
            <option value="all">All Classes</option>
            <?php
            $classes = $conn->query("SELECT DISTINCT class FROM students");
            while ($c = $classes->fetch_assoc()) {
              $selected = ($_GET['class'] ?? '') == $c['class'] ? 'selected' : '';
              echo "<option value='{$c['class']}' $selected>{$c['class']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-3 d-grid">
          <button type="submit" class="btn btn-primary">🔍 Filter</button>
        </div>
      </form>

      <?php if (isset($_GET['start_date'], $_GET['end_date'], $_GET['class'])): ?>
        <div class="mb-3 d-flex gap-2 flex-wrap">
          <a href="export_attendance_summary_pdf.php?start_date=<?= $_GET['start_date']; ?>&end_date=<?= $_GET['end_date']; ?>&class=<?= $_GET['class']; ?>" target="_blank" class="btn btn-outline-secondary btn-sm">🧾 Export PDF</a>
          <a href="export_attendance_summary_excel.php?start_date=<?= $_GET['start_date']; ?>&end_date=<?= $_GET['end_date']; ?>&class=<?= $_GET['class']; ?>" class="btn btn-outline-success btn-sm">📊 Export Excel</a>
          <button onclick="window.print()" class="btn btn-outline-primary btn-sm">🖨️ Print</button>
        </div>
      <?php endif; ?>

      <?php
      if (isset($_GET['start_date']) && isset($_GET['end_date']) && isset($_GET['class'])) {
          $start = $_GET['start_date'];
          $end = $_GET['end_date'];
          $class = $_GET['class'];

          echo "<h5 class='mt-3'>Showing attendance from <strong>$start</strong> to <strong>$end</strong> for class: <strong>" . ($class == 'all' ? "All" : $class) . "</strong></h5>";

          $sql = "SELECT * FROM students";
          if ($class !== 'all') {
              $sql .= " WHERE class = '$class'";
          }

          $students = $conn->query($sql);

          echo "<div class='table-responsive mt-3'><table class='table table-bordered table-striped'>
                  <thead class='table-light'>
                    <tr>
                      <th>Image</th>
                      <th>Name</th>
                      <th>Roll</th>
                      <th>Class</th>
                      <th>Present</th>
                      <th>Absent</th>
                      <th>Late</th>
                    </tr>
                  </thead>
                  <tbody>";

          $labels = [];
          $present = [];
          $absent = [];
          $late = [];

          while ($student = $students->fetch_assoc()) {
              $sid = $student['id'];
              $counts = ['present' => 0, 'absent' => 0, 'late' => 0];

              $att_sql = "SELECT status FROM attendance 
                          WHERE student_id = $sid 
                          AND date BETWEEN '$start' AND '$end'";
              $att_result = $conn->query($att_sql);
              while ($row = $att_result->fetch_assoc()) {
                  $status = $row['status'];
                  if (isset($counts[$status])) {
                      $counts[$status]++;
                  }
              }

              $img = $student['image'] ? "<img src='uploads/{$student['image']}' width='50' class='img-thumbnail'>" : "-";

              echo "<tr>
                      <td>$img</td>
                      <td>{$student['name']}</td>
                      <td>{$student['roll']}</td>
                      <td>{$student['class']}</td>
                      <td>{$counts['present']}</td>
                      <td>{$counts['absent']}</td>
                      <td>{$counts['late']}</td>
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
