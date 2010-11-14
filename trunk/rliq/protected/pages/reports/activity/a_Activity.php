<?php

class a_Activity extends TPage
{
	
	public $idtm_activity;
	private $ActivityRecord;
	
	public function onLoad($param){
		
		parent::onLoad($param);
		if(!$this->isPostBack && !$this->isCallback){
			$this->idtm_activity = $this->Request['idtm_activity'];
			$this->getSelected();
			$this->ActivityZielebindList();
			$this->bindListAufgaben();
			$this->bindListInput();
			$this->bindListOutput();
			$this->bindListProtokollDetail();
			
			$this->act_pspcode->Text = $this->ActivityRecord->act_pspcode;
			$this->act_name->Text = $this->ActivityRecord->act_name;
			$this->act_dauer->Text = $this->ActivityRecord->act_dauer;
			$this->act_descr->Text = $this->ActivityRecord->act_descr;
			$this->act_dauerIST->Text = "0";
			$this->idtm_organisation->Text = OrganisationRecord::finder()->findByPk($this->ActivityRecord->idtm_organisation)->org_name;
			$this->act_startdate->Text = $this->ActivityRecord->act_startdate;
			$this->act_enddate->Text = $this->ActivityRecord->act_enddate;
		}
	}
	
	private function bindListProtokollDetail(){
			//noch einbinden, welche Ziele erf�llt werden
			$sql = "SELECT idtt_ziele FROM tm_activity_has_tt_ziele WHERE idtm_activity = ".$this->idtm_activity;
			$meineziele = ActivityZieleRecord::finder()->findBySQL($sql);
			
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "idtt_ziele = :suchtext";
    		$criteria->Parameters[':suchtext'] = $meineziele->idtt_ziele;
			$criteria->OrdersBy["idtm_protokoll_detail"] = 'asc';
    		
			$this->RepPrtDetailListe->DataSource=ProtokollDetailRecord::finder()->findAll($criteria);
			$this->RepPrtDetailListe->dataBind();
	}
	
	private function bindListInput(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "idtm_activity = :suchtext AND ino_link_type = 1";
    		$criteria->Parameters[':suchtext'] = $this->idtm_activity;
			$criteria->OrdersBy["idtm_activity"] = 'asc';
    		
			$this->RepInputListe->DataSource=ActivityInoutputView::finder()->findAll($criteria);
			$this->RepInputListe->dataBind();
	}
	
	private function bindListOutput(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "idtm_activity = :suchtext AND ino_link_type = 0";
    		$criteria->Parameters[':suchtext'] = $this->idtm_activity;
			$criteria->OrdersBy["idtm_activity"] = 'asc';
    		
			$this->RepOutputListe->DataSource=ActivityInoutputView::finder()->findAll($criteria);
			$this->RepOutputListe->dataBind();
	}
	
	public function bindListAufgaben(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="auf_tabelle = 'tm_activity' AND auf_id = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = $this->idtm_activity;
    		
			$this->RepAufgabenListe->DataSource=AufgabenRecord::finder()->findAll($criteria);
			$this->RepAufgabenListe->dataBind();
    }
	
	private function ActivityZielebindList(){		
		$criteria = new TActiveRecordCriteria();
    	$criteria->Condition = "idtm_activity = :suchtext";
    	$criteria->Parameters[':suchtext'] = $this->idtm_activity;
		$criteria->OrdersBy["idtm_activity"] = 'asc';
    	
    	$this->RepZieleListe->VirtualItemCount = count(ActivityZieleView::finder()->findAll($criteria));
  
		$this->RepZieleListe->DataSource=ActivityZieleView::finder()->findAll($criteria);
		$this->RepZieleListe->dataBind();
	}
	
	protected function bindRepProtokollDetail(){
		$criteria = new TActiveRecordCriteria();
    	$criteria->Condition ="idtm_protokoll LIKE :suchtext";
    	$criteria->Parameters[':suchtext'] = "%".$this->idtm_protokoll."%";
		$this->RepProtokollDetail->DataSource=ProtokollDetailAufgabeView::finder()->findAll($criteria);
		$this->RepProtokollDetail->dataBind();	
	}
	
	protected function getSelected(){
		$this->ActivityRecord = ActivityRecord::finder()->findByPk($this->idtm_activity);
	}
	
}
?>