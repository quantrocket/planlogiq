<?php

class ZeiterfassungContainer extends TTemplateControl {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    private $subcats = array();//list of all subcats
    private $parentcats = array();//list of all parentcats
    private $catNames=array();
    private $UserStartId = 1;

    public function onLoad($param) {
        parent::onLoad($param);
        date_default_timezone_set('Europe/Berlin');

        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_activity"));

        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            if(!$this->User->isGuest) {
                $this->createZeitPullDown();
                $tmpstartdate = new DateTime();
                $tmpstartdate->modify("-30days");
                $this->zeiterfassung_datestart->setDate($tmpstartdate->format("Y-m-d"));
                $tmpstartdate->modify("45days");
                $this->zeiterfassung_dateende->setDate($tmpstartdate->format("Y-m-d"));
                $this->bindListRCValue();
            }
        }
    }

    function time_diff_mins($start_time, $end_time) {

        $start_time = explode(':',$start_time);
        $end_time = explode(':',$end_time);
        $end_ts = $end_time[0] * 60 + $end_time[1];
        $start_ts = $start_time[0] * 60 + $start_time[1];

        if ($start_time[0] > $end_time[0]) {
            $diff_ts = (24*60) - $start_ts + $end_ts;
        } else {
            $diff_ts = $end_ts - $start_ts;
        }

        return $diff_ts;
    }

    public function calcDauer() {
        $start_zeit = $this->RCedzeit_starttime->Text;
        $end_zeit = $this->RCedzeit_endtime->Text;
        $pause = $this->RCedzeit_break->Text;
        $this->RCedzeit_dauer->setText(($this->time_diff_mins($start_zeit, $end_zeit)-$pause)/60);
    }

    public function showFahrtenDialog($sender,$param) {
        $this->FahrtenDialog->setDisplay("Dynamic");
        $sender->Visible = false;
    }

    public function hideFahrtenDialog($sender,$param) {
        $this->FahrtenDialog->setDisplay("None");
        $this->FahrtenSichtButton->Visible = true;
    }

    public function createZeitPullDown() {
        //Als erstes die Organisation
        if($this->User->getIsAdmin()) {
            $this->RCedidtm_organisation->Text="";
        }else {
            $this->RCedidtm_organisation->Text=$this->User->getUserOrgId($this->User->getUserId());           
        }
        $this->RCedidta_kosten_status->DataSource=PFH::build_SQLPullDown(KostenStatusRecord::finder(),"ta_kosten_status",array("idta_kosten_status","kst_status_name"));
        $this->RCedidta_kosten_status->dataBind();

        $PRTREE = new PFHierarchyPullDown();
        $PRTREE->setStructureTable("tm_prozess");
        $PRTREE->setRecordClass(ProzessRecord::finder());
        $PRTREE->setPKField("idtm_prozess");
        $PRTREE->setField("pro_name");
        $PRTREE->letsrun();
        $this->RCedidtm_prozess->DataSource=$PRTREE->myTree;
        $this->RCedidtm_prozess->dataBind();

        $fahrt_status=array(1=>"abbrechenbar",2=>"ausweisbar",3=>"privat");
        $this->fahrt_status->DataSource=$fahrt_status;
        $this->fahrt_status->dataBind();
        
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
        $this->FFidtm_activity->Text = $this->UserStartId;

        $this->RCedzeit_date->setDate(date('Y-m-d',time()));

//        $this->RCedidtm_activity->DataSource=PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_name","act_pspcode"),"idta_activity_type = 2","act_name ASC");
//        $this->RCedidtm_activity->dataBind();
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idtm_zeiterfassung";
    private $RCfields = array("idta_kosten_status","idtm_organisation","idtm_activity","zeit_break","zeit_dauer","zeit_descr","idtm_prozess");
    private $RCdatfields = array("zeit_date");
    private $RCtimefields = array("zeit_starttime","zeit_endtime");
    private $RChiddenfields = array();
    private $RCboolfields = array();

    public function bindListRCValue() {
        $datestart = date('Y-m-d',$this->zeiterfassung_datestart->TimeStamp);
        $dateende = date('Y-m-d',$this->zeiterfassung_dateende->TimeStamp);
        $criteria = new TActiveRecordCriteria();
        $criteria->OrdersBy['zeit_date']='DESC';
        $criteria->OrdersBy['idtm_organisation']='ASC';
        $criteria->OrdersBy['zeit_endtime']='ASC';
        if(!$this->User->getIsAdmin()) {
            $criteria->Condition ="idtm_organisation = :suchtext1 AND (zeit_date >= DATE(:suchtext2) AND zeit_date <= DATE(:suchtext3))";
            $criteria->Parameters[':suchtext1'] = $this->User->getUserOrgId($this->User->getUserId());
            $criteria->Parameters[':suchtext2'] = $datestart;
            $criteria->Parameters[':suchtext3'] = $dateende;
        }else {
            $criteria->Condition ="zeit_date >= DATE(:suchtext2) AND zeit_date <= DATE(:suchtext3)";
            $criteria->Parameters[':suchtext2'] = $datestart;
            $criteria->Parameters[':suchtext3'] = $dateende;
        }
        
        $mySQL = "SELECT idtm_activity,parent_idtm_activity,act_name,idta_activity_type FROM tm_activity";
        $mySQLOrderBy = " ORDER BY parent_idtm_activity,act_step";
        $this->load_all_cats($mySQL.$mySQLOrderBy);

        $SKNode=$this->FFidtm_activity->Text>=1?$this->FFidtm_activity->Text:$this->UserStartId;

        if($this->subCategory_list($this->subcats, $SKNode)!=''){
            $criteria->Condition.= " AND idtm_activity IN (".$this->subCategory_list($this->subcats, $SKNode).") OR idtm_activity = 0";
        }

        $this->ZeiterfassungListe->VirtualItemCount = count(ZeiterfassungRecord::finder()->findAll($criteria));

        $this->ZeiterfassungListe->DataSource=ZeiterfassungRecord::finder()->findAll($criteria);
        $this->ZeiterfassungListe->dataBind();
    }

    public function load_rcvalue($sender,$param) {

        $item = $param->Item;
        $myitem=ZeiterfassungRecord::finder()->findByPK($item->lst_idtm_zeiterfassung->Text);

        //das fahrtenbuch
        $FahrtenRecord = FahrtenbuchRecord::finder()->findByidtm_zeiterfassung($item->lst_idtm_zeiterfassung->Text);
        $this->fahrt_von->Text = $FahrtenRecord->fahrt_von;
        $this->fahrt_nach->Text = $FahrtenRecord->fahrt_nach;
        $this->fahrt_km->Text = $FahrtenRecord->fahrt_km;
        $this->fahrt_status->Text = $FahrtenRecord->fahrt_status;


        $tempus = 'RCed'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->RCedzeiterfassung_edit_status->Text = 1;
        //anzeigen der speichern buttons
        $this->SpeichernDialog->setDisplay("Dynamic");


        $this->Teddeb_id->Text = $myitem->$monus;
        $this->Teddeb_tabelle->Text = 'tm_zeiterfassung';
        $this->DetailBelegContainer->initParameters();
        $this->DetailBelegContainer->bindDetailBelegListe();
        
        $this->bindListRCValue();
    }
    
    public function RCDeleteButtonClicked($sender,$param) {
        $tempus='RCed'.$this->RCprimarykey;
        $Record = ZeiterfassungRecord::finder()->findByPK($this->$tempus->Text);
        $Record->delete();
        $this->bindListRCValue();
        $this->RCNewButtonClicked($sender,$param);
    }

    public function RCSavedButtonClicked($sender,$param) {

        $this->calcDauer();
        $tempus='RCed'.$this->RCprimarykey;

        if($this->RCedzeiterfassung_edit_status->Text == '1') {
            $RCEditRecord = ZeiterfassungRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new ZeiterfassungRecord;
        }

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCEditRecord->save();

        //das fahrtenbuch
        if(count(FahrtenbuchRecord::finder()->findByidtm_zeiterfassung($this->$tempus->Text))>0) {
            $FahrtenRecord = FahrtenbuchRecord::finder()->findByidtm_zeiterfassung($this->$tempus->Text);
            $FahrtenRecord->fahrt_von = $this->fahrt_von->Text;
            $FahrtenRecord->fahrt_nach = $this->fahrt_nach->Text;
            $FahrtenRecord->fahrt_km = $this->fahrt_km->Text;
            $FahrtenRecord->fahrt_status = $this->fahrt_status->Text;
            $FahrtenRecord->save();
        }else {
            $FahrtenRecord = new FahrtenbuchRecord;
            $FahrtenRecord->idtm_zeiterfassung = $RCEditRecord->idtm_zeiterfassung;
            $FahrtenRecord->fahrt_von = $this->fahrt_von->Text;
            $FahrtenRecord->fahrt_nach = $this->fahrt_nach->Text;
            $FahrtenRecord->fahrt_km = $this->fahrt_km->Text;
            $FahrtenRecord->fahrt_status = $this->fahrt_status->Text;
            $FahrtenRecord->save();
        }

        $this->Teddeb_id->Text = $RCEditRecord->idtm_zeiterfassung;
        $this->Teddeb_tabelle->Text = 'tm_zeiterfassung';
        $this->DetailBelegContainer->initParameters();
        $this->DetailBelegContainer->bindDetailBelegListe();

        $this->bindListRCValue();
    }

    public function RCNewButtonClicked($sender,$param) {

        $tempus = 'RCed'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = '0';

        $this->fahrt_von->Text = "";
        $this->fahrt_nach->Text = "";
        $this->fahrt_km->Text = '0';
        $this->fahrt_status->Text = '1';


        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = '08:00';
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = '1';
        }
        //ausblenden der buttons zum speichern
        $this->SpeichernDialog->setDisplay("None");
        $this->RCedzeiterfassung_edit_status->Text = '0';
        $this->RCedidtm_organisation->Text=$this->User->getUserOrgId($this->User->getUserId());
    }

    public function rcvList_PageIndexChanged($sender,$param) {
        $this->ZeiterfassungListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListRCValue();
    }

    /*
     * @function: hier kommen alle Funktionen, die ich brauche um die Liste der verfuegbaren werte zu inkludieren
     */

    public function filterActivity($sender,$param){
        $HRKEYTest = new PFHierarchyPullDown();
        $HRKEYTest->setStructureTable("tm_activity");
        $HRKEYTest->setRecordClass(ActivityRecord::finder());
        $HRKEYTest->setPKField("idtm_activity");
        $HRKEYTest->setField("act_name");
        $HRKEYTest->setStartNode($this->UserStartId);
        $HRKEYTest->setSQLCondition("idta_activity_type = 2 AND UPPER(act_name) LIKE '%".strtoupper($this->FFact_name->Text)."%'");
        $HRKEYTest->letsrun();
        $this->RCedidtm_activity->DataSource=$HRKEYTest->myTree;
        $this->RCedidtm_activity->dataBind();
    }

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