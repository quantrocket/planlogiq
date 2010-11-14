<?php

class a_Projekt_Summary extends TPage
{
	
	public function onLoad($param){
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
			$this->bindRepeaterOrganisation();
			$this->bindRepeaterZiele();	
			$this->bindRepeaterPhasen();
		}
		
	}

        public function prepareForHtml($content){
            return preg_replace("/\n/", "<br/>\n", $content);
        }
	
	public function bindRepeaterOrganisation(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="idta_organisation_type = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = 1;
    		
    		$this->RepOrganisation->VirtualItemCount = count(OrganisationRecord::finder()->findAll($criteria));
			
			$criteria->setLimit($this->RepOrganisation->PageSize);
			$criteria->setOffset($this->RepOrganisation->PageSize * $this->RepOrganisation->CurrentPageIndex);
			
			$this->RepOrganisation->VirtualItemCount = count(OrganisationRecord::finder()->findAll());
			$this->RepOrganisation->DataSource=OrganisationRecord::finder()->findAll($criteria);
			$this->RepOrganisation->dataBind();
	}
	
	public function bindRepeaterOrganisation2($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="idta_organisation_type = :suchtext1 AND parent_idtm_organisation = :suchtext2";
	    		$criteria->Parameters[':suchtext1'] = 2;
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_organisation;
	    		
	    		$item->RepOrganisation2->DataSource=OrganisationRecord::finder()->findAll($criteria);
				$item->RepOrganisation2->dataBind();
	       	}
	}
	
	public function bindRepeaterOrganisation3($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="idta_organisation_type = :suchtext1 AND parent_idtm_organisation = :suchtext2";
	    		$criteria->Parameters[':suchtext1'] = 3;
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_organisation;
	    		
	    		if(count(OrganisationRecord::finder()->findAll($criteria))==0){
	    			$this->bindRepeaterOrganisation5($sender,$param);
	    		}
	    		
	    		$item->RepOrganisation3->DataSource=OrganisationRecord::finder()->findAll($criteria);
				$item->RepOrganisation3->dataBind();
	       	}
	}
	
	public function bindRepeaterOrganisation4($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="idta_organisation_type = :suchtext1 AND parent_idtm_organisation = :suchtext2";
	    		$criteria->Parameters[':suchtext1'] = 4;
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_organisation;
	    		
	    		$item->RepOrganisation4->DataSource=OrganisationRecord::finder()->findAll($criteria);
				$item->RepOrganisation4->dataBind();
	       	}
	}
	
	public function bindRepeaterOrganisation5($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="idta_organisation_type = :suchtext1 AND parent_idtm_organisation = :suchtext2";
	    		$criteria->Parameters[':suchtext1'] = 4;
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_organisation;
	    		
	    		$item->RepOrganisation4->DataSource=OrganisationRecord::finder()->findAll($criteria);
				$item->RepOrganisation4->dataBind();
	       	}
	}
	
	public function bindRepeaterZiele(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="idta_ziele_type = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = 1;
    		
    		$this->RepOrganisation->VirtualItemCount = count(ZieleRecord::finder()->findAll($criteria));
			
			$criteria->setLimit($this->RepZiele->PageSize);
			$criteria->setOffset($this->RepZiele->PageSize * $this->RepZiele->CurrentPageIndex);
			
			$this->RepZiele->VirtualItemCount = count(ZieleRecord::finder()->findAll());
			$this->RepZiele->DataSource=ZieleRecord::finder()->findAll($criteria);
			$this->RepZiele->dataBind();
	}
	
	public function bindRepeaterZiele2($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="idta_ziele_type = :suchtext1 AND parent_idtm_ziele = :suchtext2";
	    		$criteria->Parameters[':suchtext1'] = 2;
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_ziele;
	    		
	    		$item->RepZiele2->DataSource=ZieleRecord::finder()->findAll($criteria);
				$item->RepZiele2->dataBind();
	       	}
	}
	
	public function bindRepeaterZiele3($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="idta_ziele_type = :suchtext1 AND parent_idtm_ziele = :suchtext2";
	    		$criteria->Parameters[':suchtext1'] = 3;
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_ziele;
	    		
	    		$item->RepZiele3->DataSource=ZieleRecord::finder()->findAll($criteria);
				$item->RepZiele3->dataBind();
	       	}
	}
	
	public function bindRepeaterZiele4($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="idtm_ziele = :suchtext2";
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_ziele;
	    		
	    		$item->RepZiele4->DataSource=TTZieleRecord::finder()->findAll($criteria);
				$item->RepZiele4->dataBind();
	       	}
	}
	
	public function bindRepeaterAufgabenZiele($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="auf_tabelle = :suchtext1 AND auf_id = :suchtext2";
	    		$criteria->Parameters[':suchtext1'] = "tt_ziele";
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtt_ziele;
	    		
	    		$item->RepAufZiele->DataSource=AufgabenRecord::finder()->findAll($criteria);
				$item->RepAufZiele->dataBind();
	       	}
	}
	
	public function bindRepeaterPhasen(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="idta_activity_type = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = 2;
    		
    		$this->RepPhasen->VirtualItemCount = count(ActivityRecord::finder()->findAll($criteria));
			
			$criteria->setLimit($this->RepPhasen->PageSize);
			$criteria->setOffset($this->RepPhasen->PageSize * $this->RepPhasen->CurrentPageIndex);
			
			$this->RepPhasen->VirtualItemCount = count(ActivityRecord::finder()->findAll());
			$this->RepPhasen->DataSource=ActivityRecord::finder()->findAll($criteria);
			$this->RepPhasen->dataBind();
			
			$this->generatePhasenGraph(ActivityRecord::finder()->findAll());
	}
	
	public function bindRepeaterPhasen2($sender,$param){
		
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
        		$criteria = new TActiveRecordCriteria();
        
	    		$criteria->Condition ="parent_idtm_activity = :suchtext2";
	    		$criteria->Parameters[':suchtext2'] = $item->Data->idtm_activity;
	    		
	    		$item->RepPhasen2->DataSource=ActivityRecord::finder()->findAll($criteria);
				$item->RepPhasen2->dataBind();
	       	}
	}
	
	private function generatePhasenGraph($activitys,$mytitle="title"){
		
		foreach($activitys AS $activity){
			$xdatalabel[]=$activity->act_name;
			$xdataorder[]=$activity->act_step;
			$xdatastart[]=$activity->act_startdate;
			$xdataende[]=$activity->act_enddate;
			$xdataprogress[]=$activity->act_fortschritt;
			if($activity->idta_activity_type == 1){
				$xdatamilestone[]=1;
			}else{
				$xdatamilestone[]=0;
			}
		}
		
		$datalabel = implode(',', $xdatalabel);
		$dataorder = implode(',', $xdataorder);
		$datastart = implode(',', $xdatastart);
		$dataende = implode(',', $xdataende);
		$datamilestone = implode(',', $xdatamilestone);
		$dataprogress = implode(',', $xdataprogress);
		
		$this->PhasenImage->ImageUrl = $this->getRequest()->constructUrl('page','gantt',1,array( 'datalabel' => $datalabel,'dataorder' => $dataorder,'datastart' => $datastart,'dataende' => $dataende,'datamilestone' => $datamilestone,'dataprogress' => $dataprogress, 'scale' => "month", 'title' => $mytitle), false);
	}
}
?>