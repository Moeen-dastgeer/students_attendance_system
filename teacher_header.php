<?php
session_set_cookie_params(86400 * 7); // 7 دن تک سیشن محفوظ رہے
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'teacher') {
    header("Location: index.php");
    exit;
}

// Assigned course-shift mapping
$assigned_display = [];
if (isset($_SESSION['assigned_class'])) {
    $combos = explode(',', $_SESSION['assigned_class']); // "1-2,2-1"
    foreach ($combos as $combo) {
        [$course_id, $shift_id] = explode('-', trim($combo));
        $meta = $conn->query("SELECT c.course_name, s.shift_name 
                              FROM courses c, shifts s 
                              WHERE c.id = $course_id AND s.id = $shift_id")->fetch_assoc();
        if ($meta) {
            $assigned_display[] = $meta['course_name'] . " - " . $meta['shift_name'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <span class="navbar-brand font-weight-bold">📚 Teacher Dashboard</span>
    </li>
  </ul>
  <ul class="navbar-nav ms-auto">
    <li class="nav-item">
      <span class="nav-link"><i class="fas fa-user"></i> <?= $_SESSION['teacher_name']; ?></span>
    </li>
    <li class="nav-item">
      <a href="logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </li>
  </ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="#" class="brand-link text-center"><span class="brand-text font-weight-light">Teacher Panel</span></a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column">
        <li class="nav-item"><a href="teacher_dashboard.php" class="nav-link"><i class="nav-icon fas fa-home"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="take_attendance_teacher.php" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>Take Attendance</p></a></li>
        <li class="nav-item"><a href="students_teacher.php" class="nav-link"><i class="nav-icon fas fa-users"></i><p>View Students</p></a></li>
        <li class="nav-item"><a href="leave_request.php" class="nav-link"><i class="nav-icon fas fa-calendar-plus"></i><p>Request Leave</p></a></li>
        <li class="nav-item">
          <a href="teacher_change_password.php" class="nav-link">
            <i class="nav-icon fas fa-key"></i>
            <p>Change Password</p>
          </a>
        </li>
        <li class="nav-item"><a href="logout.php" class="nav-link text-danger"><i class="nav-icon fas fa-sign-out-alt"></i><p>Logout</p></a></li>
      </ul>
    </nav>
  </div>
</aside>

<!-- Assigned Info Box -->
<div class="content-wrapper p-3">
  
