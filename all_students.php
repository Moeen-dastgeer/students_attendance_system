<?php include 'admin_header.php'; ?>
<?php include 'db.php'; ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">📋 All Registered Students</h3>

      <!-- Filters -->
      <form id="filterForm" class="row g-2 mb-4">
        <?php
        $isSuperAdmin = !isset($_SESSION['campus_id']) || $_SESSION['campus_id'] === null;
        $campuses = $conn->query("SELECT id, name FROM campuses ORDER BY name");
        ?>
        <?php if ($isSuperAdmin): ?>
        <div class="col-md-3">
          <select name="campus_id" class="form-select">
            <option value="">All Campuses</option>
            <?php while ($camp = $campuses->fetch_assoc()): ?>
              <option value="<?= $camp['id'] ?>"><?= $camp['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <?php endif; ?>

        <div class="col-md-3">
          <select name="course_id" class="form-select">
            <option value="">All Courses</option>
            <?php
            $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
            while ($c = $courses->fetch_assoc()) {
              echo "<option value='{$c['id']}'>{$c['course_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-3">
          <select name="shift_id" class="form-select">
            <option value="">All Shifts</option>
            <?php
            $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
            while ($s = $shifts->fetch_assoc()) {
              echo "<option value='{$s['id']}'>{$s['shift_name']}</option>";
            }
            ?>
          </select>
        </div>

        <div class="col-md-3">
          <input type="text" name="search" class="form-control" placeholder="Search by name">
        </div>
      </form>

      <!-- Table will be loaded here -->
      <div id="students_table"></div>
    </div>
  </div>
</div>

<?php include 'admin_footer.php'; ?>
