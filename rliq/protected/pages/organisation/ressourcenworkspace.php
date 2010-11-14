<?php

class ressourcenworkspace extends TPage {
    private $primarykey = "idta_rescalendar";
    private $MASTERRECORD = '';
    private $finder = '';
    private $fields = array("rescal_name","rescal_descr","rescal_t1","rescal_h1","rescal_t2","rescal_h2","rescal_t3","rescal_h3","rescal_t4","rescal_h4","rescal_t5","rescal_h5","rescal_t6","rescal_h6","rescal_t7","rescal_h7");
    private $listfields = array();
    private $datfields = array();
    private $hiddenfields = array();
    private $boolfields = array();
    private $exitURL = 'organisation.ressourcenworkspace';

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        //Globale definition f�r dieses Dokument
        $this->finder = ResCalendarRecord::finder();
        $this->MASTERRECORD = new ResCalendarRecord;
        //Globale definition f�r dieRessourcen
        $this->RSfinder = RessourceRecord::finder();
        $this->RSMASTERRECORD = new RessourceRecord;

        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {

            $this->RessourceListFields();
            $this->bindListResCalendar();
            $this->bindListRessource();
        }
    }

    public function bindListResCalendar() {
        $criteria = new TActiveRecordCriteria();
        $criteria->OrdersBy["idta_rescalendar"] = 'desc';

        $this->ResCalendarListe->VirtualItemCount = count(ResCalendarRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->ResCalendarListe->PageSize);
        $criteria->setOffset($this->ResCalendarListe->PageSize * $this->ResCalendarListe->CurrentPageIndex);
        $this->ResCalendarListe->DataKeyField = 'idtm_termin';

        $this->ResCalendarListe->VirtualItemCount = count(ResCalendarRecord::finder()->findAll());
        $this->ResCalendarListe->DataSource=ResCalendarRecord::finder()->findAll($criteria);
        $this->ResCalendarListe->dataBind();
    }

    public function searchTermin() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="ter_betreff LIKE :suchbedingung1";
        $criteria->Parameters[':suchbedingung1'] = "%".$this->find_termin->Text."%";

        $this->ResCalendarListe->VirtualItemCount = count(ResCalendarRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->ResCalendarListe->PageSize);
        $criteria->setOffset($this->ResCalendarListe->PageSize * $this->ResCalendarListe->CurrentPageIndex);
        $this->ResCalendarListe->DataKeyField = 'idtm_termin';

        $this->ResCalendarListe->VirtualItemCount = count(ResCalendarRecord::finder()->findAll());
        $this->ResCalendarListe->DataSource=ResCalendarRecord::finder()->findAll($criteria);
        $this->ResCalendarListe->dataBind();
    }

    public function TDeleteButtonClicked($sender,$param) {
        $tempus=$this->primarykey;
        $AEditRecord = ResCalendarRecord::finder()->findByPK($this->$tempus->Text);
        $AEditRecord->delete();
        $this->bindListResCalendar();
        $this->TNewButtonClicked($sender,$param);
    }

    public function load_rescalendar($sender,$param) {

        $item = $param->Item;
        $myitem=ResCalendarRecord::finder()->findByPK($item->lst_idta_rescalendar->Text);

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

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->rescalendar_edit_status->Text = 1;
        //$this->ParticipantbindList();
        //$this->addParticipant->setVisible(true);
    }

    public function TSavedButtonClicked($sender,$param) {

        $tempus=$this->primarykey;

        if($this->rescalendar_edit_status->Text == '1') {
            $AEditRecord = ResCalendarRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $AEditRecord = new ResCalendarRecord;
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

        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $AEditRecord->save();

        $this->bindListResCalendar();
        //$this->ParticipantbindList();
        $this->idta_rescalendar->Text=$AEditRecord->idta_rescalendar;
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

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->rescalendar_edit_status->Text = '0';
        //$this->ParticipantbindList();
    }

    public function rescalendarList_PageIndexChanged($sender,$param) {
        $this->ResCalendarListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListResCalendar();
    }


    //ANFANG DER FUNKTIONEN FUER DIE Ressourcenverwaltung

    private $RSprimarykey = "idtm_ressource";
    private $RSMASTERRECORD = '';
    private $RSfinder = '';
    private $RSfields = array("res_name","res_code","res_produktivitaet","res_kosten","res_note");
    private $RSlistfields = array("idta_rescalendar","idta_ressource_type","idta_einheit");
    private $RSdatfields = array();
    private $RShiddenfields = array();
    private $RSboolfields = array();
    private $RSexitURL = 'organisation.ressourcenworkspace';
    private $RSPrefix = 'RS';

    public function RessourceListFields() {

        $this->RSidta_rescalendar->DataSource=PFH::build_SQLPullDown(ResCalendarRecord::finder(),"ta_rescalendar",array("idta_rescalendar","rescal_name"));
        $this->RSidta_rescalendar->dataBind();

        $this->RSidta_ressource_type->DataSource=PFH::build_SQLPullDown(RessourceTypeRecord::finder(),"ta_ressource_type",array("idta_ressource_type","res_type_name"));
        $this->RSidta_ressource_type->dataBind();

        $this->RSidta_einheit->DataSource=PFH::build_SQLPullDown(EinheitRecord::finder(),"ta_einheit",array("idta_einheit","ein_name"));
        $this->RSidta_einheit->dataBind();

    }

    public function bindListRessource() {
        $criteria = new TActiveRecordCriteria();
        $criteria->OrdersBy["idtm_ressource"] = 'desc';

        $this->RessourceListe->VirtualItemCount = count(RessourceRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->RessourceListe->PageSize);
        $criteria->setOffset($this->RessourceListe->PageSize * $this->RessourceListe->CurrentPageIndex);
        $this->RessourceListe->DataKeyField = 'idtm_termin';

        $this->RessourceListe->VirtualItemCount = count(RessourceRecord::finder()->findAll());
        $this->RessourceListe->DataSource=RessourceRecord::finder()->findAll($criteria);
        $this->RessourceListe->dataBind();
    }

    public function searchRessource() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="res_name LIKE :suchbedingung1";
        $criteria->Parameters[':suchbedingung1'] = "%".$this->find_ressource->Text."%";

        $this->RessourceListe->VirtualItemCount = count(RessourceRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->RessourceListe->PageSize);
        $criteria->setOffset($this->RessourceListe->PageSize * $this->RessourceListe->CurrentPageIndex);
        $this->RessourceListe->DataKeyField = 'idtm_termin';

        $this->RessourceListe->VirtualItemCount = count(RessourceRecord::finder()->findAll());
        $this->RessourceListe->DataSource=RessourceRecord::finder()->findAll($criteria);
        $this->RessourceListe->dataBind();
    }

    public function RSTDeleteButtonClicked($sender,$param) {
        $tempus=$this->RSprimarykey;
        $AEditRecord = RessourceRecord::finder()->findByPK($this->$tempus->Text);
        $AEditRecord->delete();
        $this->bindListRessource();
        $this->RSTNewButtonClicked($sender,$param);
    }

    public function load_ressource($sender,$param) {

        $item = $param->Item;
        $myitem=RessourceRecord::finder()->findByPK($item->lst_idtm_ressource->Text);

        $tempus = $this->RSprimarykey;
        $monus = $this->RSprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RShiddenfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RSdatfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //LIST
        foreach ($this->RSlistfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->text= $myitem->$recordfield;
        }

        //BOOL
        foreach ($this->RSboolfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->RSfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->ressource_edit_status->Text = 1;
        //$this->ParticipantbindList();
        //$this->addParticipant->setVisible(true);
    }

    public function RSTSavedButtonClicked($sender,$param) {

        $tempus=$this->RSprimarykey;

        if($this->ressource_edit_status->Text == '1') {
            $AEditRecord = RessourceRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $AEditRecord = new RessourceRecord;
        }

        //HIDDEN
        foreach ($this->RShiddenfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RSdatfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $AEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //LIST
        foreach ($this->RSlistfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        //BOOL
        foreach ($this->RSboolfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RSfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $AEditRecord->save();

        $this->bindListRessource();
        //$this->ParticipantbindList();
        $this->idtm_ressource->Text=$AEditRecord->idtm_ressource;
    }

    public function RSTNewButtonClicked($sender,$param) {

        $tempus = $this->RSprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RShiddenfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RSdatfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //LIST
        foreach ($this->RSlistfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        //BOOL
        foreach ($this->RSboolfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->RSfields as $recordfield) {
            $edrecordfield = $this->RSPrefix.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->ressource_edit_status->Text = '0';
        //$this->ParticipantbindList();
    }

    public function ressourceList_PageIndexChanged($sender,$param) {
        $this->RessourceListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListRessource();
    }


    //ENDE DER FUNKTIONEN FUER DIE Ressourcenverwaltung

}
?>