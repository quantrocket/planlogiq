<?php

class prozessstepview extends TPage{
	
	private $primarykey = "idtm_prozess_step";
	private $MASTERRECORD = '';
	private $finder = '';
	private $fields = array("prostep_name","prostep_descr");	
	private $listfields = array("idtm_prozess","idtm_struktur","parent_idtm_prozess_step","idtm_organisation","error_idtm_prozess_step","parent_idtm_prozess_step");	
	private $datfields = array();
	private $hiddenfields = array();
	private $boolfields = array("prostep_valid");
	private $exitURL = 'prozess.proworkspace';
	
	public function onLoad($param){
		
		parent::onLoad($param);
		
		//Globale definition f�r dieses Dokument
		$this->finder = ProzessStepRecord::finder();
		$this->MASTERRECORD = new ProzessStepRecord;	
		
		if(!$this->isPostBack && !$this->isCallback){
			
		switch ($this->Request['modus']){
			case 0:
					//hier checken wir, wieviele schritte noch den gleichen Vater haben
					$myPreStepOne = ProzessStepRecord::finder()->findAllBySql("SELECT idtm_prozess FROM tm_prozess_step WHERE idtm_prozess_step = '".$this->Request[$this->primarykey]."'");
					$prozess_counter = count(ProzessStepRecord::finder()->findAllBySql("SELECT idtm_prozess_step FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'"));
					
					$this->idtm_prozess->DataSource=PFH::build_SQLPullDown(ProzessRecord::finder(),"tm_prozess",array("idtm_prozess","pro_name"),"idta_prozess_type = 3");
					$this->idtm_prozess->dataBind();
				
					$this->idtm_struktur->DataSource=PFH::build_SQLPullDown(StrukturRecord::finder(),"tm_struktur",array("idtm_struktur","struktur_name"));
					$this->idtm_struktur->dataBind();
				
					if($this->Request[$this->primarykey]!=1 AND $prozess_counter >= 1){
						$sql = "SELECT idtm_prozess_step, prostep_name FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'";
						$data = PFH::convertdbObjectArray(ProzessStepRecord::finder()->findAllBySql($sql),array("idtm_prozess_step","prostep_name"));
						$data[0] = "START";
					}
					else{
						$data = array();
						$data[0] = "START";
					}
					$this->parent_idtm_prozess_step->DataSource=$data;
					$this->parent_idtm_prozess_step->dataBind();
				
					$sql = "SELECT idtm_organisation, org_name FROM tm_organisation WHERE idta_organisation_type = 4";
					$data = PFH::convertdbObjectArray(OrganisationRecord::finder()->findAllBySql($sql),array("idtm_organisation","org_name"));
					$this->idtm_organisation->DataSource=$data;
					$this->idtm_organisation->dataBind();
				
					if($this->Request[$this->primarykey]!=1 AND $prozess_counter>=1){
						$sql = "SELECT idtm_prozess_step, prostep_name FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'";
						$data = PFH::convertdbObjectArray(ProzessStepRecord::finder()->findAllBySql($sql),array("idtm_prozess_step","prostep_name"));
						$data[0] = "START";
					}
					else{
						$data = array();
						$data[0] = "START";
					}
					$this->error_idtm_prozess_step->DataSource=$data;
					$this->error_idtm_prozess_step->dataBind();
				
				break;
			case 1:
					//hier checken wir, wieviele schritte noch den gleichen Vater haben
					$myPreStepOne = ProzessStepRecord::finder()->findAllBySql("SELECT idtm_prozess FROM tm_prozess_step WHERE idtm_prozess_step = '".$this->Request[$this->primarykey]."'");
					$prozess_counter = count(ProzessStepRecord::finder()->findAllBySql("SELECT * FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'"));
										
					$this->edidtm_prozess->DataSource=PFH::build_SQLPullDown(ProzessRecord::finder(),"tm_prozess",array("idtm_prozess","pro_name"),"idta_prozess_type = 3");
					$this->edidtm_prozess->dataBind();
				
					$this->edidtm_struktur->DataSource=PFH::build_SQLPullDown(StrukturRecord::finder(),"tm_struktur",array("idtm_struktur","struktur_name"));
					$this->edidtm_struktur->dataBind();

						$sql = "SELECT idtm_prozess_step, prostep_name FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'";
						$data = PFH::convertdbObjectArray(ProzessStepRecord::finder()->findAllBySql($sql),array("idtm_prozess_step","prostep_name"));
						$data[0] = "START";
					
					$this->edparent_idtm_prozess_step->DataSource=$data;
					$this->edparent_idtm_prozess_step->dataBind();

					
					$this->edidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type=4");
					$this->edidtm_organisation->dataBind();
				
						$sql = "SELECT idtm_prozess_step, prostep_name FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'";
						$data = PFH::convertdbObjectArray(ProzessStepRecord::finder()->findAllBySql($sql),array("idtm_prozess_step","prostep_name"));
						$data[0] = "START";
					
					$this->ederror_idtm_prozess_step->DataSource=$data;
					$this->ederror_idtm_prozess_step->dataBind();
					
					$this->Aedidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type=4");
					$this->Aedidtm_organisation->dataBind();
				
					$this->fillValues($this->getSelected($this->Request[$this->primarykey]));
					$this->Aedauf_id->Text = $this->Request[$this->primarykey];
					$this->RCedrcv_id->Text = $this->Request[$this->primarykey];
					$this->bindListAufgaben();
					$this->createRiskPullDown();
					$this->bindListRCValue();
					
				break;
			default:
				break; 		
		}
				
		$this->viewPanel->ActiveViewIndex=$this->Request['modus'];
		}
	}
	
	private function generateRisikoGraph($ActiveRecord) {
		
		$ydata1 = array();
		$ydata2 = array();
		$xdata = array();
		$ytitle = array("SH","EW");
 		
		$ii=0;
		
		foreach ($ActiveRecord as $DetailRecord){
			$xdata[] = $DetailRecord->idtt_rcvalue;
			$ydata1[] = $DetailRecord->rcv_schaden;
			$ydata2[] = $DetailRecord->rcv_prio;
			$ii++;
			if($ii > 10){
				break;	
			}
		}
		
		$ydata1 = implode(',', $ydata1);
		$ydata2 = implode(',', $ydata2);
		$xdata = implode(',', $xdata);
		$ytitledata = implode(',', $ytitle);
		$this->RisikoVerlaufImage->ImageUrl = $this->getRequest()->constructUrl('page','graph', 1, array( 'xdata' => $xdata, 'ydata1' => $ydata1, 'ydata2' => $ydata2, 'ytitle' => $ytitledata), false);
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
			$this->finder->deleteAll('idtm_prozess_step = ?',$this->$tempus->Value);
			
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
    		$criteria->Condition ="auf_tabelle = 'tm_prozess_step' AND auf_id = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = $this->edidtm_prozess_step->value;
    		
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
			$this->$edrecordfield->Text = '0';
		}
		
		$this->Aedaufgaben_edit_status->Text = '0';
		$this->Aedauf_tabelle->Text = "tm_prozess_step";
		$this->Aedauf_id->Text = $myidea;
    }
    
    //ENDE DER AUFGABE
    //ENDE DER AUFGABE
    //ENDE DER AUFGABE
    
    public function update_ListBox($sender,$param){
    	$parent_value = $this->edidtm_prozess->selectedValue;
    	
    	//hier checken wir, wieviele schritte noch den gleichen Vater haben
		$myPreStepOne = ProzessStepRecord::finder()->findAllBySql("SELECT idtm_prozess FROM tm_prozess_step WHERE idtm_prozess_step = '".$parent_value."'");
		$prozess_counter = count(ProzessStepRecord::finder()->findAllBySql("SELECT idtm_prozess_step FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'"));

					$sql = "SELECT idtm_prozess_step, prostep_name FROM tm_prozess_step WHERE idtm_prozess = '".$myPreStepOne[0]->idtm_prozess."'";
					$data = PFH::convertdbObjectArray(ProzessStepRecord::finder()->findAllBySql($sql),array("idtm_prozess_step","prostep_name"));
					$data[0] = "START";
		
		$this->parent_idtm_prozess_step->DataSource=$data;
		$this->parent_idtm_prozess_step->dataBind();
		
		$this->error_idtm_prozess_step->DataSource=$data;
		$this->error_idtm_prozess_step->dataBind();
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	
	private $RCprimarykey = "idtm_rcvalue";
	private $RCTTprimarykey = "idtt_rcvalue";
	private $RCfields = array("idtm_organisation","rcv_tabelle","rcv_id","rcv_comment","idtm_risiko","rcv_type");	
	private $RCTTfields = array("rcv_cby","rcv_ewk","rcv_schaden","rcv_prio","idtm_rcvalue");	
	private $RCdatfields = array();
	private $RCTTdatfields = array();
	private $RChiddenfields = array();
	private $RCTThiddenfields = array();
	private $RCboolfields = array();
	private $RCTTboolfields = array();
	
	public function bindListRCValue(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="rcv_tabelle = 'tm_prozess_step' AND rcv_id = :suchtext1";
    		$criteria->Parameters[':suchtext1'] = $this->edidtm_prozess_step->value;
    		
    		$this->RCValueListe->VirtualItemCount = count(RCValueRecord::finder()->findAll($criteria));
			
			$criteria->setLimit($this->RCValueListe->PageSize);
			$criteria->setOffset($this->RCValueListe->PageSize * $this->RCValueListe->CurrentPageIndex);
			$this->RCValueListe->DataKeyField = 'idtm_rcvalue';
			
			$this->RCValueListe->VirtualItemCount = count(RCValueRecord::finder()->findAll());
			$this->RCValueListe->DataSource=RCValueRecord::finder()->findAll($criteria);
			$this->RCValueListe->dataBind();
			
			$this->bindListRCTTValue();
    }
    
	public function bindListRCTTValue(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="idtm_rcvalue = :suchtext1 ORDER BY rcv_cdate DESC";
    		$criteria->Parameters[':suchtext1'] = $this->RCedidtm_rcvalue->Text;
    		
    		$this->RCTTValueListe->VirtualItemCount = count(RCTTValueRecord::finder()->findAll($criteria));
			
			$criteria->setLimit($this->RCTTValueListe->PageSize);
			$criteria->setOffset($this->RCTTValueListe->PageSize * $this->RCTTValueListe->CurrentPageIndex);
			$this->RCTTValueListe->DataKeyField = 'idtm_rcvalue';
			
			$this->RCTTValueListe->VirtualItemCount = count(RCTTValueRecord::finder()->findAll());
			$this->RCTTValueListe->DataSource=RCTTValueRecord::finder()->findAll($criteria);
			$this->RCTTValueListe->dataBind();
			$this->generateRisikoGraph($this->RCTTValueListe->DataSource);
    }
    
    public function load_rcvalue($sender,$param){
    	
    	$item = $param->Item;
    	$myitem=RCValueRecord::finder()->findByPK($item->lst_rcvalue_idtm_rcvalue->Text);
    	
    	$tempus = 'RCed'.$this->RCprimarykey;
		$monus = $this->RCprimarykey;
		
		$this->$tempus->Text = $myitem->$monus;
		
    	//HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->RCedrcvalue_edit_status->Text = 1;
		$this->RCTTedidtm_rcvalue->Text = $item->lst_rcvalue_idtm_rcvalue->Text;	
		
		$this->bindListRCTTValue();
    }
    
	public function load_rcttvalue($sender,$param){
    	
    	$item = $param->Item;
    	$myitem=RCTTValueRecord::finder()->findByPK($item->lst_rcttvalue_idtt_rcvalue->Text);
    	
    	$tempus = 'RCTTed'.$this->RCTTprimarykey;
		$monus = $this->RCTTprimarykey;
		
		$this->$tempus->Text = $myitem->$monus;
		
    	//HIDDEN
		foreach ($this->RCTThiddenfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->RCTTdatfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->RCTTboolfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->RCTTfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->RCTTedrcvalue_edit_status->Text = 1;
    }
    
	public function RCSavedButtonClicked($sender,$param){
		
		$tempus='RCed'.$this->RCprimarykey;
		
		if($this->RCedrcvalue_edit_status->Text == '1'){
			$RCEditRecord = RCValueRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$RCEditRecord = new RCValueRecord;
		}
	
		//HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$RCEditRecord->save();
		
		$this->bindListRCValue();
	}

	public function RCTTSavedButtonClicked($sender,$param){
		
		$tempus='RCTTed'.$this->RCTTprimarykey;
		
		if($this->RCTTedrcvalue_edit_status->Text == '1'){
			$RCTTEditRecord = RCTTValueRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$RCTTEditRecord = new RCTTValueRecord;
		}
	
		//HIDDEN
		foreach ($this->RCTThiddenfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$RCTTEditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->RCTTdatfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$RCTTEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->RCTTboolfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$RCTTEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->RCTTfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$RCTTEditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$RCTTEditRecord->save();
			
		$this->bindListRCTTValue();
	}
	
	public function RCNewButtonClicked($sender,$param){
    	
    	$myidea = $this->RCedrcv_id->Text;
		
		$tempus = 'RCed'.$this->RCprimarykey;
		$monus = $this->RCprimarykey;
		
		$this->$tempus->Text = '0';
		
    	//HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}
		
		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}
		
		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->Checked(0);
		}
		
		//NON DATUM
		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'RCed'.$recordfield;
			$this->$edrecordfield->Text = 'leer';
		}
		
		$this->RCedrcvalue_edit_status->Text = '0';
		$this->RCedrcv_tabelle->Text = "tm_prozess_step";
		$this->RCedrcv_id->Text = $myidea;
    }
    
	public function RCTTNewButtonClicked($sender,$param){
    	
    	$mysecondidea = $this->RCTTedidtm_rcvalue->Text;
		
		$tempus = 'RCTTed'.$this->RCTTprimarykey;
		$monus = $this->RCTTprimarykey;
		
		$this->$tempus->Text = '0';
		
    	//HIDDEN
		foreach ($this->RCTThiddenfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}
		
		//DATUM
		foreach ($this->RCTTdatfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}
		
		//BOOL
		foreach ($this->RCTTboolfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->Checked(0);
		}
		
		//NON DATUM
		foreach ($this->RCTTfields as $recordfield){
			$edrecordfield = 'RCTTed'.$recordfield;
			$this->$edrecordfield->Text = '0';
		}
		
		$this->RCTTedrcvalue_edit_status->Text = '0';
		$this->RCTTedidtm_rcvalue->Text = $mysecondidea;
    }
    
	public function rcvList_PageIndexChanged($sender,$param)
		{
			$this->RCValueListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListRCValue();
		}
    
	public function rcvttList_PageIndexChanged($sender,$param)
		{
			$this->RCTTValueListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListRCTTValue();
		}
    
    public function createRiskPullDown(){
    	//Als erstes die Organisation
    	$this->RCedidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type = 4");
		$this->RCedidtm_organisation->dataBind();
		//risiko oder chance
		$data=array(0=>"Risiko",1=>"Chance","leer"=>"leer");
		$this->RCedrcv_type->DataSource = $data;
		$this->RCedrcv_type->DataBind();
		//die Risikoklasse
    	$this->RCedidtm_risiko->DataSource=PFH::build_SQLPullDown(RisikoRecord::finder(),"tm_risiko",array("idtm_risiko","ris_name"));
		$this->RCedidtm_risiko->dataBind();
		//ewk
		$data=array(0=>"leer",1=>"gering",2=>"mittel",3=>"hoch",4=>"sehr hoch");
		$this->RCTTedrcv_ewk->DataSource = $data;
		$this->RCTTedrcv_ewk->DataBind();
		//prio
		$data=array(0=>"leer",1=>"10",2=>"20",3=>"30",4=>"40",5=>"50",6=>"60",7=>"70",8=>"80",9=>"90");
		$this->RCTTedrcv_prio->DataSource = $data;
		$this->RCTTedrcv_prio->DataBind(); 	
    }
    
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    


}

?>