<?php include 'superadmin_header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'superadmin_sidebar.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $campus_name = $_POST['name'];
    $admin_username = $_POST['admin_username'];
    $admin_password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    $admin_name = $_POST['admin_name'];

    // Check if username already exists in admins table
    $check = $conn->query("SELECT * FROM admins WHERE username = '$admin_username'");
    if ($check->num_rows > 0) {
        $error = "❌ Username already exists!";
    } else {
        // Step 1: Insert into campuses
        $conn->query("INSERT INTO campuses (name) VALUES ('$campus_name')");
        $campus_id = $conn->insert_id;

        // Step 2: Create associated campus admin
        $conn->query("INSERT INTO admins (name, username, password, campus_id, is_super)
                      VALUES ('$admin_name', '$admin_username', '$admin_password', $campus_id, 0)");

        $_SESSION['success'] = "✅ Campus and Admin created successfully!";
        header("Location: campuses.php");
        exit;
    }
}
?>

<div class="container py-4">
  <h3>➕ Add New Campus</h3>
  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error; ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Campus Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Admin Name</label>
      <input type="text" name="admin_name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Admin Username</label>
      <input type="text" name="admin_username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Admin Password</label>
      <input type="password" name="admin_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">💾 Add Campus</button>
  </form>
</div>

<?php include 'superadmin_footer.php'; ?>
