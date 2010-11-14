<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Prado::using('Application.3rdParty.Classes.PHPExcel');
Prado::using('Application.3rdParty.Classes.PHPExcel.Writer.*');


class WBK_Protokoll extends TPage{

    private $workbook;
    private $idtm_termin=0;
    private $idtm_protokoll=0;
    private $config=array('indent'=>true, 'output-xhtml'=>true);
    private $encoding='utf8';

    public function onPreInit($param) {

            parent::onPreInit($param);

                $completeCounter=1;

                $this->idtm_termin=$_GET['idtm_termin'];
                $this->idtm_protokoll=$_GET['idtm_protokoll'];

                $idtm_termin = $this->idtm_termin;
                $idtm_protokoll = $this->idtm_protokoll;

                $this->workbook = new PHPExcel();

                $sheet = $this->workbook->getActiveSheet();
                $sheet->setTitle('Protokoll');

                $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT);
                $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

                $sheet->getRowDimension('1')->setRowHeight(40);
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(5);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);

                $this->workbook->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $this->workbook->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setRGB('898989');
                $this->workbook->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
                $this->workbook->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');

                $sheet->mergeCells('A1:B1');
                $sheet->setCellValue('A1',"Protokoll");

                $this->workbook->getActiveSheet()->duplicateStyle( $this->workbook->getActiveSheet()->getStyle('A1'), 'A'.$completeCounter.':H'.$completeCounter );

                $counter=5;
                $completeCounter=$counter;

                $this->workbook->getActiveSheet()->getStyle('B5')->applyFromArray(
                                array(
                                        'font'    => array(
                                                'bold'      => true,
                                                'size'      => 12,
                                                'color'     => array(
                                                               'argb'=>'FFFFFFFF')
                                        ),
                                        'alignment' => array(
                                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                        ),
                                        'borders' => array(
                                                'top'     => array(
                                                        'style' => PHPExcel_Style_Border::BORDER_THIN
                                                )
                                        ),
                                        'fill' => array(
                                                'type'       => PHPExcel_Style_Fill::FILL_SOLID,
                                                'startcolor' => array(
                        				'argb' => 'FFA0A0A0'
                                    		)
                                        )
                                )
                );
                $this->workbook->getActiveSheet()->duplicateStyle( $this->workbook->getActiveSheet()->getStyle('B5'), 'B'.$counter.':C'.$counter );
                $this->workbook->getActiveSheet()->duplicateStyle( $this->workbook->getActiveSheet()->getStyle('B5'), 'E'.$counter.':G'.$counter );

                //der header fuer den Bereich
                $sheet->setCellValue('B'.$counter,"Name");
                $sheet->setCellValue('C'.$counter,"Projektrolle");
                $sheet->setCellValue('E'.$counter,"Name");
                $sheet->setCellValue('F'.$counter,"Kommentar");
                $sheet->setCellValue('G'.$counter,"AV");
                $counter++;


                $criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "idtm_termin = :suchtext";
    		$criteria->Parameters[':suchtext'] = $idtm_termin;
		$criteria->OrdersBy["idtm_organisation"] = 'asc';
                $Records = TerminOrganisationView::finder()->findAll($criteria);

                foreach($Records AS $Record){
                    $sheet->setCellValue('B'.$counter,htmlentities($Record->org_name));
                    $sheet->setCellValue('C'.$counter,htmlentities($Record->user_role_name));
                    $counter++;
                }

                $counter2 = $completeCounter;
                $counter2++;

                $criteria2 = new TActiveRecordCriteria();
    		$criteria2->Condition = "idtm_activity = :suchtext";
    		$criteria2->Parameters[':suchtext'] = $idtm_termin;

                $Records2 = ActivityParticipantsView::finder()->findAll($criteria2);

                foreach($Records2 AS $Record){
                    $sheet->setCellValue('E'.$counter2,htmlentities($Record->org_name));
                    $sheet->setCellValue('F'.$counter2,htmlentities($Record->act_part_notiz));
                    $sheet->setCellValue('G'.$counter2,htmlentities($Record->act_part_anwesend));
                    $counter2++;
                }

                $counter>=$counter2?$completeCounter=$counter:$completeCounter=$counter2;

                $completeCounter++;
                $counter3 = $completeCounter;
                $counter3++;

                $this->workbook->getActiveSheet()->getStyle('B'.$completeCounter)->getAlignment()->setWrapText(true);
                $this->workbook->getActiveSheet()->getStyle('B'.$completeCounter)->getAlignment()->setShrinkToFit(true);
                $this->workbook->getActiveSheet()->getStyle('B'.$completeCounter)->getFont()->setSize(12);
                $this->workbook->getActiveSheet()->getStyle('B'.$completeCounter)->getFont()->getColor()->setRGB('232323');
                

                $SQL = "SELECT a.* FROM `vv_protokoll_detail_aufgabe` a INNER JOIN ta_protokoll_detail_group b ON a.idta_protokoll_detail_group = b.idta_protokoll_detail_group";
		$SQL .= " WHERE idtm_protokoll = ". $idtm_protokoll;
		$Records3=ProtokollDetailAufgabeView::finder()->findAllBySQL($SQL);


                foreach($Records3 AS $Record){
                    $sheet->setCellValue('B'.$counter3,htmlentities($Record->idta_protokoll_detail_group));
                    $sheet->mergeCells('C'.$counter3.':D'.$counter3);
                    $sheet->setCellValue('C'.$counter3,htmlentities($Record->prtdet_cdate));
                    $sheet->setCellValue('E'.$counter3,htmlentities($Record->prtdet_topic));
                    $this->workbook->getActiveSheet()->getStyle('B'.$counter3)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $this->workbook->getActiveSheet()->getStyle('B'.$counter3)->getFill()->getStartColor()->setRGB('A9AB78');
                    $this->workbook->getActiveSheet()->duplicateStyle( $this->workbook->getActiveSheet()->getStyle('B'.$counter3), 'B'.$counter3.':G'.$counter3 );
                    $counter3++;
                    $sheet->mergeCells('B'.$counter3.':G'.$counter3);
                    $sheet->setCellValue('B'.$counter3,utf8_decode(strip_tags($Record->prtdet_descr)));
                    $this->workbook->getActiveSheet()->duplicateStyle( $this->workbook->getActiveSheet()->getStyle('B'.$completeCounter), 'B'.$counter3.':G'.$counter3 );
                    $counter3++;
                }
                

                $this->generate('Excel2007','risklogiq2009');
    }

    public function generate($format = "Excel5", $docName = "Tabelle"){
        switch($format){
            case 'Excel2007' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/Excel2007.php';
                $writer = new PHPExcel_Writer_Excel2007($this->workbook);
                $ext  = 'xlsx';
                $header = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                //supprime le pr�-calcul
                $writer->setPreCalculateFormulas(false);
                break;
             case 'Excel2003' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/Excel2007.php';
                $writer = new PHPExcel_Writer_Excel2007($this->workbook);
                $writer->setOffice2003Compatibility(true);
                $ext  = 'xlsx';
                //supprime le pr�-calcul
                $writer->setPreCalculateFormulas(false);
                break;
            case 'Excel5' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/Excel5.php';
                $writer = new PHPExcel_Writer_Excel5($this->workbook);
                $ext = 'xls';
                break;
            case 'CSV' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/CSV.php';
                $writer  = new PHPExcel_Writer_CSV($this->workbook);
                $writer->setDelimiter(",");//l'op�rateur de s�paration est la virgule
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'csv';
                break;
            case 'PDF' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/PDF.php';
                $writer  = new PHPExcel_Writer_PDF($this->workbook);
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'pdf';
                break;
            case 'HTML' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/HTML.php';
                $writer  = new PHPExcel_Writer_HTML($this->workbook);
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'html';
                break;

        }

        $this->getResponse()->appendHeader("Content-Type:".$header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$docName.'.'.$ext);

        $writer->save('php://output');
        exit;
      }

}

?>