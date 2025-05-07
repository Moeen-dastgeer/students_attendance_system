<?php
include 'db.php';

$conditions = [];

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

$query = "SELECT lr.*, t.name FROM leave_requests lr JOIN teachers t ON lr.teacher_id = t.id $where ORDER BY lr.id DESC";
$res = $conn->query($query);

echo "<table class='table table-bordered table-striped'>
        <thead>
          <tr><th>Teacher</th><th>From</th><th>To</th><th>Reason</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>";

if ($res->num_rows > 0) {
  while ($row = $res->fetch_assoc()) {
    $rowClass = '';
    if ($row['status'] == 'approved') {
      $status = '✅ Approved';
    } elseif ($row['status'] == 'cancelled') {
      $rowClass = 'table-danger';
      $status = '❌ Cancelled<br><small><strong>Reason:</strong> ' . $row['cancel_reason'] . '</small>';
    } else {
      $status = '⏳ Pending';
    }

    if ($row['status'] == 'pending') {
      $action = "
        <a href='?approve={$row['id']}' class='btn btn-sm btn-success me-1'>Approve</a>
        <button class='btn btn-sm btn-danger' onclick=\"cancelRequest({$row['id']})\">Cancel</button>
      ";
    } else {
      $action = "-";
    }

    echo "<tr class='$rowClass'>
            <td>{$row['name']}</td>
            <td>{$row['from_date']}</td>
            <td>{$row['to_date']}</td>
            <td>{$row['reason']}</td>
            <td>$status</td>
            <td>$action</td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='6' class='text-center'>No leave requests found.</td></tr>";
}

echo "</tbody></table>";
?>
