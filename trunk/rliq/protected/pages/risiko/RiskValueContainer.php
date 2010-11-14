<?php

class RiskValueContainer extends TTemplateControl {

    public function onInit($param) {
        parent::onInit($param);
    }

    public function onLoad($param) {
        parent::onLoad($param);
        $this->initParameter();
        if(!$this->page->isPostBack && !$this->page->isCallback){
            $this->createRiskPullDown();
            $this->createTTRiskPullDown();
            $this->createNETTTRiskPullDown();
        }
    }

    public function initParameter(){
        $this->RCedrcv_tabelle->Text = $this->page->RCedrcv_tabelle->Text;
        $this->RCedrcv_id->Text = $this->page->RCedrcv_id->Text;
    }

    public function createRiskPullDown() {
        //risiko oder chance
        $data=array(0=>"Risiko",1=>"Chance");
        $this->RCedrcv_type->DataSource = $data;
        $this->RCedrcv_type->DataBind();
        //die Risikoklasse
        $this->RCedidtm_risiko->DataSource=PFH::build_SQLPullDown(RisikoRecord::finder(),"tm_risiko",array("idtm_risiko","ris_name"));
        $this->RCedidtm_risiko->dataBind();
    }

    public function createTTRiskPullDown() {
    //ewk
        $data=array(0=>"leer",1=>"10 Prozent",2=>"20 Prozent",3=>"30 Prozent",4=>"40 Prozent",5=>"50 Prozent",6=>"60 Prozent",7=>"70 Prozent",8=>"80 Prozent",9=>"90 Prozent");
        $this->RCTTedrcv_ewk->DataSource = $data;
        $this->RCTTedrcv_ewk->DataBind();
        //prio
        $data=array(0=>"leer",1=>"10",2=>"20",3=>"30",4=>"40",5=>"50",6=>"60",7=>"70",8=>"80",9=>"90");
        $this->RCTTedrcv_prio->DataSource = $data;
        $this->RCTTedrcv_prio->DataBind();
    }

    public function createNETTTRiskPullDown() {
    //ewk
        $data=array(0=>"leer",1=>"10 Prozent",2=>"20 Prozent",3=>"30 Prozent",4=>"40 Prozent",5=>"50 Prozent",6=>"60 Prozent",7=>"70 Prozent",8=>"80 Prozent",9=>"90 Prozent");
        $this->NETRCTTedrcv_ewk->DataSource = $data;
        $this->NETRCTTedrcv_ewk->DataBind();
        //prio
        $data=array(0=>"leer",1=>"10",2=>"20",3=>"30",4=>"40",5=>"50",6=>"60",7=>"70",8=>"80",9=>"90");
        $this->NETRCTTedrcv_prio->DataSource = $data;
        $this->NETRCTTedrcv_prio->DataBind();
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idtm_rcvalue";
    private $RCTTprimarykey = "idtt_rcvalue";
    private $NETRCTTprimarykey = "idtt_rcvalue";
    private $RCfields = array("idtm_organisation","rcv_tabelle","rcv_id","rcv_comment","idtm_risiko","rcv_type");
    private $RCTTfields = array("rcv_cby","rcv_ewk","rcv_schaden","rcv_prio","idtm_rcvalue");
    private $NETRCTTfields = array("rcv_cby","rcv_ewk","rcv_schaden","rcv_prio","rcv_kosten","rcv_descr","idtm_rcvalue");
    private $RCdatfields = array();
    private $RCTTdatfields = array();
    private $NETRCTTdatfields = array();
    private $RChiddenfields = array();
    private $RCTThiddenfields = array();
    private $NETRCTThiddenfields = array();
    private $RCboolfields = array();
    private $RCTTboolfields = array();
    private $NETRCTTboolfields = array();

    public function bindListRCValue() {

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="rcv_tabelle = :suchtext1 AND rcv_id =:suchtext2";
        $criteria->Parameters[':suchtext1'] = $this->RCedrcv_tabelle->Text;
        $criteria->Parameters[':suchtext2'] = $this->RCedrcv_id->Text;

        $this->RCValueListe->VirtualItemCount = RCValueRecord::finder()->count($criteria);

        $criteria->setLimit($this->RCValueListe->PageSize);
        $criteria->setOffset($this->RCValueListe->PageSize * $this->RCValueListe->CurrentPageIndex);
        $this->RCValueListe->DataKeyField = 'idtm_rcvalue';

        $this->RCValueListe->DataSource=RCValueRecord::finder()->findAll($criteria);
        $this->RCValueListe->dataBind();
    }

    public function bindListRCTTValue() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idtm_rcvalue = :suchtext1 ORDER BY rcv_cdate DESC";
        $criteria->Parameters[':suchtext1'] = $this->RCedidtm_rcvalue->Text;

        $this->RCTTValueListe->VirtualItemCount = count(RCTTValueRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->RCTTValueListe->PageSize);
        $criteria->setOffset($this->RCTTValueListe->PageSize * $this->RCTTValueListe->CurrentPageIndex);
        $this->RCTTValueListe->DataKeyField = 'idtt_rcvalue';

        $this->RCTTValueListe->VirtualItemCount = count(RCTTValueRecord::finder()->findAll());
        $this->RCTTValueListe->DataSource=RCTTValueRecord::finder()->findAll($criteria);
        $this->RCTTValueListe->dataBind();
        $this->generateRisikoGraph($this->RCTTValueListe->DataSource);
        $this->generateNettoRisikoGraph(RCTTValueNettoRecord::finder()->findAll($criteria));
    }

    public function load_rcvalue($sender,$param) {

        $item = $param->Item;
        $myitem=RCValueRecord::finder()->findByPK($item->lst_rcvalue_idtm_rcvalue->Text);

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

        $this->RCedrcvalue_edit_status->Text = 1;
        $this->RCTTedidtm_rcvalue->Text = $item->lst_rcvalue_idtm_rcvalue->Text;
        $this->NETRCTTedidtm_rcvalue->Text = $item->lst_rcvalue_idtm_rcvalue->Text;

        $this->bindListRCTTValue();
    }

    public function load_rcttvalue($sender,$param) {

        $item = $param->Item;
        $myitem=RCTTValueRecord::finder()->findByPK($item->lst_rcttvalue_idtt_rcvalue->Text);

        $tempus = 'RCTTed'.$this->RCTTprimarykey;
        $monus = $this->RCTTprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RCTThiddenfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCTTdatfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCTTboolfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->RCTTfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->RCTTedrcvalue_edit_status->Text = 1;
        $this->load_netrcttvalue($sender, $param);
    }

    public function load_netrcttvalue($sender,$param) {

        $item = $param->Item;
        $myitem=RCTTValueNettoRecord::finder()->findByPK($item->lst_rcttvalue_idtt_rcvalue->Text);

        $tempus = 'NETRCTTed'.$this->NETRCTTprimarykey;
        $monus = $this->NETRCTTprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->NETRCTThiddenfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->NETRCTTdatfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->NETRCTTboolfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->NETRCTTfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->RCTTedrcvalue_edit_status->Text = 1;
    }

    public function RCSavedButtonClicked($sender,$param) {

        $tempus='RCed'.$this->RCprimarykey;

        if($this->RCedrcvalue_edit_status->Text == '1') {
            $RCEditRecord = RCValueRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new RCValueRecord;
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

        $this->RCedrcvalue_edit_status->Text = 1;
        $this->RCedidtm_rcvalue->Text = $RCEditRecord->idtm_rcvalue;

        $this->bindListRCValue();
        $this->bindListRCTTValue();
    }

    public function RCTTSavedButtonClicked($sender,$param) {

        $tempus='RCTTed'.$this->RCTTprimarykey;

        if($this->RCTTedrcvalue_edit_status->Text == '1') {
            $RCTTEditRecord = RCTTValueRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCTTEditRecord = new RCTTValueRecord;
        }

        //HIDDEN
        foreach ($this->RCTThiddenfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCTTdatfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCTTboolfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCTTfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCTTEditRecord->save();

        $this->NETRCTTSavedButtonClicked($sender, $param);
    }

    public function NETRCTTSavedButtonClicked($sender,$param) {

        $tempus='NETRCTTed'.$this->NETRCTTprimarykey;

        if($this->RCTTedrcvalue_edit_status->Text == '1') {
            $RCTTEditRecord = RCTTValueNettoRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCTTEditRecord = new RCTTValueNettoRecord;
        }

        //HIDDEN
        foreach ($this->NETRCTThiddenfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->NETRCTTdatfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->NETRCTTboolfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->NETRCTTfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $RCTTEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCTTEditRecord->save();

        $this->bindListRCTTValue();
    }

    public function RCNewButtonClicked($sender,$param) {

        $myidea = $this->RCedrcv_id->Text;
        $mytabelle = $this->RCedrcv_tabelle->Text;

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
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->RCedrcvalue_edit_status->Text = '0';
        $this->RCedrcv_tabelle->Text = $mytabelle;
        $this->RCedrcv_id->Text = $myidea;
    }

    public function RCTTNewButtonClicked($sender,$param) {

        $mysecondidea = $this->RCTTedidtm_rcvalue->Text;

        $tempus = 'RCTTed'.$this->RCTTprimarykey;
        $monus = $this->RCTTprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RCTThiddenfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RCTTdatfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->RCTTboolfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->RCTTfields as $recordfield) {
            $edrecordfield = 'RCTTed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->RCTTedrcvalue_edit_status->Text = '0';
        $this->RCTTedidtm_rcvalue->Text = $mysecondidea;

        $this->NETRCTTNewButtonClicked($sender, $param);
    }

    public function NETRCTTNewButtonClicked($sender,$param) {

        $mysecondidea = $this->NETRCTTedidtm_rcvalue->Text;

        $tempus = 'NETRCTTed'.$this->NETRCTTprimarykey;
        $monus = $this->NETRCTTprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->NETRCTThiddenfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->NETRCTTdatfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->NETRCTTboolfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->NETRCTTfields as $recordfield) {
            $edrecordfield = 'NETRCTTed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->NETRCTTedidtm_rcvalue->Text = $mysecondidea;

        $this->bindListRCTTValue();
    }

    public function rcvList_PageIndexChanged($sender,$param) {
        $this->RCValueListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListRCValue();
    }

    public function rcvttList_PageIndexChanged($sender,$param) {
        $this->RCTTValueListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListRCTTValue();
    }

    private function generateRisikoGraph($ActiveRecord) {

        $ydata1 = array();
        $ydata2 = array();
        $xdata = array();
        $ytitle = array("SH","EW");

        $ii=0;

        foreach ($ActiveRecord as $DetailRecord) {
            $xdata[] = $DetailRecord->idtt_rcvalue;
            $ydata1[] = $DetailRecord->rcv_schaden;
            $ydata2[] = $DetailRecord->rcv_prio;
            $ii++;
            if($ii > 10) {
                break;
            }
        }

        $ydata1 = implode(',', $ydata1);
        $ydata2 = implode(',', $ydata2);
        $xdata = implode(',', $xdata);
        $ytitledata = implode(',', $ytitle);
        $this->RisikoVerlaufImage->ImageUrl = $this->getRequest()->constructUrl('page','graph', 1, array( 'xdata' => $xdata, 'ydata1' => $ydata1, 'ydata2' => $ydata2, 'ytitle' => $ytitledata), false);
    }

    private function generateNettoRisikoGraph($ActiveRecord) {

        $ydata1 = array();
        $ydata2 = array();
        $xdata = array();
        $ytitle = array("SH","EW");

        $ii=0;

        foreach ($ActiveRecord as $DetailRecord) {
            $xdata[] = $DetailRecord->idtt_rcvalue;
            $ydata1[] = $DetailRecord->rcv_schaden;
            $ydata2[] = $DetailRecord->rcv_prio;
            $ii++;
            if($ii > 10) {
                break;
            }
        }

        $ydata1 = implode(',', $ydata1);
        $ydata2 = implode(',', $ydata2);
        $xdata = implode(',', $xdata);
        $ytitledata = implode(',', $ytitle);
        $this->RisikoVerlaufNettoImage->ImageUrl = $this->getRequest()->constructUrl('page','graph', 1, array( 'xdata' => $xdata, 'ydata1' => $ydata1, 'ydata2' => $ydata2, 'ytitle' => $ytitledata), false);
    }

//ENDE DER RISIKEN
//ENDE DER RISIKEN
//ENDE DER RISIKEN


}

?>