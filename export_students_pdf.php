<?php
define('FPDF_FONTPATH','fpdf/font/');
require('fpdf/fpdf.php');
include 'db.php';

$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'All Students', 0, 1);
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Name', 1);
$pdf->Cell(40, 10, 'Roll', 1);
$pdf->Cell(60, 10, 'Class', 1);
$pdf->Ln();

// Student Data
$pdf->SetFont('Arial', '', 12);

$result = $conn->query("SELECT name, roll, class FROM students");

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50, 10, $row['name'], 1);
    $pdf->Cell(40, 10, $row['roll'], 1);
    $pdf->Cell(60, 10, $row['class'], 1);
    $pdf->Ln();
}

// Only ONE output call
$pdf->Output();
?>
