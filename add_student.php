<?php include 'admin_header.php'; ?>
<?php include 'db.php'; ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="mb-5">
        <h3 class="card-title">➕ Add New Student</h3>
      </div>

      <form action="add_student1.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Student Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">CNIC / B-Form</label>
          <input type="text" name="cnic" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Gender</label>
          <select name="gender" class="form-select">
            <option value="">-- Select --</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Marital Status</label>
          <select name="marital_status" class="form-select">
            <option value="">-- Select --</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Guardian Name</label>
          <input type="text" name="guardian_name" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Guardian Phone</label>
          <input type="text" name="guardian_phone" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Student Phone</label>
          <input type="text" name="student_phone" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea name="address" class="form-control" rows="2"></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Education</label>
          <input type="text" name="education" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Date of Birth</label>
          <input type="date" name="dob" class="form-control">
        </div>

        <div class="mb-3">
          <label class="form-label">Select Course</label>
          <select name="course_id" class="form-select" required>
            <option value="">-- Select Course --</option>
            <?php
            $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
            while ($c = $courses->fetch_assoc()) {
                echo "<option value='{$c['id']}'>{$c['course_name']}</option>";
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
                echo "<option value='{$s['id']}'>{$s['shift_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Admission Date</label>
          <input type="date" name="admission_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Session Start Date</label>
          <input type="date" name="session_start" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Session End Date</label>
          <input type="date" name="session_end" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Upload Image (optional)</label>
          <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary w-100">➕ Add Student</button>
      </form>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
