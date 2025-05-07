<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin_username = $_SESSION['admin_name'];
$msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // Fetch current password hash
    $admin = $conn->query("SELECT * FROM admins WHERE name = '$admin_username'")->fetch_assoc();

    if (!$admin || !password_verify($current, $admin['password'])) {
        $msg = "<div class='alert alert-danger'>❌ Current password is incorrect.</div>";
    } elseif ($new !== $confirm) {
        $msg = "<div class='alert alert-warning'>⚠️ New passwords do not match.</div>";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE admins SET password = '$hashed' WHERE id = {$admin['id']}");
        $msg = "<div class='alert alert-success'>✅ Password updated successfully!</div>";
    }
}
?>

<?php include 'admin_header.php'; ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
    <div class="mb-5">
        <h3 class="card-title">🔐 Change Password</h3>
    </div>  

      <?= $msg ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password</label>
          <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Update Password</button>
      </form>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
