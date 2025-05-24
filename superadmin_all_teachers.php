<?php include 'superadmin_header.php'; ?>
<?php include 'db.php'; ?>
<?php include 'superadmin_sidebar.php'; ?>

<div class="container-fluid py-4">
  <div class="card">
    <div class="card-body">
      <div class="mb-5">
        <h4 class="card-title">👨‍🏫 All Teachers</h4>
      </div>

      <div class="row g-2 mb-3">
        <div class="col-md-4">
          <label>Filter by Campus</label>
          <select id="campus_filter" class="form-select">
            <option value="">-- All Campuses --</option>
            <?php
            $campuses = $conn->query("SELECT id, name FROM campuses ORDER BY name");
            while ($c = $campuses->fetch_assoc()) {
              echo "<option value='{$c['id']}'>{$c['name']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-md-4">
          <label>Search by Name</label>
          <input type="text" id="search_name" class="form-control" placeholder="Enter teacher name...">
        </div>
      </div>

      <div id="teacher_table">
        <!-- AJAX results will load here -->
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function fetchTeachers() {
    let campusId = $('#campus_filter').val();
    let name = $('#search_name').val();

    $.ajax({
      url: 'fetch_teachers_ajax.php',
      type: 'POST',
      data: {
        campus_id: campusId,
        search_name: name
      },
      success: function(response) {
        $('#teacher_table').html(response);
      }
    });
  }

  $(document).ready(function () {
    fetchTeachers();

    $('#campus_filter').on('change', fetchTeachers);
    $('#search_name').on('keyup', function () {
      clearTimeout(this.delay);
      this.delay = setTimeout(fetchTeachers, 300);
    });
  });
</script>

<?php include 'superadmin_footer.php'; ?>
