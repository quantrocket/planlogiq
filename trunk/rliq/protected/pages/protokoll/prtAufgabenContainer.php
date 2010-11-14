<?php

class prtAufgabenContainer extends TTemplateControl {

    /*
     * To implement the container, use the following tags
     *  <com:Application.pages.protokoll.prtAufgabenContainer ID="prtAufgabenContainer"/>
     *  <com:TActiveTextBox id="Tedauf_tabelle" Text="tm_activity" visible="false" />
     *  <com:TActiveTextBox id="Tedauf_id" Text="0" visible="false" />
     *
     */

    public $StatusArray = array( 1=>"offen", "Definition","Umsetzung","Test","Live","Produktiv");

    public function  __destruct() {
        unset($this->prtAufgabenContainer);
    }

    public function initParameters($tabelle='dummy',$filter=1){
        if($tabelle!=='dummy'){
            $this->Tedauf_tabelle->Text = $tabelle;
            $this->Tedauf_id->Text = $filter;
        }else{
            $this->Tedauf_tabelle->Text = $this->parent->Tedauf_tabelle->Text;
            $this->Tedauf_id->Text = $this->parent->Tedauf_id->Text;
            $this->MyPrtAufgabenPanel->setDisplay($this->parent->Tedauf_visible->Text);
        }
    }

    public function showprtAufgabenDialog($sender,$param) {
        $this->prtAufgabenDialog->setDisplay("Dynamic");
        $this->initPullDowns();
        $sender->visible=false;
        $this->CPRTNewButtonClicked($sender, $param);
    }

    public function hideprtAufgabenDialog($sender,$param) {
        $this->prtAufgabenDialog->setDisplay("None");
        $this->AufgabeSichtButton->Visible = true;
    }

    public function initPullDowns(){
       
    }

    public function bindListPrtAufgaben(){
        $SQL = "SELECT * FROM tm_aufgaben WHERE auf_tabelle = '".$this->Tedauf_tabelle->Text."' AND auf_id = ".$this->Tedauf_id->Text ." AND auf_deleted = 0";
        //TODO : Hier muss noch die einschraenkung beherzigt werden...

        //hier Bilde ich die summe fuer die statistiken
        $StatsSQL = "SELECT SUM(auf_dauer) AS auf_dauer FROM tm_aufgaben WHERE auf_tabelle = '".$this->Tedauf_tabelle->Text."' AND auf_id = ".$this->Tedauf_id->Text ." AND auf_deleted = 0 GROUP BY auf_tabelle, auf_id LIMIT 1";
        $auf_dauer = AufgabenRecord::finder()->findBySQL($StatsSQL)->auf_dauer;

        $this->SUM_auf_dauer->Text = $auf_dauer;
        $this->SUM_auf_dauer_day->Text = $auf_dauer/8;

        $this->CCprtAufgabenListe->DataSource=AufgabenRecord::finder()->findAllBySQL($SQL);
        $this->CCprtAufgabenListe->dataBind();
    }

    public function CPRTAddButtonClicked($sender,$param) {
        if(0 == $this->Tedauf_edit_status->Text){
            $AEditRecord= new AufgabenRecord();
        }else{
            $AEditRecord = AufgabenRecord::finder()->findByPK($this->Tedidtm_aufgaben->Text);
        }
        $AEditRecord->auf_tabelle = $this->Tedauf_tabelle->Text;
        $AEditRecord->auf_id = $this->Tedauf_id->Text;
        $AEditRecord->idtm_organisation = $this->idtm_organisation->Text;
        $AEditRecord->auf_idtm_organisation = $this->auf_idtm_organisation->Text;
        $AEditRecord->auf_priority = $this->auf_priority->Text;
        $AEditRecord->auf_dauer = $this->auf_dauer->Text;
        //$AEditRecord->auf_name = $this->prtdet_topic->Text;
        $AEditRecord->auf_beschreibung = $this->auf_beschreibung->Text;
        $AEditRecord->auf_tdate = date('Y-m-d',$this->auf_tdate->TimeStamp);
        $AEditRecord->auf_ddate = date('Y-m-d',$this->auf_ddate->TimeStamp);
        $AEditRecord->auf_done = $this->auf_done->Checked?1:0;
        $AEditRecord->save();

        $this->CPRTNewButtonClicked($sender, $param);
        $this->bindListPrtAufgaben();
        $this->hideprtAufgabenDialog($sender,$param);
    }

    public function CPRTNewButtonClicked($sender,$param) {
        $this->auf_beschreibung->Text = "Bitte beschreiben Sie die Aufgabe...";
        $this->Tedauf_edit_status->Text = 0;
        $this->getPage()->getClientScript()->registerEndScript('XCOL', "constructCollapsableFieldsets();");
    }

    public function editPrtAufgabe($sender,$param){
        if($param->CommandName=="edit"){
            $this->prtAufgabenDialog->setDisplay("Dynamic");
            $this->initPullDowns();
            $AufgabeRecord = AufgabenRecord::finder()->findByPK($param->CommandParameter);
            $this->Tedidtm_aufgaben->Text = $AufgabeRecord->idtm_aufgaben;
            $this->Tedauf_tabelle->Text = $AufgabeRecord->auf_tabelle;
            $this->Tedauf_id->Text = $AufgabeRecord->auf_id;
            $this->idtm_organisation->Text = $AufgabeRecord->idtm_organisation;
            $this->auf_idtm_organisation->Text = $AufgabeRecord->auf_idtm_organisation;
            $this->auf_priority->Text = $AufgabeRecord->auf_priority;
            $this->auf_dauer->Text = $AufgabeRecord->auf_dauer;
            //$AEditRecord->auf_name = $this->prtdet_topic->Text;
            $this->auf_beschreibung->Text = $AufgabeRecord->auf_beschreibung;
            $this->auf_tdate->setDate($AufgabeRecord->auf_tdate);
            $this->auf_ddate->setDate($AufgabeRecord->auf_ddate);
            $this->auf_done->setChecked($AufgabeRecord->auf_done);
            $this->Tedauf_edit_status->Text = 1;
            $this->AddOrSaveButtonPRTAUF->Text = "speichern";
            $this->getPage()->getClientScript()->registerEndScript('XCOL', "constructCollapsableFieldsets();");
        }
        if($param->CommandName=="taskdone"){
            $tmpstartdate = new DateTime();
            $AufgabenRecord = AufgabenRecord::finder()->findByPK($param->CommandParameter);
            $AufgabenRecord->auf_done=1;
            $AufgabenRecord->auf_ddate = $tmpstartdate->format("Y-m-d");
            $AufgabenRecord->save();
            $this->bindListPrtAufgaben();
        }
        if($param->CommandName=="mail"){
            $Aufgabe = AufgabenRecord::finder()->findByPK($param->CommandParameter);
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
            $mail->Subject = $Aufgabe->auf_name;
            $HTMLMessage ="Sehr geehrte(r) Frau/Herr ".$MailEmpfaenger->org_name.",<br/><br/>";
            $HTMLMessage.="die folgende Aufgabe wurde Ihnen zugeordnet:<br/>";
            $HTMLMessage.="<p><table><tr><td bgcolor='#efefef'>Task Nr.:</td><td>".$Aufgabe->idtm_aufgaben." </td><td bgcolor='#efefef'>Thema: </td><td>".utf8_decode($Aufgabe->auf_name)."</td></tr>";
            $HTMLMessage.="<tr><td bgcolor='#efefef'>Bis:</td><td colspan='3'><b>".$Aufgabe->auf_tdate."</b></td></tr></table>";
            //$HTMLMessage.="<p style='background-color:#efefef'><hr/>".utf8_decode($Aufgabe->prtdet_descr)."</p>";
            $HTMLMessage.="<p style='background-color:#efefef'><hr/>".utf8_decode($Aufgabe->auf_beschreibung)."<hr/></p>";
            $HTMLMessage.="Bitte nicht Antworten! Diese Mail wurde automatisch generiert...";
            $mail->MsgHTML($HTMLMessage);
            if(!$mail->Send()){
                    $this->getPage()->getClientScript()->registerEndScript('XER', "alert('Fehler!');");
            }else{
                    $this->getPage()->getClientScript()->registerEndScript('XER', "alert('Done')");
            }
        }
    }

}

?>