<?php

class StammdatenGroupContainer extends TTemplateControl {


    public function buildStammdatenGroupPullDown() {
        $this->RCedidta_struktur_type->DataSource=PFH::build_SQLPullDown(StrukturTypeRecord::finder(),"ta_struktur_type",array("idta_struktur_type","struktur_type_name"));
        $this->RCedidta_struktur_type->dataBind();

        $Usersql = "SELECT idtm_user, user_name FROM tm_user";
        $Userdata = PFH::convertdbObjectArray(UserRecord::finder()->findAllBySql($Usersql),array("idtm_user","user_name"));
        $this->idtm_user->DataSource=$Userdata;
        $this->idtm_user->dataBind();
    }

    public function propertyAction($sender,$param){
        $DetailRecord = TTStammdatensichtRecord::finder()->findByPK($param->CommandParameter);
        if($param->Item->parent_idta_stammdaten_group->Text!="no values"){
            $DetailRecord->parent_idta_stammdaten_group = $param->Item->parent_idta_stammdaten_group->Text;
            $DetailRecord->sts_stammdaten_group_use = $param->Item->sts_stammdaten_group_use->Checked?1:0;
            $DetailRecord->save();
        }else{
            $DetailRecord->parent_idta_stammdaten_group = 0;
            $DetailRecord->save();
        }
    }

    public function reloadStammdatensichtPullDown($sender,$param){
        $item=$param->item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
            {
                $values = PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
                array_push($values,array("no values","0"));
                $item->parent_idta_stammdaten_group->DataSource=$values;
                $item->parent_idta_stammdaten_group->dataBind();
            }
    }

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            $this->buildStammdatenGroupPullDown();
            $this->bindListStammdatenGroupValue();
        }
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idta_stammdaten_group";
    private $RCfields = array("stammdaten_group_name","idta_struktur_type");
    private $RCdatfields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array("stammdaten_group_original","stammdaten_group_create","stammdaten_group_multi");

    public function RCClosedButtonClicked($sender, $param) {
        $this->parent->parent->mpnlTestSG->Hide();
    }

    public function bindListStammdatenGroupValue() {
        $this->StammdatenGroupListe->DataSource=StammdatenGroupRecord::finder()->findAll();
        $this->StammdatenGroupListe->dataBind();
    }

    public function bindListStammdatensicht($sender,$param){
        $Stammdatensichten = StammdatensichtRecord::finder()->findAll('sts_aktiv = ?',"1");
        if(count($Stammdatensichten)>0){
            foreach($Stammdatensichten as $MeineSichten){
                $CheckRecord = TTStammdatensichtRecord::finder()->find('idta_stammdaten_group = ? AND idta_stammdatensicht = ?',$this->RCedidta_stammdaten_group->Text,$MeineSichten->idta_stammdatensicht);
                if(count($CheckRecord)==0){
                    $neuerRecord = new TTStammdatensichtRecord();
                    $neuerRecord->idta_stammdaten_group = $this->RCedidta_stammdaten_group->Text;
                    $neuerRecord->idta_stammdatensicht = $MeineSichten->idta_stammdatensicht;
                    $neuerRecord->save();
                }
            }
            unset($Stammdatensichten);
        }
        $this->TTStammdatenansichtListe->DataSource = TTStammdatensichtRecord::finder()->findAll('idta_stammdaten_group = ?',$this->RCedidta_stammdaten_group->Text);
        $this->TTStammdatenansichtListe->dataBind();
    }

    public function load_StammdatenGroup($sender,$param) {

        $item = $param->Item;
        $myitem=StammdatenGroupRecord::finder()->findByPK($item->lst_idta_stammdaten_group->Text);

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

        $this->RCedstammdaten_group_edit_status->Text = 1;
        $this->RCedidta_stammdaten_group->Text = $item->lst_idta_stammdaten_group->Text;
        $this->loadBerechtigung($sender,$param);
        $this->bindListStammdatensicht($sender, $param);
    }

    public function RCSavedButtonClicked($sender,$param) {

        $tempus='RCed'.$this->RCprimarykey;

        if($this->RCedstammdaten_group_edit_status->Text == '1') {
            $RCEditRecord = StammdatenGroupRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new StammdatenGroupRecord;
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

        $this->buildStammdatenGroupPullDown();
        $this->bindListStammdatenGroupValue();
    }

    public function RCDeleteButtonClicked($sender,$param) {
        $tempus='RCed'.$this->RCprimarykey;
        if($this->RCedstammdaten_group_edit_status->Text == '1') {
            StammdatenRecord::finder()->deleteByidta_stammdaten_group($this->$tempus->Text);
            $StammdatenRecord = StammdatenRecord::finder()->findAllByidta_stammdaten_group($this->$tempus->Text);
            if(count($StammdatenRecord)>=1){
                foreach($StammdatenRecord AS $StammRecord){
                    $TTStammdatenRecords = TTStammdatenRecord::finder()->findAllByidtm_stammdaten($StammRecord->idtm_stammdaten);
                    foreach($TTStammdatenRecords AS $TTStammdatenRecord){
                        TTStammdatenRecord::finder()->delteByidtm_stammdaten($TTStammdatenRecord->idtt_stammdaten);
                    }
                    TTStammdatenStammdatenRecord::finder()->deleteByidtm_stammdaten_group($StammRecord->idtm_stammdaten);
                }
            }
            $RCEditRecord = StammdatenGroupRecord::finder()->findByPK($this->$tempus->Text);
            $RCEditRecord->delete();
        }
        $this->buildStammdatenGroupPullDown();
        $this->bindListStammdatenGroupValue();
    }

    public function RCNewButtonClicked($sender,$param) {

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

        $this->RCedstammdaten_group_edit_status->Text = '0';
    }


    public function SGCStammdatenGroupList_PageIndexChanged($sender,$param) {
        $this->StammdatenGroupListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStammdatenGroupValue();
        $this->dataBind();
    }

    //the fields for the BerechtigungRecord
    //the fields for the BerechtigungRecord
    //the fields for the BerechtigungRecord

    private $XXRprimarykey = "idxx_berechtigung";
    private $XXRfields = array("xx_id","xx_modul","idtm_user");
    private $XXRdatfields = array();
    private $XXRtimefields = array();
    private $XXRhiddenfields = array();
    private $XXRboolfields = array("xx_read","xx_write","xx_create","xx_delete");

    private function loadBerechtigung($sender='',$param='') {
        $Criteria = new TActiveRecordCriteria();
        $Criteria->Condition = "xx_id = :idta_stammdaten_group AND xx_modul = :modul";
        $Criteria->Parameters[':idta_stammdaten_group'] = $this->RCedidta_stammdaten_group->Text;
        $Criteria->Parameters[':modul'] = "ta_stammdaten_group";
        $this->lstBerechtigung->DataSource=BerechtigungRecord::finder()->findAll($Criteria);
        $this->lstBerechtigung->dataBind();
    }

    public function editlstBerechtigung($sender,$param) {
        $item = $param->Item;
        $myitem=BerechtigungRecord::finder()->findByPK($item->lst_idxx_berechtigung->Text);

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
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$recordfield->Text = $my_time_text;
        }
        //NON DATUM
        foreach ($this->XXRfields as $recordfield) {
            $this->$recordfield->Text = $myitem->$recordfield;
        }
        $this->berechtigung_edit_status->Text = 1;
        $this->loadberechtigung();
    }

    public function XXRDeleteClicked($sender,$param) {
        $Record = BerechtigungRecord::finder()->findByPK($this->{$this->XXRprimarykey}->Text);
        $Record->delete();
        $this->loadBerechtigung();
        $this->XXRNewClicked($sender,$param);
    }

    public function lstBerechtigung_PageIndexChanged($sender,$param) {
        $this->lstBerechtigung->CurrentPageIndex = $param->NewPageIndex;
        $this->loadBerechtigung();
    }

    public function XXRNewClicked($sender,$param) {
        $monus = $this->XXRprimarykey;
        $this->$monus->Text = '0';

        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $this->$recordfield->setValue('0');
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $this->$recordfield->setDate(date('Y-m-d',time()));
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
        $this->xx_modul->Text = "ta_stammdaten_group";
        $this->xx_id->Text = $this->RCedidta_stammdaten_group->Text;
        $this->berechtigung_edit_status->Text = '0';
    }

    public function XXRSaveClicked($sender,$param) {
        if($this->berechtigung_edit_status->Text == '1') {
            $BREditRecord = BerechtigungRecord::finder()->findByPK($this->{$this->XXRprimarykey}->Text);
        }
        else {
            $BREditRecord = new BerechtigungRecord;
        }
        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Value;
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $BREditRecord->$recordfield=date('Y-m-d',$this->$recordfield->TimeStamp);
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Checked?1:0;
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
    
}

?>