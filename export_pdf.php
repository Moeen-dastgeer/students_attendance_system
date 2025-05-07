<?php
require('fpdf.php');
include 'db.php';

$date = date('Y-m-d');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Attendance Report - '.$date, 0, 1);
$pdf->SetFont('Arial','',12);

$result = $conn->query("SELECT s.name, s.roll, a.status FROM attendance a JOIN students s ON a.student_id = s.id WHERE a.date = '$date'");
while($row = $result->fetch_assoc()) {
    $pdf->Cell(0,10,"{$row['name']} - {$row['roll']} - {$row['status']}",0,1);
}

$pdf->Output();
?>
