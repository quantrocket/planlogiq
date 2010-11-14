<?php

class promapview extends TPage
{
	
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param){
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
			
			$tree = $this->MyTree;
			$node = new ProzessRecord();
			PFH::fullInTreeProzessRecord($tree, $node);
		
			$NEWRECORD = $this->NewRecord;
			$NEWRECORD->setText("neues Element anlegen");
			$NEWRECORD->setToPage("prozess.prozessview");
			$NEWRECORD->setGetVariables('modus=0');
			
			$NEWSTRECORD = $this->NewStepRecord;
			$NEWSTRECORD->setText("neuen Prozessschritt anlegen");
			$NEWSTRECORD->setToPage("prozess.prozessstepview");
			$NEWSTRECORD->setGetVariables('modus=0');
		
			$this->bindListOrgListe();
		}
		
	}
	
	
    public function bindListOrgListe(){
    	
    		$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="idta_prozess_type = :suchtext1 OR idta_prozess_type = :suchtext2 OR idta_prozess_type = :suchtext3";
    		$criteria->Parameters[':suchtext1'] = "1";
    		$criteria->Parameters[':suchtext2'] = "4";
    		$criteria->Parameters[':suchtext3'] = "2";
    		
    		$this->OrgListe->VirtualItemCount = count(ProzessRecord::finder()->withprotype()->findAll($criteria));
    		
    		$criteria->setLimit($this->OrgListe->PageSize);
			$criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
			$this->OrgListe->DataKeyField = 'idtm_prozess';
			
			$this->OrgListe->DataSource=ProzessRecord::finder()->withprotype()->findAll($criteria);
			$this->OrgListe->dataBind();
			
    }
    
	public function dtgList_viewChildren($sender,$param){
    	
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="parent_idtm_prozess = :suchtext1 AND (idta_prozess_type <> '1' AND idta_prozess_type <> '4' AND idta_prozess_type <> '2')";
    		$criteria->Parameters[':suchtext1'] = $param->Item->lst_pro_idtm_prozess->Text;
    		$criteria->setLimit($this->ProListe->PageSize);
			$criteria->setOffset($this->ProListe->PageSize * $this->ProListe->CurrentPageIndex);
			$this->ProListe->DataKeyField = 'idtm_prozess';
			
			$this->ProListe->VirtualItemCount = count(ProzessRecord::finder()->withprotype()->findAll($criteria));
			$this->ProListe->DataSource=ProzessRecord::finder()->withprotype()->findAll($criteria);
			$this->ProListe->dataBind();
			
			$this->clearProStep();
    }
	
    public function bindListProStepListe($sender,$param){
    	
    		$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="idtm_prozess = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = $param->Item->lst_propro_idtm_prozess->Text;
    		$criteria->setLimit($this->ProStepListe->PageSize);
			$criteria->setOffset($this->ProStepListe->PageSize * $this->ProStepListe->CurrentPageIndex);
			$this->ProStepListe->DataKeyField = 'idtm_prozess_step';
			
			$this->ProStepListe->VirtualItemCount = count(ProzessStepRecord::finder()->findAll($criteria));
			$this->ProStepListe->DataSource=ProzessStepRecord::finder()->findAll($criteria);
			$this->ProStepListe->dataBind();
    	
    }
    
	public function dtgList_PageIndexChanged($sender,$param)
		{
			$this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListOrgListe();
		}

		public function prostepList_PageIndexChanged($sender,$param)
		{
			$this->ProStepListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListProStepListe();
		}
	
	public function dtgList_deleteCommand($sender,$param)
		{
			$item=$param->Item;
			$finder = ProzessRecord::finder();
			$finder->deleteAll('idtm_prozess = ?',$item->lst_org_idtm_prozess->Text);
			$this->bindListOrgListe();
		}
    
	public function searchOrg($sender,$param){
    	
    		$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="pro_name LIKE :suchtext";
    		$criteria->Parameters[':suchtext'] = "%".$this->find_org->Text."%";
			$criteria->setLimit($this->OrgListe->PageSize);
			$criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
			$this->OrgListe->DataKeyField = 'idtm_prozess';
			
			$this->OrgListe->VirtualItemCount = count(ProzessRecord::finder()->withprotype()->find($criteria));
			$this->OrgListe->DataSource=ProzessRecord::finder()->withprotype()->findAll($criteria);
			$this->OrgListe->dataBind();
    	
    }
	
    public function clearProStep(){
    	
    		$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="prostep_name LIKE :suchtext";
    		$criteria->Parameters[':suchtext'] = "%leer%";
			$criteria->setLimit($this->ProStepListe->PageSize);
			$criteria->setOffset($this->ProStepListe->PageSize * $this->ProStepListe->CurrentPageIndex);
			$this->ProStepListe->DataKeyField = 'idtm_prozess_step';
			
			$this->ProStepListe->VirtualItemCount = count(ProzessStepRecord::finder()->find($criteria));
			$this->ProStepListe->DataSource=ProzessStepRecord::finder()->findAll($criteria);
			$this->ProStepListe->dataBind();
    	
    }
    
 	
	public function dtgList_editCommand($sender,$param)
    {
        $url=$this->getRequest()->constructUrl('page',"prozess.prozessview",array('modus'=>'1','idtm_prozess'=>$param->Item->lst_pro_idtm_prozess->Text));
        $this->Response->redirect($url);
    }
    
	public function proList_editCommand($sender,$param)
    {
        $url=$this->getRequest()->constructUrl('page',"prozess.prozessview",array('modus'=>'1','idtm_prozess'=>$param->Item->lst_propro_idtm_prozess->Text));
        $this->Response->redirect($url);
    }

	public function prostepList_editCommand($sender,$param)
    {
        $url=$this->getRequest()->constructUrl('page',"prozess.prozessstepview",array('modus'=>'1','idtm_prozess_step'=>$param->Item->lst_prostep_idtm_prozess_step->Text));
        $this->Response->redirect($url);
    }
}
?>