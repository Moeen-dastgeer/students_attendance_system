<?php
include 'db.php';
session_start();

$id = $_GET['id'] ?? 0;

$conn->query("DELETE FROM courses WHERE id = $id");
$_SESSION['success'] = "🗑️ Course deleted successfully!";
header("Location: all_courses.php");
exit;
?>
