<?php
include 'db.php';
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=students.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Name', 'Roll', 'Class']);

$result = $conn->query("SELECT name, roll, class FROM students");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
?>
