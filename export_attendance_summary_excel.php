<?php
include 'db.php';

$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';
$class = $_GET['class'] ?? 'all';

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=attendance_summary.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Name', 'Roll', 'Class', 'Present', 'Absent', 'Late']);

$sql = "SELECT * FROM students";
if ($class !== 'all') {
    $sql .= " WHERE class = '$class'";
}
$students = $conn->query($sql);

while ($student = $students->fetch_assoc()) {
    $sid = $student['id'];
    $counts = ['present'=>0, 'absent'=>0, 'late'=>0];

    $att_sql = "SELECT status FROM attendance WHERE student_id = $sid AND date BETWEEN '$start' AND '$end'";
    $att_result = $conn->query($att_sql);
    while ($row = $att_result->fetch_assoc()) {
        $status = $row['status'];
        if (isset($counts[$status])) {
            $counts[$status]++;
        }
    }

    fputcsv($output, [
        $student['name'],
        $student['roll'],
        $student['class'],
        $counts['present'],
        $counts['absent'],
        $counts['late']
    ]);
}
fclose($output);
?>
