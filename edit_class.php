<?php include 'admin_header.php'; ?>
<?php
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "Class ID not provided.";
    exit;
}

$id = intval($_GET['id']);
$class = $conn->query("SELECT * FROM classes WHERE id = $id")->fetch_assoc();

if (!$class) {
    echo "Class not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['class_name']);

    if (!empty($new_name)) {
        $conn->query("UPDATE classes SET class_name = '$new_name' WHERE id = $id");
        $_SESSION['success'] = "✅ Class updated successfully.";
        header("Location: all_classes.php");
        exit;
    } else {
        $msg = "<div class='alert alert-warning mt-2'>⚠️ Class name cannot be empty.</div>";
    }
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="card-title mb-4">✏️ Edit Class</h4>

      <?php if (isset($msg)) echo $msg; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Class Name</label>
          <input type="text" name="class_name" class="form-control" value="<?= htmlspecialchars($class['class_name']); ?>" required>
        </div>

        <button type="submit" class="btn btn-success w-100">💾 Update Class</button>
      </form>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
