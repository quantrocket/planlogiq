<?php

Prado::using('Application.3rdParty.wikiParser.WikiParser');

class ProtokollRenderer extends TDataListItemRenderer{

    public function initBrowser(){
        $this->DMSFileBrowser->loadDirectory();
    }

    public function initComments(){
        $this->KommentarContainerNOP->initParameters();
        $this->KommentarContainerNOP->bindListComments();
        $this->KommentarContainerNOP->__destruct();
    }

    public function initPrtAufgaben(){
        $this->prtAufgabenContainer->initParameters();
        $this->prtAufgabenContainer->bindListPrtAufgaben();
        $this->prtAufgabenContainer->__destruct();
    }

   function wiki2html($text){
        $myWikiParser = new WikiParser();
        $text = $myWikiParser->parse($text);
        return $text;
    }

     public function PFMailSend($sender,$param){
        $Aufgabe = ProtokollDetailAufgabeView::finder()->findByidtm_protokoll_detail($param->CallbackParameter);
        $MailEmpfaenger = OrganisationRecord::finder()->findByPk($Aufgabe->idtm_organisation);
        $mail = new PHPMailer();
        $mail->From = "info@planlogiq.com";
        $mail->FromName = "planlogIQ";
        $mail->Host = "smtp.1und1.de";
        $mail->Mailer = "smtp";
        $mail->SMTPAuth = true;
        $mail->IsSendmail(); //nur bei 1und1
        $mail->Username = "info@planlogiq.com";
        $mail->Password = "";
        $mail->AddAddress(KommunikationRecord::finder()->find('idtm_organisation=? AND kom_ismain=1 AND kom_type = 3',$Aufgabe->idtm_organisation)->kom_information,$MailEmpfaenger->org_name);
        $mail->Subject = $Aufgabe->prtdet_topic;
        $HTMLMessage ="Sehr geehrte(r) Frau/Herr ".$MailEmpfaenger->org_name.",<br/><br/>";
        $HTMLMessage.="die folgende Aufgabe wurde Ihnen zugeordnet:<br/>";
        $HTMLMessage.="<p><table><tr><td bgcolor='#efefef'>TOP Nr.:</td><td>".$Aufgabe->idtm_protokoll_detail_group." </td><td bgcolor='#efefef'>Thema: </td><td>".utf8_decode($Aufgabe->prtdet_topic)."</td></tr>";
        $HTMLMessage.="<tr><td bgcolor='#efefef'>Bis:</td><td colspan='3'><b>".$Aufgabe->auf_tdate."</b></td></tr></table>";
        $HTMLMessage.="<p style='background-color:#efefef'><hr/>".utf8_decode($Aufgabe->prtdet_descr)."</p>";
        $HTMLMessage.="<p style='background-color:#efefef'><hr/>".utf8_decode($Aufgabe->auf_beschreibung)."<hr/></p>";
        $HTMLMessage.="Bitte nicht Antworten! Diese Mail wurde automatisch generiert...";
        $mail->MsgHTML($HTMLMessage);
        if(!$mail->Send()){
                $this->PFMAILER->TEXT = "error sending the message";
        }else{
                $this->PFMAILER->TEXT = "..done..";
        }
    }
	
}
?>