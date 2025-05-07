<?php
include 'admin_header.php';
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$teacher = $conn->query("SELECT * FROM teachers WHERE id = $id")->fetch_assoc();

$assigned_combos = explode(',', $teacher['class_assigned']); // e.g. ['1-2', '2-3']

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $combo_array = $_POST['assigned'] ?? [];
    $combo_string = implode(',', $combo_array); // 1-2,2-3

    $password = $_POST['password'];
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("UPDATE teachers SET name='$name', class_assigned='$combo_string', password='$password' WHERE id=$id");
    } else {
        $conn->query("UPDATE teachers SET name='$name', class_assigned='$combo_string' WHERE id=$id");
    }

    $_SESSION['success'] = "✅ Teacher updated successfully!";
    header("Location: all_teachers.php");
    exit;
}
?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-center mb-4">✏️ Edit Teacher</h3>

          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="<?= $teacher['name']; ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Assign Course + Shift</label>
              <select name="assigned[]" class="form-select" multiple required>
                <?php
                $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
                $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");

                $shifts_arr = [];
                while ($s = $shifts->fetch_assoc()) {
                    $shifts_arr[] = $s;
                }

                while ($c = $courses->fetch_assoc()) {
                    echo "<optgroup label='{$c['course_name']}'>";
                    foreach ($shifts_arr as $s) {
                        $combo = "{$c['id']}-{$s['id']}";
                        $selected = in_array($combo, $assigned_combos) ? 'selected' : '';
                        echo "<option value='$combo' $selected>{$s['shift_name']}</option>";
                    }
                    echo "</optgroup>";
                }
                ?>
              </select>
              <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple combinations</small>
            </div>

            <div class="mb-4">
              <label class="form-label">New Password (optional)</label>
              <input type="password" name="password" class="form-control">
              <small class="text-muted">Leave blank to keep current password</small>
            </div>

            <button type="submit" class="btn btn-success w-100">💾 Update Teacher</button>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
