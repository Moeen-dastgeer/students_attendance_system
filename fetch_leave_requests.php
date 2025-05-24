<?php
session_start();
include 'db.php';

$conditions = [];
$params = [];

$campus_id = $_SESSION['campus_id'] ?? null;
$role = $_SESSION['role'] ?? '';

if ($role === 'admin' && $campus_id) {
  $conditions[] = "t.campus_id = " . intval($campus_id);
}

if (!empty($_POST['filter_status'])) {
  $status = $conn->real_escape_string($_POST['filter_status']);
  $conditions[] = "lr.status = '$status'";
}

if (!empty($_POST['teacher_name'])) {
  $name = $conn->real_escape_string($_POST['teacher_name']);
  $conditions[] = "t.name LIKE '%$name%'";
}

if (!empty($_POST['from_date'])) {
  $from = $conn->real_escape_string($_POST['from_date']);
  $conditions[] = "lr.from_date >= '$from'";
}

if (!empty($_POST['to_date'])) {
  $to = $conn->real_escape_string($_POST['to_date']);
  $conditions[] = "lr.to_date <= '$to'";
}

$where = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

$query = "SELECT lr.*, t.name 
          FROM leave_requests lr 
          JOIN teachers t ON lr.teacher_id = t.id 
          $where 
          ORDER BY lr.id DESC";

$res = $conn->query($query);

echo "<table class='table table-bordered table-striped'>
        <thead class='table-light'>
          <tr>
            <th>Teacher</th>
            <th>From</th>
            <th>To</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>";

if ($res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
    $statusBadge = match ($row['status']) {
      'approved'  => "<span class='badge bg-success'>Approved</span>",
      'cancelled' => "<span class='badge bg-danger'>Cancelled</span><br><small><strong>Reason:</strong> {$row['cancel_reason']}</small>",
      default     => "<span class='badge bg-warning text-dark'>Pending</span>",
    };

    $actions = "-";
    if ($row['status'] === 'pending') {
      $actions = "
        <a href='?approve={$row['id']}' class='btn btn-sm btn-success me-1'>Approve</a>
        <button class='btn btn-sm btn-danger' onclick=\"cancelRequest({$row['id']})\">Cancel</button>
      ";
    }

    echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['from_date']}</td>
            <td>{$row['to_date']}</td>
            <td>{$row['reason']}</td>
            <td>$statusBadge</td>
            <td>$actions</td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='6' class='text-center'>No leave requests found.</td></tr>";
}

echo "</tbody></table>";
?>
