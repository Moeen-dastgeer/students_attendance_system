<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
  exit("❌ Unauthorized access.");
}

$id = intval($_POST['id']);
$reason = $conn->real_escape_string($_POST['reason'] ?? '');
$campus_id = $_SESSION['campus_id'] ?? null;

// Check if leave request exists and belongs to the same campus (optional safety)
$check = $conn->query("
  SELECT lr.*, t.campus_id 
  FROM leave_requests lr 
  JOIN teachers t ON lr.teacher_id = t.id 
  WHERE lr.id = $id
");

if ($check->num_rows == 0) {
  exit("❌ Leave request not found.");
}

$leave = $check->fetch_assoc();
if ($campus_id && $leave['campus_id'] != $campus_id) {
  exit("❌ You are not authorized to cancel this request.");
}

// Only allow cancelling if it's still pending
if ($leave['status'] !== 'pending') {
  exit("⚠️ Cannot cancel. Already processed.");
}

// Update leave status
$update = $conn->query("
  UPDATE leave_requests 
  SET status = 'cancelled', cancel_reason = '$reason' 
  WHERE id = $id
");

if ($update) {
  echo "✅ Leave request cancelled.";
} else {
  echo "❌ Failed to cancel leave.";
}
?>
