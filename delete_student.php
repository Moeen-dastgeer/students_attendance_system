<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location:index.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "❌ Student ID not provided.";
    header("Location: all_students.php");
    exit;
}

$id = $_GET['id'];

// fetch image for delete
$result = $conn->query("SELECT image FROM students WHERE id = $id");
$student = $result->fetch_assoc();

if ($student && $student['image']) {
    $path = "uploads/" . $student['image'];
    if (file_exists($path)) {
        unlink($path); // delete image file
    }
}

// delete related attendance
$conn->query("DELETE FROM attendance WHERE student_id = $id");

// delete student
if ($conn->query("DELETE FROM students WHERE id = $id") === TRUE) {
    $_SESSION['success'] = "🗑️ Student deleted successfully!";
} else {
    $_SESSION['error'] = "❌ Failed to delete student.";
}

header("Location: all_students.php");
exit;
?>
