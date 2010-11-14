<?php

ini_set('allow_url_open',1);

Prado::using('Application.fpdf.fpdf');
Prado::using('Application.fpdf.htmltoolkit');
Prado::using('Application.fpdf.html2fpdf');

class PDF2HTML_Protokoll_Standard extends TPage {

    private $ext = 'pdf';
    private $docName = 'Test';
    private $header = 'application/pdf';
    
    public function onPreInit($param){

        $myPDF = new HTML2FPDF();
        $myPDF->setBasePath(Prado::getFrameworkPath());

        $url = "http://".$this->Application->Parameters['PDFHost']."/rliq/index.php?page=reports.protokoll.a_Protokoll&idtm_protokoll=2&idtm_termin=0";
        $html = "";
        
        $html = file_get_contents($url);
        $myPDF->WriteHTML($html);

        $myPDF->Output();

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$this->docName.'.'.$this->ext);

        $writer->save('php://output');
        exit;
      }

}
?>