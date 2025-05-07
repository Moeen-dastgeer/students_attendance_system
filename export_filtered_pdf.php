<?php
require('fpdf.php');
include 'db.php';

$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';
$class = $_GET['class'] ?? 'all';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,"Filtered Attendance Report",0,1);
$pdf->SetFont('Arial','',12);

$pdf->Cell(0,10,"Date Range: $start to $end",0,1);
$pdf->Cell(0,10,"Class: " . ($class == 'all' ? 'All' : $class),0,1);
$pdf->Ln();

$sql = "SELECT s.name, s.roll, s.class, a.status, a.date 
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE a.date BETWEEN '$start' AND '$end'";

if ($class !== 'all') {
    $sql .= " AND s.class = '$class'";
}

$sql .= " ORDER BY a.date, s.class, s.name";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    $line = "{$row['date']} - {$row['class']} - {$row['name']} - {$row['roll']} - {$row['status']}";
    $pdf->Cell(0,10,$line,0,1);
}

$pdf->Output();
?>
