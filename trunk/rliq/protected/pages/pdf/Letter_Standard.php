<?php

Prado::using('Application.fpdf.fpdf');

class Letter_Standard extends TPage {

    private $ext = 'pdf';
    private $docName = 'Test';
    private $header = 'application/pdf';

    private $pdf; //the pdf object

    private $B;
    private $I;
    private $U;
    private $HREF;

    public function onPreInit($param){

        //emnitialisation
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';

        date_default_timezone_set('Europe/Berlin');

        $this->pdf=new FPDF('P','mm','A4');
        $this->pdf->AddPage();

        $this->Header();
        $this->FaltMarken();
        $this->AdressHeader($_GET['idtm_organisation']);
        $this->ContentSubject($_GET['idtm_aufgaben']);
        $this->LetterContent($_GET['idtm_aufgaben']);

        $this->pdf->Ln(20);
        $this->pdf->setX(24);
        $this->pdf->Cell(120,10,'Mit freundlichen Gruessen',0,1);

        $this->pdf->Output();

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$this->docName.'.'.$this->ext);

        $writer->save('php://output');
        exit;
      }

   private function LetterContent($idtm_aufgaben){
       $Aufgabe = AufgabenRecord::finder()->findByidtm_aufgaben($idtm_aufgaben);
       $this->pdf->SetFont('Arial','',11);
       $this->pdf->setLeftMargin(24);
       $this->WriteHTML($Aufgabe->auf_beschreibung);
       //$this->pdf->MultiCell(160,6,utf8_decode($Aufgabe->auf_beschreibung),0,"L");
   }
       
   private function Header(){
	//Title
//	$this->pdf->Cell(30,10,'planlogIQ - Biberstrasse 8 - 1010 Wien',0,0,'R');
//	$this->pdf->Cell(120);
	//Logo
        $this->pdf->image('../rliq/themes/basic/gfx/logo.jpg',160,8,33);
	//Arial bold 15
	$this->pdf->SetFont('Arial','',8);
	//Move to the right
	//Line break
	$this->pdf->Ln(20);
    }

    private function AdressHeader($idtm_organisation){
        $Organisation = OrganisationRecord::finder()->findBy_idtm_organisation($idtm_organisation);
        
        $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$idtm_organisation." AND adresse_ismain = 1";
        $Adresse=AdresseRecord::finder()->findBySQL($sql);

        $this->pdf->SetY(48);
        $this->pdf->SetX(24);
        $this->pdf->SetFont('Arial','',7);
        $this->pdf->Cell(85,4,'Philipp Frenzel, Biberstrasse 8/23, 1010 Wien','B',1);
        $this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','B',12);
        $this->pdf->Cell(0,10,utf8_decode($Organisation->org_name));
        $this->pdf->Ln(5);
        $this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','',11);
        $this->pdf->Cell(0,10,utf8_decode($Adresse->adresse_street));
        $this->pdf->Ln(5);
        $this->pdf->SetX(28);
        $this->pdf->SetFont('Arial','',11);
        $this->pdf->Cell(0,10,utf8_decode($Adresse->adresse_zip)." ".utf8_decode($Adresse->adresse_town));
        $this->pdf->Ln(9);
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
        $Aufgabe = AufgabenRecord::finder()->findByidtm_aufgaben($idtm_aufgaben);
        $this->pdf->SetXY(135,102);
        $this->pdf->Cell(60,0,$Aufgabe->auf_cdate,0);
        $this->pdf->SetY(102);
        $this->pdf->SetX(24);
        $this->pdf->SetFont('Arial','B',12);
        $subject = 'Betreff: '.$Aufgabe->auf_name;
        $this->pdf->Cell(0,10,$subject);
        $this->pdf->Ln(15);
    }

function WriteHTML($html){
    //HTML parser
    //$html=str_replace("\n",' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else{
                $this->pdf->Write(5,html_entity_decode($e));
            }
        }
        else
        {
            //Tag
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag,$attr){
    //Opening tag
    if($tag=='STRONG' or $tag=='EM' or $tag=='U'){
        $reformat = array('STRONG'=>'B','EM'=>'I','U'=>'U');
        $this->SetStyle($reformat[$tag],true);
    }
    if($tag=='A')
        $this->HREF=$attr['HREF'];
    if($tag=='BR')
        $this->pdf->Ln(5);
}

function CloseTag($tag){
    //Closing tag
    if($tag=='STRONG' or $tag=='EM' or $tag=='U'){
        $reformat = array('STRONG'=>'B','EM'=>'I','U'=>'U');
        $this->SetStyle($reformat[$tag],false);
    }
    if($tag=='A')
        $this->HREF='';
}

function SetStyle($tag,$enable){
    //Modify style and select corresponding font
    $this->{$tag}+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->pdf->SetFont('',$style);
}

function PutLink($URL,$txt){
    //Put a hyperlink
    $this->pdf->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->pdf->Write(5,utf8_decode($txt),$URL);
    $this->SetStyle('U',false);
    $this->pdf->SetTextColor(0);
}

}
?>