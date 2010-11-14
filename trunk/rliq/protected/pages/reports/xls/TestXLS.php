<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Prado::using('Application.3rdParty.Classes.PHPExcel');
Prado::using('Application.3rdParty.Classes.PHPExcel.Writer.*');

$workbook = new PFExcel();

$sheet = $workbook->getActiveSheet();
$sheet->setTitle('RISKLOGIQ');

$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

$sheet->getRowDimension('1')->setRowHeight(50);

$workbook->getActiveSheet()->getStyle('A1')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true,
                                'size'          => 12,
                                'color'     =>array(
                                               'rgb'=>'FF00FF00')
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			),
			'borders' => array(
				'top'     => array(
 					'style' => PHPExcel_Style_Border::BORDER_THIN
 				)
			),
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
	  			'rotation'   => 90,
	 			'startcolor' => array(
	 				'argb' => 'FFA0A0A0'
	 			),
	 			'endcolor'   => array(
	 				'argb' => 'FFFFFFFF'
	 			)
	 		)
		),
		'A3:E3'
);

$workbook->getActiveSheet()->duplicateStyle( $workbook->getActiveSheet()->getStyle('A1'), 'B1:ZZ1' );

$sheet->setCellValue('A1',"RISKLOGIQ");
$sheet->mergeCells('A1:B1');

$Records = ActivityRecord::finder()->findAll();

$counter=5;
foreach($Records AS $Record){
    $sheet->setCellValue('A'.$counter,$Record->act_pspcode);
    $sheet->setCellValue('B'.$counter,$Record->act_name);
    $sheet->setCellValue('C'.$counter,$Record->act_dauer);
    $sheet->setCellValue('D'.$counter,$Record->act_faz);
    $sheet->setCellValue('E'.$counter,$Record->act_fez);
    $counter++;
}

$workbook->generate('Excel2007','risklogiq2009');

?>
