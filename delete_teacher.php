<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "❌ Invalid teacher ID.";
    header("Location: all_teachers.php");
    exit;
}

$id = intval($_GET['id']);

// Get teacher image to delete from folder
$teacher = $conn->query("SELECT image FROM teachers WHERE id = $id")->fetch_assoc();
if ($teacher && $teacher['image']) {
    $path = "uploads/" . $teacher['image'];
    if (file_exists($path)) {
        unlink($path);
    }
}

// Now delete teacher
if ($conn->query("DELETE FROM teachers WHERE id = $id") === TRUE) {
    $_SESSION['success'] = "🗑️ Teacher deleted successfully!";
} else {
    $_SESSION['error'] = "❌ Failed to delete teacher.";
}

header("Location: all_teachers.php");
exit;
?>
