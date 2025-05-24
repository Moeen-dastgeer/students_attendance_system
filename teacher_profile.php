<?php
include 'admin_header.php';
include 'db.php';


if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>❌ Teacher ID missing.</div>";
    exit;
}

$id = intval($_GET['id']);
$teacher = $conn->query("SELECT * FROM teachers WHERE id = $id")->fetch_assoc();

if (!$teacher) {
    echo "<div class='alert alert-danger'>❌ Teacher not found.</div>";
    exit;
}

// Load course and shift mapping
$courseMap = [];
$shiftMap = [];

$courses = $conn->query("SELECT * FROM courses");
while ($c = $courses->fetch_assoc()) {
    $courseMap[$c['id']] = $c['course_name'];
}

$shifts = $conn->query("SELECT * FROM shifts");
while ($s = $shifts->fetch_assoc()) {
    $shiftMap[$s['id']] = $s['shift_name'];
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">👨‍🏫 Teacher Profile</h3>

      <div class="row">
        <div class="col-md-3">
          <?= $teacher['image'] ? "<img src='uploads/{$teacher['image']}' class='img-thumbnail' width='120'>" : "<div class='text-muted'>No Image</div>"; ?>
        </div>
        <div class="col-md-9">
          <p><strong>Name:</strong> <?= htmlspecialchars($teacher['name']); ?></p>
          <p><strong>Username:</strong> <?= htmlspecialchars($teacher['username']); ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($teacher['phone'] ?? '-') ?></p>
          <p><strong>CNIC:</strong> <?= htmlspecialchars($teacher['cnic'] ?? '-') ?></p>
          <p><strong>Gender:</strong> <?= ucfirst($teacher['gender'] ?? '-') ?></p>
          <p><strong>Hire Date:</strong> <?= $teacher['hire_date'] ?? '-' ?></p>

          <p><strong>Assigned Courses & Shifts:</strong><br>
            <?php
            $comboList = explode(',', $teacher['class_assigned']);
            foreach ($comboList as $combo) {
                $combo = trim($combo);
                if (strpos($combo, '-') !== false) {
                    list($cid, $sid) = explode('-', $combo);
                    $courseName = $courseMap[$cid] ?? 'Unknown';
                    $shiftName = $shiftMap[$sid] ?? 'Unknown';
                    echo "<span class='badge bg-primary me-1'>{$courseName} - {$shiftName}</span>";
                }
            }
            ?>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
