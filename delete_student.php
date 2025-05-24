<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location:index.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "❌ Student ID not provided.";
    header("Location: all_students.php");
    exit;
}

$id = intval($_GET['id']); // Security

// Check if student exists
$student_q = $conn->query("SELECT image, campus_id FROM students WHERE id = $id");
if ($student_q->num_rows === 0) {
    $_SESSION['error'] = "❌ Student not found.";
    header("Location: all_students.php");
    exit;
}

$student = $student_q->fetch_assoc();

// Campus Check: only allow if admin belongs to same campus (super admin can bypass)
if (isset($_SESSION['campus_id']) && $_SESSION['campus_id'] != $student['campus_id']) {
    $_SESSION['error'] = "❌ You are not authorized to delete this student.";
    header("Location: all_students.php");
    exit;
}

// Delete image if exists
if (!empty($student['image'])) {
    $image_path = "uploads/" . $student['image'];
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Delete related attendance
$conn->query("DELETE FROM attendance WHERE student_id = $id");

// Delete student record
if ($conn->query("DELETE FROM students WHERE id = $id") === TRUE) {
    $_SESSION['success'] = "🗑️ Student deleted successfully!";
} else {
    $_SESSION['error'] = "❌ Failed to delete student.";
}

header("Location: all_students.php");
exit;
?>
