<?php
require('protected/modules/fpdf.php');

class reportProduct extends FPDF
{
//Page header
function Header()
{
    $this->SetFont('Arial','B',16);
    //Title Header
	$this->Cell(0,10,'Product Report','B',1,'L');
	$this->Ln(5);
	$this->SetFont('Arial','B',12);
    $this->Cell(20,10,'ID',1,0,'C');
	$this->Cell(30,10,'Name',1,0,'C');
	$this->Cell(10,10,'Qty',1,0,'C');
	$this->Cell(20,10,'Price',1,0,'C');
	$this->Cell(20,10,'Imported',1,0,'C');
    //Line break
    $this->Ln(10);
}

//Page footer
function Footer()
{
    //Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

?>