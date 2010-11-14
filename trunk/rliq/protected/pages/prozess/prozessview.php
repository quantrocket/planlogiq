<?php

class prozessview extends TPage{
	
	private $primarykey = "idtm_prozess";
	private $MASTERRECORD = '';
	private $finder = '';
	private $fields = array("pro_name","idta_prozess_type","pro_descr","parent_idtm_prozess","pro_step");	
	private $datfields = array();
	private $exitURL = 'prozess.proworkspace';
	
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }
    
    public function onInit($param){
		
		parent::onInit($param);
		
		//Globale definition f�r dieses Dokument
		$this->finder = ProzessRecord::finder();
		$this->MASTERRECORD = new ProzessRecord;
		
		if(!$this->isPostBack){
		
		switch ($this->Request['modus']){
			case 0:
				$sql = "SELECT idta_prozess_type, pro_type_name FROM ta_prozess_type";
				$data = PFH::convertdbObjectArray(ProzessTypeRecord::finder()->findAllBySql($sql),array("idta_prozess_type","pro_type_name"));
				$this->idta_prozess_type->DataSource=$data;
				$this->idta_prozess_type->dataBind();
				
				if($this->Request[$this->primarykey]!=1){
					$sql = "SELECT idtm_prozess, pro_name FROM tm_prozess ORDER BY idta_prozess_type";
					$data = PFH::convertdbObjectArray(ProzessRecord::finder()->findAllBySql($sql),array("idtm_prozess","pro_name"));
				}
				else{
					$data = array();
					$data[0] = "START";
				}
					$this->parent_idtm_prozess->DataSource=$data;
					$this->parent_idtm_prozess->dataBind();
				break;
			case 1:
				$sql = "SELECT idta_prozess_type, pro_type_name FROM ta_prozess_type";
				$data = PFH::convertdbObjectArray(ProzessTypeRecord::finder()->findAllBySql($sql),array("idta_prozess_type","pro_type_name"));
				$this->edidta_prozess_type->DataSource=$data;
				$this->edidta_prozess_type->dataBind();
				
				if($this->Request[$this->primarykey]!=1){
					$sql = "SELECT idtm_prozess, pro_name FROM tm_prozess ORDER BY idta_prozess_type";
					$data = PFH::convertdbObjectArray(ProzessRecord::finder()->findAllBySql($sql),array("idtm_prozess","pro_name"));
				}
				else{
					$data = array();
					$data[0] = "START";
				}
					$this->edparent_idtm_prozess->DataSource=$data;
					$this->edparent_idtm_prozess->dataBind();
					
				$this->fillValues($this->getSelected($this->Request[$this->primarykey]));
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
			$this->finder->deleteAll('idtm_prozess = ?',$this->$tempus->Value);
			
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