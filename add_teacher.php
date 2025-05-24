<?php include 'admin_header.php'; ?>
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $hire_date = $_POST['hire_date'];
    $phone = $_POST['phone'];
    $cnic = $_POST['cnic'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];

    $campus_id = $_SESSION['campus_id'] ?? null;

    // Image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image = time() . '_' . basename($_FILES['image']['name']);
        $target = "uploads/" . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    // Get selected course-shift combos
    $assigned_array = $_POST['assigned'] ?? [];
    $assigned_string = implode(',', $assigned_array); // "1-2,1-3"

    // Check duplicate username
    $check = $conn->query("SELECT * FROM teachers WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $msg = "<div class='alert alert-danger mt-3'>❌ Username already exists!</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO teachers 
            (name, username, password, phone, cnic, address, gender, hire_date, image, class_assigned, campus_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssi", $name, $username, $password, $phone, $cnic, $address, $gender, $hire_date, $image, $assigned_string, $campus_id);
        $stmt->execute();

        $msg = "<div class='alert alert-success mt-3'>✅ Teacher added successfully!</div>";
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-10">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="mb-4 text-center">
          <h3 class="card-title">➕ Add New Teacher</h3>
        </div>

        <?php if (isset($msg)) echo $msg; ?>

        <form method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Hire Date</label>
              <input type="date" name="hire_date" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">CNIC</label>
              <input type="text" name="cnic" class="form-control">
            </div>

            <div class="col-md-12 mb-3">
              <label class="form-label">Address</label>
              <textarea name="address" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-select">
                <option value="">-- Select Gender --</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Upload Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
            </div>

            <div class="col-md-12 mb-4">
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
          </div>

          <button type="submit" class="btn btn-primary w-100">Add Teacher</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
