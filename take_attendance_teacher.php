<?php include 'teacher_header.php'; ?>
<?php
include 'db.php';

// Extract assigned combos from session
$assigned_combos = explode(',', $_SESSION['assigned_class']); // e.g. ["1-2", "2-1"]
$today = date('Y-m-d');

// Prepare course & shift info for dropdown
$courses = $conn->query("SELECT * FROM courses");
$shifts = $conn->query("SELECT * FROM shifts");

$combo_options = [];
foreach ($assigned_combos as $combo) {
    [$cid, $sid] = explode('-', trim($combo));
    $cname = $conn->query("SELECT course_name FROM courses WHERE id = $cid")->fetch_assoc()['course_name'] ?? 'Unknown';
    $sname = $conn->query("SELECT shift_name FROM shifts WHERE id = $sid")->fetch_assoc()['shift_name'] ?? 'Unknown';
    $combo_options[] = ['value' => "$cid-$sid", 'label' => "$cname - $sname"];
}
?>

<div class="card shadow-sm">
  <div class="card-body">
    <h3 class="card-title mb-4">📝 Take Attendance</h3>

    <div class="mb-3">
      <label class="form-label">Select Course & Shift</label>
      <select id="combo_select" class="form-select" onchange="loadStudents()">
        <option value="">-- Select Course + Shift --</option>
        <?php foreach ($combo_options as $option): ?>
          <option value="<?= $option['value'] ?>"><?= $option['label'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div id="students_table" class="table-responsive"></div>
    <div id="result" class="mt-3"></div>
  </div>
</div>

<script>
function loadStudents() {
  const combo = document.getElementById('combo_select').value;
  if (!combo) {
    document.getElementById('students_table').innerHTML = '';
    return;
  }

  const [course_id, shift_id] = combo.split('-');
  fetch(`load_students_teacher.php?course_id=${course_id}&shift_id=${shift_id}`)
    .then(res => res.text())
    .then(data => {
      document.getElementById('students_table').innerHTML = data;
    });
}

function markAttendance(studentId, status) {
  const row = document.getElementById("row-" + studentId);
  fetch('save_attendance_teacher.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: `student_id=${studentId}&status=${status}`
  })
  .then(res => res.text())
  .then(data => {
    // Badge update
    const statusTd = row.querySelector('.status-badge');
    const colors = {
      present: 'success',
      absent: 'danger',
      late: 'warning',
      leave: 'info'
    };
    const badge = `<span class="badge bg-${colors[status] || 'secondary'} text-capitalize">${status}</span>`;
    if (statusTd) statusTd.innerHTML = badge;

    // Button highlight
    row.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
    row.querySelector(`button[onclick*="${status}"]`)?.classList.add('active');

    // Show result message
    const resultBox = document.getElementById("result");
    if (resultBox) {
      resultBox.innerHTML = `<div class="alert alert-info mt-2 py-1 px-2">✅ ${data}</div>`;
    }
  });
}
</script>

<?php include 'teacher_footer.php'; ?>
