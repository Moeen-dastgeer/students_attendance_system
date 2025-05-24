<?php include 'superadmin_header.php'; ?>
<?php include 'superadmin_sidebar.php'; ?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['shift_name']);
    if ($name !== '') {
        $conn->query("INSERT INTO shifts (shift_name) VALUES ('$name')");
        $_SESSION['success'] = "✅ Shift added successfully.";
        header("Location: all_shifts.php");
        exit;
    } else {
        $error = "❌ Shift name cannot be empty.";
    }
}
?>

<div class="container py-4">
  <h3 class="mb-4">➕ Add New Shift</h3>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Shift Name</label>
      <input type="text" name="shift_name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">💾 Save</button>
  </form>
</div>

<?php include 'superadmin_footer.php'; ?>
