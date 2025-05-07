<?php
require('fpdf.php');
include 'db.php';
$class = $_GET['class'];
$date = $_GET['date'];

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Attendance Report - '.$class.' ('.$date.')',0,1);
$pdf->SetFont('Arial','',12);

$sql = "SELECT s.name, s.roll, a.status FROM attendance a
        JOIN students s ON a.student_id = s.id
        WHERE s.class = '$class' AND a.date = '$date'";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    $pdf->Cell(0,10,"{$row['name']} - {$row['roll']} - {$row['status']}",0,1);
}
$pdf->Output();
?>