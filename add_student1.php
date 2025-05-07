<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Collect form data
$name = $_POST['name'];
$roll = $_POST['roll'];
$course_id = $_POST['course_id'];
$shift_id = $_POST['shift_id'];

$admission_date = $_POST['admission_date'];
$session_start = $_POST['session_start'];
$session_end = $_POST['session_end'];

$image = '';
if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
    $image = time() . '_' . basename($_FILES['image']['name']);
    $target = "uploads/" . $image;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);
}

// Insert into database
$sql = "INSERT INTO students 
        (name, roll, course_id, shift_id, admission_date, session_start, session_end, image)
        VALUES 
        ('$name', '$roll', $course_id, $shift_id, '$admission_date', '$session_start', '$session_end', '$image')";

if ($conn->query($sql) === TRUE) {
    $_SESSION['success'] = "✅ Student added successfully!";
    header("Location: all_students.php");
    exit;
} else {
    $_SESSION['error'] = "❌ Error: " . $conn->error;
    header("Location: add_student.php");
    exit;
}
?>
