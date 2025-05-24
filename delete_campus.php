<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    $_SESSION['error'] = "❌ Invalid campus ID.";
    header("Location: campuses.php");
    exit;
}

// First, delete the admin linked to this campus (but only if not super admin)
$conn->query("DELETE FROM admins WHERE campus_id = $id AND is_super = 0");

// Then delete the campus itself
$conn->query("DELETE FROM campuses WHERE id = $id");

$_SESSION['success'] = "🗑️ Campus and its admin deleted!";
header("Location: campuses.php");
exit;
?>
