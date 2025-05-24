<!-- superadmin_students.php -->
<?php include 'superadmin_header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'superadmin_sidebar.php'; ?>

<div class="container-fluid py-4">
  <div class="card">
    <div class="card-body">
      <div class="mb-5">
        <h4 class="card-title">🎓 All Students</h4>
      </div>

      <div class="row g-2 mb-3">
        <div class="col-md-3">
          <select id="filter_campus" class="form-select">
            <option value="">All Campuses</option>
            <?php
              $camps = $conn->query("SELECT * FROM campuses ORDER BY name");
              while ($c = $camps->fetch_assoc()) {
                echo "<option value='{$c['id']}'>{$c['name']}</option>";
              }
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <select id="filter_course" class="form-select">
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
          <select id="filter_shift" class="form-select">
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
          <input type="text" id="search_name" class="form-control" placeholder="🔍 Search by name">
        </div>
      </div>

      <div id="student_table_area"></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function fetchStudents() {
  const campus = $('#filter_campus').val();
  const course = $('#filter_course').val();
  const shift = $('#filter_shift').val();
  const search = $('#search_name').val();

  $.post('ajax_fetch_students1.php', {
    campus_id: campus,
    course_id: course,
    shift_id: shift,
    search: search
  }, function(data) {
    $('#student_table_area').html(data);
  });
}

$(document).ready(function () {
  fetchStudents();
  $('#filter_campus, #filter_course, #filter_shift').on('change', fetchStudents);
  $('#search_name').on('keyup', function () {
    clearTimeout(this.delay);
    this.delay = setTimeout(fetchStudents, 300);
  });
});
</script>

<?php include 'superadmin_footer.php'; ?>
