<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class PFExcel extends PHPExcel{

    public function __construct(){
        parent::__construct();
    }

    public function generate($format = "Excel5", $docName = "Tabelle"){
        switch($format){
            case 'Excel2007' :
                include dirname(__FILE__).'/../3rdParty/Classes/PHPExcel/Writer/Excel2007.php';
                $writer = new PHPExcel_Writer_Excel2007($this);
                $ext  = 'xlsx';
                $header = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                //supprime le pre-calcul
                $writer->setPreCalculateFormulas(false);
                break;
             case 'Excel2003' :
                include dirname(__FILE__).'/../3rdParty/Classes/PHPExcel/Writer/Excel2007.php';
                $writer = new PHPExcel_Writer_Excel2007($this);
                $writer->setOffice2003Compatibility(true);
                $ext  = 'xlsx';
                //supprime le pre-calcul
                $writer->setPreCalculateFormulas(false);
                break;
            case 'Excel5' :
                include dirname(__FILE__).'/../3rdParty/Classes/PHPExcel/Writer/Excel5.php';
                $writer = new PHPExcel_Writer_Excel5($this);
                $ext = 'xls';
                break;
            case 'CSV' :
                include dirname(__FILE__).'/../3rdParty/Classes/PHPExcel/Writer/CSV.php';
                $writer  = new PHPExcel_Writer_CSV($this);
                $writer->setDelimiter(",");//l'op�rateur de s�paration est la virgule
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'csv';
                break;
            case 'PDF' :
                include dirname(__FILE__).'/../3rdParty/Classes/PHPExcel/Writer/PDF.php';
                $writer  = new PHPExcel_Writer_PDF($this);
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'pdf';
                break;
            case 'HTML' :
                include dirname(__FILE__).'/../3rdParty/Classes/PHPExcel/Writer/HTML.php';
                $writer  = new PHPExcel_Writer_HTML($this);
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'html';
                break;

        }
        header('Content-type:'.$header);
        header('Content-Disposition:inline;filename='.$docName.'.'.$ext);
        $writer->save('php://output');
      }

}


?>
