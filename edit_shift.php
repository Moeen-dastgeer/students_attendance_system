<?php include 'admin_header.php'; ?>
<?php
include 'db.php';

$id = $_GET['id'] ?? 0;
$shift = $conn->query("SELECT * FROM shifts WHERE id = $id")->fetch_assoc();

if (!$shift) {
    echo "<div class='alert alert-danger'>Invalid shift ID.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['shift_name']);
    if ($name !== '') {
        $conn->query("UPDATE shifts SET shift_name = '$name' WHERE id = $id");
        $_SESSION['success'] = "✅ Shift updated successfully.";
        header("Location: all_shifts.php");
        exit;
    } else {
        $error = "❌ Shift name cannot be empty.";
    }
}
?>

<div class="container py-4">
  <h3 class="mb-4">✏️ Edit Shift</h3>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Shift Name</label>
      <input type="text" name="shift_name" value="<?= $shift['shift_name'] ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">💾 Update</button>
  </form>
</div>

<?php include 'admin_footer.php'; ?>
