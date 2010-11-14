<?php

class strukturview extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    private $primarykey = "idtm_struktur";
    private $MASTERRECORD = '';
    private $finder = '';
    private $fields = array("struktur_name","idta_struktur_type","parent_idtm_struktur","idtm_stammdaten");
    private $datfields = array();
    private $exitURL = 'struktur.strworkspace';

    public function onInit($param) {

        parent::onInit($param);

        //Globale definition f�r dieses Dokument
        $this->finder = StrukturRecord::finder();
        $this->MASTERRECORD = new StrukturRecord;

        if(!$this->isPostBack && !$this->isCallback) {

            switch ($this->Request['modus']) {
                case 0:
                    $sql = "SELECT idta_struktur_type, struktur_type_name FROM ta_struktur_type";
                    $data = PFH::convertdbObjectArray(StrukturTypeRecord::finder()->findAllBySql($sql),array("idta_struktur_type","struktur_type_name"));
                    $this->idta_struktur_type->DataSource=$data;
                    $this->idta_struktur_type->dataBind();

                    $HRKEYTest = new PFHierarchyPullDown();
                    $HRKEYTest->setStructureTable("tm_struktur");
                    $HRKEYTest->setRecordClass(StrukturRecord::finder());
                    $HRKEYTest->setPKField("idtm_struktur");
                    $HRKEYTest->setField("struktur_name");
                    $HRKEYTest->letsrun();

                    $this->parent_idtm_struktur->DataSource=$HRKEYTest->myTree;
                    $this->parent_idtm_struktur->dataBind();

                    $this->idtm_stammdaten->DataSource=PFH::build_SQLPullDown(StammdatenRecord::finder(),"tm_stammdaten",array("idtm_stammdaten","stammdaten_name"));
                    $this->idtm_stammdaten->dataBind();
                    break;
                case 1:
                    $sql = "SELECT idta_struktur_type, struktur_type_name FROM ta_struktur_type";
                    $data = PFH::convertdbObjectArray(StrukturTypeRecord::finder()->findAllBySql($sql),array("idta_struktur_type","struktur_type_name"));
                    $this->edidta_struktur_type->DataSource=$data;
                    $this->edidta_struktur_type->dataBind();

                    if($this->Request[$this->primarykey]!=1) {
                        $HRKEYTest = new PFHierarchyPullDown();
                        $HRKEYTest->setStructureTable("tm_struktur");
                        $HRKEYTest->setRecordClass(StrukturRecord::finder());
                        $HRKEYTest->setPKField("idtm_struktur");
                        $HRKEYTest->setField("struktur_name");
                        $HRKEYTest->letsrun();
                        $data=$HRKEYTest->myTree;
                    }
                    else {
                        $data = array();
                        $data[0] = "START";
                    }
                    $this->edparent_idtm_struktur->DataSource=$data;
                    $this->edparent_idtm_struktur->dataBind();

                    $this->fillValues($this->getSelected($this->Request[$this->primarykey]));

                    $this->edidtm_stammdaten->DataSource=PFH::build_SQLPullDown(StammdatenRecord::finder(),"tm_stammdaten",array("idtm_stammdaten","stammdaten_name"));
                    $this->edidtm_stammdaten->dataBind();

                    $Usersql = "SELECT idtm_user, user_name FROM tm_user";
                    $Userdata = PFH::convertdbObjectArray(UserRecord::finder()->findAllBySql($Usersql),array("idtm_user","user_name"));
                    $this->idtm_user->DataSource=$Userdata;
                    $this->idtm_user->dataBind();

                    $this->loadBerechtigung();
                    break;
                default:
                    break;
            }

            $this->viewPanel->ActiveViewIndex=$this->Request['modus'];
            $this->StrukturStammdatenGroupContainer->RCedidtm_struktur->Text=$this->Request[$this->primarykey];
        }
    }

    protected function fillValues($item) {

        $tempus = 'ed'.$this->primarykey;
        $monus = $this->primarykey;

        $this->$tempus->Value = $item->$monus;

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $this->$edrecordfield->setDate(date($item->$recordfield));
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
        foreach($this->finder->findAllByparent_idtm_struktur($this->$tempus->Value)AS $TRec) {
            WerteRecord::finder()->deleteAll('idtm_struktur = ?',$TRec->idtm_struktur);
        }
        WerteRecord::finder()->deleteAll('idtm_struktur = ?',$this->$tempus->Value);
        $this->finder->deleteAll('parent_idtm_struktur = ?',$this->$tempus->Value);
        $this->finder->deleteAll('idtm_struktur = ?',$this->$tempus->Value);
        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }


    public function editButtonClicked($sender,$param) {

        $tempus= 'ed'.$this->primarykey;

        $EditRecord = $this->finder->findByPK($this->$tempus->Value);

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $this->$edrecordfield->setDate(date($item->$recordfield));
        }

        foreach ($this->fields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $EditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $EditRecord->save();

        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }

    public function insertButtonClicked($sender,$param) {

        $EditRecord = $this->MASTERRECORD;

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $EditRecord->$recordfield = date("Y-m-d",$this->$recordfield->Text);
        }


        foreach ($this->fields as $recordfield) {
            $EditRecord->$recordfield = $this->$recordfield->Text;
        }

        $EditRecord->save();

        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }


    //the fields for the BerechtigungRecord
    private $XXRprimarykey = "idxx_berechtigung";
    private $XXRfields = array("xx_id","xx_modul","idtm_user");
    private $XXRdatfields = array();
    private $XXRtimefields = array();
    private $XXRhiddenfields = array();
    private $XXRboolfields = array("xx_read","xx_write","xx_create","xx_delete");

    private function loadBerechtigung($sender='',$param='') {
        $Criteria = new TActiveRecordCriteria();
        $Criteria->Condition = "xx_id = :idtm_struktur AND xx_modul = :modul";
        $Criteria->Parameters[':idtm_struktur'] = $this->edidtm_struktur->Data;
        $Criteria->Parameters[':modul'] = "tm_struktur";
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
        $this->xx_modul->Text = "tm_struktur";
        $this->xx_id->Text = $this->edidtm_struktur->Value;
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