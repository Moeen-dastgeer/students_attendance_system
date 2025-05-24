<?php include 'teacher_header.php'; ?>
<?php
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit;
}

$today = date('Y-m-d');
$campus_id = $_SESSION['campus_id'] ?? null;
$assigned_combos = explode(',', $_SESSION['assigned_class']);
$total_students = 0;
$summary = ['present' => 0, 'absent' => 0, 'late' => 0];
$combo_labels = [];

foreach ($assigned_combos as $combo) {
    [$course_id, $shift_id] = explode('-', trim($combo));

    // Course + Shift name
    $meta = $conn->query("SELECT c.course_name, s.shift_name 
                          FROM courses c, shifts s 
                          WHERE c.id = $course_id AND s.id = $shift_id")->fetch_assoc();
    $label = $meta ? $meta['course_name'] . ' - ' . $meta['shift_name'] : 'Unknown';

    // Build student filter
    $student_filter = "course_id = $course_id AND shift_id = $shift_id AND status = 'active'";
    if ($campus_id) $student_filter .= " AND campus_id = $campus_id";

    // Count students
    $count = $conn->query("SELECT COUNT(*) as total FROM students WHERE $student_filter")->fetch_assoc()['total'];
    $total_students += $count;
    $combo_labels[] = ['label' => $label, 'count' => $count];

    // Attendance summary
    $student_ids = $conn->query("SELECT id FROM students WHERE $student_filter");
    $ids = [];
    while ($sid = $student_ids->fetch_assoc()) {
        $ids[] = $sid['id'];
    }

    if (count($ids)) {
        $id_list = implode(',', $ids);
        $att = $conn->query("SELECT status, COUNT(*) as count FROM attendance 
                             WHERE date = '$today' AND student_id IN ($id_list) 
                             GROUP BY status");
        while ($row = $att->fetch_assoc()) {
            $summary[$row['status']] += $row['count'];
        }
    }
}
?>

<div class="container py-4">
  <!-- Cards -->
  <div class="row">
    <div class="col-md-3 col-6 mb-3">
      <div class="small-box bg-primary">
        <div class="inner"><h3><?= $total_students ?></h3><p>Total Assigned Students</p></div>
        <div class="icon"><i class="fas fa-users"></i></div>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="small-box bg-success">
        <div class="inner"><h3><?= $summary['present'] ?></h3><p>Present Today</p></div>
        <div class="icon"><i class="fas fa-user-check"></i></div>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="small-box bg-danger">
        <div class="inner"><h3><?= $summary['absent'] ?></h3><p>Absent Today</p></div>
        <div class="icon"><i class="fas fa-user-times"></i></div>
      </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
      <div class="small-box bg-warning">
        <div class="inner"><h3><?= $summary['late'] ?></h3><p>Late Today</p></div>
        <div class="icon"><i class="fas fa-clock"></i></div>
      </div>
    </div>
  </div>

  <!-- Breakdown per course + shift -->
  <div class="card mt-4">
    <div class="card-header"><strong>📘 Assigned Courses & Shifts</strong></div>
    <div class="card-body">
      <ul class="list-group">
        <?php foreach ($combo_labels as $item): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= $item['label'] ?>
            <span class="badge bg-secondary"><?= $item['count'] ?> students</span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>

<?php include 'teacher_footer.php'; ?>
