<?php

class StrukturStrukturContainer extends TTemplateControl {

    public function onInit($param) {
        parent::onInit($param);
    //$this->bindListStrukturStrukturValue();
    }

    public function onLoad($param) {
        parent::onLoad($param);
        $this->SSedidtm_struktur_from->Text = $this->page->idtm_struktur->Text;
        if(!$this->page->isPostBack && !$this->page->isCallback) {
            $this->buildStrukturStrukturPullDown();
            $this->bindListStrukturStrukturValue();
        }
    }

    public function buildStrukturStrukturPullDown() {
        $this->SSedidta_feldfunktion->DataSource = PFH::build_SQLPullDown(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name"),"idta_struktur_type = ".$this->page->idta_struktur_type->Text);
        $this->SSedidta_feldfunktion->DataBind();

        $StrukRec = StrukturRecord::finder()->findByidtm_struktur($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_struktur"));
        $this->SSedidtm_struktur_to->DataSource = PFH::build_SQLPullDown(StrukturRecord::finder(),"tm_struktur INNER JOIN ta_feldfunktion ON tm_struktur.idta_struktur_type = ta_feldfunktion.idta_struktur_type",array("idtm_struktur","struktur_name"),"ff_type=3 AND struktur_lft BETWEEN ".$StrukRec->struktur_lft." AND ".$StrukRec->struktur_rgt);
        $this->SSedidtm_struktur_to->DataBind();
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $SSprimarykey = "idtm_struktur_tm_struktur";
    private $SSfields = array("idtm_struktur_from","idtm_struktur_to","idta_feldfunktion");
    private $SSdatfields = array();
    private $SShiddenfields = array();
    private $SSboolfields = array();

    public function SSClosedButtonClicked($sender, $param) {
        $this->page->mpnlStrukturStruktur->Hide();
    }

    public function bindListStrukturStrukturValue() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="idtm_struktur_from = :suchbedingung1";
        $criteria->Parameters[':suchbedingung1'] = $this->page->idtm_struktur->Text;

        $this->StrukturStrukturListe->VirtualItemCount = count(StrukturStrukturRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->StrukturStrukturListe->PageSize);
        $criteria->setOffset($this->StrukturStrukturListe->PageSize * $this->StrukturStrukturListe->CurrentPageIndex);
        $this->StrukturStrukturListe->DataKeyField = 'idtm_struktur_tm_struktur';

        $this->StrukturStrukturListe->DataSource=StrukturStrukturRecord::finder()->findAll($criteria);
        $this->StrukturStrukturListe->dataBind();
    }

    public function SSDeleteButtonClicked($sender,$param) {

        $tempus='SSed'.$this->SSprimarykey;

        if($this->SSedstrukturstruktur_edit_status->Text == '1') {
            $SSeditRecord = StrukturStrukturRecord::finder()->findByPK($this->$tempus->Text);
            $SSeditRecord->delete();
        }
        $this->bindListStrukturStrukturValue();
    }

    public function load_StrukturStruktur($sender,$param) {

        $item = $param->Item;
        $myitem=StrukturStrukturRecord::finder()->findByPK($item->ss_idtm_struktur_tm_struktur->Text);

        $tempus = 'SSed'.$this->SSprimarykey;
        $monus = $this->SSprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->SShiddenfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->SSdatfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->SSboolfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->SSfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->SSedstrukturstruktur_edit_status->Text = 1;
        $this->SSedidtm_struktur_tm_struktur->Text = $item->ss_idtm_struktur_tm_struktur->Text;

    }

    public function SSSavedButtonClicked($sender,$param) {

        $tempus='SSed'.$this->SSprimarykey;

        if($this->SSedstrukturstruktur_edit_status->Text == '1') {
            $SSeditRecord = StrukturStrukturRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $SSeditRecord = new StrukturStrukturRecord;
        }

        //HIDDEN
        foreach ($this->SShiddenfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $SSeditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->SSdatfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $SSeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->SSboolfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $SSeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->SSfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $SSeditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $SSeditRecord->save();

        $this->bindListStrukturStrukturValue();
    }

    public function SSNewButtonClicked($sender,$param) {

        $pivotbericht = $this->page->idtm_struktur->Text;

        $tempus = 'SSed'.$this->SSprimarykey;
        $monus = $this->SSprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->SShiddenfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->SSdatfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->SSboolfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->SSfields as $recordfield) {
            $edrecordfield = 'SSed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->SSedidtm_struktur_from->Text = $pivotbericht;
        $this->SSedstrukturstruktur_edit_status->Text = '0';
    }


    public function rcvList_PageIndexChanged($sender,$param) {
        $this->StrukturStrukturListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStrukturStrukturValue();
    }


//ENDE DER RISIKEN
//ENDE DER RISIKEN
//ENDE DER RISIKEN


}

?>