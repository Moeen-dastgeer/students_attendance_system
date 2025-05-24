<?php include 'teacher_header.php'; ?>
<?php
include 'db.php';

$assigned_combos = explode(',', $_SESSION['assigned_class']); // ["1-2", "2-1"]
$campus_id = $_SESSION['campus_id'] ?? null;
$options = [];

// Prepare dropdown options
foreach ($assigned_combos as $combo) {
    [$cid, $sid] = explode('-', trim($combo));
    $course = $conn->query("SELECT course_name FROM courses WHERE id = $cid")->fetch_assoc()['course_name'] ?? 'Unknown';
    $shift = $conn->query("SELECT shift_name FROM shifts WHERE id = $sid")->fetch_assoc()['shift_name'] ?? 'Unknown';
    $options[] = ['value' => "$cid-$sid", 'label' => "$course - $shift"];
}

$selected = $_GET['combo'] ?? '';
$students = [];

if ($selected) {
    [$course_id, $shift_id] = explode('-', $selected);
    if (in_array("$course_id-$shift_id", $assigned_combos)) {
        $sql = "SELECT s.*, c.course_name, sh.shift_name 
                FROM students s 
                JOIN courses c ON s.course_id = c.id 
                JOIN shifts sh ON s.shift_id = sh.id 
                WHERE s.course_id = $course_id 
                  AND s.shift_id = $shift_id 
                  AND s.status = 'active'";

        if ($campus_id) {
            $sql .= " AND s.campus_id = $campus_id";
        }

        $students = $conn->query($sql);
    }
}
?>

<div class="card shadow-sm">
  <div class="card-body">
    <h3 class="card-title mb-4">📋 Students Assigned to You</h3>

    <!-- Dropdown filter -->
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-6">
        <select name="combo" class="form-select" onchange="this.form.submit()" required>
          <option value="">-- Select Course + Shift --</option>
          <?php foreach ($options as $op): ?>
            <option value="<?= $op['value'] ?>" <?= ($selected == $op['value']) ? 'selected' : '' ?>>
              <?= $op['label'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </form>

    <!-- Student Table -->
    <?php if ($selected && $students && $students->num_rows > 0): ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Name</th>
              <th>Course</th>
              <th>Shift</th>
              <th>Profile</th>
            </tr>
          </thead>
          <tbody>
            <?php while($s = $students->fetch_assoc()): ?>
              <tr>
                <td><?= $s['name']; ?></td>
                <td><?= $s['course_name']; ?></td>
                <td><?= $s['shift_name']; ?></td>
                <td><a href="student_profile_teacher.php?id=<?= $s['id']; ?>" class="btn btn-sm btn-primary">👁️ View</a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php elseif ($selected): ?>
      <div class="alert alert-warning">⚠️ No students found for selected course + shift.</div>
    <?php endif; ?>
  </div>
</div>

<?php include 'teacher_footer.php'; ?>
