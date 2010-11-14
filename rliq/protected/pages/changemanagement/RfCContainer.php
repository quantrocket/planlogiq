<?php

class RfCContainer extends TTemplateControl
{

        public function onLoad($param){
		parent::onLoad($param);
                if(!$this->page->isPostBack && !$this->page->isCallback){
                    $this->createRfCPullDown();
                    $this->bindListRfCValue();
                }
	}
	
	public function createRfCPullDown(){
            //Als erstes die Organisation
            $this->RfCedsuggest_idtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type = 4");
            $this->RfCedsuggest_idtm_organisation->dataBind();
            //Als erstes die Organisation
            $this->RfCedgenemigt_idtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type = 4");
            $this->RfCedgenemigt_idtm_organisation->dataBind();
            //einlesen der aktivitaeten
            $this->RfCedidtm_activity->DataSource=PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_pspcode","act_name"));
            $this->RfCedidtm_activity->dataBind();
        }


	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

	private $RCprimarykey = "idtm_changerequest";
	private $RCfields = array("rfc_descr","rfc_ifnot","idtm_activity","rfc_code","suggest_idtm_organisation","genemigt_idtm_organisation","rfc_dauer");
	private $RCdatfields = array("rfc_suggestdate","rfc_gdate");
	private $RChiddenfields = array();
	private $RCboolfields = array("rfc_status");

	public function bindListRfCValue(){

                $this->ChangeListe->VirtualItemCount = count(RfCRecord::finder()->findAll());

                $criteria = new TActiveRecordCriteria();
                $criteria->setLimit($this->ChangeListe->PageSize);
		$criteria->setOffset($this->ChangeListe->PageSize * $this->ChangeListe->CurrentPageIndex);
		$this->ChangeListe->DataKeyField = 'idtm_rcvalue';

		$this->ChangeListe->VirtualItemCount = count(RfCRecord::finder()->findAll());
		$this->ChangeListe->DataSource=RfCRecord::finder()->findAll($criteria);

		$this->ChangeListe->dataBind();
    }

    public function load_rfcvalue($sender,$param){

    	$item = $param->Item;
    	$myitem=RfCRecord::finder()->findByPK($item->lst_idtm_changerequest->Text);

    	$tempus = 'RfCed'.$this->RCprimarykey;
		$monus = $this->RCprimarykey;

		$this->$tempus->Text = $myitem->$monus;

    	//HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}

		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}

		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}

		//NON DATUM
		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}

		$this->RfCedrfc_edit_status->Text = 1;
		$this->RfCedidtm_changerequest->Text = $item->lst_idtm_changerequest->Text;

    }

	public function RCSavedButtonClicked($sender,$param){

		$tempus='RfCed'.$this->RCprimarykey;

		if($this->RfCedrfc_edit_status->Text == '1'){
			$RCEditRecord = RfCRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$RCEditRecord = new RfCRecord;
		}

		//HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Value;
		}

		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}

		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}

		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$RCEditRecord->save();

		$this->bindListRfCValue();
	}

	public function RCNewButtonClicked($sender,$param){

	$tempus = 'RfCed'.$this->RCprimarykey;
	$monus = $this->RCprimarykey;

	$this->$tempus->Text = '0';

    	//HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}

		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}

		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
                        $this->$edrecordfield->setChecked(0);
		}

		//NON DATUM
		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'RfCed'.$recordfield;
			$this->$edrecordfield->Text = '0';
		}

		$this->RfCedrfc_edit_status->Text = '0';
    }


	public function rcvList_PageIndexChanged($sender,$param)
		{
			$this->ChangeListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListRfCValue();
		}

    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN


}

?>