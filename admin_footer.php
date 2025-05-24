</div> <!-- /.content-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
function fetchStudents() {
  $.ajax({
    url: 'ajax_fetch_students.php',
    method: 'POST',
    data: $('#filterForm').serialize(),
    success: function (data) {
      $('#students_table').html(data);
    }
  });
}

$(document).ready(function () {
  fetchStudents(); // initial load

  $('#filterForm select, #filterForm input[name="search"]').on('change keyup', function () {
    clearTimeout(this.delay);
    this.delay = setTimeout(fetchStudents, 300);
  });
});

function updateStatus(id, status) {
  $.post('update_student_status.php', { id: id, status: status }, function (res) {
    alert(res);
  });
}
</script>

</body>
</html>
