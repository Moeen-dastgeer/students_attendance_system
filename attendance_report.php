<?php include 'superadmin_header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'superadmin_sidebar.php'; ?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-4">📊 Student Attendance Report</h3>

      <!-- Filters -->
      <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
          <label class="form-label">Campus</label>
          <select name="campus_id" class="form-select">
            <option value="">All</option>
            <?php
            $campuses = $conn->query("SELECT * FROM campuses ORDER BY name");
            while ($camp = $campuses->fetch_assoc()) {
              $sel = ($_GET['campus_id'] ?? '') == $camp['id'] ? 'selected' : '';
              echo "<option value='{$camp['id']}' $sel>{$camp['name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Course</label>
          <select name="course_id" class="form-select">
            <option value="">All</option>
            <?php
            $courses = $conn->query("SELECT * FROM courses ORDER BY course_name");
            while ($c = $courses->fetch_assoc()) {
              $sel = ($_GET['course_id'] ?? '') == $c['id'] ? 'selected' : '';
              echo "<option value='{$c['id']}' $sel>{$c['course_name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Shift</label>
          <select name="shift_id" class="form-select">
            <option value="">All</option>
            <?php
            $shifts = $conn->query("SELECT * FROM shifts ORDER BY shift_name");
            while ($s = $shifts->fetch_assoc()) {
              $sel = ($_GET['shift_id'] ?? '') == $s['id'] ? 'selected' : '';
              echo "<option value='{$s['id']}' $sel>{$s['shift_name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">From</label>
          <input type="date" name="from" value="<?= $_GET['from'] ?? '' ?>" class="form-control">
        </div>
        <div class="col-md-3">
          <label class="form-label">To</label>
          <input type="date" name="to" value="<?= $_GET['to'] ?? '' ?>" class="form-control">
        </div>
        <div class="col-md-3 align-self-end">
          <button type="submit" class="btn btn-primary w-100">🔍 Filter</button>
        </div>
      </form>

      <?php if (isset($_GET['from']) && isset($_GET['to'])): ?>
      <div class="table-responsive">
        <table class="table table-bordered" id="attendanceTable">
          <thead class="table-dark">
            <tr>
              <th>Student</th>
              <th>Course</th>
              <th>Shift</th>
              <th>Campus</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $from = $_GET['from'];
          $to = $_GET['to'];
          $filter = "WHERE a.date BETWEEN '$from' AND '$to'";

          if (!empty($_GET['campus_id'])) {
            $campus_id = $_GET['campus_id'];
            $filter .= " AND s.campus_id = $campus_id";
          }
          if (!empty($_GET['course_id'])) {
            $filter .= " AND s.course_id = " . $_GET['course_id'];
          }
          if (!empty($_GET['shift_id'])) {
            $filter .= " AND s.shift_id = " . $_GET['shift_id'];
          }

          $query = "SELECT a.date, a.status, s.name, c.course_name, sh.shift_name, cam.name as campus 
                    FROM attendance a
                    JOIN students s ON a.student_id = s.id
                    LEFT JOIN courses c ON s.course_id = c.id
                    LEFT JOIN shifts sh ON s.shift_id = sh.id
                    LEFT JOIN campuses cam ON s.campus_id = cam.id
                    $filter
                    ORDER BY a.date DESC";

          $result = $conn->query($query);
          while ($row = $result->fetch_assoc()):
          ?>
            <tr>
              <td><?= $row['name'] ?></td>
              <td><?= $row['course_name'] ?></td>
              <td><?= $row['shift_name'] ?></td>
              <td><?= $row['campus'] ?></td>
              <td><?= $row['date'] ?></td>
              <td>
                <?php
                $color = match ($row['status']) {
                  'present' => 'success',
                  'absent' => 'danger',
                  'late' => 'warning',
                  'leave' => 'info',
                  default => 'secondary',
                };
                echo "<span class='badge bg-$color text-capitalize'>{$row['status']}</span>";
                ?>
              </td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#attendanceTable').DataTable({
      dom: 'Bfrtip',
      paging: true,
      ordering: true,
      info: true,
      responsive: true
    });
  });
</script>

<?php include 'superadmin_footer.php'; ?>
