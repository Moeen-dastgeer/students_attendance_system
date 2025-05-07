<?php
session_set_cookie_params(86400 * 7);
session_start();
include 'db.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'teacher') {
        header("Location: teacher_dashboard.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check Admin
    $admin = $conn->query("SELECT * FROM admins WHERE username = '$username'");
    if ($admin->num_rows == 1) {
        $a = $admin->fetch_assoc();
        if (password_verify($password, $a['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['role'] = 'admin';
            $_SESSION['admin_name'] = $a['name'];
            header("Location: dashboard.php");
            exit;
        }
    }

    $teacher = $conn->query("SELECT * FROM teachers WHERE username = '$username'");
    if ($teacher->num_rows == 1) {
        $t = $teacher->fetch_assoc();
        if (password_verify($password, $t['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['role'] = 'teacher';
            $_SESSION['teacher_name'] = $t['name'];
            $_SESSION['teacher_id'] = $t['id'];
            $_SESSION['assigned_class'] = $t['class_assigned']; // e.g. "1-2,2-1"
            header("Location: teacher_dashboard.php");
            exit;
        }
    }

    $error = "❌ Invalid username or password";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Admin/Teacher</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center min-vh-100">
  <div class="card shadow p-4 w-100" style="max-width: 400px;">
    <h3 class="mb-3 text-center">🔐 Login</h3>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger py-2"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required autofocus>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>
</div>

</body>
</html>
