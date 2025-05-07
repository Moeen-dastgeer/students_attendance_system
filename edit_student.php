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
    $roll = $_POST['roll'];
    $course_id = $_POST['course_id'];
    $shift_id = $_POST['shift_id'];

    $image = $student['image']; // Default: old image
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    $conn->query("UPDATE students 
                  SET name='$name', roll='$roll', course_id=$course_id, shift_id=$shift_id, image='$image' 
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

        <div class="mb-3">
          <label class="form-label">Roll Number</label>
          <input type="text" name="roll" class="form-control" value="<?= $student['roll']; ?>" required>
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
