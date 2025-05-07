<?php
include 'db.php';
session_start();

$id = $_GET['id'] ?? 0;
$conn->query("DELETE FROM shifts WHERE id = $id");
$_SESSION['success'] = "🗑️ Shift deleted successfully.";
header("Location: all_shifts.php");
exit;
?>
