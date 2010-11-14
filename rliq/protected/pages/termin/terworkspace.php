<?php

class terworkspace extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }
    
    private $primarykey = "idtm_termin";
    private $MASTERRECORD = '';
    private $finder = '';
    private $fields = array("ter_betreff","ter_descr","ter_ort","idta_termin_type");
    private $listfields = array("idtm_activity");
    private $datfields = array("ter_startdate","ter_enddate");
    private $timefields = array("ter_starttime","ter_endtime");
    private $hiddenfields = array();
    private $boolfields = array();
    private $exitURL = 'termin.terworkspace';

    private $subcats = array();//list of all subcats
    private $parentcats = array();//list of all parentcats
    private $catNames=array();
    private $UserStartId = 1;

    public function onLoad($param) {

        //Globale definition f�r dieses Dokument
        $this->finder = TerminRecord::finder();
        $this->MASTERRECORD = new TerminRecord;

        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_activity"));

        date_default_timezone_set('Europe/Berlin');
        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {

            $HRKEYTest = new PFHierarchyPullDown();
            $HRKEYTest->setStructureTable("tm_activity");
            $HRKEYTest->setRecordClass(ActivityRecord::finder());
            $HRKEYTest->setPKField("idtm_activity");
            $HRKEYTest->setField("act_name");
            $HRKEYTest->setStartNode($this->UserStartId);
            $HRKEYTest->setSQLCondition("idta_activity_type = 2");
            $HRKEYTest->letsrun();
            $this->FFidtm_activity->DataSource=$HRKEYTest->myTree;
            $this->FFidtm_activity->dataBind();

            $tmpstartdate = new DateTime();
            $this->ter_startdate->setDate($tmpstartdate->format("Y-m-d"));
            $this->ter_enddate->setDate($tmpstartdate->format("Y-m-d"));

            $this->ttidtm_ressource->DataSource=PFH::build_SQLPullDown(RessourceRecord::finder(),"tm_ressource",array("idtm_ressource","res_name"),"idta_ressource_type>1");
            $this->ttidtm_ressource->dataBind();

            $this->bindListTermin();
        }
    }

    public function bindListTermin() {
        $mySQL = "SELECT idtm_activity,parent_idtm_activity,act_name,idta_activity_type FROM tm_activity";
        $mySQLOrderBy = " ORDER BY parent_idtm_activity,act_step";
        $this->load_all_cats($mySQL.$mySQLOrderBy);

        $SKNode=$this->FFidtm_activity->Text>=1?$this->FFidtm_activity->Text:$this->UserStartId;

        if($this->subCategory_list($this->subcats, $SKNode)!=''){
            $sql = "SELECT * FROM tm_termin";
            $sql .= " WHERE tm_termin.idtm_activity IN (". $this->subCategory_list($this->subcats, $SKNode).")";
            $sql .= " ORDER BY ter_startdate DESC";
            $this->TerminListe->DataSource=TerminRecord::finder()->findAllBySQL($sql);
            $this->TerminListe->dataBind();
        }
    }

    public function searchTermin() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="ter_betreff LIKE :suchbedingung1";
        $criteria->Parameters[':suchbedingung1'] = "%".$this->find_termin->Text."%";

        $this->TerminListe->VirtualItemCount = count(TerminRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->TerminListe->PageSize);
        $criteria->setOffset($this->TerminListe->PageSize * $this->TerminListe->CurrentPageIndex);
        $this->TerminListe->DataKeyField = 'idtm_termin';

        $this->TerminListe->VirtualItemCount = count(TerminRecord::finder()->findAll());
        $this->TerminListe->DataSource=TerminRecord::finder()->findAll($criteria);
        $this->TerminListe->dataBind();
    }

    public function TDeleteButtonClicked($sender,$param) {
        $tempus=$this->primarykey;
        $AEditRecord = TerminRecord::finder()->findByPK($this->$tempus->Text);
        $AEditRecord->delete();
        $this->bindListTermin();
        $this->TNewButtonClicked($sender,$param);
        $this->getPage()->getClientScript()->registerEndScript('xdhxs', "scheduler.load('".$this->getRequest()->constructUrl('page','termin.TerminConnector')."')");
    }

    public function load_termin($sender,$param) {

        $myitem=TerminRecord::finder()->findByPK($sender->CommandParameter);

        $tempus = $this->primarykey;
        $monus = $this->primarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->hiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->text= $myitem->$recordfield;
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->timefields as $recordfield) {
            $edrecordfield = $recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->termin_edit_status->Text = 1;
        $this->ParticipantbindList();
        $this->RessourcebindList();
        //$this->addParticipant->setVisible(true);
    }

    public function TSavedButtonClicked($sender,$param) {

        $tempus=$this->primarykey;

        if($this->termin_edit_status->Text == '1') {
            $AEditRecord = TerminRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $AEditRecord = new TerminRecord;
        }

        //HIDDEN
        foreach ($this->hiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->timefields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $AEditRecord->save();

        $this->bindListTermin();
        $this->ParticipantbindList();
        $this->RessourcebindList();
        $this->termin_edit_status->Text = 1;
        $this->idtm_termin->Text=$AEditRecord->idtm_termin;
        $this->getPage()->getClientScript()->registerEndScript('xdhxs', "scheduler.load('".$this->getRequest()->constructUrl('page','termin.TerminConnector')."')");
    }

    public function TNewButtonClicked($sender,$param) {

        $tempus = $this->primarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->hiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        foreach ($this->timefields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '00:00';
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $tmpstartdate = new DateTime();
        $this->ter_startdate->setDate($tmpstartdate->format("Y-m-d"));
        $this->ter_enddate->setDate($tmpstartdate->format("Y-m-d"));

        $this->termin_edit_status->Text = '0';
        $this->idta_termin_type->Text = '1';
        $this->ParticipantbindList();
        $this->RessourcebindList();
    }

    public function terminList_PageIndexChanged($sender,$param) {
        $this->TerminListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListTermin();
    }


    //ANFANG DER FUNKTIONEN FUER DIE LISTE Participant

    public function removeParticipant($sender,$param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $deleteRecord = TerminOrganisationRecord::finder();
        $deleteRecord->deleteByPk($param->Item->lstpart_idtm_termin_organisation->Text);
        $this->ParticipantbindList();
    }

    public function addParticipant($sender,$param) {
        if($this->termin_edit_status->Text == 0){
            $this->TSavedButtonClicked($sender, $param);
        }

        $myRecord = new TerminOrganisationRecord;
        $myRecord->idtm_termin = $this->idtm_termin->Text;
        $myRecord->idtm_organisation = $this->ttidtm_organisation->Text;
        $myRecord->save();
        $this->ParticipantbindList();
    }

    private function ParticipantbindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_termin = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->idtm_termin->Text;
        $criteria->OrdersBy["idtm_organisation"] = 'asc';

        $this->ParticipantListe->DataSource=TerminOrganisationView::finder()->findAll($criteria);
        $this->ParticipantListe->dataBind();
    }

    public function participant_PageIndexChanged($sender,$param) {
        $this->ParticipantListe->CurrentPageIndex = $param->NewPageIndex;
        $this->ParticipantbindList();
    }

    //ENDE DER FUNKTIONEN FUER DIE LISTE Participant

    //ANFANG DER FUNKTIONEN FUER DIE LISTE Ressource

    public function removeRessource($sender,$param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $deleteRecord = TerminRessourceRecord::finder();
        $deleteRecord->deleteByPk($param->Item->lstpart_idtm_termin_ressource->Text);
        $this->RessourcebindList();
    }

    public function addRessource($sender,$param) {

        if($this->termin_edit_status->Text == 0){
            $this->TSavedButtonClicked($sender, $param);
        }

        $myRecord = new TerminRessourceRecord;

        $myRecord->idtm_termin = $this->idtm_termin->Text;
        $myRecord->idtm_ressource = $this->ttidtm_ressource->Text;

        $myRecord->save();
        $this->RessourcebindList();
    }

    private function RessourcebindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_termin = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->idtm_termin->Text;
        $criteria->OrdersBy["idtm_ressource"] = 'asc';

        $this->RessourceListe->DataSource=TerminRessourceView::finder()->findAll($criteria);
        $this->RessourceListe->dataBind();
    }

    public function ressource_PageIndexChanged($sender,$param) {
        $this->RessourceListe->CurrentPageIndex = $param->NewPageIndex;
        $this->RessourcebindList();
    }

    public function StartDateChanged($sender,$param){
        $tmpstartdate = new DateTime($sender->getDate());
        $this->ter_enddate->setDate($tmpstartdate->format("Y-m-d"));
    }

    /*
     * @function: hier kommen alle Funktionen, die ich brauche um die Liste der verfuegbaren werte zu inkludieren
     */

     public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

    private function load_all_cats($TTSQL) {
        $rows = ActivityRecord::finder()->findAllbySQL($TTSQL);
        foreach($rows as $row) {
            $this->subcats[$row->parent_idtm_activity][]=$row->idtm_activity;
            $this->parentcats[$row->idtm_activity]=$row->parent_idtm_activity;
        }
    }

    private function subCategory_list($subcats,$catID) {
        $lst = $catID; //id des ersten Startelements...
        if(array_key_exists($catID,$subcats)) {
            foreach($subcats[$catID] as $subCatID) {
                $lst .= ", " . $this->subCategory_list($subcats, $subCatID);
            }
        }
        return $lst;
    }

    private function parentCategory_list($parentcats,$catID) {
        $lst = $catID; //id des ersten Startelements...
        while($parentcats[$catID] != NULL) {
            $catID = $parentcats[$catID];
            $lst .= ", " . $catID;
        }
        return $lst;
    }

}
?>