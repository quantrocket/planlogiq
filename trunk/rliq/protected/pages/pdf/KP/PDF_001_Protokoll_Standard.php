<?php

Prado::using('Application.fpdf.fpdf');
Prado::using('Application.fpdf.htmltoolkit');
Prado::using('Application.fpdf.html2fpdf');

class PDF_001_Protokoll_Standard extends TPage {

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
        $SQL = "SELECT a.* FROM `vv_protokoll_detail_aufgabe` a";
        $SQL .= " INNER JOIN ta_protokoll_detail_group b ON a.idtm_protokoll_detail_group = b.idtm_protokoll_detail_group";
        $SQL .= " WHERE a.idtm_protokoll = ". $idtm_protokoll;
        $SQL .= " ORDER BY idtm_protokoll_detail_group, idtm_protokoll_detail ";
        $ProtokollDetails=ProtokollDetailAufgabeView::finder()->findAllBySQL($SQL);

        $this->pdf->Ln(10);
        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->Cell(185,6,'Protokoll ',1,0,'B',1);
        $this->InternalCounter++;
        
        foreach($ProtokollDetails AS $ProtokollDetail){
            $this->pdf->Ln(7);
            //$this->pdf->SetX(20);
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->SetFillColor(116,145,97);
            $this->pdf->SetDrawColor(116,145,97);
            $this->pdf->Cell(15,6,$this->InternalCounter,1,0,'C',1);
            $this->InternalCounter++;
            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetDrawColor(234,242,220);
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->Cell(5,6,' ',1,0,'',1);
            $this->pdf->Cell(165,6,utf8_decode($ProtokollDetail->prtdet_topic),1,0,'',1);
            $this->pdf->SetFont('Arial','',11);
            $this->pdf->Ln();
            //$this->pdf->SetX(28);
            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->WriteHTML($ProtokollDetail->prtdet_descr);
            if($ProtokollDetail->idta_protokoll_ergebnistype < 3){
             //   $this->pdf->Ln(7);
                //$this->pdf->SetX(20);
                $this->pdf->SetFillColor(255,165,0);
                $this->pdf->SetDrawColor(255,165,0);
                $this->pdf->SetFont('Arial','B',8);
                
                $this->pdf->Cell(170,5,'Auftrag',1,0,'',1);
                $this->pdf->Ln();
                //$this->pdf->SetX(20);
                
                $this->pdf->SetFillColor(255,255,255);
                $this->pdf->SetFont('Arial','',9);
                $this->pdf->MultiCell(170,6,utf8_decode($ProtokollDetail->auf_beschreibung),1,'L',0);
              
                $this->pdf->SetFillColor(255,255,255);
                $this->pdf->Cell(110,5,' Wer',1,0,'',1);
                $this->pdf->Cell(30,5,' Bis',1,0,'',1);
                $this->pdf->Cell(30,5,' Erledigt',1,0,'',1);
                $this->pdf->Ln();
       
                $this->pdf->Cell(110,6," ".utf8_decode(OrganisationRecord::finder()->findByPk($ProtokollDetail->idtm_organisation)->org_name),1,'');
                $this->pdf->Cell(30,6,$ProtokollDetail->auf_tdate,1,'L',0);
                $this->pdf->Cell(30,6,$ProtokollDetail->auf_done==1?$ProtokollDetail->auf_ddate:'-',1,'L',0);
                $this->pdf->Ln(7);
            }
            
             
        }
   }
       
   private function Header(){
	//Title
//	$this->pdf->Cell(30,10,'planlogIQ - Biberstrasse 8 - 1010 Wien',0,0,'R');
//	$this->pdf->Cell(120);
	//Logo
        $this->pdf->image('../rliq/themes/basic/gfx/logo.jpg',140,8,50);
	//Arial bold 15
	$this->pdf->SetFont('Arial','',8);
	//Move to the right
	//Line break
	$this->pdf->Ln(20);
    }

    private function ProtokollHeader($idtm_protokoll){
        $Protokoll = ProtokollRecord::finder()->findBy_idtm_protokoll($idtm_protokoll);
        
        $this->pdf->SetY(38);
        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Helvetica','B',10);
        $this->pdf->Cell(185,6,'\\\\ Thema - '.utf8_decode($Protokoll->prt_name),1,0,'B',1);
        $this->pdf->Ln(7);
        //$this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(30,5,'Moderator: ',0);
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(60,5,utf8_decode(OrganisationRecord::finder()->findByidtm_organisation($Protokoll->idtm_organisation)->org_name),0);
        $this->pdf->Ln();
        //$this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(30,5,'Typ: ',0);
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(60,5,utf8_decode(ProtokollTypeRecord::finder()->findByPK($Protokoll->idta_protokoll_type)->prt_type_name),0);
        $this->pdf->Ln();
        //$this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(30,5,'Ort: ',0);
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(60,5,utf8_decode($Protokoll->prt_location),0);
        $this->pdf->Ln();
        //$this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(30,5,'Datum: ',0);
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(60,5,utf8_decode($Protokoll->prt_cdate),0);
        $this->pdf->Ln(7);

        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Helvetica','B',10);
        $this->pdf->Cell(185,6,'\\\\ Eingeladen ',1,0,'B',1);
        $this->pdf->Ln(7);

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_termin = :suchtext";
        $criteria->Parameters[':suchtext'] = $Protokoll->idtm_termin;
        $criteria->OrdersBy["idtm_organisation"] = 'asc';
        $InvitedPP=TerminOrganisationView::finder()->findAll($criteria);
        
        foreach($InvitedPP as $InvitedP){
            $this->pdf->SetX(20);
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->Cell(100,5,utf8_decode(OrganisationRecord::finder()->findByPK($InvitedP->idtm_organisation)->org_vorname).' '.utf8_decode($InvitedP->org_name),0);
            $this->pdf->Ln();
        }

        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Helvetica','B',10);
        $this->pdf->Cell(185,6,'\\\\ Anwesend ',1,0,'B',1);
        $this->pdf->Ln(7);

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext";
        $criteria->Parameters[':suchtext'] = $Protokoll->idtm_termin;
        $criteria->OrdersBy["idtm_organisation"] = 'asc';
        $AnwesendPP=ActivityParticipantsView::finder()->findAll($criteria);

        foreach($AnwesendPP as $InvitedP){
            $this->pdf->SetX(20);
            $this->pdf->SetFont('Arial','I',10);
            $this->pdf->Cell(100,5,utf8_decode(OrganisationRecord::finder()->findByPK($InvitedP->idtm_organisation)->org_vorname).' '.utf8_decode($InvitedP->org_name),0);
            $this->pdf->Cell(60,5,utf8_decode($InvitedP->act_part_notiz),0);
            $this->pdf->Cell(10,5,$InvitedP->act_part_anwesend==1?'Ja':'Nein',0);
            $this->pdf->Ln();
        }
    }

    private function FaltMarken(){
        $this->pdf->SetXY(1, 105);
        $this->pdf->Cell(7,0,'','B');
        $this->pdf->SetXY(4, 165);
        $this->pdf->Cell(5,0,'','B');
        $this->pdf->SetXY(1, 220);
        $this->pdf->Cell(7,0,'','B');
    }

    private function ContentSubject($idtm_aufgaben){

    }

}
?>