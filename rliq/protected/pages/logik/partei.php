<?php
class Partei extends TPage
{
	
	public function onLoad($param){
		parent::onLoad($sender,$param);
		if(!$this->IsPostBack && !$this->isCallback)
			{
				$this->bindList();
			}
	}
	
	public function bindList()
		{
			
			$SQL1 = "SELECT ta_adresse.idta_adresse FROM ta_adresse INNER JOIN ta_partei_has_ta_adresse ON ta_adresse.idta_adresse = ta_partei_has_ta_adresse.idta_adresse INNER JOIN ta_partei ON ta_partei_has_ta_adresse.idta_partei = ta_partei.idta_partei WHERE ta_partei.idtm_user = ".$this->User->getUserId($this->User->Name);
			$adressen = AdresseRecord::finder()->findAllBySql($SQL1);
			$listeadresse = (array)$adressen;
			
			$validate = PFH::checkCountStatement(AdresseRecord::finder()->findBySql($SQL1));
			
			$criteria = new TActiveRecordCriteria();
			$ii=0;
			
			if($validate){
				foreach($listeadresse as $walker){
        			if($ii==0){
        				$myCondition = 'idta_adresse = '.$walker->idta_adresse;
        			}
        			else{
						$myCondition .= ' OR idta_adresse = '.$walker->idta_adresse;
        			}
        			$ii++;
				}
			}
			else{
				$myCondition = "idta_adresse = '0'";
			}
			
			
			$criteria->Condition = $myCondition;
			
			$criteria->setLimit($this->RepeaterWaren->PageSize);
			$criteria->setOffset($this->RepeaterWaren->PageSize * $this->RepeaterWaren->CurrentPageIndex);
			
			
			if(is_Object(WarenRecord::finder()->find($criteria))){
        		$this->RepeaterWaren->VirtualItemCount=WarenRecord::finder()->find($criteria)->count();
        	}
        	
			$this->RepeaterWaren->DataSource = WarenRecord::finder()->findAll($criteria);
			$this->RepeaterWaren->DataBind();
		}
	
    /**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
     */
    public function onInit($param)
    {
        parent::onInit($param);
        
        if(!$this->IsPostBack)  // if the page is requested the first time
        	{
        	// get the total number of posts available
            $criteria = new TActiveRecordCriteria;
            $criteria->Condition = 'idtm_user = :idtm_user';
        	$criteria->Parameters[':idtm_user'] = $this->User->getUserId($this->User->Name);
        	if(is_Object(ParteiRecord::finder()->find($criteria))){
        		$this->Repeater->VirtualItemCount=ParteiRecord::finder()->find($criteria)->count();
        	}
        	// populates post data into the repeater
            $this->populateData();        	
        	}
    }
    
 	
    /**
     * Deletes a specified user record.
     * This method responds to the datagrid's OnDeleteCommand event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
    public function deleteButtonClicked($sender,$param)
    {
        // obtains the datagrid item that contains the clicked delete button
        $item=$param->Item;
        // obtains the primary key corresponding to the datagrid item
        $idta_partei=$this->dg_partei->DataKeys[$item->ItemIndex];
        // deletes the user record with the specified username primary key
        ParteiRecord::finder()->deleteByPk($idta_partei);
    }
    
	public function newButtonClicked($sender,$param)
    {
        $url=$this->getRequest()->constructUrl('page',"logik.newPartei");
        $this->Response->redirect($url);
    }
    
	public function newButtonWarenClicked($sender,$param)
    {
        $url=$this->getRequest()->constructUrl('page',"logik.waren",array('modus'=>'0'));
        $this->Response->redirect($url);
    }
    
	public function newButtonFrachtClicked($sender,$param)
    {
        $url=$this->getRequest()->constructUrl('page',"logik.fracht",array('modus'=>'0'));
        $this->Response->redirect($url);
    }
     
    /**
     * Event handler to the OnPageIndexChanged event of the pager.
     * This method is invoked when the user clicks on a page button
     * and thus changes the page of posts to display.
     */
    public function pageChanged($sender,$param)
    {
        // change the current page index to the new one
        $this->Repeater->CurrentPageIndex=$param->NewPageIndex;
        // re-populate data into the repeater
        $this->populateData();
        $this->bindList();
    }
    
	/* Event handler to the OnPageIndexChanged event of the pager.
     * This method is invoked when the user clicks on a page button
     * and thus changes the page of posts to display.
     */
    public function pageChangedWaren($sender,$param)
    {
        // change the current page index to the new one
        $this->RepeaterWaren->CurrentPageIndex=$param->NewPageIndex;
        // re-populate data into the repeater
        $this->bindList();
        $this->populateData();
    }
 
    /**
     * Determines which page of posts to be displayed and
     * populates the repeater with the fetched data.
     */
    protected function populateData()
    {
        $offset=$this->Repeater->CurrentPageIndex*$this->Repeater->PageSize;
        $limit=$this->Repeater->PageSize;
        if($offset+$limit>$this->Repeater->VirtualItemCount)
            $limit=$this->Repeater->VirtualItemCount-$offset;
        $this->Repeater->DataSource=$this->getPartei($offset,$limit);
        $this->Repeater->dataBind();
    }
    
    public function dataBindRepeater2($sender,$param) {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
            $criteria_p=new TActiveRecordCriteria;
            $criteria_p->Condition = 'idta_partei = :idta_partei';
            $criteria_p->Parameters[':idta_partei'] = $item->data->idta_partei;

            $templisteadresse = ParteiAdresseRecord::finder()->findAll($criteria_p);
            $listeadresse = (array)$templisteadresse;
            //print_r($listeadresse);
            $mydata=array();

            foreach($listeadresse as $walker) {
                $conditionx = new TActiveRecordCriteria;
                $conditionx->Condition = 'idta_adresse = :idta_adresse';
                $conditionx->Parameters[':idta_adresse'] = $walker->idta_adresse;
                array_push($mydata,AdresseRecord::finder()->find($conditionx));
            }

            //print_r($mydata);
            $item->Repeater2->DataSource=$mydata;
            $item->Repeater2->dataBind();
        }
    }
    
    /**
     * Fetches posts from database with offset and limit.
     */
    protected function getPartei($offset, $limit)
    {
        // Construts a query criteria
        $criteria_t=new TActiveRecordCriteria;
        $criteria_t->Condition = 'idtm_user = :idtm_user';
        $criteria_t->Parameters[':idtm_user'] = $this->User->getUserId($this->User->Name);
        $criteria_t->OrdersBy['partei_name']='asc';
        $criteria_t->Limit=$limit;
        $criteria_t->Offset=$offset;
        // query for the posts with the above criteria and with author information
        return ParteiRecord::finder()->findAll($criteria_t);
    }
    
}
?>