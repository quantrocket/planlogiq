<?php

class AufgabenContainer extends TTemplateControl {

    /*
     * To implement the container, use the following tags
     *  <com:Application.pages.container.AufgabenContainer ID="AufgabenContainer"/>
     *  <com:TActiveTextBox id="Tedauf_tabelle" Text="tm_activity" visible="false" />
     *  <com:TActiveTextBox id="Tedauf_id" Text="0" visible="false" />
     *  <com:TActiveTextBox id="Tedauf_user_id" Text="0" visible="false" />
     *
     */


    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            $this->initPullDowns();
            $this->bindListTAValue();
            //$this->initParameters();
        }
    }

    public function initPullDowns(){
        $this->ttidtm_ressource->DataSource=PFH::build_SQLPullDown(RessourceRecord::finder(),"tm_ressource",array("idtm_ressource","res_name"));
        $this->ttidtm_ressource->dataBind();

        $this->Tedidta_aufgaben_type->DataSource=PFH::build_SQLPullDown(AufgabenTypeRecord::finder(),"ta_aufgaben_type",array("idta_aufgaben_type","auf_type_name"));
        $this->Tedidta_aufgaben_type->dataBind();
    }

    public function initParameters(){
        $this->Tedauf_tabelle->Text = $this->page->Tedauf_tabelle->Text;
        $this->Tedauf_id->Text = $this->page->Tedauf_id->Text;
        $this->Tedauf_user_id->Text = $this->page->Tedauf_user_id->Text;
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idtm_aufgaben";
    private $RCfields = array("idtm_organisation",
        "auf_idtm_organisation",
        "auf_beschreibung",
        "auf_priority",
        "auf_name",
        "auf_tabelle",
        "auf_id",
        "auf_dauer",
        "auf_tag",
        "idta_aufgaben_type");
    private $RCdatfields = array("auf_tdate");
    private $RCtimefields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array("auf_done");

    public function bindListTAValue() {
        $this->initParameters();
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="auf_tabelle = :suchtext1 AND auf_id = :suchtext2 AND auf_deleted = 0";
        $criteria->Parameters[':suchtext1'] = $this->Tedauf_tabelle->Text;
        $criteria->Parameters[':suchtext2'] = $this->Tedauf_id->Text;

        //the personalisation part
        if($this->page->Tedauf_user_id->Text >= 1) {
            $criteria->Condition .= "AND auf_done = 0 AND auf_name IS NOT NULL";
            $criteria->Condition .=" AND idtm_organisation = :suchtext3";
            $criteria->Parameters[':suchtext3'] = $this->page->Tedauf_user_id->Text;            
        }
        $criteria->OrdersBy['auf_tdate']='DESC';

        $this->page->AufgabenContainer->CCAufgabenListe->DataSource=AufgabenRecord::finder()->findAll($criteria);
        $this->page->AufgabenContainer->CCAufgabenListe->dataBind();

    }

    private function bindListRessource(){
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition = "idtm_aufgabe = :suchtext";
            $criteria->Parameters[':suchtext'] = $this->Tedidtm_aufgaben->Text;
            $criteria->OrdersBy["idtm_ressource"] = 'asc';
            $this->RessourceListe->DataSource=AufgabeRessourceView::finder()->findAll($criteria);
            $this->RessourceListe->dataBind();
    }

    /**
     * load_aufgabenvalue
     * @param <object> $sender
     * @param <array> $param if you want to display a single record, param->Item->lstcc_idtm_aufgaben->Text should be included
     */

    public function load_aufgabenvalue($sender,$param) {

        if($sender->Id=="RepeaterLoadAufgabe"){
            $myitem=AufgabenRecord::finder()->findByPK($sender->CommandParameter);
        }else{
            $item = $param->Item;
            $myitem=AufgabenRecord::finder()->findByPK($item->lstcc_idtm_aufgaben->Text);
        }
        $tempus = 'Ted'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->Tedaufgaben_edit_status->Text = 1;
        $this->Tedsuggest_idtm_organisation->Text = OrganisationRecord::finder()->findByidtm_organisation($this->Tedidtm_organisation->Text)->org_name;

        $this->bindListRessource();
    }

    public function load_aufgabenvalue_byID($idtm_aufgabe) {

        $myitem=AufgabenRecord::finder()->findByPK($idtm_aufgabe);

        $tempus = 'Ted'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->Tedaufgaben_edit_status->Text = 1;
        $this->Tedsuggest_idtm_organisation->Text = OrganisationRecord::finder()->findByidtm_organisation($this->Tedidtm_organisation->Text)->org_name;

        $this->bindListRessource();
    }
    
    public function TADeleteButtonClicked($sender,$param) {
        $tempus='Ted'.$this->RCprimarykey;
        $Record = AufgabenRecord::finder()->findByPK($this->$tempus->Text);
        $Record->auf_deleted = 1;
        $Record->save();
        $this->bindListTAValue();
        $this->TNewButtonClicked($sender,$param);
    }

    public function TASavedButtonClicked($sender,$param) {

        $tempus='Ted'.$this->RCprimarykey;

        if($this->Tedaufgaben_edit_status->Text == '1') {
            $RCEditRecord = AufgabenRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new AufgabenRecord;
        }

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCEditRecord->save();

        $this->bindListTAValue();
    }

    public function TANewButtonClicked($sender,$param) {

        $tempus = 'Ted'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = '00:00';
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->Tedsuggest_idtm_organisation->Text = "";
        $this->Tedaufgaben_edit_status->Text = '0';
        $this->Tedidta_aufgaben_type->Text = '1';
        $this->initParameters();
        $this->bindListRessource();
    }

    public function rcvList_PageIndexChanged($sender,$param) {
        $this->CCAufgabenListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListTAValue();
    }

    public function suggestOrganisation($sender,$param) {
    // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $mySQL = "SELECT idtm_organisation,org_name FROM tm_organisation WHERE org_name LIKE '%".$token."%'";
        $sender->DataSource=PFH::convertdbObjectSuggest(TActiveRecord::finder('OrganisationRecord')->findAllBySQL($mySQL),array('idtm_organisation','org_name'));
        $sender->dataBind();
    }

    public function checkOrganisationName($sender,$param) {
    // valid if the username is not found in the database
        $param->IsValid=OrganisationRecord::finder()->findByidtm_organisation($this->Tedsuggest_idtm_organisation->Text)===null;
    }

    public function suggestionSelectedOne($sender,$param) {
        $id=$sender->Suggestions->DataKeys[ $param->selectedIndex ];
        $this->Tedidtm_organisation->Text=$id;
    }

    public function addRessource($sender,$param){
        //auf welche dimension sollen die werte zugeordnet werden
        $rIndecies = $this->ttidtm_ressource->SelectedIndices;
        foreach($rIndecies as $index)
        {
            $myRecord = new AufgabeRessourceRecord();
            $myRecord->idtm_aufgabe = $this->Tedidtm_aufgaben->Text;
            $myRecord->idtm_ressource = $this->ttidtm_ressource->Items[$index]->Value;
            $myRecord->auf_res_dauer = $this->ttauf_res_dauer->Text;
            $myRecord->save();
        }
        $this->bindListRessource();
    }

    public function removeRessource($sender,$param){
        AufgabeRessourceRecord::finder()->deleteByPk($param->Item->lstpart_idtm_aufgabe_ressource->Text);
        $this->bindListRessource();
    }

}

?>