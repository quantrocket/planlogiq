<?php

class KommentarContainer extends TTemplateControl {

    private $myPeriode = "10001";

    public function onLoad($param) {

        parent::onLoad($param);

        if($this->Request['periode']!='') {
            $this->myPeriode = $this->Request['periode'];
        }

        if(!$this->Page->IsPostBack && !$this->Page->isCallback) {
            if(isset($_GET['idta_variante'])) {
                $this->bindListComment();
            }
        }
    }

    public function showCommentDialog($sender,$param) {
        $this->CommentDialog->setDisplay("Dynamic");
        $sender->Visible = false;
    }

    public function hideCommentDialog($sender,$param) {
        $this->CommentDialog->setDisplay("None");
        $this->KommentarSichtButton->Visible = true;
    }

    /* here comes the part for the comments */

    private $COMprimarykey = "idqs_comments";
    private $COMfields = array("idtm_organisation","com_page","com_id","com_content","com_modul","idta_variante","idta_periode");
    private $COMdatfields = array("com_cdate");
    private $COMtimefields = array();
    private $COMhiddenfields = array();
    private $COMboolfields = array();

    public function bindListComment() {
        $MySQLString = "SELECT * FROM qs_comments WHERE ";
        $MySQLString .= "com_modul = '".$this->page->ccom_modul->Text."' AND com_id = ".$_GET['idtm_struktur']." AND idta_variante= ".$_GET['idta_variante']." AND idta_periode IN (".$this->getInPeriods($this->myPeriode).") ORDER BY com_cdate DESC, idqs_comments DESC";
        $this->KommentarListe->DataSource=CommentRecord::finder()->findAllBySQL($MySQLString);
        $this->KommentarListe->dataBind();
    }

    private function getInPeriods($per_intern) {
        $SQLInString = $per_intern;
        $ResultsRecord = PeriodenRecord::finder()->findAllByparent_idta_perioden(PeriodenRecord::finder()->findByper_intern($per_intern)->idta_perioden);
        if(count($ResultsRecord)>=1) {
            foreach($ResultsRecord AS $ResultRecord) {
                $SQLInString .= ",".$ResultRecord->per_intern;
            }
        }
        return $SQLInString;
    }

    public function CCOMDeleteButtonClicked($sender,$param) {
        $item=$sender->CommandParameter;
        $Record = CommentRecord::finder()->findByidqs_comments($item);
        $Record->delete();
        $this->bindListComment();
    }

    public function CCOMSaveButtonClicked($sender,$param) {

        $SaveRecord = new CommentRecord;

        $SaveRecord->com_cdate = date("Y-m-d");
        $SaveRecord->com_content = $this->com_content->Text;
        $SaveRecord->com_modul = $this->page->ccom_modul->Text;
        $SaveRecord->com_id = $this->page->ccom_id->Text;
        $SaveRecord->idta_variante = $this->page->cidta_variante->Text;
        $SaveRecord->idta_periode = $this->page->cidta_periode->Text;
        $SaveRecord->idtm_organisation = $this->page->cidtm_organisation->Text;

        $SaveRecord->save();
        $this->CCOMNewButtonClicked($sender, $param);
        $this->bindListComment();
    }

    public function CCOMNewButtonClicked($sender,$param) {
        $this->com_content->Text = "";
    }

    public function pageKommentarListeChanged($sender,$param) {
        $this->KommentarListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListComment();
    }

}

?>