<?php

class prtview extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    private $primarykey = "idtm_protokoll";
    private $MASTERRECORD = '';
    private $finder = '';
    private $fields = array("idtm_organisation","prt_name","prt_location","idtm_termin");
    private $listfields = array("idta_protokoll_type");
    private $datfields = array("prt_cdate");
    private $hiddenfields = array();
    private $boolfields = array();
    private $exitURL = 'protokoll.prtworkspace';

    private $auf_done = array(0=>"offen","erledigt","alle");

    public $StatusArray = array( 1=>"offen", "Definition","Umsetzung","Test","Live","Produktiv");

    public $ii = 1;
    public $ff = 1;
    public $alphabet;

    public function onLoad($param) {

        date_default_timezone_set('Europe/Berlin');
        parent::onLoad($param);

        //Globale definition für dieses Dokument
        $this->finder = ProtokollRecord::finder();
        $this->MASTERRECORD = new ProtokollRecord;

        if(!$this->isPostBack && !$this->isCallback) {
            $this->alphabet = range('a','z');
            $this->initPullDowns();
            $this->load_prtdetailsgroup();           
        }
    }

    public function initPullDowns() {
        $this->edidta_protokoll_type->DataSource=PFH::build_SQLPullDown(ProtokollTypeRecord::finder(),"ta_protokoll_type",array("idta_protokoll_type","prt_type_name"));
        $this->edidta_protokoll_type->dataBind();

        //hier checken wir, wieviele schritte noch den gleichen Vater haben
        $this->edbindList();
        $this->fillValues($this->getSelected($this->Request[$this->primarykey]));

        $this->CCProtokollDetailGroupListPageSize->DataSource=array(5=>"5",10=>"10",15=>"15",20=>"20");
        $this->CCProtokollDetailGroupListPageSize->dataBind();
        $this->CCProtokollDetailGroupListPageSize->Text="5";

        $this->edParticipantbindList();
        $this->edTerminParticipantbindList();

        $this->CBAufgabeDone->DataSource = $this->auf_done;
        $this->CBAufgabeDone->dataBind();
    }


    public function edbuildZielePullDown() {
        foreach($this->edProtokollDetailGroupList->Items as $Gitem) {
            //if($Gitem->ItemType==='Item' || $Gitem->ItemType==='AlternatingItem'){
            foreach($Gitem->edProtokollDetailList->Items as $item) {
                $item->edidtm_activity->DataSource=PFH::build_SQLPullDown(TTZieleRecord::finder(),"tt_ziele",array("idtm_activity","ttzie_name"));
                $item->edidtm_activity->dataBind();
                $item->edidta_protokoll_ergebnistype->DataSource=PFH::build_SQLPullDown(ProtokollErgebnistypeRecord::finder(),"ta_protokoll_ergebnistype",array("idta_protokoll_ergebnistype","prt_ergtype_name"));
                $item->edidta_protokoll_ergebnistype->dataBind();
                $item->Aedidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type = 4");
                $item->Aedidtm_organisation->dataBind();
            }
            //}
        }
    }

     public function edProtokollDetailGroupList_PageIndexChanged($sender,$param) {
        $this->edProtokollDetailGroupList->CurrentPageIndex = $param->NewPageIndex;
        $this->load_prtdetailsgroup($sender,$param);
    }

    public function load_prtdetailsgroup() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idtm_protokoll = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->edidtm_protokoll->Text;
        $criteria->OrdersBy['idtm_protokoll_detail_group']='DESC';

        $this->edProtokollDetailGroupList->VirtualItemCount=ProtokollDetailGroupRecord::finder()->count($criteria);

        //->page->AufgabenContainerOrganisation
        $criteria->setLimit($this->CCProtokollDetailGroupListPageSize->Text);

        if($this->CCProtokollDetailGroupListPageSize->Text<=1){
            $this->edProtokollDetailGroupList->PageSize = 5;
        }else{
            $this->edProtokollDetailGroupList->PageSize=1*$this->CCProtokollDetailGroupListPageSize->Text;
        }
        $criteria->setOffset($this->CCProtokollDetailGroupListPageSize->Text * $this->edProtokollDetailGroupList->CurrentPageIndex);

        $this->edProtokollDetailGroupList->DataSource=ProtokollDetailGroupRecord::finder()->findAll($criteria);
        $this->edProtokollDetailGroupList->dataBind();
    }

    public function load_prtdetails($sender,$param) {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
            $mySQL = "SELECT idtm_protokoll_detail FROM vv_protokoll_detail_aufgabe WHERE idtm_protokoll_detail_group = ".$item->Data->idtm_protokoll_detail_group;
            if($this->CBAufgabeDone->Text == 0 AND $this->CBAufgabeDone->Text != ''){
                $mySQL .=" AND (auf_done = ".$this->CBAufgabeDone->Text." AND idta_protokoll_ergebnistype<3)";
            }
            $execSQL = "SELECT * FROM vv_protokoll_detail WHERE idtm_protokoll_detail IN (".$mySQL.") ORDER BY idtm_protokoll_detail DESC";
            $item->CCPrtRep->DataSource=ProtokollDetailView::finder()->findAllBySQL($execSQL);
            $item->CCPrtRep->dataBind();
        }
    }

    public function propertyAction($sender,$param) {
        if($param->CommandName==='add') {
            $newPrtDetailGroupRecord = new ProtokollDetailGroupRecord;
            $newPrtDetailGroupRecord->idtm_protokoll = $this->edidtm_protokoll->Data;
            $newPrtDetailGroupRecord->save();
            $newPrtDetailGroupRecord->idtm_protokoll_detail_group = $newPrtDetailGroupRecord->idta_protokoll_detail_group;
            $newPrtDetailGroupRecord->save();

            $newprtdetRecord = new ProtokollDetailRecord;
            $newprtdetRecord->idtm_protokoll_detail_group = $newPrtDetailGroupRecord->idta_protokoll_detail_group;
            $newprtdetRecord->idta_protokoll_ergebnistype = 3;
            $newprtdetRecord->idtm_protokoll = $this->edidtm_protokoll->Data;
            $newprtdetRecord->idtm_activity = ActivityRecord::finder()->findByparent_idtm_activity('0')->idtm_activity;
            $newprtdetRecord->prtdet_wvl = 0;
            $newprtdetRecord->save();
            
            $newaufgabenRecord = new AufgabenRecord;
            $newaufgabenRecord->auf_id = $newprtdetRecord->idtm_protokoll_detail;
            $newaufgabenRecord->auf_tabelle = 'tm_protokoll_detail';
            $newaufgabenRecord->idtm_organisation = 1;
            $newaufgabenRecord->auf_deleted = 0;
            $newaufgabenRecord->save();
        }
        else if($param->CommandName==='remove') {
            $deleteRecord = ProtokollDetailRecord::finder();
            $deleteRecord->deleteByPk($param->CommandParameter);
        }else if($param->CommandName==='save') {
            $this->saveList();
        }
        $this->load_prtdetailsgroup();
    }

    public function addNewDetailGroup($sender,$param) {
        $newprtdetRecord = new ProtokollDetailRecord;
        $newprtdetRecord->idtm_protokoll_detail_group = $param->CallbackParameter;
        $newprtdetRecord->idta_protokoll_ergebnistype = 3;
        $newprtdetRecord->idtm_protokoll = $this->edidtm_protokoll->Data;
        $CheckStart = TerminRecord::finder()->findByPK($this->page->edidtm_termin->Text)->idtm_activity;
        if($CheckStart>0){
            $newprtdetRecord->idtm_activity = $CheckStart;
        }else{
            $newprtdetRecord->idtm_activity = ActivityRecord::finder()->findByparent_idtm_activity(0)->idtm_activity;
        }
        $newprtdetRecord->prtdet_wvl = 0;
        $newprtdetRecord->save();

        $newaufgabenRecord = new AufgabenRecord;
        $newaufgabenRecord->auf_id = $newprtdetRecord->idtm_protokoll_detail;
        $newaufgabenRecord->auf_tabelle = 'tm_protokoll_detail';
        $newaufgabenRecord->auf_name = 'Protokollpunkt';
        $newaufgabenRecord->idtm_organisation = 1;
        $newaufgabenRecord->auf_deleted = 0;
        $newaufgabenRecord->save();
        $this->load_prtdetailsgroup();
    }

    public function DetailGroupDone($sender,$param){
        $tmpstartdate = new DateTime();
        $AufgabenRecord = AufgabenRecord::finder()->find('auf_tabelle = ? AND auf_id = ?','tm_protokoll_detail',$param->CallbackParameter);
        $AufgabenRecord->auf_done=1;
        $AufgabenRecord->auf_ddate = $tmpstartdate->format("Y-m-d");
        $AufgabenRecord->save();
        $this->load_prtdetailsgroup();
    }

    public function removeDetailGroup($sender,$param) {
        $deleteRecord = ProtokollDetailRecord::finder();
        $deleteRecord->deleteByidtm_protokoll_detail($param->CallbackParameter);
        $deleteAufgabe = AufgabenRecord::finder();
        $deleteAufgabe->deleteAll('auf_tabelle = ? AND auf_id = ?','tm_protokoll_detail',$param->CallbackParameter);
        $this->load_prtdetailsgroup();
    }


    protected function fillValues($item) {

        $tempus = 'ed'.$this->primarykey;
        $monus = $this->primarykey;

        $this->$tempus->Text = $item->$monus;

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $this->$edrecordfield->setDate($item->$recordfield);
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $this->$edrecordfield->setChecked($item->$recordfield);
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $this->$edrecordfield->setSelectedValue($item->$recordfield);
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $this->$edrecordfield->Text = $item->$recordfield;
        }

    }

    protected function getSelected($key) {
        $item = $this->finder->findByPk($key);
        return $item;
    }

    public function deleteButtonClicked($sender,$param) {
        $tempus= 'ed'.$this->primarykey;
        $deleteRecord = ProtokollRecord::finder();
        $deleteRecord->deleteAll('idtm_protokoll = ?',$this->$tempus->Text);
        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }


    public function editButtonClicked($sender,$param) {

        if($this->edidtm_protokoll->Text==""){
            $this->insertButtonClicked($sender, $param);
        }else {
            $tempus='ed'.$this->primarykey;

            $EditRecord = $this->finder->findByPK($this->$tempus->Text);

            //DATUM
            foreach ($this->datfields as $recordfield) {
                $edrecordfield = 'ed'.$recordfield;
                $EditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
            }

            //BOOL
            foreach ($this->boolfields as $recordfield) {
                $edrecordfield = 'ed'.$recordfield;
                $EditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
            }

            //LIST
            foreach ($this->listfields as $recordfield) {
                $edrecordfield = 'ed'.$recordfield;
                $EditRecord->$recordfield = $this->$edrecordfield->Text;
            }

            //NON DATUM
            foreach ($this->fields as $recordfield) {
                $edrecordfield = 'ed'.$recordfield;
                $EditRecord->$recordfield = $this->$edrecordfield->Text;
            }

            $EditRecord->save();

        }
    }

    public function insertButtonClicked($sender,$param) {

        $EditRecord = $this->MASTERRECORD;
        $Prefix="ed";
        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = $Prefix.$recordfield;
            $EditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = $Prefix.$recordfield;
            $EditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = $Prefix.$recordfield;
            $EditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->fields as $recordfield) {
            $edrecordfield = $Prefix.$recordfield;
            $EditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $EditRecord->save();
        $this->edidtm_protokoll->Text = $EditRecord->idtm_protokoll;
    }

    //ANFANG DER FUNKTIONEN FUER DIE LISTE ACTIVITY
    private function edbindList() {
        $SQL1 = "SELECT tm_termin.* FROM tm_termin ORDER BY ter_startdate DESC";
        $SQL1 .= " LIMIT ".$this->edActListe->PageSize * $this->edActListe->CurrentPageIndex.",".$this->edActListe->PageSize;

        $this->edActListe->DataKeyField = 'idtm_termin';
        $validate = PFH::checkCountStatement(TerminRecord::finder()->findBySql($SQL1));
        if($validate) {
            $this->edActListe->VirtualItemCount = TerminRecord::finder()->findBySql($SQL1)->Count();
        }else {
            $this->edActListe->VirtualItemCount = 0;
        }
        $this->edActListe->DataSource=TerminRecord::finder()->findAllBySql($SQL1);
        $this->edActListe->dataBind();
    }

    public function eddtgList_PageIndexChanged($sender,$param) {
        $this->edActListe->CurrentPageIndex = $param->NewPageIndex;
        $this->edbindList();
    }

    public function edcmd_chooseActivity($sender,$param) {
        $item=$param->Item;
        $this->edidtm_termin_label->Text=$item->edlst_ter_betreff->Text;
        $this->edprt_name->Text=$item->edlst_ter_betreff->Text;
        $this->edprt_location->Text=$item->edlst_ter_ort->Text;
        $this->edprt_cdate->Text=$item->edlst_ter_startdate->Text;
        $this->edidtm_termin->Data=$item->edlst_idtm_termin->Text;
        //$this->edbindList();
        $this->edTerminParticipantbindList();
    }


    //ENDE DER FUNKTIONEN FUER DIE LISTE TTZIELE
    //ANFANG DER FUNKTIONEN FUER DIE LISTE Participant

    private $PPprimarykey = "idtm_activity_participant";
    private $PPfields = array("act_part_notiz");
    private $PPlistfields = array();
    private $PPdatfields = array();
    private $PPhiddenfields = array();
    private $PPboolfields = array("act_part_anwesend");

    public function PPDeleteButtonClicked($sender,$param) {
        //#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
        $tempus=$this->PPprimarykey;
        $AEditRecord = ActivityParticipantsRecord::finder()->findByPK($this->$tempus->Text);
        $AEditRecord->delete();
        $this->PPNewButtonClicked($sender,$param);
        $this->edParticipantbindList();
    }

    public function addInvitedParticipant($sender,$param) {
        if($this->edidtm_protokoll->Text=="") {
            $this->insertButtonClicked($sender,$param);
        }
        $AEditRecord = new ActivityParticipantsRecord;
        $AEditRecord->idtm_activity = $this->edidtm_termin->Text;
        $AEditRecord->idtm_organisation = $param->Item->edlstpart_idtm_organisation->Text;
        $AEditRecord->act_part_anwesend = '1';
        $AEditRecord->save();
        $this->edParticipantbindList();
    }

    public function PPSavedButtonClicked() {

        $tempus=$this->PPprimarykey;

        if($this->activity_termin_edit_status->Text == '1') {
            $AEditRecord = ActivityParticipantsRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $AEditRecord = new ActivityParticipantsRecord();
        }

        //HIDDEN
        foreach ($this->PPhiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->PPdatfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->PPboolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->PPfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $AEditRecord->idtm_activity = $this->edidtm_termin->Text;
        $AEditRecord->idtm_organisation = $this->edttidtm_organisation->Text;

        $AEditRecord->save();

        $this->edParticipantbindList();
    }

    public function PPNewButtonClicked($sender,$param) {

        $tempus = $this->PPprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->PPhiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->PPdatfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->PPboolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->PPfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->edttidtm_organisation->Text = '0';
        $this->activity_termin_edit_status->Text = '0';

        $this->edParticipantbindList();
    }

    private function edParticipantbindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_activity = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->edidtm_termin->Text;
        $criteria->OrdersBy["idtm_organisation"] = 'asc';

        $this->ParticipantListe->VirtualItemCount = count(ActivityParticipantsView::finder()->findAll($criteria));

        $criteria->setLimit($this->ParticipantListe->PageSize);
        $criteria->setOffset($this->ParticipantListe->PageSize * $this->ParticipantListe->CurrentPageIndex);
        $this->ParticipantListe->DataKeyField = 'idtm_organisation';

        $this->ParticipantListe->DataSource=ActivityParticipantsView::finder()->findAll($criteria);
        $this->ParticipantListe->dataBind();
    }

    public function edparticipant_PageIndexChanged($sender,$param) {
        $this->ParticipantListe->CurrentPageIndex = $param->NewPageIndex;
        $this->edParticipantbindList();
    }
	
    public function load_participant($sender,$param){
        $item = $param->Item;
        $myitem=ActivityParticipantsRecord::finder()->findByPK($item->lstpart_idtm_activity_participant->Text);

        $tempus = $this->PPprimarykey;
        $monus = $this->PPprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->PPhiddenfields as $recordfield){
                $edrecordfield = $recordfield;
                $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->PPdatfields as $recordfield){
                $edrecordfield = $recordfield;
                $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->PPboolfields as $recordfield){
                $edrecordfield = $recordfield;
                $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->PPfields as $recordfield){
                $edrecordfield = $recordfield;
                $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->edttidtm_organisation->Text = $myitem->idtm_organisation;
        $this->activity_termin_edit_status->Text = 1;
        $this->edParticipantbindList();
    }

    //ENDE DER FUNKTIONEN FUER DIE LISTE Participant


    //ANFANG TERMIN TEILNEHMER

    private function edTerminParticipantbindList() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_termin = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->edidtm_termin->Text;
        $criteria->OrdersBy["idtm_organisation"] = 'asc';

        $this->edTerminParticipant->VirtualItemCount = count(TerminOrganisationView::finder()->findAll($criteria));
        $criteria->setLimit($this->edTerminParticipant->PageSize);
        $criteria->setOffset($this->edTerminParticipant->PageSize * $this->edTerminParticipant->CurrentPageIndex);
        $this->edTerminParticipant->DataKeyField = 'idtm_organisation';

        $this->edTerminParticipant->DataSource=TerminOrganisationView::finder()->findAll($criteria);
        $this->edTerminParticipant->dataBind();
    }

    

    //ENDE TERMIN TEILNEHMER

    //END OF THE PART WITH THE TASKS

    public function lstCCPrtRepEdit($sender,$param) {
        $sender->EditItemIndex=$param->Item->ItemIndex;
        $this->load_prtdetailsEdit($sender, $param);
    }

    public function lstCCPrtRepCancel($sender,$param) {
        $sender->SelectedItemIndex=-1;
        $sender->EditItemIndex=-1;
        $this->load_prtdetailsgroup();
    }

    public function lstCCPrtRepSave($sender,$param) {
        $item=$param->Item;
        $RCEditRecord = ProtokollDetailRecord::finder()->findByPK($item->idtm_protokoll_detail->Text);

        $RCEditRecord->prtdet_descr = $item->prtdet_descr->Text;
        $RCEditRecord->prtdet_topic = $item->prtdet_topic->Text;
        $RCEditRecord->prtdet_wvl = $item->prtdet_wvl->Checked?1:0;
        $RCEditRecord->idtm_protokoll = $item->idtm_protokoll->Text;
        
        $RCEditRecord->idta_protokoll_ergebnistype = $item->idta_protokoll_ergebnistype->Text;
        if($item->idtm_activity->Text==0 OR $item->idtm_activity->Text==''){
            $CheckStart = TerminRecord::finder()->findByPK($this->page->edidtm_termin->Text)->idtm_activity;
            if($CheckStart>0){
                $RCEditRecord->idtm_activity = $CheckStart;
            }
        }else{
            $RCEditRecord->idtm_activity = $item->idtm_activity->Text;
        }

        $RCEditRecord->save();

        $sender->EditItemIndex=-1;
        $this->load_prtdetailsgroup();
    }

    public function load_prtdetailsEdit($sender,$param) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idtm_protokoll_detail_group = :suchtext";
        $criteria->Parameters[':suchtext'] = $param->CommandParameter;
        $criteria->OrdersBy['idtm_protokoll_detail']='DESC';

        $sender->DataSource=ProtokollDetailView::finder()->findAll($criteria);
        $sender->dataBind();
    }

}
?>