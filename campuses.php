<?php include 'superadmin_header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'superadmin_sidebar.php'; ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>🏫 Manage Campuses</h3>
    <a href="add_campus.php" class="btn btn-primary">➕ Add Campus</a>
</div>
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Campus Name</th>
          <th>Admin Username</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("
          SELECT c.id, c.name,a.username AS admin_username 
          FROM campuses c
          LEFT JOIN admins a ON c.id = a.campus_id
        ");
        while ($row = $result->fetch_assoc()):
        ?>
          <tr>
            <td><?= htmlspecialchars($row['name']); ?></td>
            <td><?= htmlspecialchars($row['admin_username'] ?? 'N/A'); ?></td>
            <td>
              <a href="edit_campus.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
              <a href="delete_campus.php?id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">🗑️ Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'superadmin_footer.php'; ?>
