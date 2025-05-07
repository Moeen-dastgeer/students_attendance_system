<?php include 'admin_header.php'; ?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Get selected course-shift combos
    $assigned_array = $_POST['assigned'] ?? [];
    $assigned_string = implode(',', $assigned_array); // "1-2,1-3"

    // Check duplicate username
    $check = $conn->query("SELECT * FROM teachers WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $msg = "<div class='alert alert-danger mt-3'>❌ Username already exists!</div>";
    } else {
        $conn->query("INSERT INTO teachers (name, username, password, class_assigned) VALUES ('$name', '$username', '$password', '$assigned_string')");
        $msg = "<div class='alert alert-success mt-3'>✅ Teacher added successfully!</div>";
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="mb-5">
          <h3 class="card-title text-center">➕ Add New Teacher</h3>
        </div>

        <?php if (isset($msg)) echo $msg; ?>

        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Assign Course + Shift</label>
            <select name="assigned[]" class="form-select" multiple required>
              <?php
              $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
              $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");

              $all_shifts = [];
              while ($s = $shifts->fetch_assoc()) {
                  $all_shifts[] = $s;
              }

              while ($c = $courses->fetch_assoc()) {
                  echo "<optgroup label='{$c['course_name']}'>";
                  foreach ($all_shifts as $s) {
                      $val = "{$c['id']}-{$s['id']}";
                      echo "<option value='$val'>{$s['shift_name']}</option>";
                  }
                  echo "</optgroup>";
              }
              ?>
            </select>
            <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple combinations</small>
          </div>

          <button type="submit" class="btn btn-primary w-100">Add Teacher</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
