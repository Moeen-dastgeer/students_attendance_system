<?php include 'admin_header.php'; include 'db.php'; ?>

<?php
$courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
$shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
?>

<div class="container mt-5">
  <h4 class="mb-4">🖨️ Student ID Card Generator</h4>

  <form id="cardForm" class="row g-3" method="GET" target="_blank">
    <div class="col-md-4">
      <label class="form-label">Select Course</label>
      <select name="course_id" class="form-select" required>
        <option value="">-- Select Course --</option>
        <?php while ($c = $courses->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"><?= $c['course_name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Select Shift</label>
      <select name="shift_id" class="form-select" required>
        <option value="">-- Select Shift --</option>
        <?php while ($s = $shifts->fetch_assoc()): ?>
          <option value="<?= $s['id'] ?>"><?= $s['shift_name'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">Card Side</label>
      <select name="side" id="sideSelect" class="form-select" required>
        <option value="front">Front Side</option>
        <option value="back">Back Side</option>
      </select>
    </div>

    <div class="col-md-4" id="countBox" style="display: none;">
      <label class="form-label">How Many Cards (Back)?</label>
      <input type="number" name="count" class="form-control" placeholder="e.g. 12" min="1" max="100">
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">🖨️ Generate</button>
    </div>
  </form>
</div>

<script>
document.getElementById('sideSelect').addEventListener('change', function () {
  const countBox = document.getElementById('countBox');
  if (this.value === 'back') {
    countBox.style.display = 'block';
  } else {
    countBox.style.display = 'none';
  }
});

document.getElementById('cardForm').addEventListener('submit', function (e) {
  const side = document.getElementById('sideSelect').value;
  if (side === 'front') {
    this.action = 'student_card_front.php';
  } else {
    this.action = 'student_card_back.php';
  }
});
</script>

<?php include 'admin_footer.php'; ?>
