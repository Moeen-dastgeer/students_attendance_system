<?php
include 'db.php';

$id = $_GET['id'];
$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';

$student = $conn->query("SELECT * FROM students WHERE id = $id")->fetch_assoc();

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=student_attendance_summary.csv');

$output = fopen('php://output', 'w');

fputcsv($output, ["Student: {$student['name']}"]);
fputcsv($output, ["Roll: {$student['roll']}", "Class: {$student['class']}"]);
fputcsv($output, ["Date Range: $start to $end"]);
fputcsv($output, []);

fputcsv($output, ['Date', 'Status']);

$summary = ['present' => 0, 'absent' => 0, 'late' => 0, 'leave' => 0];

$query = "SELECT date, status FROM attendance WHERE student_id = $id AND date BETWEEN '$start' AND '$end' ORDER BY date";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['date'], $row['status']]);
    if (isset($summary[$row['status']])) {
        $summary[$row['status']]++;
    }
}

fputcsv($output, []);
fputcsv($output, ['Summary']);
fputcsv($output, ['Present', 'Absent', 'Late', 'leave']);
fputcsv($output, [$summary['present'], $summary['absent'], $summary['late'], $summary['leave']]);

fclose($output);
?>
