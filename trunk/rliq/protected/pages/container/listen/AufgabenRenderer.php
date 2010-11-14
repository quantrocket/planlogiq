<?php

Prado::using('Application.3rdParty.wikiParser.WikiParser');

class AufgabenRenderer extends TDataListItemRenderer{

    public function initBrowser(){
        $this->DMSFileBrowser->loadDirectory();
    }

    public function initComments(){
        $this->KommentarContainerNOP->initParameters();
        $this->KommentarContainerNOP->bindListComments();
        $this->KommentarContainerNOP->__destruct();
    }

    function wiki2html($text){
        $myWikiParser = new WikiParser();
        $text = $myWikiParser->parse($text);
        return $text;
    }

    public function CreatePDFLink($idtm_aufgaben){
        $AufgabenRecord = AufgabenRecord::finder()->findByidtm_aufgaben($idtm_aufgaben);
        $parameter['idtm_aufgaben']=$AufgabenRecord->idtm_aufgaben;
        $parameter['idtm_organisation']=$AufgabenRecord->auf_id;
        $url = $this->getApplication()->getRequest()->constructUrl('page','pdf.Letter_Standard', $parameter);
        return $url;
    }

    public function PFMailSend($sender,$param){
        $Aufgabe = AufgabenRecord::finder()->findByPK($sender->CommandParameter);
        $mail = new PHPMailer();
        $mail->From = "info@planlogiq.com";
        $mail->FromName = "planlogIQ";
        $mail->Host = "smtp.1und1.de";
        $mail->Mailer = "smtp";
        $mail->SMTPAuth = true;
        $mail->IsSendmail(); //nur bei 1und1
        $mail->Username = "info@planlogiq.com";
        $mail->Password = "";
        $mail->AddAddress(KommunikationRecord::finder()->find('idtm_organisation=? AND kom_ismain=1 AND kom_type = 3',$Aufgabe->auf_idtm_organisation)->kom_information,OrganisationRecord::finder()->findByPK($Aufgabe->auf_idtm_organisation)->org_name);
        $mail->Subject = $Aufgabe->auf_name;
        $mail->MsgHTML($Aufgabe->auf_beschreibung);
        if(!$mail->Send()){
                $this->PFMAILER->TEXT = "There was an error sending the message";
        }else{
                $this->PFMAILER->TEXT = "..done..";
        }
    }

     public function infoChanged($sender,$param){
        $AufgabenRecord = AufgabenRecord::finder()->findByPK($this->DMSFileId->Text);
        $FieldToChange = $sender->Id;
        $AufgabenRecord->$FieldToChange = $sender->Text;
        $AufgabenRecord->save();
    }

    public function setTaskDone($sender,$param){
        $AufgabenRecord = AufgabenRecord::finder()->findByPK($this->DMSFileId->Text);
        if($AufgabenRecord->auf_done == 0){
            $AufgabenRecord->auf_done = 1;
        }else{
            $AufgabenRecord->auf_done = 0;
        }
        $AufgabenRecord->save();
        $this->parent->parent->parent->bindListTAValue();
    }
	
}
?>