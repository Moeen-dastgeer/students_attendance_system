<?php include 'admin_header.php'; ?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = trim($_POST['class_name']);

    if (!empty($class_name)) {
        $check = $conn->query("SELECT * FROM classes WHERE class_name = '$class_name'");
        if ($check->num_rows > 0) {
            $msg = "<div class='alert alert-danger mt-2'>❌ This class already exists!</div>";
        } else {
            $conn->query("INSERT INTO classes (class_name) VALUES ('$class_name')");
            $msg = "<div class='alert alert-success mt-2'>✅ Class added successfully!</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning mt-2'>⚠️ Class name cannot be empty.</div>";
    }
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="card-title mb-4">➕ Add New Class</h4>

      <?php if (isset($msg)) echo $msg; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Class Name</label>
          <input type="text" name="class_name" class="form-control" placeholder="e.g. BSCS, BBA, FA" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">➕ Add Class</button>
      </form>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
