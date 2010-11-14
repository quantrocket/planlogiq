<?php

class ttzieleview extends TPage {

    private $primarykey = "idtt_ziele";
    private $MASTERRECORD = '';
    private $finder = '';
    private $fields = array("ttzie_name","ttzie_descr");
    private $listfields = array("idtm_prozess","idtm_ziele","idtm_organisation");
    private $datfields = array();
    private $hiddenfields = array();
    private $boolfields = array("prostep_valid");
    private $exitURL = 'ziele.zieworkspace';

    public function onPreInit($param) {
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        parent::onLoad($param);

        //Globale definition f�r dieses Dokument
        $this->finder = TTZieleRecord::finder();
        $this->MASTERRECORD = new TTZieleRecord;

        if(!$this->isPostBack && !$this->isCallback) {

            switch ($this->Request['modus']) {
                case 0:
                //hier checken wir, wieviele schritte noch den gleichen Vater haben
                    $myPreStepOne = TTZieleRecord::finder()->findAllBySql("SELECT idtm_prozess FROM tt_ziele WHERE idtt_ziele = '".$this->Request[$this->primarykey]."'");
                    $prozess_counter = count(TTZieleRecord::finder()->findAllBySql("SELECT idtt_ziele FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'"));

                    $this->idtm_prozess->DataSource=PFH::build_SQLPullDown(ProzessRecord::finder(),"tm_prozess",array("idtm_prozess","pro_name"),"idta_prozess_type = 3");
                    $this->idtm_prozess->dataBind();

                    $HRKEYTest = new PFHierarchyPullDown();
                    $HRKEYTest->setStructureTable("tm_ziele");
                    $HRKEYTest->setRecordClass(ZieleRecord::finder());
                    $HRKEYTest->setPKField("idtm_ziele");
                    $HRKEYTest->setField("zie_name");
                    $HRKEYTest->setSQLCondition("idta_ziele_type = 3 OR idta_ziele_type=1 OR idta_ziele_type=2");
                    $HRKEYTest->letsrun();

                    $this->idtm_ziele->DataSource=$HRKEYTest->myTree;
                    $this->idtm_ziele->dataBind();

                    $sql = "SELECT idtm_organisation, org_name FROM tm_organisation WHERE idta_organisation_type = 4";
                    $data = PFH::convertdbObjectArray(OrganisationRecord::finder()->findAllBySql($sql),array("idtm_organisation","org_name"));
                    $this->idtm_organisation->DataSource=$data;
                    $this->idtm_organisation->dataBind();

                    break;
                case 1:
                //hier checken wir, wieviele schritte noch den gleichen Vater haben
                    $myPreStepOne = TTZieleRecord::finder()->findAllBySql("SELECT idtm_ziele FROM tt_ziele WHERE idtt_ziele = '".$this->Request[$this->primarykey]."'");
                    $prozess_counter = count(TTZieleRecord::finder()->findAllBySql("SELECT * FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'"));

                    $this->edidtm_prozess->DataSource=PFH::build_SQLPullDown(ProzessRecord::finder(),"tm_prozess",array("idtm_prozess","pro_name"),"idta_prozess_type = 3");
                    $this->edidtm_prozess->dataBind();

                    $this->edidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type=4");
                    $this->edidtm_organisation->dataBind();

                    $HRKEYTest = new PFHierarchyPullDown();
                    $HRKEYTest->setStructureTable("tm_ziele");
                    $HRKEYTest->setRecordClass(ZieleRecord::finder());
                    $HRKEYTest->setPKField("idtm_ziele");
                    $HRKEYTest->setField("zie_name");
                    $HRKEYTest->setSQLCondition("idta_ziele_type = 3 OR idta_ziele_type=1 OR idta_ziele_type=2");
                    $HRKEYTest->letsrun();

                    $this->edidtm_ziele->DataSource=$HRKEYTest->myTree;
                    $this->edidtm_ziele->dataBind();

                    $this->fillValues($this->getSelected($this->Request[$this->primarykey]));
                    $this->Tedauf_id->Text = $this->Request[$this->primarykey];

                    //the parameters for the RiskValueContainer
                    $this->RiskValueContainer->RCedrcv_tabelle->Text="tt_ziele";
                    $this->RiskValueContainer->RCedrcv_id->Text=$this->Request[$this->primarykey];
                    break;
                default:
                    break;
            }

            $this->viewPanel->ActiveViewIndex=$this->Request['modus'];
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

        $this->RCedrcv_id->Text=$item->$monus;

    }

    protected function getSelected($key) {
        $item = $this->finder->findByPk($key);
        return $item;
    }

    public function deleteButtonClicked($sender,$param) {
        $tempus= 'ed'.$this->primarykey;
        $this->finder->deleteAll('idtt_ziele = ?',$this->$tempus->Value);

        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }


    public function editButtonClicked($sender,$param) {

        $tempus='ed'.$this->primarykey;

        $EditRecord = $this->finder->findByPK($this->$tempus->Value);

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = 'ed'.$recordfield;
            $EditRecord->$edrecordfield->setDate(date($item->$recordfield));
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

        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }

    public function insertButtonClicked($sender,$param) {

        $EditRecord = $this->MASTERRECORD;

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $EditRecord->$recordfield = date("Y-m-d",$this->$recordfield->Text);
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $EditRecord->$recordfield = $this->$recordfield->Checked?1:0;
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $EditRecord->$recordfield = $this->$recordfield->Text;
        }

        foreach ($this->fields as $recordfield) {
            $EditRecord->$recordfield = $this->$recordfield->Text;
        }

        $EditRecord->save();

        $this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
    }

    public function update_ListBox($sender,$param) {
        $parent_value = $this->edidtm_ziele->selectedValue;

        //hier checken wir, wieviele schritte noch den gleichen Vater haben
        $myPreStepOne = TTZieleRecord::finder()->findAllBySql("SELECT idtm_ziele FROM tt_ziele WHERE idtt_ziele = '".$parent_value."'");
        $prozess_counter = count(TTZieleRecord::finder()->findAllBySql("SELECT idtt_ziele FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'"));

        $sql = "SELECT idtt_ziele, ttzie_name FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'";
        $data = PFH::convertdbObjectArray(TTZieleRecord::finder()->findAllBySql($sql),array("idtt_ziele","ttzie_name"));
        $data[0] = "START";

        $this->idtm_ziele->DataSource=$data;
        $this->idtm_ziele->dataBind();
    }

}

?>