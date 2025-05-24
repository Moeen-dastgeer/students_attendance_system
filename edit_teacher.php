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
    $phone = $_POST['phone'];
    $cnic = $_POST['cnic'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $hire_date = $_POST['hire_date'];

    $combo_array = $_POST['assigned'] ?? [];
    $combo_string = implode(',', $combo_array);

    $image = $teacher['image'];
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    $password = $_POST['password'];
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("UPDATE teachers SET 
            name='$name', phone='$phone', cnic='$cnic', address='$address', gender='$gender', hire_date='$hire_date',
            image='$image', class_assigned='$combo_string', password='$password'
            WHERE id=$id");
    } else {
        $conn->query("UPDATE teachers SET 
            name='$name', phone='$phone', cnic='$cnic', address='$address', gender='$gender', hire_date='$hire_date',
            image='$image', class_assigned='$combo_string'
            WHERE id=$id");
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

          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="<?= $teacher['name']; ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="<?= $teacher['phone']; ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">CNIC</label>
              <input type="text" name="cnic" class="form-control" value="<?= $teacher['cnic']; ?>">
            </div>

            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea name="address" class="form-control" rows="2"><?= $teacher['address']; ?></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-select">
                <option value="">-- Select Gender --</option>
                <option value="male" <?= $teacher['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $teacher['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Hire Date</label>
              <input type="date" name="hire_date" class="form-control" value="<?= $teacher['hire_date']; ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Current Image</label><br>
              <?php if ($teacher['image']) echo "<img src='uploads/{$teacher['image']}' width='80'><br><br>"; ?>
              <input type="file" name="image" class="form-control" accept="image/*">
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
