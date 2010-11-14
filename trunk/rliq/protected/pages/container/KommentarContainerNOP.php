<?php

class KommentarContainerNOP extends TTemplateControl {

    /*
     * To implement the container, use the following tags
     *  <com:Application.pages.container.AufgabenContainerOrganisation ID="AufgabenContainerOrganisation"/>
     *  <com:TActiveTextBox id="Tedcom_tabelle" Text="tm_activity" visible="false" />
     *  <com:TActiveTextBox id="Tedcom_id" Text="0" visible="false" />
     *
     */

    public $idta_organisation_art = array("-1"=>"alle","0"=>"Team","Eigent.","Lieferant","WEG","Mietobjekt","sonstige","Kunde","Lieferant");

    public function  __destruct() {
        unset($this->KommentarContainerNOP);
    }

    public function initParameters(){
        $this->Tedcom_tabelle->Text = $this->parent->Tedcom_tabelle->Text;
        $this->Tedcom_id->Text = $this->parent->Tedcom_id->Text;
    }

    public function showCommentDialog($sender,$param) {
        $this->CommentDialog->setDisplay("Dynamic");
        $this->initPullDowns();
        $sender->Visible = false;
    }

    public function hideCommentDialog($sender,$param) {
        $this->CommentDialog->setDisplay("None");
        $this->KommentarSichtButton->Visible = true;
    }

    public function initPullDowns(){
        $this->CBidta_organisation_art->DataSource = $this->idta_organisation_art;
        $this->CBidta_organisation_art->dataBind();
    }

    public function bindParameterListComments($tabelle,$filter){
        $this->Tedcom_tabelle->Text = $tabelle;
        $this->Tedcom_id->Text = $filter;
        $SQL = "SELECT * FROM qs_comments WHERE com_modul = '".$tabelle."' AND com_id = ".$filter;
        //TODO : Hier muss noch die einschraenkung beherzigt werden...

        $this->CCKommentarListe->DataSource=CommentRecord::finder()->findAllBySQL($SQL);
        $this->CCKommentarListe->dataBind();
    }

    public function bindListComments(){
        $SQL = "SELECT * FROM qs_comments WHERE com_modul = '".$this->Tedcom_tabelle->Text."' AND com_id = ".$this->Tedcom_id->Text;
        //TODO : Hier muss noch die einschraenkung beherzigt werden...
        
        $this->CCKommentarListe->DataSource=CommentRecord::finder()->findAllBySQL($SQL);
        $this->CCKommentarListe->dataBind();
    }

    public function CCOMSaveButtonClicked($sender,$param) {
        $SaveRecord = new CommentRecord;

        $SaveRecord->com_cdate = date("Y-m-d");
        $SaveRecord->com_content = $this->com_content->Text;
        $SaveRecord->com_modul = $this->Tedcom_tabelle->Text;
        $SaveRecord->com_id = $this->Tedcom_id->Text;
        $SaveRecord->idta_variante = $this->CBidta_organisation_art->Text; //sichtbar fuer bruecke
        $SaveRecord->idtm_organisation = $this->User->getUserOrgId($this->User->getUserId());

        $SaveRecord->save();
        $this->CCOMNewButtonClicked($sender, $param);
        $this->bindListComments();
        $this->hideCommentDialog($sender,$param);
    }

    public function CCOMNewButtonClicked($sender,$param) {
        $this->com_content->Text = "";
    }

}

?>