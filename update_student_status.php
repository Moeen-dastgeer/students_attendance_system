<?php
include 'db.php';
$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';

if (in_array($status, ['active', 'passout', 'blocked'])) {
  $conn->query("UPDATE students SET status = '$status' WHERE id = $id");
  echo "✅ Status updated!";
} else {
  echo "❌ Invalid status!";
}
