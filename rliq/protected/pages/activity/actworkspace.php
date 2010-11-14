<?php

Prado::using('Application.app_code.PFDBActivityTools');

class actworkspace extends TPage {

    private $primarykey = "idtm_activity";
    private $fields = array("act_name", "act_colorcode", "act_dauer", "act_descr", "act_fortschritt", "act_step", "act_pspcode", "act_faz", "act_saz", "act_fez", "act_sez", "act_gp", "act_fp", "idtm_organisation", "parent_idtm_activity");
    private $listfields = array("idta_activity_type");
    private $datfields = array("act_startdate", "act_enddate");
    private $hiddenfields = array();
    private $boolfields = array();
    private $timefields = array();
    private $exitURL = 'activity.actworkspace';
    private $subcats = array(); //list of all subcats
    private $parentcats = array(); //list of all parentcats
    private $catNames = array();
    private $UserStartId = 1;
    private $auf_done = array(0 => "offen", "erledigt", "alle");
    public $StatusArray = array(1 => "offen", "Definition", "Umsetzung", "Test", "Live", "Produktiv");
    public $idta_organisation_art = array("-1" => "alle", "0" => "Team", "Eigent.", "Lieferant", "WEG", "Mietobjekt", "sonstige", "Kunde", "Lieferant");

    public function onPreInit($param) {
        $myTheme = $this->User->getUserTheme($this->User->getUserId(), 'mod_theme');
        $this->setTheme($myTheme);        
    }


    public function onLoad($param) {

        date_default_timezone_set('Europe/Berlin');

        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name), "tm_activity"));

        if (!$this->isPostBack && !$this->isCallback) {

            $this->edidta_activity_type->DataSource = PFH::build_SQLPullDown(ActivityTypeRecord::finder(), "ta_activity_type", array("idta_activity_type", "act_type_name"));
            $this->edidta_activity_type->dataBind();

            $this->ttedidtt_ziele->DataSource = PFH::build_SQLPullDown(TTZieleRecord::finder(), "tt_ziele", array("idtt_ziele", "ttzie_name"));
            $this->ttedidtt_ziele->dataBind();

            //los interfaces input
            $this->ttedidtm_inoutput->DataSource = PFH::build_SQLPullDown(ActivityInoutputView::finder(), "vv_activity_inoutput", array("idtm_inoutput", "ino_name"), "ino_link_type=0");
            $this->ttedidtm_inoutput->dataBind();

            //los interfaces
            $this->Iedidta_inoutput_type->DataSource = PFH::build_SQLPullDown(InoutputTypeRecord::finder(), "ta_inoutput_type", array("idta_inoutput_type", "ino_type_name"));
            $this->Iedidta_inoutput_type->dataBind();

            $Usersql = "SELECT idtm_user, user_name FROM tm_user";
            $Userdata = PFH::convertdbObjectArray(UserRecord::finder()->findAllBySql($Usersql), array("idtm_user", "user_name"));
            $this->idtm_user->DataSource = $Userdata;
            $this->idtm_user->dataBind();

            $this->loadBerechtigung();

            $this->CCProtokollDetailGroupListPageSize->DataSource = array(5 => "5", 10 => "10", 15 => "15", 20 => "20");
            $this->CCProtokollDetailGroupListPageSize->dataBind();
            $this->CCProtokollDetailGroupListPageSize->Text = "5";

            $this->CBAufgabeDone->DataSource = $this->auf_done;
            $this->CBAufgabeDone->dataBind();

            $this->CBidta_organisation_art->DataSource = $this->idta_organisation_art;
            $this->CBidta_organisation_art->dataBind();

            $data = array(0 => "Normalfolge(E/A)", 1 => "Anfangsfolge(A/A)", 2 => "Sprungfolge(A/E)", 3 => "Endfolge(E/E)");
            $this->edactact_type->DataSource = $data;
            $this->edactact_type->dataBind();
            $this->getPage()->getClientScript()->registerEndScript('XACTCF', "constructCollapsableFieldsets();");
            
            $this->generateZeitVerlaufImage(0);
        }
    }

    public function initPullDowns($idtm_activity) {
        
    }

    public function initWindowLink($idtm_activity) {
        $LinkText = "javascript:win_organisation_openwin('";
        $parameter['idtm_activity'] = $idtm_activity;
        $LinkText.= $this->getApplication()->getRequest()->constructUrl('page','activity.window.actdescrwindow', $parameter);
        $LinkText.="')";
        $this->openDescrWindow->NavigateUrl = $LinkText;
    }

    public function initRelations($idtm_activity) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext";
        $criteria->Parameters[':suchtext'] = $idtm_activity;
        $criteria->OrdersBy["idtm_activity"] = 'asc';
        $this->edActivityOrganisationListe->DataSource = ActivityOrganisationView::finder()->findAll($criteria);
        $this->edActivityOrganisationListe->dataBind();
    }

    public function view_Activity($idtm_activity) {

        $item = ActivityRecord::finder()->findByidtm_activity($idtm_activity);

        if($item->idta_activity_type=='4' OR $item->idta_activity_type=='1'){
            $this->edActivityActivitybindList($idtm_activity);
        }else{
            $this->loadDateFromSubCats($idtm_activity);
        }

        if ($item->idta_activity_type == 2) {
            $this->displayOrgaPanel->Display = "Dynamic";
            $this->displayNetzplanPanel->Display = "None";
        } else {
            $this->displayOrgaPanel->Display = "None";
            if ($item->idta_activity_type == 1 OR $item->idta_activity_type == 4) {
                $this->displayNetzplanPanel->Display = "Dynamic";
            } else {
                $this->displayNetzplanPanel->Display = "None";
            }
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->setDate($item->$recordfield);
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->setChecked($item->$recordfield);
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->setSelectedValue($item->$recordfield);
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->Text = $item->$recordfield;
        }

        $this->initWindowLink($idtm_activity);
        $this->initPullDowns($idtm_activity);
        if($item->idta_activity_type==2){
            $this->initRelations($idtm_activity);
        }

        //los aufgabos
        $this->Tedauf_id->Text = $idtm_activity;
        $this->Iedino_id->Text = $idtm_activity;

        //the parameters for the RiskValueContainer
        $this->RCedrcv_tabelle->Text = "tm_activity";
        $this->RCedrcv_id->Text = $idtm_activity;
        $this->RiskValueContainer->initParameter();
        $this->RiskValueContainer->bindListRCValue();

        $this->initPrtAufgaben('tm_activity',$idtm_activity);
        
        $this->edidtm_activity->Text = $idtm_activity;
        $this->edactivity_edit_status->Text = '1';
        
        $this->edActivityZielebindList();
        $this->edActivityInoutputbindList();

        $this->bindListInterface();
        $this->bindActivityProtokollListe($idtm_activity);

        //die grafiken
        $this->generateZeitVerlaufImage($idtm_activity);
        
        //$this->edActivityOrganisationbindList();
        //hier laden wir die berechtigungnen am knoten
        $this->loadBerechtigung();
    }

    public function initPrtAufgaben($tabelle,$filter){
        $this->prtAufgabenContainer->initParameters($tabelle,$filter);
        $this->prtAufgabenContainer->bindListPrtAufgaben();
        $this->prtAufgabenContainer->__destruct();
    }

    /*
     * @function
     */

    public function loadDateFromSubCats($idtm_activity){
        $ActRecord = ActivityRecord::finder()->findByPK($idtm_activity);
        $mySQL = "SELECT MIN(act_startdate) AS act_startdate, MAX(act_enddate) AS act_enddate, SUM(act_dauer) AS act_dauer FROM tm_activity";
        $mySQL .= " WHERE (act_lft BETWEEN " . $ActRecord->act_lft . " AND " . $ActRecord->act_rgt . ") AND idtm_activity <> ".$idtm_activity." AND (idta_activity_type = 1 OR idta_activity_type>=4)";
        $MyResult = ActivityRecord::finder()->findBySQL($mySQL);
        $ActRecord->act_startdate = $MyResult->act_startdate;
        $ActRecord->act_enddate = $MyResult->act_enddate;
        $ActRecord->act_dauer = $MyResult->act_dauer;
        $this->edact_startdate->Text = $ActRecord->act_startdate;
        $this->edact_enddate->Text = $ActRecord->act_enddate;
        $this->edact_dauer->Text = $ActRecord->act_dauer;
        $ActRecord->save();
    }

    /*
     * @function: hier kommen alle Funktionen, die ich brauche um die Liste der verfuegbaren werte zu inkludieren
     */

    public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

    private function load_all_cats($TTSQL) {
        $rows = ActivityRecord::finder()->findAllbySQL($TTSQL);
        foreach ($rows as $row) {
            $this->subcats[$row->parent_idtm_activity][] = $row->idtm_activity;
            $this->parentcats[$row->idtm_activity] = $row->parent_idtm_activity;
        }
    }

    private function subCategory_list($subcats, $catID) {
        $lst = $catID; //id des ersten Startelements...
        if (array_key_exists($catID, $subcats)) {
            foreach ($subcats[$catID] as $subCatID) {
                $lst .= ", " . $this->subCategory_list($subcats, $subCatID);
            }
        }
        return $lst;
    }

    private function parentCategory_list($parentcats, $catID) {
        $lst = $catID; //id des ersten Startelements...
        while ($parentcats[$catID] != NULL) {
            $catID = $parentcats[$catID];
            $lst .= ", " . $catID;
        }
        return $lst;
    }

    private function bindActivityProtokollListe($idtm_activity) {
        if ($this->CCProtokollDetailGroupListPageSize->Text <= 1 AND $this->CCProtokollDetailGroupListPageSize->Text != '') {
            $this->ActivityProtokollListe->PageSize = 5;
        } else {
            $this->ActivityProtokollListe->PageSize = 1 * $this->CCProtokollDetailGroupListPageSize->Text;
        }

        $SKNode = $idtm_activity >= 1 ? $idtm_activity : $this->UserStartId;

            $sqlall = "SELECT idtm_protokoll_detail FROM vv_protokoll_detail_aufgabe";
            $sqlcount = "SELECT count(idtm_protokoll_detail) AS idtm_protokoll_detail FROM vv_protokoll_detail_aufgabe";
            $sql = " WHERE (act_lft BETWEEN " . ActivityRecord::finder()->findByPK($SKNode)->act_lft . " AND " . ActivityRecord::finder()->findByPK($SKNode)->act_rgt . ")";

            if ($this->CBAufgabeDone->Text == 0 AND $this->CBAufgabeDone->Text != '') {
                $sql .= " AND (auf_done = " . $this->CBAufgabeDone->Text . " AND idta_protokoll_ergebnistype<3)";
            }
            if ($this->CBAufgabeDone->Text == 1 AND $this->CBAufgabeDone->Text != '') {
                $sql .= " AND (auf_done = " . $this->CBAufgabeDone->Text . " OR idta_protokoll_ergebnistype>2)";
            }
            if ($this->CBidta_organisation_art->Text >= 0 AND $this->CBidta_organisation_art->Text != '') {
                $sql .= " AND (idta_organisation_art = " . $this->CBidta_organisation_art->Text . ")";
            }

            $this->ActivityProtokollListe->VirtualItemCount = ProtokollDetailView::finder()->findBySQL($sqlcount . $sql)->idtm_protokoll_detail;

            $sql .= " ORDER BY prtdet_cdate DESC";
            $sql .= " LIMIT ".(($this->ActivityProtokollListe->CurrentPageIndex)*$this->ActivityProtokollListe->PageSize).", ".$this->ActivityProtokollListe->PageSize;

            $exeSQL = "SELECT * FROM vv_protokoll_detail WHERE idtm_protokoll_detail IN (".$sqlall . $sql.") LIMIT ".(($this->ActivityProtokollListe->CurrentPageIndex)*$this->ActivityProtokollListe->PageSize).", ".$this->ActivityProtokollListe->PageSize;
            $this->ActivityProtokollListe->DataSource = ProtokollDetailView::finder()->findAllBySQL($exeSQL);
            $this->ActivityProtokollListe->dataBind();

    }

    public function ActivityProtokollListe_PageIndexChanged($sender, $param) {
        $this->ActivityProtokollListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindActivityProtokollListe($this->edidtm_activity->Text);
    }

    public function renewActivityProtokollListe($sender, $param) {
        $this->bindActivityProtokollListe($this->edidtm_activity->Text);
    }

    protected function getSelected($key) {
        $item = ActivityRecord::finder()->findByPk($key);
        return $item;
    }

    public function DeleteButtonClicked($sender, $param) {
        $tempus = 'ed' . $this->primarykey;
        ActivityRecord::finder()->deleteAll('idtm_activity = ?', $this->$tempus->Text);
        PFDBActivityTools::rebuild_NestedInformation(0,1);
        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }

    public function SavedButtonClicked($sender, $param) {

        $tempus = 'ed' . $this->primarykey;

        if ($this->edactivity_edit_status->Text == '1') {
            $EditRecord = ActivityRecord::finder()->findByPK($this->$tempus->Text);
        } else {
            $EditRecord = new ActivityRecord();
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $EditRecord->$recordfield = date('Y-m-d', $this->$edrecordfield->Timestamp);
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $EditRecord->$recordfield = $this->$edrecordfield->Checked ? 1 : 0;
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $EditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $EditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $EditRecord->save();

        $idtm_activity = $EditRecord->idtm_activity;

        $this->initWindowLink($idtm_activity);
        $this->initPullDowns($idtm_activity);
        $this->initRelations($idtm_activity);

        //los aufgabos
        $this->Tedauf_id->Text = $idtm_activity;
        $this->Iedino_id->Text = $idtm_activity;

        $this->edidtm_activity->Text = $idtm_activity;

        if ($this->edactivity_edit_status->Text == '0') {
            //$idta_activity_type = $this->edidta_activity_type->Text;
            $act_name = $this->edact_name->Text;
            $this->getPage()->getClientScript()->registerEndScript('xinsert', "tree.insertNewChild(tree.getSelectedItemId()||0,$idtm_activity,'$act_name')");
            PFDBActivityTools::rebuild_NestedInformation(0,1);
        }

        $this->edactivity_edit_status->Text = '1';

        $this->edActivityActivitybindList($idtm_activity);
        $this->edActivityZielebindList();
        $this->edActivityInoutputbindList();

        $this->bindListInterface();
        $this->bindActivityProtokollListe($idtm_activity);

        $this->edparent_idtm_activity->Text = $EditRecord->parent_idtm_activity;
    }

    /*
     *
     * @param idtm_activity - The sender ID, new parent
     * @param idta_activity_type - the new elementtype
     */

    public function add_context_Activity($idtm_activity, $idta_activity_type) {
        $this->NewButtonClicked($sender, $param);
        $this->edparent_idtm_activity->Text = $idtm_activity;
        $this->edidta_activity_type->Text = $idta_activity_type;
    }

    public function NewButtonClicked($sender, $param) {

        $idtm_activity = 0;

        if ($sender->ID == "newChild") {
            $parent_idtm_activity = $this->edidtm_activity->Text;
        }
        $tempus = 'ed' . $this->primarykey;
        $monus = $this->primarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->hiddenfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d', time()));
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->timefields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->Text = '00:00';
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = 'ed' . $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        if ($sender->ID == "newChild") {
            $this->edparent_idtm_activity->Text = $parent_idtm_activity;
        }
        $this->initWindowLink($idtm_activity);
        $this->initPullDowns($idtm_activity);
        $this->initRelations($idtm_activity);

        //damit das auch als neuer interpretiert wird
        $this->edactivity_edit_status->Text = '0';

        //los aufgabos
        $this->Tedauf_id->Text = $idtm_activity;
        $this->Iedino_id->Text = $idtm_activity;

        $this->edActivityActivitybindList($idtm_activity);
        $this->edActivityZielebindList();
        $this->edActivityInoutputbindList();

        $this->bindListInterface();
        $this->edidtm_organisation->Text = $this->User->getUserOrgId($this->User->getUserId());
    }

    public function insertButtonClicked($sender, $param) {

        $EditRecord = new ActicityRecord();

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $EditRecord->$recordfield = date('Y-m-d', $this->$recordfield->Timestamp);
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $EditRecord->$recordfield = $this->$recordfield->Checked ? 1 : 0;
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $EditRecord->$recordfield = $this->$recordfield->Text;
        }

        foreach ($this->fields as $recordfield) {
            $EditRecord->$recordfield = $this->$recordfield->Text;
        }

        $EditRecord->save();
    }

    //ANFANG DER FUNKTIONEN FUER DIE LISTE AKTIVITAETEN
    public function removeActivityActivity($sender, $param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $deleteRecord = ActivityActivityRecord::finder();
        $deleteRecord->deleteByPk($param->Item->lstact_idta_activity_activity->Text);
        $this->edActivityActivitybindList($param->Item->lst_idtm_activity->Text);
    }

    public function removeActivityOrganisation($sender, $param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $deleteRecord = ActivityOrganisationRecord::finder();
        $deleteRecord->deleteByPk($param->Item->lstlink_idtm_activity_has_tm_organisation->Text);
        $this->initRelations($this->edidtm_activity->Text);
    }

    public function addActivityActivity() {

        $myRecord = new ActivityActivityRecord;

        $myRecord->idtm_activity = $this->edidtm_activity->Text;
        $myRecord->pre_idtm_activity = $this->ttedidtm_activity->Text;
        $myRecord->actact_minz = $this->edactact_minz->Text;
        $myRecord->actact_maxz = $this->edactact_maxz->Text;
        $myRecord->actact_type = $this->edactact_type->Text;

        $myRecord->save();
        $this->edActivityActivitybindList($this->edidtm_activity->Text);
    }

    public function addActivityOrganisation($sender, $param) {
        if ($this->edidta_activity_type->Text == 2) {
            $myRecord = new ActivityOrganisationRecord;
            $myRecord->idtm_activity = $this->edidtm_activity->Text;
            $myRecord->idtm_organisation = $this->edlinkidtm_organisation->Text;
            $myRecord->org_stundensatz = $this->edlinkorg_stundensatz->Text;
            $myRecord->save();
            $this->initRelations($this->edidtm_activity->Text);
        }
    }

    private function edActivityActivitybindList($idtm_activity) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext";
        $criteria->Parameters[':suchtext'] = $idtm_activity;
        $criteria->OrdersBy["idtm_activity"] = 'asc';

        $this->ActivityActivityListe->DataSource = ActivityActivityView::finder()->findAll($criteria);
        $this->ActivityActivityListe->dataBind();
    }

    public function edActivityActivity_PageIndexChanged($sender, $param) {
        $this->ActivityActivityListe->CurrentPageIndex = $param->NewPageIndex;
        $this->edActivityActivitybindList();
    }

    public function edActivityOrganisation_PageIndexChanged($sender, $param) {
        $this->edActivityOrganisationListe->CurrentPageIndex = $param->NewPageIndex;
        //$this->edActivityOrganisationbindList();
    }

    public function cmd_chooseActivityActivity($sender, $param) {
        $item = $param->Item;
        $this->idtm_activity_label->Text = $item->lst_act_name->Text;
        $this->idtm_activity->Data = $item->lst_idtm_activity->Text;
        $this->bindList();
    }

    public function edcmd_chooseActivityActivity($sender, $param) {
        $item = $param->Item;
        $this->edidtm_activity_label->Text = $item->edlst_act_name->Text;
        $this->edidtm_activity->Data = $item->edlst_idtm_activity->Text;
        $this->bindList();
    }

    //ENDE DER FUNKTIONEN FUER DIE LISTE AKTIVITAETEN
    //ANFANG DER FUNKTIONEN FUER DIE LISTE ZIELE

    public function removeActivityZiele($sender, $param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $deleteRecord = ActivityZieleRecord::finder();
        $deleteRecord->deleteByPk($param->Item->lstact_idtm_activity_has_tt_ziele->Text);
        $this->edActivityZielebindList();
    }

    public function addActivityZiele() {
        $myRecord = new ActivityZieleRecord;

        $myRecord->idtm_activity = $this->edidtm_activity->Text;
        $myRecord->idtt_ziele = $this->ttedidtt_ziele->Text;

        $myRecord->save();
        $this->edActivityZielebindList();
    }

    private function ActivityZielebindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->idtm_activity->Value;
        $criteria->OrdersBy["idtm_activity"] = 'asc';

        $this->ActivityZieleListe->DataSource = ActivityZieleView::finder()->findAll($criteria);
        $this->ActivityZieleListe->dataBind();
    }

    private function edActivityZielebindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->edidtm_activity->Text;
        $criteria->OrdersBy["idtm_activity"] = 'asc';

        $this->ActivityZieleListe->DataSource = ActivityZieleView::finder()->findAll($criteria);
        $this->ActivityZieleListe->dataBind();
    }

    public function edActivityZiele_PageIndexChanged($sender, $param) {
        $this->ActivityZieleListe->CurrentPageIndex = $param->NewPageIndex;
        $this->edActivityZielebindList();
    }

    //ENDE DER FUNKTIONEN FUER DIE LISTE ZIELE

    /* here comes the part for the tasks */
    /* here comes the part for the tasks */
    /* here comes the part for the tasks */
    /* here comes the part for the tasks */

    public function DetailGroupDone($sender,$param){
        $tmpstartdate = new DateTime();
        $AufgabenRecord = AufgabenRecord::finder()->find('auf_tabelle = ? AND auf_id = ?','tm_protokoll_detail',$param->CallbackParameter);
        $AufgabenRecord->auf_done=1;
        $AufgabenRecord->auf_ddate = $tmpstartdate->format("Y-m-d");
        $AufgabenRecord->save();
        $this->bindActivityProtokollListe($this->edidtm_activity->Text);
    }

    /* here comes the part for the INTERFACE */
    /* here comes the part for the INTERFACE */
    /* here comes the part for the INTERFACE */
    /* here comes the part for the INTERFACE */

    private $Iprimarykey = "idtm_inoutput";
    private $Ifields = array("ino_descr", "ino_name", "ino_tabelle", "ino_id", "idta_inoutput_type");
    private $Idatfields = array();
    private $Ihiddenfields = array();
    private $Iboolfields = array();

    public function bindListInterface() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext1 AND ino_link_type=0";
        $criteria->Parameters[':suchtext1'] = $this->edidtm_activity->Text;

        $this->InterfaceListe->DataSource = ActivityInoutputView::finder()->findAll($criteria);
        $this->InterfaceListe->dataBind();
    }

    public function interfaceList_PageIndexChanged($sender, $param) {
        $this->InterfaceListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListInterface();
    }

    public function load_interface($sender, $param) {

        $item = $param->Item;
        $myitem = InoutputRecord::finder()->findByPK($item->lst_idtm_inoutput->Text);

        $tempus = 'Ied' . $this->Iprimarykey;
        $monus = $this->Iprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->Ihiddenfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->Idatfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->Iboolfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->Ifields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->Iedino_edit_status->Text = 1;
    }

    public function IDeleteButtonClicked($sender, $param) {
        $tempus = 'Ied' . $this->Iprimarykey;
        $IEditRecord = InoutputRecord::finder()->findByPK($this->$tempus->Text);
        $IEditRecord->delete();
        $this->bindListInterface();
        $this->INewButtonClicked($sender, $param);
    }

    public function ISavedButtonClicked($sender, $param) {

        $tempus = 'Ied' . $this->Iprimarykey;

        if ($this->Iedino_edit_status->Text == '1') {
            $IEditRecord = InoutputRecord::finder()->findByPK($this->$tempus->Text);
        } else {
            $IEditRecord = new InoutputRecord;
            //here comes the part, where the relation tabel is filled in
            /* $InoutActivityRecord = new InoutputActivityRecord;
              $InoutActivityRecord->idtm_activity = $this->edidtm_activity->Text;
              $InoutActivityRecord->idtm_inoutput = $this->Iedidtm_inoutput->Text;
              $InoutActivityRecord->ino_link_type = 0;
              $InoutActivityRecord->save(); */
        }

        //HIDDEN
        foreach ($this->Ihiddenfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $IEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->Idatfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $IEditRecord->$recordfield = date('Y-m-d', $this->$edrecordfield->Timestamp);
        }

        //BOOL
        foreach ($this->Iboolfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $IEditRecord->$recordfield = $this->$edrecordfield->Checked ? 1 : 0;
        }

        foreach ($this->Ifields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $IEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $IEditRecord->save();

        if ($this->Iedino_edit_status->Text == '1') {
            //kommt noch
        } else {
            //here comes the part, where the relation tabel is filled in
            $InoutActivityRecord = new InoutputActivityRecord;
            $InoutActivityRecord->idtm_activity = $this->edidtm_activity->Text;
            $InoutActivityRecord->idtm_inoutput = $IEditRecord->idtm_inoutput;
            $InoutActivityRecord->ino_link_type = 0;
            $InoutActivityRecord->save();
        }

        $this->bindListInterface();
    }

    public function INewButtonClicked($sender, $param) {

        $myidea = $this->Iedino_id->Text;

        $tempus = 'Ied' . $this->Iprimarykey;
        $monus = $this->Iprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->Ihiddenfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->Idatfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d', time()));
        }

        //BOOL
        foreach ($this->Iboolfields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->Ifields as $recordfield) {
            $edrecordfield = 'Ied' . $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->Iedino_edit_status->Text = '0';
        $this->Iedino_tabelle->Text = "tm_activity";
        $this->Iedino_id->Text = $myidea;
    }

    //ENDE DER INTERFACE
    //ENDE DER INTERFACE
    //ENDE DER INTERFACE
    //ANFANG DER FUNKTIONEN FUER DIE LISTE INPUTS

    public function removeActivityInoutput($sender, $param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $deleteRecord = InoutputActivityRecord::finder();
        $deleteRecord->deleteByPk($param->Item->lstact_idtm_activity_has_tm_inoutput->Text);
        $this->edActivityInoutputbindList();
    }

    public function addActivityInoutput() {

        $myRecord = new InoutputActivityRecord;

        $myRecord->idtm_activity = $this->edidtm_activity->Text;
        $myRecord->idtm_inoutput = $this->ttedidtm_inoutput->Text;
        $myRecord->ino_link_type = 1;

        $myRecord->save();
        $this->edActivityInoutputbindList();
    }

    private function ActivityInoutputbindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext AND ino_link_type = 1";
        $criteria->Parameters[':suchtext'] = $this->idtm_activity->Value;
        $criteria->OrdersBy["idtm_activity"] = 'asc';

        $this->ActivityInoutputListe->DataSource = ActivityInoutputView::finder()->findAll($criteria);
        $this->ActivityInoutputListe->dataBind();
    }

    private function edActivityInoutputbindList() {

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext AND ino_link_type = 1";
        $criteria->Parameters[':suchtext'] = $this->edidtm_activity->Text;
        $criteria->OrdersBy["idtm_activity"] = 'asc';

        $this->ActivityInoutputListe->DataSource = ActivityInoutputView::finder()->findAll($criteria);
        $this->ActivityInoutputListe->dataBind();
    }

    public function edActivityInoutput_PageIndexChanged($sender, $param) {
        $this->ActivityInoutputListe->CurrentPageIndex = $param->NewPageIndex;
        $this->edActivityInoutputbindList();
    }

    //ENDE DER FUNKTIONEN FUER DIE LISTE INPUTS
    //ANFANG DER FUNKTIONEN FUER DIE LISTE Ressource

    public function RessourceNewButtonClicked($sender, $param) {
        $this->ttidtm_ressource->Text = 0;
        $this->ttauf_res_dauer->Text = 0;
        $this->ttidtm_aufgabe_ressource->Text = 0;
    }

    public function removeRessource($sender, $param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $deleteRecord = AufgabeRessourceRecord::finder();
        $deleteRecord->deleteByPk($this->ttidtm_aufgabe_ressource->Text);
        $this->RessourcebindList();
    }

    public function loadRessource($sender, $param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $LoadRecord = AufgabeRessourceRecord::finder();
        $MyRecord = $LoadRecord->findByPk($param->Item->lstpart_idtm_aufgabe_ressource->Text);
        $this->ttidtm_ressource->Text = $MyRecord->idtm_ressource;
        $this->ttauf_res_dauer->Text = $MyRecord->auf_res_dauer;
        $this->ttidtm_aufgabe_ressource->Text = $MyRecord->idtm_aufgabe_ressource;
    }

    public function addRessource() {

        if ($this->ttidtm_aufgabe_ressource->Text > 0) {
            $myRecord = AufgabeRessourceRecord::finder()->findByPK($this->ttidtm_aufgabe_ressource->Text);
        } else {
            $myRecord = new AufgabeRessourceRecord;
        }

        $myRecord->idtm_aufgabe = $this->Aedidtm_aufgaben->Text;
        $myRecord->idtm_ressource = $this->ttidtm_ressource->Text;
        $myRecord->auf_res_dauer = $this->ttauf_res_dauer->Text;

        $myRecord->save();
        $this->RessourcebindList();
    }

    private function RessourcebindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_aufgabe = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->Aedidtm_aufgaben->Text;
        $criteria->OrdersBy["idtm_ressource"] = 'asc';

        $this->RessourceListe->DataSource = AufgabeRessourceView::finder()->findAll($criteria);
        $this->RessourceListe->dataBind();
    }

    public function ressource_PageIndexChanged($sender, $param) {
        $this->RessourceListe->CurrentPageIndex = $param->NewPageIndex;
        $this->RessourcebindList();
    }

    //ENDE DER FUNKTIONEN FUER DIE LISTE Ressource

    public function suggestOrganisation($sender, $param) {
        // Get the token
        $token = $param->getToken();
        // Sender is the Suggestions repeater
        $mySQL = "SELECT idtm_organisation,org_name,org_vorname FROM tm_organisation WHERE org_name LIKE '%" . $token . "%'";
        $sender->DataSource = PFH::convertdbObjectSuggest(TActiveRecord::finder('OrganisationRecord')->findAllBySQL($mySQL), array('idtm_organisation', 'org_name', 'org_vorname'));
        $sender->dataBind();
    }

    public function suggestionSelectedOne($sender, $param) {
        $id = $sender->Suggestions->DataKeys[$param->selectedIndex];
        $this->edidtm_organisation->Text = $id;
    }

    //the fields for the BerechtigungRecord
    private $XXRprimarykey = "idxx_berechtigung";
    private $XXRfields = array("xx_id", "xx_modul", "idtm_user");
    private $XXRdatfields = array();
    private $XXRtimefields = array();
    private $XXRhiddenfields = array();
    private $XXRboolfields = array("xx_read", "xx_write", "xx_create", "xx_delete");

    private function loadBerechtigung($sender='', $param='') {
        $Criteria = new TActiveRecordCriteria();
        $Criteria->Condition = "xx_id = :idtm_activity AND xx_modul = :modul";
        $Criteria->Parameters[':idtm_activity'] = $this->edidtm_activity->Text;
        $Criteria->Parameters[':modul'] = "tm_activity";
        $this->lstBerechtigung->DataSource = BerechtigungRecord::finder()->findAll($Criteria);
        $this->lstBerechtigung->dataBind();
    }

    public function editlstBerechtigung($sender, $param) {
        $item = $param->Item;
        $myitem = BerechtigungRecord::finder()->findByPK($item->lst_idxx_berechtigung->Text);

        $monus = $this->XXRprimarykey;
        $this->$monus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $this->$recordfield->setText($myitem->$recordfield);
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $this->$recordfield->setDate($myitem->$recordfield);
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $this->$recordfield->setChecked($myitem->$recordfield);
        }
        //TIME
        foreach ($this->XXRtimefields as $recordfield) {
            $my_time = explode(':', $myitem->$recordfield);
            $my_time_text = $my_time[0] . ':' . $my_time[1];
            $this->$recordfield->Text = $my_time_text;
        }
        //NON DATUM
        foreach ($this->XXRfields as $recordfield) {
            $this->$recordfield->Text = $myitem->$recordfield;
        }
        $this->berechtigung_edit_status->Text = 1;
        $this->loadberechtigung();
    }

    public function XXRDeleteClicked($sender, $param) {
        $Record = BerechtigungRecord::finder()->findByPK($this->{$this->XXRprimarykey}->Text);
        $Record->delete();
        $this->loadBerechtigung();
        $this->XXRNewClicked($sender, $param);
    }

    public function lstBerechtigung_PageIndexChanged($sender, $param) {
        $this->lstBerechtigung->CurrentPageIndex = $param->NewPageIndex;
        $this->loadBerechtigung();
    }

    public function XXRNewClicked($sender, $param) {
        $monus = $this->XXRprimarykey;
        $this->$monus->Text = '0';

        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $this->$recordfield->setValue('0');
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $this->$recordfield->setDate(date('Y-m-d', time()));
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $this->$recordfield->setChecked(0);
        }
        //NON DATUM
        foreach ($this->XXRtimefields as $recordfield) {
            $this->$recordfield->Text = '00:00';
        }
        //NON DATUM
        foreach ($this->XXRfields as $recordfield) {
            $this->$recordfield->Text = '0';
        }
        $this->xx_modul->Text = "tm_activity";
        $this->xx_id->Text = $this->edidtm_activity->Text;
        $this->berechtigung_edit_status->Text = '0';
    }

    public function XXRSaveClicked($sender, $param) {
        if ($this->berechtigung_edit_status->Text == '1') {
            $BREditRecord = BerechtigungRecord::finder()->findByPK($this->{$this->XXRprimarykey}->Text);
        } else {
            $BREditRecord = new BerechtigungRecord;
        }
        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Value;
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $BREditRecord->$recordfield = date('Y-m-d', $this->$recordfield->Timestamp);
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Checked ? 1 : 0;
        }
        foreach ($this->XXRtimefields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Text;
        }
        foreach ($this->XXRfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Text;
        }
        $BREditRecord->save();
        $this->loadBerechtigung();
    }

    //load berechtigungen ende -------------------------------------------------------------

    public function lstCCProtokollRepeaterEdit($sender, $param) {
        $sender->EditItemIndex = $param->Item->ItemIndex;
        $this->bindActivityProtokollListe($this->page->edidtm_activity->Text);
    }

    public function lstCCProtokollRepeaterCancel($sender, $param) {
        $sender->SelectedItemIndex = -1;
        $sender->EditItemIndex = -1;
        $this->bindActivityProtokollListe($this->page->edidtm_activity->Text);
    }

    public function lstCCProtokollRepeaterSave($sender, $param) {
        $item = $param->Item;
        $RCEditRecord = ProtokollDetailRecord::finder()->findByPK($item->idtm_protokoll_detail->Text);

        $RCEditRecord->prtdet_descr = $item->prtdet_descr->Text;
        $RCEditRecord->prtdet_topic = $item->prtdet_topic->Text;
        $RCEditRecord->prtdet_wvl = $item->prtdet_wvl->Checked ? 1 : 0;
        $RCEditRecord->idtm_protokoll = $item->idtm_protokoll->Text;

        $RCEditRecord->idta_protokoll_ergebnistype = $item->idta_protokoll_ergebnistype->Text;
        $RCEditRecord->idtm_activity = $item->idtm_activity->Text;

        $RCEditRecord->save();

        $sender->EditItemIndex = -1;
        $this->bindActivityProtokollListe($this->page->edidtm_activity->Text);
    }

    /*
     * Generation der Statistikbilder
     * @Author: Philipp Frenzel
     * @Kontakt: pf@prologiq.de
     */

    private function generateZeitVerlaufImage($idtm_activity=0) {

        if($idtm_activity>0){
            $myActInSQL = "(act_lft BETWEEN " . ActivityRecord::finder()->findByPK($idtm_activity)->act_lft . " AND " . ActivityRecord::finder()->findByPK($idtm_activity)->act_rgt . ")";

            $xdata1 = array();
            $xdata2 = array();
            $ytitle = array("Stunden");
            $title = array("Monat");
            $legend = array("verbar", "nicht verbar");

            $ii = 0;

            $sql = "SELECT DATE_FORMAT(zeit_date,'%Y%m') AS zeit_date, SUM(CASE WHEN idta_kosten_status='1' THEN zeit_dauer ELSE 0 END) AS zeit_dauer, SUM(CASE WHEN idta_kosten_status='2' OR idta_kosten_status='3' THEN zeit_dauer ELSE 0 END) AS zeit_break FROM tm_zeiterfassung INNER JOIN tm_activity ON tm_zeiterfassung.idtm_activity = tm_activity.idtm_activity WHERE " . $myActInSQL . " AND YEAR(zeit_date) = YEAR(NOW()) GROUP BY DATE_FORMAT(zeit_date,'%Y-%m') LIMIT 0, 15 ";
            $ActiveRecord = ZeiterfassungRecord::finder()->findAllBySQL($sql);
            if (is_array($ActiveRecord)) {
                foreach ($ActiveRecord as $DetailRecord) {
                    $xdata1[] = "[".$DetailRecord->zeit_date.",".$DetailRecord->zeit_dauer."]";
                    $xdata2[] = "[".$DetailRecord->zeit_date.",".$DetailRecord->zeit_break."]";
                    $ii++;
                    if ($ii > 12) {
                        break;
                    }
                }

                unset($ActiveRecord);

                if(count($xdata1)>1){
                    $xdata1 = implode(',', $xdata1);
                    $xdata2 = implode(',', $xdata2);
                }   
            }

            if(count($xdata1)>=1){
                $this->getPage()->getClientScript()->registerEndScript('xzvi', "drawPFMultiChart('ZeitVerlaufImage',new Array($xdata1),new Array($xdata2),'line')");
            }else{
                $xdata1[]="[0,'no Data']";
                $xdata2[]="[0,'no Data']";
                $this->getPage()->getClientScript()->registerEndScript('xzvi', "drawPFMultiChart('ZeitVerlaufImage',new Array($xdata1),new Array($xdata2),'line')");
            }

            unset($xdata1, $xdata2);

            $this->generateActivityCookieImage($idtm_activity,$myActInSQL);
            $this->generateActivityTypeCookieImage($idtm_activity,$myActInSQL);
            $this->generateActivityVerlaufImage($idtm_activity,$myActInSQL);
        }
    }

    private function generateActivityVerlaufImage($idtm_activity,$myActInSQL) {

        $xdata = array();
        $ytitle = array("Anzahl");
        $title = array("Woche");

        $ii = 0;

        $sql = "SELECT DATE_FORMAT(auf_cdate,'W %u') AS auf_cdate, count(idtm_aufgaben) AS idtm_aufgaben FROM vv_protokoll_detail_aufgabe WHERE " . $myActInSQL . " GROUP BY DATE_FORMAT(auf_cdate,'%Y-%u') ORDER BY auf_cdate ASC";
        $ActiveRecord = ProtokollDetailAufgabeView::finder()->findAllBySQL($sql);
        if (is_array($ActiveRecord)) {
            foreach ($ActiveRecord as $DetailRecord) {
                if($DetailRecord->auf_cdate!=''){
                    $xdata[] = "['".$DetailRecord->auf_cdate."',".$DetailRecord->idtm_aufgaben."]";
                    $ii++;
                }
                if ($ii > 52) {
                    break;
                }
            }

            unset($ActiveRecord);
            if(count($xdata)>1){
                $xdata = implode(',', $xdata);
            }
       
            if(count($xdata)>=1){
                $this->getPage()->getClientScript()->registerEndScript('xvi', "drawPFChart('ActivityVerlaufImage',new Array($xdata),'line')");
            }else{
                $xdata[]="[0,'no Data']";
                $this->getPage()->getClientScript()->registerEndScript('xvi', "drawPFChart('ActivityVerlaufImage',new Array($xdata),'line')");
            }
            
            unset($xdata);
        }
    }

    private function generateActivityCookieImage($idtm_activity,$myActInSQL) {

        $xdata = array();

        $ii = 0;
        $taskStati=array(0=>"offen","geschl","slot","Info","sonstiges");

        $sql = "SELECT auf_done, count(idtm_aufgaben) AS idtm_aufgaben FROM vv_protokoll_detail_aufgabe WHERE " . $myActInSQL . " AND idta_protokoll_ergebnistype < 3 GROUP BY auf_done ";
        //print_r($sql);
        $ActiveRecord = ProtokollDetailAufgabeView::finder()->findAllBySQL($sql);
        if (is_array($ActiveRecord)) {
            foreach ($ActiveRecord as $DetailRecord) {
                if($DetailRecord->idta_protokoll_ergebnistype>2){
                    $xdata[] = "[".$DetailRecord->idtm_aufgaben.",'".$taskStati[$DetailRecord->idta_protokoll_ergebnistype]."']";
                }else{
                    $xdata[] = "[".$DetailRecord->idtm_aufgaben.",'".$taskStati[$DetailRecord->auf_done]."']";
                }
            }
            unset($ActiveRecord);
            if(count($xdata)>1){
                $xdata = implode(',', $xdata);
            }
            if(count($xdata)>=1){
                $this->getPage()->getClientScript()->registerEndScript('xci', "drawPFChart('ActivityCookieImage',new Array($xdata),'pie')");
            }else{
                $xdata[]="[0,'no Data']";
                $this->getPage()->getClientScript()->registerEndScript('xci', "drawPFChart('ActivityCookieImage',new Array($xdata),'pie')");
            }
            unset($xdata);
        }
    }

     private function generateActivityTypeCookieImage($idtm_activity,$myActInSQL) {

        $xdata = array();

        $ii = 0;
        $taskStati=array(1=>"Beschluss","Auftrag","Info","sonstiges");

        $sql = "SELECT idta_protokoll_ergebnistype, count(idtm_aufgaben) AS idtm_aufgaben FROM vv_protokoll_detail_aufgabe WHERE " . $myActInSQL . " GROUP BY idta_protokoll_ergebnistype ORDER BY idta_protokoll_ergebnistype ASC";
        //print_r($sql);
        $ActiveRecord = ProtokollDetailAufgabeView::finder()->findAllBySQL($sql);
        if (is_array($ActiveRecord)) {
            foreach ($ActiveRecord as $DetailRecord) {
                $xdata[] = "[".$DetailRecord->idtm_aufgaben.",'".$taskStati[$DetailRecord->idta_protokoll_ergebnistype]."']";
            }
            unset($ActiveRecord);

            if(count($xdata)>1){
                $xdata = implode(',', $xdata);
            }

            if(count($xdata)>=1){
                $this->getPage()->getClientScript()->registerEndScript('xtci', "drawPFChart('ActivityTypeCookieImage',new Array($xdata),'pie')");
            }else{
                $xdata[]="[0,'no Data']";
                $this->getPage()->getClientScript()->registerEndScript('xtci', "drawPFChart('ActivityTypeCookieImage',new Array($xdata),'pie')");
            }
            unset($xdata);
        }
    }

}
?>