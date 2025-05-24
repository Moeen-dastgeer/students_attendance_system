<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    exit("❌ Unauthorized access.");
}

$student_id = intval($_POST['student_id']);
$status = $_POST['status'];
$date = date('Y-m-d');

// Get current admin's campus_id (if not super admin)
$campus_id = $_SESSION['campus_id'] ?? null;

// Fetch student info
$student = $conn->query("SELECT campus_id FROM students WHERE id = $student_id")->fetch_assoc();

if (!$student) {
    exit("❌ Student not found.");
}

// If admin is NOT super admin, check campus match
if ($campus_id && $student['campus_id'] != $campus_id) {
    exit("❌ You are not authorized to mark attendance for this student.");
}

// Check if attendance already exists
$check = $conn->query("SELECT * FROM attendance WHERE student_id = $student_id AND date = '$date'");

if ($check->num_rows > 0) {
    $conn->query("UPDATE attendance SET status = '$status' WHERE student_id = $student_id AND date = '$date'");
    echo "✅ Updated to $status";
} else {
    $conn->query("INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$status')");
    echo "✅ Marked $status";
}
?>
