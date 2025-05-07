<?php include 'teacher_header.php'; ?>
<?php
include 'db.php';

$teacher_id = $_SESSION['teacher_id'];
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $teacher = $conn->query("SELECT password FROM teachers WHERE id = $teacher_id")->fetch_assoc();

    if (!password_verify($old, $teacher['password'])) {
        $error = "❌ Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error = "❌ New password and confirmation do not match.";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE teachers SET password = '$hashed' WHERE id = $teacher_id");
        $success = "✅ Password updated successfully!";
    }
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-4">🔑 Change Password</h4>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" name="old_password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
      </form>
    </div>
  </div>
</div>

<?php include 'teacher_footer.php'; ?>
