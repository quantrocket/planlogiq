<?php

class strukturtypen extends TPage {

    private $PREFIX = '';

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {
        parent::onLoad($param);
        date_default_timezone_set('Europe/Berlin');
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            if(!$this->User->isGuest) {
                $this->initPullDowns();
                $this->bindListStrukturTypen();
            }
        }
    }

    public function initPullDowns() {
        
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idta_struktur_type";
    private $RCfields = array("struktur_type_name");
    private $RCdatfields = array();
    private $RCtimefields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array();

    public function bindListStrukturTypen() {
        $criteria = new TActiveRecordCriteria();
        $criteria->OrdersBy['struktur_type_name']='ASC';
        $this->StrukturtypeListe->VirtualItemCount = count(StrukturTypeRecord::finder()->findAll($criteria));
        $this->StrukturtypeListe->DataSource=StrukturTypeRecord::finder()->findAll($criteria);
        $this->StrukturtypeListe->dataBind();
    }

    public function load_rcvalue($sender,$param) {

        $item = $param->Item;
        $myitem=StrukturTypeRecord::finder()->findByPK($item->edlst_idta_struktur_type->Text);

        $tempus = $this->PREFIX.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->strukturtype_edit_status->Text = 1;
        
        $this->bindListStrukturTypen();
        $this->FeldfunktionContainer->initParameters();
        $this->FeldfunktionContainer->bindListFeldfunktionValue();
    }
    
    public function RCDeleteButtonClicked($sender,$param) {
        $tempus=$this->PREFIX.$this->RCprimarykey;
        $Record = StrukturTypeRecord::finder()->findByPK($this->$tempus->Text);
        $Record->delete();
        $this->bindListStrukturTypen();
        $this->RCNewButtonClicked($sender,$param);
    }

    public function RCSavedButtonClicked($sender,$param) {

        $tempus=$this->PREFIX.$this->RCprimarykey;

        if($this->strukturtype_edit_status->Text == '1') {
            $RCEditRecord = StrukturTypeRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new StrukturTypeRecord;
        }

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCEditRecord->save();

        $this->bindListStrukturTypen();
    }

    public function RCNewButtonClicked($sender,$param) {

        $tempus = $this->PREFIX.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->Text = '00:00';
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = $this->PREFIX.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->strukturtype_edit_status->Text = '0';
    }

    public function rcvList_PageIndexChanged($sender,$param) {
        $this->StrukturtypeListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStrukturTypen();
    }

}

?>