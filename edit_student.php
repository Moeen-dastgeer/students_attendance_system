<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "Student ID not provided.";
    exit;
}

$id = $_GET['id'];
$student = $conn->query("SELECT * FROM students WHERE id = $id")->fetch_assoc();

if (!$student) {
    echo "Student not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $cnic = $_POST['cnic'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $marital_status = $_POST['marital_status'] ?? '';
    $guardian_name = $_POST['guardian_name'] ?? '';
    $guardian_phone = $_POST['guardian_phone'] ?? '';
    $student_phone = $_POST['student_phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $education = $_POST['education'] ?? '';
    $dob = $_POST['dob'] ?? null;

    $course_id = $_POST['course_id'];
    $shift_id = $_POST['shift_id'];
    $admission_date = $_POST['admission_date'];
    $session_start = $_POST['session_start'];
    $session_end = $_POST['session_end'];
    $campus_id = $_SESSION['campus_id'] ?? $student['campus_id'];

    $image = $student['image'];
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    $conn->query("UPDATE students SET 
        name='$name',
        cnic='$cnic',
        gender='$gender',
        marital_status='$marital_status',
        guardian_name='$guardian_name',
        guardian_phone='$guardian_phone',
        student_phone='$student_phone',
        address='$address',
        education='$education',
        dob='$dob',
        course_id=$course_id,
        shift_id=$shift_id,
        admission_date='$admission_date',
        session_start='$session_start',
        session_end='$session_end',
        image='$image',
        campus_id=$campus_id
        WHERE id = $id");

    $_SESSION['success'] = "✅ Student updated successfully!";
    header("Location: student_profile.php?id=$id");
    exit;
}
?>

<?php include 'admin_header.php'; ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">✏️ Edit Student</h3>

      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Student Name</label>
          <input type="text" name="name" class="form-control" value="<?= $student['name']; ?>" required>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">CNIC / B-Form</label>
            <input type="text" name="cnic" class="form-control" value="<?= $student['cnic']; ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
              <option value="male" <?= $student['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
              <option value="female" <?= $student['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
              <option value="other" <?= $student['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
            </select>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Marital Status</label>
            <select name="marital_status" class="form-select">
              <option value="single" <?= $student['marital_status'] === 'single' ? 'selected' : '' ?>>Single</option>
              <option value="married" <?= $student['marital_status'] === 'married' ? 'selected' : '' ?>>Married</option>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" value="<?= $student['dob']; ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Guardian Name</label>
            <input type="text" name="guardian_name" class="form-control" value="<?= $student['guardian_name']; ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Guardian Phone</label>
            <input type="text" name="guardian_phone" class="form-control" value="<?= $student['guardian_phone']; ?>">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Student Phone</label>
            <input type="text" name="student_phone" class="form-control" value="<?= $student['student_phone']; ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Education</label>
            <input type="text" name="education" class="form-control" value="<?= $student['education']; ?>">
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control"><?= $student['address']; ?></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Select Course</label>
          <select name="course_id" class="form-select" required>
            <option value="">-- Select Course --</option>
            <?php
            $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
            while ($c = $courses->fetch_assoc()) {
                $sel = $student['course_id'] == $c['id'] ? 'selected' : '';
                echo "<option value='{$c['id']}' $sel>{$c['course_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Select Shift</label>
          <select name="shift_id" class="form-select" required>
            <option value="">-- Select Shift --</option>
            <?php
            $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
            while ($s = $shifts->fetch_assoc()) {
                $sel = $student['shift_id'] == $s['id'] ? 'selected' : '';
                echo "<option value='{$s['id']}' $sel>{$s['shift_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Admission Date</label>
            <input type="date" name="admission_date" class="form-control" value="<?= $student['admission_date'] ?>" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Session Start</label>
            <input type="date" name="session_start" class="form-control" value="<?= $student['session_start'] ?>" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Session End</label>
            <input type="date" name="session_end" class="form-control" value="<?= $student['session_end'] ?>" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Current Image:</label><br>
          <?php if ($student['image']) echo "<img src='uploads/{$student['image']}' width='80'><br><br>"; ?>
          <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success w-100">💾 Update Student</button>
      </form>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
