<?php include 'admin_header.php'; ?>
<?php
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">📋 All Registered Students</h3>

      <!-- Filters -->
      <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
          <select name="course_id" class="form-select" onchange="this.form.submit()">
            <option value="">All Courses</option>
            <?php
            $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
            while ($c = $courses->fetch_assoc()) {
                $sel = ($_GET['course_id'] ?? '') == $c['id'] ? 'selected' : '';
                echo "<option value='{$c['id']}' $sel>{$c['course_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-4">
          <select name="shift_id" class="form-select" onchange="this.form.submit()">
            <option value="">All Shifts</option>
            <?php
            $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
            while ($s = $shifts->fetch_assoc()) {
                $sel = ($_GET['shift_id'] ?? '') == $s['id'] ? 'selected' : '';
                echo "<option value='{$s['id']}' $sel>{$s['shift_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-4">
          <input type="text" name="search" class="form-control" placeholder="Search by name or roll..." value="<?= $_GET['search'] ?? '' ?>">
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Roll</th>
              <th>Course</th>
              <th>Shift</th>
              <th>Profile</th>
            </tr>
          </thead>
          <tbody>
          <?php
            $search = $_GET['search'] ?? '';
            $courseFilter = $_GET['course_id'] ?? '';
            $shiftFilter = $_GET['shift_id'] ?? '';

            $query = "SELECT s.*, c.course_name, sh.shift_name 
                      FROM students s
                      LEFT JOIN courses c ON s.course_id = c.id
                      LEFT JOIN shifts sh ON s.shift_id = sh.id
                      WHERE 1=1";

            if ($search) {
                $query .= " AND (s.name LIKE '%$search%' OR s.roll LIKE '%$search%')";
            }
            if ($courseFilter) {
                $query .= " AND s.course_id = $courseFilter";
            }
            if ($shiftFilter) {
                $query .= " AND s.shift_id = $shiftFilter";
            }

            $query .= " ORDER BY c.course_name, sh.shift_name, s.name";

            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                $img = $row['image'] ? "<img src='uploads/{$row['image']}' width='50' class='img-thumbnail'>" : "-";
                echo "<tr>
                        <td>$img</td>
                        <td>{$row['name']}</td>
                        <td>{$row['roll']}</td>
                        <td>{$row['course_name']}</td>
                        <td>{$row['shift_name']}</td>
                        <td><a href='student_profile.php?id={$row['id']}' class='btn btn-sm btn-info'>👤 View</a></td>
                      </tr>";
            }
          ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
