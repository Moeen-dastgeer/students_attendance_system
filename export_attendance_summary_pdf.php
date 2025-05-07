<?php
define('FPDF_FONTPATH','fpdf/font/');
require('fpdf/fpdf.php');
include 'db.php';

$start = $_GET['start_date'] ?? '';
$end = $_GET['end_date'] ?? '';
$class = $_GET['class'] ?? 'all';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,"Attendance Summary Report",0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,"Date Range: $start to $end",0,1);
$pdf->Cell(0,10,"Class: " . ($class == 'all' ? 'All' : $class),0,1);
$pdf->Ln();

$pdf->SetFont('Arial','B',11);
$pdf->Cell(40,10,'Name',1);
$pdf->Cell(30,10,'Roll',1);
$pdf->Cell(30,10,'Class',1);
$pdf->Cell(30,10,'Present',1);
$pdf->Cell(30,10,'Absent',1);
$pdf->Cell(30,10,'Late',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);

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

    $pdf->Cell(40,10,$student['name'],1);
    $pdf->Cell(30,10,$student['roll'],1);
    $pdf->Cell(30,10,$student['class'],1);
    $pdf->Cell(30,10,$counts['present'],1);
    $pdf->Cell(30,10,$counts['absent'],1);
    $pdf->Cell(30,10,$counts['late'],1);
    $pdf->Ln();
}

$pdf->Output();
?>
