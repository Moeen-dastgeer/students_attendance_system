<?php
include 'db.php';

$student_id = intval($_POST['student_id']);
$status = $_POST['status'];
$date = date('Y-m-d');

// Check if attendance already exists
$check = $conn->query("SELECT * FROM attendance WHERE student_id = $student_id AND date = '$date'");

if ($check->num_rows > 0) {
    $conn->query("UPDATE attendance SET status = '$status' WHERE student_id = $student_id AND date = '$date'");
    // echo "✅ Updated to $status";
} else {
    $conn->query("INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$status')");
    // echo "✅ Marked $status";
}
?>
