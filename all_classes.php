<?php include 'admin_header.php'; ?>
<?php
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM classes WHERE id = $id");
    $_SESSION['success'] = "✅ Class deleted successfully.";
    header("Location: all_classes.php");
    exit;
}

// Fetch all classes
$classes = $conn->query("SELECT * FROM classes ORDER BY class_name ASC");
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="card-title">📚 All Classes</h4>
        <a href="add_class.php" class="btn btn-primary btn-sm">➕ Add Class</a>
      </div>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Class Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $classes->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['class_name']); ?></td>
                <td>
                  <a href="edit_class.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                  <a href="all_classes.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this class?')" class="btn btn-sm btn-danger">🗑️ Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
