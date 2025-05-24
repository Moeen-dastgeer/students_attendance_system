<?php include 'superadmin_header.php'; 
include 'db.php';
include 'superadmin_sidebar.php';
?>
<?php
$shifts = $conn->query("SELECT * FROM shifts ORDER BY id DESC");
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="card-title">⏰ All Shifts</h3>
    <a href="add_shift.php" class="btn btn-primary">➕ Add New Shift</a>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>


  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Shift Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($s = $shifts->fetch_assoc()): ?>
          <tr>
            <td><?= $s['id'] ?></td>
            <td><?= $s['shift_name'] ?></td>
            <td>
              <a href="edit_shift.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
              <a href="delete_shift.php?id=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this shift?')">🗑️ Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'superadmin_footer.php'; ?>
