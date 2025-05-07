<?php
include 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=attendance.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Name', 'Roll', 'Status']);

$date = date('Y-m-d');
$result = $conn->query("SELECT s.name, s.roll, a.status FROM attendance a JOIN students s ON a.student_id = s.id WHERE a.date = '$date'");
while($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['name'], $row['roll'], $row['status']]);
}
fclose($output);
?>