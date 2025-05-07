<?php
include 'db.php';

$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';
$class = $_GET['class'] ?? 'all';

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=filtered_attendance.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Date', 'Class', 'Name', 'Roll', 'Status']);

$sql = "SELECT s.name, s.roll, s.class, a.status, a.date 
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE a.date BETWEEN '$start' AND '$end'";

if ($class !== 'all') {
    $sql .= " AND s.class = '$class'";
}

$sql .= " ORDER BY a.date, s.class, s.name";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [$row['date'], $row['class'], $row['name'], $row['roll'], $row['status']]);
}
fclose($output);
?>
