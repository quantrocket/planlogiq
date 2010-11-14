<?php

class prtdetailview extends TPage{

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

	private $finder;
	private $MASTERRECORD;
	
	public function onLoad($param){
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
			$this->bindList();
		}
	}
	
	private function generateListFields(){
		$this->idtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type = 4");
		$this->idtm_organisation->dataBind();
			
		$this->idta_protokoll_ergebnistype->DataSource=PFH::build_SQLPullDown(ProtokollErgebnistypeRecord::finder(),"ta_protokoll_ergebnistype",array("idta_protokoll_ergebnistype","prt_ergtype_name"));
		$this->idta_protokoll_ergebnistype->dataBind();
	}
	
	public function bindList(){
		$criteria = new TActiveRecordCriteria();
		$criteria->OrdersBy["auf_tdate"] = 'desc';
		
		$this->ProtokollDetailGroupList->DataSource=ProtokollDetailAufgabeView::finder()->findAll($criteria);
		$this->ProtokollDetailGroupList->dataBind();
		
		$this->generateListFields();
	}
	
	public function loadErgebnistype($sender,$param){		
		$criteria = new TActiveRecordCriteria();
		$criteria->Condition="idta_protokoll_ergebnistype = :suchtext";
		$criteria->Parameters[':suchtext'] = $this->idta_protokoll_ergebnistype->Text;
		
		$this->ProtokollDetailGroupList->DataSource=ProtokollDetailAufgabeView::finder()->findAll($criteria);
		$this->ProtokollDetailGroupList->dataBind();
	}
	
	public function loadOrganisation($sender,$param){
		$criteria = new TActiveRecordCriteria();
		$criteria->Condition="idtm_organisation = :suchtext";
		$criteria->Parameters[':suchtext'] = $this->idtm_organisation->Text;	
		
		$this->ProtokollDetailGroupList->DataSource=ProtokollDetailAufgabeView::finder()->findAll($criteria);
		$this->ProtokollDetailGroupList->dataBind();
	}
	
	public function btnShow_OnClick($sender, $param){
		    $this->mpnlTest->Show();
			$this->load_protokoll_detail($sender, $param);
	}
		
	public function btnClose_OnClick($sender, $param) {
		    $this->mpnlTest->Hide();
	//		$this->bindListAufgaben();
	}
	
	public function load_protokoll_detail($sender,$param){
		$myPrimaryKey = $sender->CommandParameter;
                $myitem=ProtokollDetailRecord::finder()->findByPK($myPrimaryKey);
		$this->viewprtdet_descr->Text = $myitem->prtdet_descr;
	}
	
}
?>