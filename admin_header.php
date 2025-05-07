<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(86400 * 7); // Only if session not already started
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- ✅ Navbar with Sidebar Toggle -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <!-- Sidebar toggle button -->
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <span class="navbar-brand font-weight-bold">👑 Admin Dashboard</span>
    </li>
  </ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="#" class="brand-link text-center"><span class="brand-text font-weight-light">Attendance System</span></a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column">
        <li class="nav-item"><a href="dashboard.php" class="nav-link"><i class="nav-icon fas fa-home"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="add_student.php" class="nav-link"><i class="nav-icon fas fa-user-plus"></i><p>Add Student</p></a></li>
        <li class="nav-item"><a href="all_students.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>All Students</p></a></li>
        <li class="nav-item"><a href="all_courses.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>All Courses</p></a></li>
        <li class="nav-item"><a href="all_shifts.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>All Shifts</p></a></li>
        <li class="nav-item"><a href="add_teacher.php" class="nav-link"><i class="nav-icon fas fa-user-tie"></i><p>Add Teacher</p></a></li>
        <li class="nav-item"><a href="all_teachers.php" class="nav-link"><i class="nav-icon fas fa-users-cog"></i><p>Manage Teachers</p></a></li>
        <li class="nav-item"><a href="view_leave_requests.php" class="nav-link"> <i class="nav-icon fas fa-file-alt"></i><p>Manage Leaves</p></a></li>
        <li class="nav-item"><a href="take_attendance.php" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>Take Attendance</p></a></li>
        <li class="nav-item"><a href="view_attendance_filtered.php" class="nav-link"><i class="nav-icon fas fa-calendar-alt"></i><p>View Attendance</p></a></li>
        <li class="nav-item">
          <a href="admin_change_password.php" class="nav-link">
            <i class="nav-icon fas fa-key"></i>
            <p>Change Password</p>
          </a>
        </li>
        <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p></a></li>
      </ul>
    </nav>
  </div>
</aside>

<!-- Page Content Wrapper -->
<div class="content-wrapper p-4">
