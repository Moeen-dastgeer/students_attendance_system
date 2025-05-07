<?php include 'admin_header.php'; ?>
<?php
include 'db.php';
$courses = $conn->query("SELECT * FROM courses ORDER BY id DESC");
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="card-title">📚 All Courses</h3>
    <a href="add_course.php" class="btn btn-primary">➕ Add New Course</a>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Course Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($c = $courses->fetch_assoc()): ?>
          <tr>
            <td><?= $c['id'] ?></td>
            <td><?= $c['course_name'] ?></td>
            <td>
              <a href="edit_course.php?id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
              <a href="delete_course.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this course?')">🗑️ Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
