<?php
Prado::using('Application.3rdParty.wikiParser.WikiParser');
Prado::using('Application.fpdf.fpdf');
Prado::using('Application.fpdf.htmltoolkit');
Prado::using('Application.fpdf.html2fpdf');

class PDF_001_KP_Protokoll_Standard extends TPage {

    private $ext = 'pdf';
    private $docName = 'Protokoll';
    private $header = 'application/pdf';

    private $pdf; //the pdf object
    private $bi = 1; //bold and italic support
    private $debug = 0; //if the app is debugged

    private $B;
    private $I;
    private $U;
    private $HREF;
    private $PRE;

    public $InternalCounter = 0;

    public function onPreInit($param){

        //emnitialisation
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';

        date_default_timezone_set('Europe/Berlin');

        $this->pdf=new HTML2FPDF('P','mm','A4');
        $this->pdf->SetAutoPageBreak(true); 
        $this->pdf->AddPage();

        $this->Header();
        $this->FaltMarken();
        $this->ProtokollHeader($_GET['idtm_protokoll']);
        $this->LetterContent($_GET['idtm_protokoll']);

        $this->pdf->Output();

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$this->docName.'.'.$this->ext);

        $writer->save('php://output');
        exit;
      }

   private function LetterContent($idtm_protokoll){
        $SQL = "SELECT idtm_protokoll_detail_group FROM ta_protokoll_detail_group WHERE idtm_protokoll = ". $idtm_protokoll;
        $execSQL = "SELECT * FROM vv_protokoll_detail WHERE idtm_protokoll_detail_group IN (".$SQL.") ORDER BY idtm_protokoll_detail DESC";

        $ProtokollDetails = ProtokollDetailView::finder()->findAllBySQL($execSQL);

        $this->pdf->Ln(10);
        $this->pdf->SetX(25);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Helvetica','',12);
        $this->pdf->Cell(185,6,strtoupper('Protokoll'),0,1);
        $this->InternalCounter++;
        
        foreach($ProtokollDetails AS $ProtokollDetail){

            $this->pdf->Ln(7);
            $this->pdf->SetX(25);
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->SetFillColor(116,145,97);
            $this->pdf->SetDrawColor(116,145,97);
            $this->pdf->SetTextColor(0,0,0);
            $this->pdf->Cell(150,4,$this->InternalCounter.' \\\\ '.utf8_decode($ProtokollDetail->prtdet_topic),'B',1);
            $this->InternalCounter++;
            $this->pdf->SetLeftMargin(25);
            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetTextColor(0,0,0);
            $myHTML = $this->wiki2html($ProtokollDetail->prtdet_descr);
            $this->pdf->WriteHTML(utf8_decode($myHTML));
            if($ProtokollDetail->idta_protokoll_ergebnistype < 3){

                $SubSQL = "SELECT * FROM tm_aufgaben WHERE auf_tabelle = 'tm_protokoll_detail' AND auf_id = ".$ProtokollDetail->idtm_protokoll_detail ." AND auf_deleted = 0";
                $PrtSubAufagben = AufgabenRecord::finder()->findAllBySQL($SubSQL);
               
                foreach($PrtSubAufagben as $PrtAufgabe){

                    $this->pdf->SetFont('Arial','',10);
                    $this->pdf->Cell(5,5,'\\\\',0,0);
                    $this->pdf->SetTextColor(217,0,0);
                    $this->pdf->Cell(145,5,' Auftrag',0,1);

                    $this->pdf->SetFillColor(255,255,255);
                    $this->pdf->SetFont('Arial','',10);
                    $this->pdf->SetTextColor(43,51,43);
                    $this->pdf->MultiCell(150,6,utf8_decode($PrtAufgabe->auf_beschreibung),0,1);

                    $this->pdf->SetDrawColor(217,0,0);
                    $this->pdf->Cell(90,5,' Wer','B',0);
                    $this->pdf->Cell(30,5,' Bis','B',0);
                    $this->pdf->Cell(30,5,' Erledigt','B',1);

                    $this->pdf->Cell(90,8," ".utf8_decode(OrganisationRecord::finder()->findByPk($PrtAufgabe->idtm_organisation)->org_name),0,0);
                    $this->pdf->Cell(30,8,$PrtAufgabe->auf_tdate,0,0);
                    $this->pdf->Cell(30,8,$PrtAufgabe->auf_done==1?$PrtAufgabe->auf_ddate:'-',0,1);

                }
                    
                $SubSQL = "";
            }
               
        }
   }
       
   private function Header(){
	//Title
//	$this->pdf->Cell(30,10,'planlogIQ - Biberstrasse 8 - 1010 Wien',0,0,'R');
//	$this->pdf->Cell(120);
	//Logo
        $this->pdf->image('../rliq/themes/basic/gfx/logo_kp_neu.jpg',110,8,66);
	//Move to the right
	//Line break
        $this->pdf->SetXY(127, 20);
        $this->pdf->SetTextColor(43,51,43);
        $this->pdf->SetFont('Helvetica','',8);
        $this->pdf->Cell(60,4,'Graf Moser Management GmbH',0,0,'',0);
        $this->pdf->Ln();$this->pdf->SetX(127);
        $this->pdf->Cell(60,4,'Herrengasse 5/2/7 | 1010 Wien',0,0,'',0);
        $this->pdf->Ln();$this->pdf->SetX(127);
        $this->pdf->Cell(60,4,'T: +43-(0)1-513 02 50 | F: +43-(0)1-513 02 50-20',0,0,'',0);
        $this->pdf->Ln();$this->pdf->SetX(127);
        $this->pdf->Cell(60,4,'www.kulturplanner.com',0,0,'',0);
    }

    private function ProtokollHeader($idtm_protokoll){
        $Protokoll = ProtokollRecord::finder()->findBy_idtm_protokoll($idtm_protokoll);
        
        $this->pdf->SetXY(25,60);
        $this->pdf->SetDrawColor(43,51,43);
        $this->pdf->SetFont('Helvetica','',10);
        $this->pdf->Cell(185,5,strtoupper('Protokollnotiz zum Termin am '.date('j.n.Y',strtotime($Protokoll->prt_cdate))),0,0,'B',0);
        $this->pdf->Ln();
        $this->pdf->SetX(25);
        $this->pdf->SetDrawColor(43,51,43);
        $this->pdf->SetFont('Helvetica','',10);
        $this->pdf->Cell(185,5,strtoupper('Projekt: '.utf8_decode($Protokoll->idtm_termin)),0,0,'B',0);
        $this->pdf->Ln();
        
        //$this->pdf->SetX(28);
        $this->pdf->SetXY(25,105);
        $this->pdf->SetDrawColor(43,51,43);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(50,5,'Thema ','B');
        $this->pdf->Cell(100,5,utf8_decode($Protokoll->prt_name),'B',1);
        $this->pdf->SetX(25);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(50,5,'Moderator ','B');
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(100,5,utf8_decode(OrganisationRecord::finder()->findByidtm_organisation($Protokoll->idtm_organisation)->org_name),'B',1);
        $this->pdf->SetX(25);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(50,5,'Typ ','B');
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(100,5,utf8_decode(ProtokollTypeRecord::finder()->findByPK($Protokoll->idta_protokoll_type)->prt_type_name),'B',1);
        $this->pdf->SetX(25);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(50,5,'Ort ','B');
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(100,5,utf8_decode($Protokoll->prt_location),'B',1);
        $this->pdf->SetX(25);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(50,5,'Datum ','B');
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(100,5,utf8_decode($Protokoll->prt_cdate),'B',1);
        $this->pdf->Ln();

        $this->pdf->SetX(25);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(50,5,'Eingeladen ','B',0);
        $this->pdf->SetFont('Arial','',10);

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext";
        $criteria->Parameters[':suchtext'] = $Protokoll->idtm_termin;
        $criteria->OrdersBy["idtm_organisation"] = 'asc';
        $AnwesendPP=ActivityParticipantsView::finder()->findAll($criteria);

        $i=0;

        foreach($AnwesendPP as $InvitedP){
            if($i>0){
                $this->pdf->SetX(25);
                $this->pdf->SetFont('Arial','',10);
                $this->pdf->Cell(50,5,' ','B');
            }
            $this->pdf->Cell(50,5,utf8_decode(OrganisationRecord::finder()->findByPK($InvitedP->idtm_organisation)->org_vorname).' '.utf8_decode($InvitedP->org_name),'B',0);
            $this->pdf->Cell(50,5,$InvitedP->act_part_anwesend==1?'anwesend':'','B',1);
            $i++;
        }

        $this->pdf->Ln();

    }

    private function FaltMarken(){
        $this->pdf->SetXY(1, 100); //105
        $this->pdf->Cell(6,0,'','B');
    }

     function wiki2html($text){
        $myWikiParser = new WikiParser();
        $text = $myWikiParser->parse($text);
        return $text;
    }

}
?>