<?php
session_start();
include 'db.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];
$conn->query("DELETE FROM teachers WHERE id = $id");

header("Location: all_teachers.php");
exit;
?>
