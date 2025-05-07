<?php
include 'teacher_header.php';
include 'db.php';

$teacher_id = $_SESSION['teacher_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];
    $reason = $_POST['reason'];

    $conn->query("INSERT INTO leave_requests (teacher_id, from_date, to_date, reason) VALUES ($teacher_id, '$from', '$to', '$reason')");
    $msg = "✅ Leave request submitted successfully.";
}
?>

<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="card-title mb-3">📩 Request Leave</h4>

      <?php if (isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

      <form method="POST">
        <div class="mb-3">
          <label>From Date</label>
          <input type="date" name="from_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>To Date</label>
          <input type="date" name="to_date" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Reason</label>
          <textarea name="reason" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">📤 Send Request</button>
      </form>

      <hr>
      <h5 class="mt-4">📋 My Leave Requests</h5>
      <table class="table table-bordered table-striped">
        <thead><tr><th>From</th><th>To</th><th>Reason</th><th>Status</th></tr></thead>
        <tbody>
        <?php
        $res = $conn->query("SELECT * FROM leave_requests WHERE teacher_id = $teacher_id ORDER BY id DESC");
        while($row = $res->fetch_assoc()) {
          $status = $row['status'] == 'approved' ? '✅ Approved' : '⏳ Pending';
          echo "<tr>
                  <td>{$row['from_date']}</td>
                  <td>{$row['to_date']}</td>
                  <td>{$row['reason']}</td>
                  <td>$status</td>
                </tr>";
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'teacher_footer.php'; ?>
