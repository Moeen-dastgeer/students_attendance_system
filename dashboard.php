<?php include 'admin_header.php'; ?>
<?php
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location:index.php');
    exit;
}

$filter = $_GET['filter'] ?? 'today';
$course_filter = $_GET['course_id'] ?? 'all';
$shift_filter = $_GET['shift_id'] ?? 'all';
$today = date('Y-m-d');
$start_date = match ($filter) {
  'last7' => date('Y-m-d', strtotime('-7 days')),
  'last30' => date('Y-m-d', strtotime('-30 days')),
  default => $today
};

$where = [];
if ($course_filter !== 'all') $where[] = "course_id = $course_filter";
if ($shift_filter !== 'all') $where[] = "shift_id = $shift_filter";
$filter_sql = $where ? "WHERE " . implode(' AND ', $where) : "";

$total_students = $conn->query("SELECT COUNT(*) as total FROM students $filter_sql")->fetch_assoc()['total'];

$att_sql = "SELECT a.status, COUNT(*) as count FROM attendance a
JOIN students s ON a.student_id = s.id
WHERE a.date BETWEEN '$start_date' AND '$today'";
if ($course_filter !== 'all') $att_sql .= " AND s.course_id = $course_filter";
if ($shift_filter !== 'all') $att_sql .= " AND s.shift_id = $shift_filter";
$att_sql .= " GROUP BY a.status";

$summary = ['present' => 0, 'absent' => 0, 'late' => 0];
$att_query = $conn->query($att_sql);
while ($row = $att_query->fetch_assoc()) {
    $summary[$row['status']] = $row['count'];
}

$class_query = $conn->query("
  SELECT CONCAT(c.course_name, ' - ', sh.shift_name) as label, COUNT(*) as total
  FROM students s
  JOIN courses c ON s.course_id = c.id
  JOIN shifts sh ON s.shift_id = sh.id
  GROUP BY s.course_id, s.shift_id
");
$classes = []; $class_counts = [];
while ($row = $class_query->fetch_assoc()) {
  $classes[] = $row['label'];
  $class_counts[] = $row['total'];
}

$course_list = $conn->query("SELECT * FROM courses ORDER BY course_name");
$shift_list = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
?>

<div class="container py-4">
  <!-- Filters -->
  <form method="get" class="row g-2 mb-4 align-items-center">
  <!-- Date Range Buttons -->
  <div class="col-12 col-md-6">
    <label class="form-label fw-bold d-block">📆 Date Range</label>
    <div class="btn-group" role="group">
      <a href="?filter=today&course_id=<?= $course_filter ?>&shift_id=<?= $shift_filter ?>" class="btn btn-outline-primary <?= $filter=='today'?'active':'' ?>">Today</a>
      <a href="?filter=last7&course_id=<?= $course_filter ?>&shift_id=<?= $shift_filter ?>" class="btn btn-outline-primary <?= $filter=='last7'?'active':'' ?>">Last 7 Days</a>
      <a href="?filter=last30&course_id=<?= $course_filter ?>&shift_id=<?= $shift_filter ?>" class="btn btn-outline-primary <?= $filter=='last30'?'active':'' ?>">Last 30 Days</a>
    </div>
  </div>

  <!-- Course Dropdown -->
  <div class="col-md-3">
    <label class="form-label fw-bold">Course</label>
    <select name="course_id" class="form-select" onchange="this.form.submit()">
      <option value="all">All</option>
      <?php while($c = $course_list->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>" <?= $course_filter==$c['id'] ? 'selected' : '' ?>><?= $c['course_name'] ?></option>
      <?php endwhile; ?>
    </select>
  </div>

  <!-- Shift Dropdown -->
  <div class="col-md-3">
    <label class="form-label fw-bold">Shift</label>
    <select name="shift_id" class="form-select" onchange="this.form.submit()">
      <option value="all">All</option>
      <?php while($s = $shift_list->fetch_assoc()): ?>
        <option value="<?= $s['id'] ?>" <?= $shift_filter==$s['id'] ? 'selected' : '' ?>><?= $s['shift_name'] ?></option>
      <?php endwhile; ?>
    </select>
  </div>
</form>

  <!-- Cards -->
  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
        <div class="inner"><h3><?= $total_students ?></h3><p>Total Students</p></div>
        <div class="icon"><i class="fas fa-user-graduate"></i></div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
        <div class="inner"><h3><?= $summary['present'] ?></h3><p>Present</p></div>
        <div class="icon"><i class="fas fa-user-check"></i></div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="small-box bg-danger">
        <div class="inner"><h3><?= $summary['absent'] ?></h3><p>Absent</p></div>
        <div class="icon"><i class="fas fa-user-times"></i></div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
        <div class="inner"><h3><?= $summary['late'] ?></h3><p>Late Today</p></div>
        <div class="icon"><i class="fas fa-clock"></i></div>
      </div>
    </div>
  </div>

  <!-- Charts -->
  <div class="row">
    <div class="col-md-6">
      <div class="card card-outline card-primary">
        <div class="card-header"><h5 class="card-title">Attendance Summary</h5></div>
        <div class="card-body"><canvas id="attendanceChart"></canvas></div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-outline card-secondary">
        <div class="card-header"><h5 class="card-title">Course + Shift Count</h5></div>
        <div class="card-body"><canvas id="classChart"></canvas></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('attendanceChart'), {
  type: 'pie',
  data: {
    labels: ['Present', 'Absent', 'Late'],
    datasets: [{
      data: [<?= $summary['present']; ?>, <?= $summary['absent']; ?>, <?= $summary['late']; ?>],
      backgroundColor: ['#28a745', '#dc3545', '#ffc107']
    }]
  }
});

new Chart(document.getElementById('classChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($classes); ?>,
    datasets: [{
      label: 'Students',
      data: <?= json_encode($class_counts); ?>,
      backgroundColor: '#007bff'
    }]
  },
  options: {
    scales: { y: { beginAtZero: true } }
  }
});
</script>

<?php include 'admin_footer.php'; ?>