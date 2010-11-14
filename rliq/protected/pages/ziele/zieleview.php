<?php

class zieleview extends TPage{
	
	private $primarykey = "idtm_ziele";
	private $MASTERRECORD = '';
	private $finder = '';
	private $fields = array("zie_name","idta_ziele_type","zie_descr","parent_idtm_ziele"); //,"idtm_activity" removed pf 20090702
	private $datfields = array();
	private $exitURL = 'ziele.zieworkspace';
	
	public function onPreInit($param){
            $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
            $this->setTheme($myTheme);
        }
        
    public function onInit($param){
		
		parent::onInit($param);
		
		//Globale definition fuer dieses Dokument
		$this->finder = ZieleRecord::finder();
		$this->MASTERRECORD = new ZieleRecord;
		
		if(!$this->isPostBack){
		
		switch ($this->Request['modus']){
			case 0:
				$sql = "SELECT idta_ziele_type, zie_type_name FROM ta_ziele_type";
				$data = PFH::convertdbObjectArray(ZieleTypeRecord::finder()->findAllBySql($sql),array("idta_ziele_type","zie_type_name"));
				$this->idta_ziele_type->DataSource=$data;
				$this->idta_ziele_type->dataBind();

                                $HRKEYTest = new PFHierarchyPullDown();
                                $HRKEYTest->setStructureTable("tm_ziele");
                                $HRKEYTest->setRecordClass(ZieleRecord::finder());
                                $HRKEYTest->setPKField("idtm_ziele");
                                $HRKEYTest->setField("zie_name");
				$HRKEYTest->letsrun();
                                
                                $this->parent_idtm_ziele->DataSource=$HRKEYTest->myTree;
				$this->parent_idtm_ziele->dataBind();

                                $this->idtm_activity->DataSource=PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_pspcode","act_name"),"idta_activity_type = 2");
                                $this->idtm_activity->dataBind();

				break;
			case 1:
				$sql = "SELECT idta_ziele_type, zie_type_name FROM ta_ziele_type";
				$data = PFH::convertdbObjectArray(ZieleTypeRecord::finder()->findAllBySql($sql),array("idta_ziele_type","zie_type_name"));
				$this->edidta_ziele_type->DataSource=$data;
				$this->edidta_ziele_type->dataBind();
				
				$HRKEYTest = new PFHierarchyPullDown();
                                $HRKEYTest->setStructureTable("tm_ziele");
                                $HRKEYTest->setRecordClass(ZieleRecord::finder());
                                $HRKEYTest->setPKField("idtm_ziele");
                                $HRKEYTest->setField("zie_name");
				$HRKEYTest->letsrun();

                                $this->edparent_idtm_ziele->DataSource=$HRKEYTest->myTree;
				$this->edparent_idtm_ziele->dataBind();

					
				$this->fillValues($this->getSelected($this->Request[$this->primarykey]));

                                $this->edidtm_activity->DataSource=PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_pspcode","act_name"),"idta_activity_type = 2");
				$this->edidtm_activity->dataBind();
				//$this->bindListed();
				break;
			default:
				break; 		
		}
				
		$this->viewPanel->ActiveViewIndex=$this->Request['modus'];
		}
	}
	
	protected function fillValues($item){
		
		$tempus = 'ed'.$this->primarykey;
		$monus = $this->primarykey;
		
		$this->$tempus->Value = $item->$monus;
		
		//DATUM
		foreach ($this->datfields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$this->$edrecordfield->setDate(date($item->$recordfield));
		}
		
		//NON DATUM
		foreach ($this->fields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$this->$edrecordfield->Text = $item->$recordfield;
		}
		
	}
	
	protected function getSelected($key){
		$item = $this->finder->findByPk($key);
		return $item;
	}
	
	public function deleteButtonClicked($sender,$param)
		{
			$tempus= 'ed'.$this->primarykey;
			$this->finder->deleteAll('idtm_ziele = ?',$this->$tempus->Value);
			
			$this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
		}
    
	
	public function editButtonClicked($sender,$param){
		
		$tempus= 'ed'.$this->primarykey;
		
		$EditRecord = $this->finder->findByPK($this->$tempus->Value);
	
		//DATUM
		foreach ($this->datfields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$this->$edrecordfield->setDate(date($item->$recordfield));
		}
		
		foreach ($this->fields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$EditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$EditRecord->save();
			
		$this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
	}
	
	public function insertButtonClicked($sender,$param){
		
		$EditRecord = $this->MASTERRECORD;
		
		//DATUM
		foreach ($this->datfields as $recordfield){
			$EditRecord->$recordfield = date("Y-m-d",$this->$recordfield->Text);
		}
		
		
		foreach ($this->fields as $recordfield){
			$EditRecord->$recordfield = $this->$recordfield->Text;
		}

		$EditRecord->save();
			
		$this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
	}
			
}

?>