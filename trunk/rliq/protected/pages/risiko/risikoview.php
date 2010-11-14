<?php

class risikoview extends TPage{

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }
    
	private $primarykey = "idtm_risiko";
	private $MASTERRECORD = '';
	private $finder = '';
	private $fields = array("ris_name","idta_risiko_type","ris_descr","parent_idtm_risiko");	
	private $datfields = array();
	private $exitURL = 'risiko.risworkspace';
	
	public function onInit($param){
		
		parent::onInit($param);
		
		//Globale definition f�r dieses Dokument
		$this->finder = RisikoRecord::finder();
		$this->MASTERRECORD = new RisikoRecord;
		
		if(!$this->isPostBack){
		
		switch ($this->Request['modus']){
			case 0:
				$sql = "SELECT idta_risiko_type, ris_type_name FROM ta_risiko_type";
				$data = PFH::convertdbObjectArray(RisikoTypeRecord::finder()->findAllBySql($sql),array("idta_risiko_type","ris_type_name"));
				$this->idta_risiko_type->DataSource=$data;
				$this->idta_risiko_type->dataBind();
				
				if($this->Request[$this->primarykey]!=1){
					$sql = "SELECT idtm_risiko, ris_name FROM tm_risiko ORDER BY idta_risiko_type";
					$data = PFH::convertdbObjectArray(RisikoRecord::finder()->findAllBySql($sql),array("idtm_risiko","ris_name"));
				}
				else{
					$data = array();
					$data[0] = "START";
				}
					$this->parent_idtm_risiko->DataSource=$data;
					$this->parent_idtm_risiko->dataBind();
				break;
			case 1:
				$sql = "SELECT idta_risiko_type, ris_type_name FROM ta_risiko_type";
				$data = PFH::convertdbObjectArray(RisikoTypeRecord::finder()->findAllBySql($sql),array("idta_risiko_type","ris_type_name"));
				$this->edidta_risiko_type->DataSource=$data;
				$this->edidta_risiko_type->dataBind();
				
				if($this->Request[$this->primarykey]!=1){
					$sql = "SELECT idtm_risiko, ris_name FROM tm_risiko ORDER BY idta_risiko_type";
					$data = PFH::convertdbObjectArray(RisikoRecord::finder()->findAllBySql($sql),array("idtm_risiko","ris_name"));
				}
				else{
					$data = array();
					$data[0] = "START";
				}
					$this->edparent_idtm_risiko->DataSource=$data;
					$this->edparent_idtm_risiko->dataBind();
					
				$this->fillValues($this->getSelected($this->Request[$this->primarykey]));
				//$this->bindListed();
				break;
			default:
				break; 		
		}
				
		$this->viewPanel->ActiveViewIndex=$this->Request['modus'];

                //the parameters for the RiskValueContainer
                $this->RCedrcv_tabelle->Text="tm_risiko";
                $this->RCedrcv_id->Text=$this->Request[$this->primarykey];
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
			$this->finder->deleteAll('idtm_risiko = ?',$this->$tempus->Value);
			
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