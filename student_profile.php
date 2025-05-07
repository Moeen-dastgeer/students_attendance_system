<?php
include 'admin_header.php';
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location:index.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "Student ID not provided.";
    exit;
}

$id = $_GET['id'];
$sql = "SELECT s.*, c.course_name, sh.shift_name 
        FROM students s
        LEFT JOIN courses c ON s.course_id = c.id
        LEFT JOIN shifts sh ON s.shift_id = sh.id
        WHERE s.id = $id";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit;
}

$summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0];
$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';

$query = "SELECT date, status FROM attendance WHERE student_id = $id";
if ($start && $end) {
    $query .= " AND date BETWEEN '$start' AND '$end'";
}
$query .= " ORDER BY date DESC";
$att = $conn->query($query);
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title">👤 Student Profile</h3>

      <div class="row">
        <div class="col-md-3">
          <?= $student['image'] ? "<img src='uploads/{$student['image']}' class='img-thumbnail' width='100'>" : "<div class='text-muted'>No Image</div>"; ?>
        </div>
        <div class="col-md-9">
          <p><strong>Name:</strong> <?= $student['name']; ?></p>
          <p><strong>Roll Number:</strong> <?= $student['roll']; ?></p>
          <p><strong>Course:</strong> <?= $student['course_name']; ?></p>
          <p><strong>Shift:</strong> <?= $student['shift_name']; ?></p>
        </div>
      </div>

      <hr>
      <h5>📅 Attendance History</h5>

      <form method="GET" class="row g-2 mb-3">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <div class="col-md-5">
          <label>From:</label>
          <input type="date" name="start_date" value="<?= $start; ?>" class="form-control" required>
        </div>
        <div class="col-md-5">
          <label>To:</label>
          <input type="date" name="end_date" value="<?= $end; ?>" class="form-control" required>
        </div>
        <div class="col-md-2 d-grid">
          <label>&nbsp;</label>
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="table-light">
            <tr><th>Date</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php while($row = $att->fetch_assoc()): ?>
              <tr>
                <td><?= $row['date']; ?></td>
                <td><?= ucfirst($row['status']); ?></td>
              </tr>
              <?php $summary[$row['status']]++; ?>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <?php if ($start && $end): ?>
        <h5 class="mt-4">📊 Summary (<?= $start; ?> to <?= $end; ?>)</h5>
        <ul>
          <li>✅ Present: <?= $summary['present']; ?></li>
          <li>❌ Absent: <?= $summary['absent']; ?></li>
          <li>🕒 Late: <?= $summary['late']; ?></li>
          <li>✈️ Leave: <?= $summary['leave']; ?></li>
        </ul>

        <div class="mb-3">
          <a href="export_single_student_pdf.php?id=<?= $id; ?>&start_date=<?= $start; ?>&end_date=<?= $end; ?>" class="btn btn-outline-secondary btn-sm" target="_blank">🧾 Export PDF</a>
          <a href="export_single_student_excel.php?id=<?= $id; ?>&start_date=<?= $start; ?>&end_date=<?= $end; ?>" class="btn btn-outline-success btn-sm">📊 Export Excel</a>
        </div>

        <canvas id="attendanceChart" style="max-width: 600px; margin-top: 30px;"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          const ctx = document.getElementById('attendanceChart').getContext('2d');
          new Chart(ctx, {
            type: 'bar',
            data: {
              labels: ['Present', 'Absent', 'Late'],
              datasets: [{
                label: 'Attendance Count',
                data: [<?= $summary['present']; ?>, <?= $summary['absent']; ?>, <?= $summary['late']; ?>،<?= $summary['leave']; ?>],
                backgroundColor: ['#198754', '#dc3545', '#fd7e14']
              }]
            },
            options: {
              responsive: true,
              plugins: {
                title: {
                  display: true,
                  text: 'Attendance Summary Chart'
                },
                legend: { display: false }
              }
            }
          });
        </script>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
