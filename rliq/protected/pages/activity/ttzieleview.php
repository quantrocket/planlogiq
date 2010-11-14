<?php

class ttzieleview extends TPage{
	
	private $primarykey = "idtt_ziele";
	private $MASTERRECORD = '';
	private $finder = '';
	private $fields = array("ttzie_name","ttzie_descr");
	private $listfields = array("idtm_prozess","idtm_ziele","idtm_organisation");	
	private $datfields = array();
	private $hiddenfields = array();
	private $boolfields = array("prostep_valid");
	private $exitURL = 'ziele.zieworkspace';
	
	public function onPreInit($param){
            $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
            $this->setTheme($myTheme);
        }
    public function onLoad($param){
		
		parent::onLoad($param);
		
		//Globale definition f�r dieses Dokument
		$this->finder = TTZieleRecord::finder();
		$this->MASTERRECORD = new TTZieleRecord;	
		
		if(!$this->isPostBack && !$this->isCallback){
			
		switch ($this->Request['modus']){
			case 0:
					//hier checken wir, wieviele schritte noch den gleichen Vater haben
					$myPreStepOne = TTZieleRecord::finder()->findAllBySql("SELECT idtm_prozess FROM tt_ziele WHERE idtt_ziele = '".$this->Request[$this->primarykey]."'");
					$prozess_counter = count(TTZieleRecord::finder()->findAllBySql("SELECT idtt_ziele FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'"));
					
					$this->idtm_prozess->DataSource=PFH::build_SQLPullDown(ProzessRecord::finder(),"tm_prozess",array("idtm_prozess","pro_name"),"idta_prozess_type = 3");
					$this->idtm_prozess->dataBind();
				
					$this->idtm_ziele->DataSource=PFH::build_SQLPullDown(ZieleRecord::finder(),"tm_ziele",array("idtm_ziele","zie_name"),"(idta_ziele_type = 1 OR idta_ziele_type = 3)");
					$this->idtm_ziele->dataBind();
					
					$sql = "SELECT idtm_organisation, org_name FROM tm_organisation WHERE idta_organisation_type = 4";
					$data = PFH::convertdbObjectArray(OrganisationRecord::finder()->findAllBySql($sql),array("idtm_organisation","org_name"));

                                        $this->idtm_organisation->DataSource=$data;
					$this->idtm_organisation->dataBind();
				
				break;
			case 1:
					//hier checken wir, wieviele schritte noch den gleichen Vater haben
					$myPreStepOne = TTZieleRecord::finder()->findAllBySql("SELECT idtm_ziele FROM tt_ziele WHERE idtt_ziele = '".$this->Request[$this->primarykey]."'");
					$prozess_counter = count(TTZieleRecord::finder()->findAllBySql("SELECT * FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'"));
										
					$this->edidtm_prozess->DataSource=PFH::build_SQLPullDown(ProzessRecord::finder(),"tm_prozess",array("idtm_prozess","pro_name"),"idta_prozess_type = 3");
					$this->edidtm_prozess->dataBind();
					
					$this->edidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type=4");
					$this->edidtm_organisation->dataBind();
				
					$this->edidtm_ziele->DataSource=PFH::build_SQLPullDown(ZieleRecord::finder(),"tm_ziele",array("idtm_ziele","zie_name"),"(idta_ziele_type = 1 OR idta_ziele_type = 3)");
					$this->edidtm_ziele->dataBind();					
					
					$this->Aedidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type=4");
					$this->Aedidtm_organisation->dataBind();
				
					$this->fillValues($this->getSelected($this->Request[$this->primarykey]));
					$this->Aedauf_id->Text = $this->Request[$this->primarykey];
					$this->bindListAufgaben();
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
		
		//BOOL
		foreach ($this->boolfields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$this->$edrecordfield->setChecked($item->$recordfield);
		}
		
		//LIST
		foreach ($this->listfields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$this->$edrecordfield->setSelectedValue($item->$recordfield);
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
			$this->finder->deleteAll('idtt_ziele = ?',$this->$tempus->Value);
			
			$this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
		}
    
	
	public function editButtonClicked($sender,$param){
		
		$tempus='ed'.$this->primarykey;
		
		$EditRecord = $this->finder->findByPK($this->$tempus->Value);
	
		//DATUM
		foreach ($this->datfields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$EditRecord->$edrecordfield->setDate(date($item->$recordfield));
		}
		
		//BOOL
		foreach ($this->boolfields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$EditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		//LIST
		foreach ($this->listfields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$EditRecord->$recordfield = $this->$edrecordfield->Text;
		}
		
		//NON DATUM
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
		
		//BOOL
		foreach ($this->boolfields as $recordfield){
			$EditRecord->$recordfield = $this->$recordfield->Checked?1:0;
		}
		
		//LIST
		foreach ($this->listfields as $recordfield){
			$EditRecord->$recordfield = $this->$recordfield->Text;
		}
		
		foreach ($this->fields as $recordfield){
			$EditRecord->$recordfield = $this->$recordfield->Text;
		}

		$EditRecord->save();
			
		$this->Response->redirect($this->getRequest()->constructUrl('page',$this->exitURL));
	}
	
	/* here comes the part for the tasks */
	/* here comes the part for the tasks */
	/* here comes the part for the tasks */
	/* here comes the part for the tasks */
	
	private $Aprimarykey = "idtm_aufgaben";
	private $Afields = array("idtm_organisation","auf_beschreibung","auf_priority","auf_name","auf_tabelle","auf_id");	
	private $Adatfields = array("auf_tdate");
	private $Ahiddenfields = array();
	private $Aboolfields = array();
	
	
	
	public function bindListAufgaben(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="auf_tabelle = 'tt_ziele' AND auf_id = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = $this->edidtt_ziele->value;
    		
    		$this->AufgabenListe->VirtualItemCount = count(AufgabenRecord::finder()->findAll($criteria));
			
			$criteria->setLimit($this->AufgabenListe->PageSize);
			$criteria->setOffset($this->AufgabenListe->PageSize * $this->AufgabenListe->CurrentPageIndex);
			$this->AufgabenListe->DataKeyField = 'idtm_aufgaben';
			
			$this->AufgabenListe->VirtualItemCount = count(AufgabenRecord::finder()->findAll());
			$this->AufgabenListe->DataSource=AufgabenRecord::finder()->findAll($criteria);
			$this->AufgabenListe->dataBind();
    }
    
    public function load_aufgabe($sender,$param){
    	
    	$item = $param->Item;
    	$myitem=AufgabenRecord::finder()->findByPK($item->lst_aufgaben_idtm_aufgaben->Text);
    	
    	$tempus = 'Aed'.$this->Aprimarykey;
		$monus = $this->Aprimarykey;
		
		$this->$tempus->Text = $myitem->$monus;
		
    	//HIDDEN
		foreach ($this->Ahiddenfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->Adatfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->Aboolfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->Afields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->Aedaufgaben_edit_status->Text = 1;
    }
    
	public function ASavedButtonClicked($sender,$param){
		
		$tempus='Aed'.$this->Aprimarykey;
		
		if($this->Aedaufgaben_edit_status->Text == '1'){
			$AEditRecord = AufgabenRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$AEditRecord = new AufgabenRecord;
		}
	
		//HIDDEN
		foreach ($this->Ahiddenfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$AEditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->Adatfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$AEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->Aboolfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$AEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->Afields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$AEditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$AEditRecord->save();
			
		$this->bindListAufgaben();
	}

	public function ANewButtonClicked($sender,$param){
    	
    	$myidea = $this->Aedauf_id->Text;
		
		$tempus = 'Aed'.$this->Aprimarykey;
		$monus = $this->Aprimarykey;
		
		$this->$tempus->Text = '0';
		
    	//HIDDEN
		foreach ($this->Ahiddenfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}
		
		//DATUM
		foreach ($this->Adatfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}
		
		//BOOL
		foreach ($this->Aboolfields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->Checked(0);
		}
		
		//NON DATUM
		foreach ($this->Afields as $recordfield){
			$edrecordfield = 'Aed'.$recordfield;
			$this->$edrecordfield->Text = 'leer';
		}
		
		$this->Aedaufgaben_edit_status->Text = '0';
		$this->Aedauf_tabelle->Text = "tt_ziele";
		$this->Aedauf_id->Text = $myidea;
    }
    
    //ENDE DER AUFGABE
    //ENDE DER AUFGABE
    //ENDE DER AUFGABE
    
    public function update_ListBox($sender,$param){
    	$parent_value = $this->edidtm_ziele->selectedValue;
    	
    	//hier checken wir, wieviele schritte noch den gleichen Vater haben
		$myPreStepOne = TTZieleRecord::finder()->findAllBySql("SELECT idtm_ziele FROM tt_ziele WHERE idtt_ziele = '".$parent_value."'");
		$prozess_counter = count(TTZieleRecord::finder()->findAllBySql("SELECT idtt_ziele FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'"));

					$sql = "SELECT idtt_ziele, ttzie_name FROM tt_ziele WHERE idtm_ziele = '".$myPreStepOne[0]->idtm_ziele."'";
					$data = PFH::convertdbObjectArray(TTZieleRecord::finder()->findAllBySql($sql),array("idtt_ziele","ttzie_name"));
					$data[0] = "START";
		
		//$this->idtm_ziele->DataSource=$data;
		//$this->idtm_ziele->dataBind();
    }    

}

?>