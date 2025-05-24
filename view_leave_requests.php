<?php include 'admin_header.php'; ?>
<?php
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin_campus_id = $_SESSION['campus_id'] ?? null;

// Handle approval
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE leave_requests SET status = 'approved' WHERE id = $id");
    $_SESSION['success'] = "✅ Leave request approved.";
    header("Location: view_leave_requests.php");
    exit;
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="card-title mb-4">🗂️ Leave Requests (Teachers)</h4>

      <!-- Flash Messages -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
      <?php endif; ?>

      <!-- Filters -->
      <div class="row g-2 mb-3">
        <div class="col-md-3">
          <select id="filter_status" class="form-select">
            <option value="">-- All Status --</option>
            <option value="pending">⏳ Pending</option>
            <option value="approved">✅ Approved</option>
            <option value="cancelled">❌ Cancelled</option>
          </select>
        </div>

        <div class="col-md-3">
          <input type="text" id="teacher_name" class="form-control" placeholder="Search Teacher">
        </div>

        <div class="col-md-2">
          <input type="date" id="from_date" class="form-control">
        </div>

        <div class="col-md-2">
          <input type="date" id="to_date" class="form-control">
        </div>
      </div>

      <!-- Results -->
      <div id="leave_table">
        <!-- AJAX Results will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function fetchLeaves() {
    const status = $('#filter_status').val();
    const teacher = $('#teacher_name').val();
    const from = $('#from_date').val();
    const to = $('#to_date').val();

    $.ajax({
      url: 'fetch_leave_requests.php',
      method: 'POST',
      data: {
        filter_status: status,
        teacher_name: teacher,
        from_date: from,
        to_date: to
      },
      success: function (data) {
        $('#leave_table').html(data);
      }
    });
  }

  function cancelRequest(id) {
    const reason = prompt("Enter cancellation reason:");
    if (!reason) return;

    if (confirm("Are you sure you want to cancel this request?")) {
      fetch('cancel_leave.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&reason=' + encodeURIComponent(reason)
      })
      .then(res => res.text())
      .then(data => {
        alert(data);
        fetchLeaves(); // reload table
      });
    }
  }

  $(document).ready(function () {
    fetchLeaves();

    $('#filter_status, #from_date, #to_date').on('change', fetchLeaves);
    $('#teacher_name').on('keyup', function () {
      clearTimeout(this.delay);
      this.delay = setTimeout(fetchLeaves, 300);
    });
  });
</script>

<?php include 'admin_footer.php'; ?>
