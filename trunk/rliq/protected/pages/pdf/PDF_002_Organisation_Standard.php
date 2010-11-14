<?php

Prado::using('Application.fpdf.fpdf');
Prado::using('Application.fpdf.htmltoolkit');
Prado::using('Application.fpdf.html2fpdf');

class PDF_002_Organisation_Standard extends TPage {

    private $ext = 'pdf';
    private $docName = 'Steckbrief_Planlogiq';
    private $header = 'application/pdf';

    private $pdf; //the pdf object
    private $bi = 1; //bold and italic support
    private $debug = 0; //if the app is debugged

    private $B;
    private $I;
    private $U;
    private $HREF;
    private $PRE;

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
        $this->ProtokollHeader($_GET['idtm_organisation']);
        $this->LetterContent($_GET['idtm_organisation']);

        $this->pdf->Output();

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$this->docName.'.'.$this->ext);

        $writer->save('php://output');
        exit;
      }

   private function LetterContent($idtm_organisation){
        $SQL = "SELECT * FROM `vv_aufgaben` ";
        $SQL .= " WHERE idtm_organisation = ". $idtm_organisation;
        $SQL .= " OR (auf_tabelle = 'tm_organisation' AND auf_id = ". $idtm_organisation .")";
        $SQL .= " ORDER BY auf_done ASC, auf_tdate ASC";
        $AufgabenDetails=AufgabenView::finder()->findAllBySQL($SQL);

        $this->pdf->Ln(10);
        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->Cell(185,6,utf8_decode('Aktivitäten: '),1,0,'B',1);
        $this->pdf->Ln(7);

        foreach($AufgabenDetails AS $AufgabenDetail){
            //$this->pdf->SetX(20);
            $this->pdf->SetFont('Arial','',9);
            $this->pdf->SetFillColor(116,145,97);
            $this->pdf->SetDrawColor(116,145,97);
            $this->pdf->Cell(15,6,$AufgabenDetail->idtm_aufgaben,1,0,'C',1);
            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->SetDrawColor(234,242,220);
            $this->pdf->Cell(170,6,utf8_decode($AufgabenDetail->auf_name),1,0,'',1);
            $this->pdf->Ln(7);
            //$this->pdf->SetX(28);
            $this->pdf->SetFillColor(255,255,255);
            $this->pdf->WriteHTML($AufgabenDetail->auf_beschreibung);
            $this->pdf->Ln(8);
            //$this->pdf->SetX(20);
            //$this->pdf->SetX(20);
                
            $this->pdf->Cell(125,5,'Wer',1,0,'',1);
            $this->pdf->Cell(30,5,'Bis',1,0,'',1);
            $this->pdf->Cell(30,5,'Erledigt',1,0,'',1);
            $this->pdf->Ln();
            
            $this->pdf->Cell(125,6,utf8_decode($AufgabenDetail->org_responsible),1,'');
            $this->pdf->Cell(30,6,$AufgabenDetail->auf_tdate,1,'L',0);
            $this->pdf->Cell(30,6,$AufgabenDetail->auf_done==1?$AufgabenDetail->auf_ddate:'-',1,'L',0);
            
            $this->pdf->Ln(8);
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

    private function ProtokollHeader($idtm_organisation){
        $komType = array(1=>'Telefon','Fax','Mail');
        $Organisation = OrganisationRecord::finder()->findBy_idtm_organisation($idtm_organisation);
        
        //Criteria for selection in joins
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_organisation = :suchtext";
        $criteria->Parameters[':suchtext'] = $idtm_organisation;

        $this->pdf->SetY(38);
        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Helvetica','B',12);
        $this->pdf->Cell(185,6,utf8_decode($Organisation->org_name).' '.utf8_decode($Organisation->org_vorname),1,0,'B',1);
        $this->pdf->Ln(7);
        //$this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','',10);
        $this->pdf->Cell(30,5,'Matchcode: ',0);
        $this->pdf->SetFont('Arial','B',10);
        $this->pdf->Cell(60,5,utf8_decode($Organisation->org_matchkey),0);
        $this->pdf->Ln();

        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->Cell(185,6,'Adressen ',1,0,'B',1);
        $this->pdf->Ln(7);

        $Adressen = OrganisationAdresseRecord::finder()->findAll($criteria);
        
        foreach($Adressen as $Adresse){
            $Adr = AdresseRecord::finder()->findByidta_adresse($Adresse->idta_adresse);
            $this->pdf->SetX(20);
            $this->pdf->SetFont('Arial','',10);
            $this->pdf->Cell(20,5,utf8_decode($Adr->adresse_zip),0);
            $this->pdf->SetFont('Arial','',10);
            $this->pdf->Cell(60,5,utf8_decode($Adr->adresse_town),0);
            $this->pdf->SetFont('Arial','',10);
            $this->pdf->Cell(60,5,utf8_decode($Adr->adresse_street),0);
            $this->pdf->Ln();
            unset($Adr);
        }
        //$this->pdf->SetX(20);
        $this->pdf->SetFillColor(234,242,220);
        $this->pdf->SetDrawColor(234,242,220);
        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->Cell(185,6,'Kommunikation ',1,0,'B',1);
        $this->pdf->Ln(7);
        
        $criteria->OrdersBy["idtm_organisation"] = 'asc';
        $Kommunikation=KommunikationRecord::finder()->findAll($criteria);
        
        foreach($Kommunikation as $Komm){            
            $this->pdf->SetX(20);
            $this->pdf->SetFont('Arial','',10);
            $this->pdf->Cell(30,5,utf8_decode($komType[$Komm->kom_type]),0);
            $this->pdf->SetFont('Arial','B',10);
            $this->pdf->Cell(60,5,utf8_decode($Komm->kom_information),0);
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

function WriteHTML($html,$bi=1)
    {
        //remove all unsupported tags
        $this->bi=$bi;
        $html = htmlspecialchars_decode($html);
        if ($bi)
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr><b><i><u><strong><em>");
        else
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr>");
        $html=str_replace("\n",' ',$html); //replace carriage returns by spaces
        
        $html = str_replace('&trade;','™',$html);
        $html = str_replace('&copy;','©',$html);
        $html = str_replace('&euro;','€',$html);
        $html = str_replace('&nbsp;',' ',$html);
        $html = str_replace('&ouml;','ö',$html);
        $html = str_replace('&auml;','ä',$html);
        $html = str_replace('&uuml;','ü',$html);
        $html = str_replace('&Ouml;','Ö',$html);
        $html = str_replace('&Auml;','Ä',$html);
        $html = str_replace('&Uuml;','Ü',$html);

        // debug
        if ($this->debug) { echo utf8_decode($html); exit; }

        $html = utf8_decode($html);

        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        $skip=false;
        foreach($a as $i=>$e)
        {
            if (!$skip) {
                if($this->HREF)
                    $e=str_replace("\n","",str_replace("\r","",$e));
                if($i%2==0)
                {
                    // new line
                    if($this->PRE)
                        $e=str_replace("\r","\n",$e);
                    else
                        $e=str_replace("\r","",$e);
                    //Text
                    if($this->HREF) {
                        $this->PutLink($this->HREF,$e);
                        $skip=true;
                    } else{
                        $this->pdf->Write(5,htmlspecialchars_decode($e));
                    }
                } else {
                    //Tag
                    if (substr(trim($e),0,1)=='/')
                        $this->CloseTag(strtoupper(substr($e,strpos($e,'/'))));
                    else {
                        //Extract attributes
                        $a2=explode(' ',$e);
                        $tag=strtoupper(array_shift($a2));
                        $attr=array();
                        foreach($a2 as $v) {
                            if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                                $attr[strtoupper($a3[1])]=$a3[2];
                        }
                        $this->OpenTag($tag,$attr);
                    }
                }
            } else {
                $this->HREF='';
                $skip=false;
            }
        }
        $this->pdf->Ln(5);
    }

    function OpenTag($tag,$attr)
    {
        //Opening tag
        switch($tag){
            case 'STRONG':
            case 'B':
                if ($this->bi)
                    $this->SetStyle('B',true);
                else
                    $this->SetStyle('U',true);
                break;
            case 'H1':
                $this->pdf->Ln(5);
                $this->pdf->SetTextColor(150,0,0);
                $this->pdf->SetFontSize(22);
                break;
            case 'H2':
                $this->pdf->Ln(5);
                $this->pdf->SetFontSize(18);
                $this->SetStyle('U',true);
                break;
            case 'H3':
                $this->pdf->Ln(5);
                $this->pdf->SetFontSize(16);
                $this->SetStyle('U',true);
                break;
            case 'H4':
                $this->pdf->Ln(5);
                $this->pdf->SetTextColor(102,0,0);
                $this->pdf->SetFontSize(14);
                if ($this->bi)
                    $this->SetStyle('B',true);
                break;
            case 'PRE':
                $this->pdf->SetFont('Courier','',11);
                $this->pdf->SetFontSize(11);
                $this->SetStyle('B',false);
                $this->SetStyle('I',false);
                $this->PRE=true;
                break;
            case 'RED':
                $this->pdf->SetTextColor(255,0,0);
                break;
            case 'BLOCKQUOTE':
                $this->mySetTextColor(100,0,45);
                $this->pdf->Ln(3);
                break;
            case 'BLUE':
                $this->pdf->SetTextColor(0,0,255);
                break;
            case 'I':
            case 'EM':
                if ($this->bi)
                    $this->SetStyle('I',true);
                break;
            case 'U':
                $this->SetStyle('U',true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if(!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->pdf->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                    $this->pdf->Ln(3);
                }
                break;
            case 'LI':
                $this->pdf->Ln(4);
                //$this->pdf->setX('28');
                $this->pdf->SetTextColor(190,0,0);
                $this->pdf->Write(5,'>> ');
                $this->mySetTextColor(-1);
                break;
            case 'TR':
                $this->pdf->Ln(7);
                $this->PutLine();
                break;
            case 'BR':
                $this->pdf->Ln(2);
                break;
            case 'P':
                $this->pdf->Ln(6);
                break;
            case 'HR':
                $this->PutLine();
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                    $coul=hex2dec($attr['COLOR']);
                    $this->mySetTextColor($coul['R'],$coul['G'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->pdf->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag=='H1' || $tag=='H2' || $tag=='H3' || $tag=='H4'){
            $this->pdf->Ln(6);
            $this->pdf->SetFont('Times','',12);
            $this->pdf->SetFontSize(12);
            $this->pdf->SetStyle('U',false);
            $this->pdf->SetStyle('B',false);
            $this->mySetTextColor(-1);
        }
        if ($tag=='PRE'){
            $this->pdf->SetFont('Times','',12);
            $this->pdf->SetFontSize(12);
            $this->PRE=false;
        }
        if ($tag=='RED' || $tag=='BLUE')
            $this->mySetTextColor(-1);
        if ($tag=='BLOCKQUOTE'){
            $this->mySetTextColor(0,0,0);
            $this->pdf->Ln(3);
        }
        if($tag=='STRONG')
            $tag='B';
        if($tag=='EM')
            $tag='I';
        if((!$this->bi) && $tag=='B')
            $tag='U';
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->pdf->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='FONT'){
            if ($this->issetcolor==true) {
                $this->pdf->SetTextColor(0,0,0);
            }
            if ($this->issetfont) {
                $this->pdf->SetFont('Arial','',12);
                $this->issetfont=false;
            }
        }
    }

    function SetStyle($tag,$enable)
    {
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s) {
            if($this->$s>0)
                $style.=$s;
        }
        $this->pdf->SetFont('',$style);
    }

    function PutLink($URL,$txt)
    {
        //Put a hyperlink
        $this->pdf->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->pdf->Write(6,htmlspecialchars_decode($txt),$URL);
        $this->SetStyle('U',false);
        $this->mySetTextColor(-1);
    }

    function PutLine()
    {
        $this->pdf->Ln(2);
        $this->pdf->Line($this->pdf->GetX(),$this->pdf->GetY(),$this->pdf->GetX()+187,$this->pdf->GetY());
        $this->pdf->Ln(3);
    }

    function mySetTextColor($r,$g=0,$b=0){
        static $_r=0, $_g=0, $_b=0;

        if ($r==-1)
            $this->pdf->SetTextColor($_r,$_g,$_b);
        else {
            $this->pdf->SetTextColor($r,$g,$b);
            $_r=$r;
            $_g=$g;
            $_b=$b;
        }
    }

    function PutMainTitle($title) {
        if (strlen($title)>55)
            $title=substr($title,0,55)."...";
        $this->pdf->SetTextColor(33,32,95);
        $this->pdf->SetFontSize(20);
        $this->pdf->SetFillColor(255,204,120);
        $this->pdf->Cell(0,20,$title,1,1,"C",1);
        $this->pdf->SetFillColor(255,255,255);
        $this->pdf->SetFontSize(12);
        $this->pdf->Ln(5);
    }

    function PutMinorHeading($title) {
        $this->pdf->SetFontSize(12);
        $this->pdf->Cell(0,5,$title,0,1,"C");
        $this->pdf->SetFontSize(12);
    }

    function PutMinorTitle($title,$url='') {
        $title=str_replace('http://','',$title);
        if (strlen($title)>70)
            if (!(strrpos($title,'/')==false))
                $title=substr($title,strrpos($title,'/')+1);
        $title=substr($title,0,70);
        $this->pdf->SetFontSize(16);
        if ($url!='') {
            $this->SetStyle('U',false);
            $this->pdf->SetTextColor(0,0,180);
            $this->pdf->Cell(0,6,$title,0,1,"C",0,$url);
            $this->pdf->SetTextColor(0,0,0);
            $this->SetStyle('U',false);
        } else
            $this->pdf->Cell(0,6,$title,0,1,"C",0);
        $this->pdf->SetFontSize(12);
        $this->pdf->Ln(4);
    }

}
?>