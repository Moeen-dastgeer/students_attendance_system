<?php include 'superadmin_header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'superadmin_sidebar.php'; ?>
<?php
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid ID."; exit;
}

// Fetch campus info
$campus = $conn->query("SELECT * FROM campuses WHERE id = $id")->fetch_assoc();

// Fetch admin for this campus
$admin = $conn->query("SELECT * FROM admins WHERE campus_id = $id AND is_super = 0 LIMIT 1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campus_name = $_POST['name'];
    $admin_name = $_POST['admin_name'];
    $admin_username = $_POST['admin_username'];
    $admin_password = $_POST['admin_password'];

    // Update campus
    $conn->query("UPDATE campuses SET name='$campus_name' WHERE id=$id");

    // Update admin
    if ($admin_password) {
        $hashed = password_hash($admin_password, PASSWORD_DEFAULT);
        $conn->query("UPDATE admins SET name='$admin_name', username='$admin_username', password='$hashed' 
                      WHERE id = {$admin['id']}");
    } else {
        $conn->query("UPDATE admins SET name='$admin_name', username='$admin_username' 
                      WHERE id = {$admin['id']}");
    }

    $_SESSION['success'] = "✅ Campus and admin updated successfully!";
    header("Location: campuses.php");
    exit;
}
?>

<div class="container py-4">
  <h3>✏️ Edit Campus</h3>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Campus Name</label>
      <input type="text" name="name" value="<?= $campus['name']; ?>" class="form-control" required>
    </div>

    <hr class="my-4">
    <h5>🧑‍💼 Admin Info</h5>

    <div class="mb-3">
      <label class="form-label">Admin Name</label>
      <input type="text" name="admin_name" value="<?= $admin['name'] ?? ''; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Admin Username</label>
      <input type="text" name="admin_username" value="<?= $admin['username'] ?? ''; ?>" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">New Password (optional)</label>
      <input type="password" name="admin_password" class="form-control">
      <small class="text-muted">Leave blank to keep current password</small>
    </div>

    <button type="submit" class="btn btn-success">💾 Update Campus</button>
  </form>
</div>

<?php include 'superadmin_footer.php'; ?>
