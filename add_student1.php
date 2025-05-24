<?php
session_start();
include 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Collect data
$name = $_POST['name'];
$cnic = $_POST['cnic'] ?? '';
$gender = $_POST['gender'] ?? '';
$marital_status = $_POST['marital_status'] ?? '';
$guardian_name = $_POST['guardian_name'] ?? '';
$guardian_phone = $_POST['guardian_phone'] ?? '';
$student_phone = $_POST['student_phone'] ?? '';
$address = $_POST['address'] ?? '';
$education = $_POST['education'] ?? '';
$dob = $_POST['dob'] ?? null;

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

$campus_id = $_SESSION['campus_id'] ?? null;

// ✅ Insert into DB with default status 'active'
$sql = "INSERT INTO students (
    name, cnic, gender, marital_status, guardian_name, guardian_phone, student_phone,
    address, education, dob,
    course_id, shift_id, admission_date, session_start, session_end, image, campus_id, status
) VALUES (
    '$name', '$cnic', '$gender', '$marital_status', '$guardian_name', '$guardian_phone', '$student_phone',
    '$address', '$education', '$dob',
    $course_id, $shift_id, '$admission_date', '$session_start', '$session_end', '$image', " . ($campus_id ?? 'NULL') . ", 'active'
)";

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
