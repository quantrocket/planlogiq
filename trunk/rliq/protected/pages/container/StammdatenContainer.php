<?php

class StammdatenContainer extends TTemplateControl {


    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->isPostBack && !$this->page->isCallback) {
            $this->bindListStammdatenValue();

            $this->RCedidta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
            $this->RCedidta_stammdaten_group->dataBind();

            $this->RCTedidta_variante->DataSource=PFH::build_SQLPullDown(VarianteRecord::finder(),"ta_variante",array("idta_variante","var_descr"));
            $this->RCTedidta_variante->dataBind();

            $this->RCTedidta_periode->DataSource=PFH::build_SQLPullDown(PeriodenRecord::finder(),"ta_perioden",array("idta_perioden","per_extern"));
            $this->RCTedidta_periode->dataBind();
        }
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idtm_stammdaten";
    private $RCfields = array("stammdaten_name","stammdaten_key_extern","idta_stammdaten_group");
    private $RCdatfields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array("stammdaten_aktiv");

    public function RCClosedButtonClicked($sender, $param) {
        $this->parent->parent->mpnlTestS->Hide();
    }

    public function bindListStammdatenValue() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idta_stammdaten_group = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->RCedidta_stammdaten_group->Text;
        $this->StammdatenListe->DataSource=StammdatenRecord::finder()->findAll($criteria);
        $this->StammdatenListe->dataBind();
        $this->initTreiberPullDowns();
    }

    public function initTreiberPullDowns(){
        try{
            $myidta_struktur_type = StammdatenGroupRecord::finder()->findByidta_stammdaten_group($this->RCedidta_stammdaten_group->Text)->idta_struktur_type;
        }catch(Exception $e){
            $myidta_struktur_type = 1;
        }
        $this->RCTDedidta_feldfunktion_from->DataSource = PFH::build_SQLPullDown(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name"),"idta_struktur_type = '".$myidta_struktur_type."'");
        $this->RCTDedidta_feldfunktion_from->DataBind();
        $this->RCTDedidta_stammdaten_group->DataSource = PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
        $this->RCTDedidta_stammdaten_group->DataBind();
        $this->RCTDedidtm_stammdaten_to->DataSource = PFH::build_SQLPullDown(StammdatenRecord::finder(),"tm_stammdaten",array("idtm_stammdaten","stammdaten_name"));
        $this->RCTDedidtm_stammdaten_to->DataBind();
    }

    public function load_Stammdaten($sender,$param) {

        $item = $param->Item;
        $myitem=StammdatenRecord::finder()->findByPK($item->lst_idtm_stammdaten->Text);

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

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->RCedstammdaten_edit_status->Text = 1;
        $this->RCedidtm_stammdaten->Text = $item->lst_idtm_stammdaten->Text;

        $this->RCTedidtm_stammdaten->Text=$item->lst_idtm_stammdaten->Text;
        $this->RCTNewButtonClicked($sender,$param);
        $this->bindList_StammdatenLink($sender,$param);
        $this->bindListTTStammdatenValue();
        $this->buildFieldList($this->RCedidta_stammdaten_group->Text);
    }

    public function RCDeleteButtonClicked($sender,$param) {
        $tempus='RCed'.$this->RCprimarykey;

        if($this->RCedstammdaten_edit_status->Text == '1') {
            $RCEditRecord = StammdatenRecord::finder()->findByPK($this->$tempus->Text);
            $RCEditRecord->delete();
        }
        $this->bindListStammdatenValue();
        $this->bindListTTStammdatenValue();
        $this->RCNewButtonClicked($sender, $param);
    }

    public function RCSavedButtonClicked($sender,$param) {

        $tempus='RCed'.$this->RCprimarykey;

        if($this->RCedstammdaten_edit_status->Text == '1') {
            $RCEditRecord = StammdatenRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new StammdatenRecord;
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

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCEditRecord->save();

        $this->RCTedidtm_stammdaten->Text=$RCEditRecord->idtm_stammdaten;
        $this->bindListStammdatenValue();
    }

    public function RCNewButtonClicked($sender,$param) {
        $idta_stammdaten_group = $this->RCedidta_stammdaten_group->Text;

        $tempus = 'RCed'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = '0';

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
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->RCedidta_stammdaten_group->Text = $idta_stammdaten_group;
        $this->RCedstammdaten_edit_status->Text = '0';
    }


    public function StammdatenList_PageIndexChanged($sender,$param) {
        $this->StammdatenListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStammdatenValue();
    }

        /* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCTprimarykey = "idtt_stammdaten";
    private $RCTfields = array("idta_feldfunktion","idta_variante","idtm_stammdaten","tt_stammdaten_value","idta_periode");
    private $RCTdatfields = array();
    private $RCThiddenfields = array();
    private $RCTboolfields = array();

    public function bindListTTStammdatenValue() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_stammdaten = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->RCedidtm_stammdaten->Text;
        $this->TTStammdatenListe->DataSource=TTStammdatenRecord::finder()->findAll($criteria);
        $this->TTStammdatenListe->dataBind();
    }

    public function load_TTStammdaten($sender,$param) {
        $item = $param->Item;
        $myitem=TTStammdatenRecord::finder()->findByPK($item->lst_idtt_stammdaten->Text);

        $tempus = 'RCTed'.$this->RCTprimarykey;
        $monus = $this->RCTprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RCThiddenfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCTdatfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCTboolfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->RCTfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->RCTedttstammdaten_edit_status->Text = 1;
        $this->RCTedidtt_stammdaten->Text = $item->lst_idtt_stammdaten->Text;

    }

    public function RCTSavedButtonClicked($sender,$param) {

        $tempus='RCTed'.$this->RCTprimarykey;

        if($this->RCTedttstammdaten_edit_status->Text == '1') {
            $RCTEditRecord = TTStammdatenRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCTEditRecord = new TTStammdatenRecord;
        }

        //HIDDEN
        foreach ($this->RCThiddenfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $RCTEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCTdatfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $RCTEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCTboolfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $RCTEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCTfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $RCTEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCTEditRecord->idtm_stammdaten=$this->RCedidtm_stammdaten->Text;
        $RCTEditRecord->save();

        $this->bindListTTStammdatenValue();
    }

    public function RCTNewButtonClicked($sender,$param) {

        $tempus = 'RCTed'.$this->RCTprimarykey;
        $monus = $this->RCTprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RCThiddenfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RCTdatfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->RCTboolfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->RCTfields as $recordfield) {
            $edrecordfield = 'RCTed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->RCTedttstammdaten_edit_status->Text = '0';
    }


    public function TTStammdatenList_PageIndexChanged($sender,$param) {
        $this->StammdatenListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStammdatenValue();
    }

    public function buildFieldList($idta_stammdaten_group){
        $Result = StammdatenGroupRecord::finder()->findBy_idta_stammdaten_group($idta_stammdaten_group);
        if(is_Object($Result)){
            $this->RCTedidta_feldfunktion->DataSource = PFH::build_SQLPullDown(FeldfunktionRecord::finder(), "ta_feldfunktion", array("idta_feldfunktion","ff_name"), "idta_struktur_type = '".$Result->idta_struktur_type."' AND ff_type = 1");
            $this->RCTedidta_feldfunktion->dataBind();
        }
    }

    public function load_StammdatenLink($sender,$param){
        $idtm_stammdaten = $param->Item->lst_idtm_stammdaten->Text;
        
    }

    public function bindList_StammdatenLink($sender,$param){
        $idtm_stammdaten = $this->RCedidtm_stammdaten->Text;
        $this->RCedstammdatenlink_edit_status->Text = 0;
        $this->StammdatenLinkListe->DataSource=StammdatenLinkRecord::finder()->findAllByidtm_stammdaten_from($idtm_stammdaten);
        $this->StammdatenLinkListe->dataBind();
        $this->RCTDedidtm_stammdaten_from->Text = $idtm_stammdaten;
        $this->RCTDedidtm_stammdaten_from_label->Text = StammdatenRecord::finder()->findByidtm_stammdaten($idtm_stammdaten)->stammdaten_name;
    }

    public function RCTTSavedButtonClicked($sender,$param){
        if($this->RCedstammdatenlink_edit_status->Text==0){
            $StammdatenLink = new StammdatenLinkRecord();
        }else{
            $StammdatenLink = StammdatenLinkRecord::finder()->findByPK($this->RCTDedidta_stammdaten_link->Text);
        }
        $StammdatenLink->idta_stammdaten_link=$this->RCTDedidta_stammdaten_link->Text;
	$StammdatenLink->idtm_stammdaten_from=$this->RCTDedidtm_stammdaten_from->Text;
	$StammdatenLink->idtm_stammdaten_to=$this->RCTDedidtm_stammdaten_to->Text;
	$StammdatenLink->idta_stammdaten_group=$this->RCTDedidta_stammdaten_group->Text;
	$StammdatenLink->idta_feldfunktion_from=$this->RCTDedidta_feldfunktion_from->Text;
        $StammdatenLink->save();
        $this->bindList_StammdatenLink($sender, $param);
        $this->RCedstammdatenlink_edit_status->Text = 1;
    }

}

?>