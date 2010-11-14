<?php

Prado::using('Application.app_code.MyTreeList');

class risworkspace extends TPage
{
    
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

	public function onLoad($param){
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
		
			$NEWRECORD = $this->NewRecord;
			$NEWRECORD->setText("neues Element anlegen");
			$NEWRECORD->setToPage("risiko.risikoview");
			$NEWRECORD->setGetVariables('modus=0');
			
			$RISKNAV = $this->RiskNav;
			$RISKNAV->setText("Risikonavigator");
			$RISKNAV->setToPage("risiko.risikonavigator");
		
			$this->bindListOrgListe();
		}
		
	}
	
	
    public function bindListOrgListe(){
    	
    		$criteria = new TActiveRecordCriteria();
    		$criteria->setLimit($this->OrgListe->PageSize);
			$criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
			$this->OrgListe->DataKeyField = 'idtm_risiko';
			
			$this->OrgListe->VirtualItemCount = count(RisikoRecord::finder()->withristype()->findAll());
			$this->OrgListe->DataSource=RisikoRecord::finder()->withristype()->findAll($criteria);
			$this->OrgListe->dataBind();
    	
    }
    
	public function dtgList_PageIndexChanged($sender,$param)
		{
			$this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListOrgListe();
		}
		
	public function dtgList_deleteCommand($sender,$param)
		{
			$item=$param->Item;
			$finder = RisikoRecord::finder();
			$finder->deleteAll('idtm_risiko = ?',$item->lst_org_idtm_risiko->Text);
			$this->bindListOrgListe();
		}
    
	public function searchOrg($sender,$param){
    	
    		$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="ris_name LIKE :suchtext";
    		$criteria->Parameters[':suchtext'] = "%".$this->find_org->Text."%";
			$criteria->setLimit($this->OrgListe->PageSize);
			$criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
			$this->OrgListe->DataKeyField = 'idtm_risiko';
			
			$this->OrgListe->VirtualItemCount = count(RisikoRecord::finder()->withristype()->find($criteria));
			$this->OrgListe->DataSource=RisikoRecord::finder()->withristype()->findAll($criteria);
			$this->OrgListe->dataBind();
    	
    }
    
 	
	public function dtgList_editCommand($sender,$param)
    {
        $url=$this->getRequest()->constructUrl('page',"risiko.risikoview",array('modus'=>'1','idtm_risiko'=>$param->Item->lst_ris_idtm_risiko->Text));
        $this->Response->redirect($url);
    }
	
    public function jump_special($sender,$param){
        $url=$this->getRequest()->constructUrl('page',"risiko.risikoview",array('modus'=>'1','idtm_risiko'=>$param->Item->lst_ris_idtm_risiko->Text));
        $this->Response->redirect($url);
    }
}
?>