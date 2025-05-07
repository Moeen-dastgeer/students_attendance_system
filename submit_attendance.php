<?php
include 'db.php';
$date = date('Y-m-d');
foreach ($_POST['attendance'] as $student_id => $status) {
    $sql = "INSERT INTO attendance (student_id, date, status) VALUES ($student_id, '$date', '$status')";
    $conn->query($sql);
}
echo "Attendance submitted successfully.";
$conn->close();
?>