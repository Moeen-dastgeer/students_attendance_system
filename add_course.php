<?php include 'superadmin_header.php'; ?>
<?php include 'superadmin_sidebar.php';?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = trim($_POST['course_name']);
    if ($course_name !== '') {
        $conn->query("INSERT INTO courses (course_name) VALUES ('$course_name')");
        $_SESSION['success'] = "✅ Course added successfully!";
        header("Location: all_courses.php");
        exit;
    } else {
        $error = "❌ Course name cannot be empty.";
    }
}
?>

<div class="container py-4">
  <h3 class="mb-4">➕ Add New Course</h3>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Course Name</label>
      <input type="text" name="course_name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">💾 Save Course</button>
  </form>
</div>

<?php include 'superadmin_footer.php'; ?>
