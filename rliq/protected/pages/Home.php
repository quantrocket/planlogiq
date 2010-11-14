<?php

//Prado::using('Application.common.*');

class Home extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        date_default_timezone_set('Europe/Berlin');
        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {
            if(!$this->User->isGuest) {

                //TASKPART
                $this->Tedauf_user_id->Text=$this->User->getUserOrgId($this->User->getUserId());

                $this->AufgabenContainerOrganisation->initParameters();
                $this->AufgabenContainerOrganisation->bindListTAValue();
                $this->AufgabenContainerOrganisation->initYearPullDown();
                
                //los aufgabos
                $ULSQL = "SELECT * FROM tt_user_log ORDER BY ul_time DESC LIMIT 20";
                $this->UserLogging->DataSource = TTUserLogRecord::finder()->findAllBySQL($ULSQL);
                $this->UserLogging->dataBind();
            }
        }

    }

    public function btnShow_OnClick($sender, $param) {
        $this->AufgabenContainer->load_aufgabenvalue_byID($sender->CommandParameter);
        $id=$this->mpnlTest->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function btnClose_OnClick($sender, $param) {
        $this->mpnlTest->Hide();
        $this->bindListAufgaben();
    }

    public function pageUserLoggingListeChanged($sender,$param) {
        $this->UserLogging->CurrentPageIndex=$param->NewPageIndex;
        $ULSQL = "SELECT * FROM tt_user_log ORDER BY ul_time DESC LIMIT 20";
        $this->UserLogging->DataSource = TTUserLogRecord::finder()->findAllBySQL($ULSQL);
        $this->UserLogging->dataBind();
    }

    public function bindListTermine() {        
        $today = date("Y-m-d");
        $SQL = "SELECT tm_termin.* FROM tm_termin INNER JOIN tm_termin_organisation ON tm_termin.idtm_termin = tm_termin_organisation.idtm_termin WHERE tm_termin_organisation.idtm_organisation='".$this->User->getUserOrgId($this->User->getUserId())."' ORDER BY ter_startdate ASC";
        $this->lstTermine->DataSource=TerminRecord::finder()->findAllBySQL($SQL);
        $this->lstTermine->dataBind();
    }

    public function addTerminEintrag($sender,$param){
        
    }

    public function lstTermine_pageIndexChanged($sender,$param) {
        $this->lstTermine->CurrentPageIndex=$param->NewPageIndex;
        $this->bindListTermine();
    }

}
?>