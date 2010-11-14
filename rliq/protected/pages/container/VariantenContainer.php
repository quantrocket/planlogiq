<?php

class VariantenContainer extends TTemplateControl {

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack && !$this->page->IsCallback) {
            $PeriodPullDown = new PFPeriodPullDown();
            $PeriodPullDown->setStructureTable("ta_perioden");
            $PeriodPullDown->setRecordClass(PeriodenRecord::finder());
            $PeriodPullDown->setPKField("idta_perioden");
            $PeriodPullDown->setField("per_extern");
            $PeriodPullDown->letsrun();

            $this->RCedidta_perioden->DataSource=$PeriodPullDown->myTree;
            $this->RCedidta_perioden->dataBind();

            $Usersql = "SELECT idtm_user, user_name FROM tm_user";
            $Userdata = PFH::convertdbObjectArray(UserRecord::finder()->findAllBySql($Usersql),array("idtm_user","user_name"));
            $this->var_idtm_user->DataSource=$Userdata;
            $this->var_idtm_user->dataBind();

            $this->bindListVariantenValue();
        }
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idta_variante";
    private $RCfields = array("var_descr","w_id_variante","idtm_user","idta_perioden");
    private $RCdatfields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array("var_default");

    public function RCClosedButtonClicked($sender, $param) {
        $this->parent->parent->mpnlTest->Hide();
    }

    public function bindListVariantenValue() {

        $this->VarianteListe->VirtualItemCount = count(VarianteRecord::finder()->findAll());

        $criteria = new TActiveRecordCriteria();
        $criteria->setLimit($this->VarianteListe->PageSize);
        $criteria->setOffset($this->VarianteListe->PageSize * $this->VarianteListe->CurrentPageIndex);
        $this->VarianteListe->DataKeyField = 'idtm_rcvalue';

        $this->VarianteListe->VirtualItemCount = count(VarianteRecord::finder()->findAll());
        $this->VarianteListe->DataSource=VarianteRecord::finder()->findAll($criteria);

        $this->VarianteListe->dataBind();
    }

    public function load_variante($sender,$param) {

        $item = $param->Item;
        $myitem=VarianteRecord::finder()->findByPK($item->lst_idta_variante->Text);

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

        $this->RCedvariante_edit_status->Text = 1;
        $this->RCedidta_variante->Text = $item->lst_idta_variante->Text;

        $this->loadBerechtigung();
    }

    public function RCSavedButtonClicked($sender,$param) {

        $tempus='RCed'.$this->RCprimarykey;

        if($this->RCedvariante_edit_status->Text == '1') {
            $RCEditRecord = VarianteRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new VarianteRecord;
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

        $this->bindListVariantenValue();
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

        $this->RCedvariante_edit_status->Text = '0';
    }
    
    public function rcvList_PageIndexChanged($sender,$param) {
        $this->VarianteListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListVariantenValue();
    }

//ENDE DER RISIKEN
//ENDE DER RISIKEN
//ENDE DER RISIKEN

    //the fields for the BerechtigungRecord
    private $XXRprimarykey = "idxx_berechtigung";
    private $XXRfields = array("xx_id","xx_modul","idtm_user");
    private $XXRdatfields = array();
    private $XXRtimefields = array();
    private $XXRhiddenfields = array();
    private $XXRboolfields = array("xx_read","xx_write","xx_create","xx_delete");

    private function loadBerechtigung($sender='',$param='') {
        $Criteria = new TActiveRecordCriteria();
        $Criteria->Condition = "xx_id = :idta_variante AND xx_modul = :modul";
        $Criteria->Parameters[':idta_variante'] = $this->RCedidta_variante->Text;
        $Criteria->Parameters[':modul'] = "idta_variante";
        $this->var_lstBerechtigung->DataSource=BerechtigungRecord::finder()->findAll($Criteria);
        $this->var_lstBerechtigung->dataBind();
    }

    public function var_editlstBerechtigung($sender,$param) {
        $item = $param->Item;
        $myitem=BerechtigungRecord::finder()->findByPK($item->var_lst_idxx_berechtigung->Text);

        $monus = 'var_'.$this->XXRprimarykey;
        $this->$monus->Text = $myitem->{$this->XXRprimarykey};

        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }
        //TIME
        foreach ($this->XXRtimefields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }
        //NON DATUM
        foreach ($this->XXRfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }
        $this->var_berechtigung_edit_status->Text = 1;
        $this->loadberechtigung();
    }

    public function XXXRDeleteClicked($sender,$param) {
        $tempus = 'var_'.$this->XXRprimarykey;
        $Record = BerechtigungRecord::finder()->findByPK($this->$tempus->Text);
        $Record->delete();
        $this->loadBerechtigung();
        $this->XXXRNewClicked($sender,$param);
    }

    public function var_lstBerechtigung_PageIndexChanged($sender,$param) {
        $this->var_lstBerechtigung->CurrentPageIndex = $param->NewPageIndex;
        $this->loadBerechtigung();
    }

    public function XXXRNewClicked($sender,$param) {
        $tempus = 'var_'.$this->XXRprimarykey;
        $monus = $this->XXRprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->XXRfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->var_xx_modul->Text = "idta_variante";
        $this->var_xx_id->Text = $this->RCedidta_variante->Text;
        $this->var_berechtigung_edit_status->Text = '0';
    }

    public function XXXRSaveClicked($sender,$param) {

        $tempus = 'var_'.$this->XXRprimarykey;

        if($this->var_berechtigung_edit_status->Text == '1') {
            $BREditRecord = BerechtigungRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $BREditRecord = new BerechtigungRecord;
        }
        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $BREditRecord->$recordfield = $this->$edrecordfield->Value;
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $BREditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $BREditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }
        foreach ($this->XXRtimefields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $BREditRecord->$recordfield = $this->$edrecordfield->Text;
        }
        foreach ($this->XXRfields as $recordfield) {
            $edrecordfield = 'var_'.$recordfield;
            $BREditRecord->$recordfield = $this->$edrecordfield->Text;
        }
        $BREditRecord->save();
        $this->loadBerechtigung();
    }


}

?>