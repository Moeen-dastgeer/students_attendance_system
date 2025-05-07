<?php include 'admin_header.php'; ?>
<?php
include 'db.php';

$id = $_GET['id'] ?? 0;
$course = $conn->query("SELECT * FROM courses WHERE id = $id")->fetch_assoc();

if (!$course) {
    echo "<div class='alert alert-danger'>Invalid course ID.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = trim($_POST['course_name']);
    if ($course_name !== '') {
        $conn->query("UPDATE courses SET course_name = '$course_name' WHERE id = $id");
        $_SESSION['success'] = "✅ Course updated successfully!";
        header("Location: all_courses.php");
        exit;
    } else {
        $error = "❌ Course name cannot be empty.";
    }
}
?>

<div class="container py-4">
  <h3 class="mb-4">✏️ Edit Course</h3>

  <?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Course Name</label>
      <input type="text" name="course_name" value="<?= $course['course_name'] ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">💾 Update</button>
  </form>
</div>

<?php include 'admin_footer.php'; ?>
