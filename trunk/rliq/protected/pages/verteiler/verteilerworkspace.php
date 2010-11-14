<?php

class verteilerworkspace extends TPage
{
	private $primarykey = "idtm_verteiler";
	private $MASTERRECORD = '';
	private $finder = '';
	private $fields = array("ver_name","ver_descr");	
	private $listfields = array("ver_day","ver_zyklus");	
	private $datfields = array();
	private $hiddenfields = array();
	private $boolfields = array("ver_valid");
	private $exitURL = 'verteiler.verteilerworkspace';
	
	public function onLoad($param){
		
		//Globale definition f�r dieses Dokument
		$this->finder = VerteilerRecord::finder();
		$this->MASTERRECORD = new VerteilerRecord;	
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
				
			$this->ttidtm_organisation->DataSource=PFH::build_SQLPullDown(OrganisationRecord::finder(),"tm_organisation",array("idtm_organisation","org_name"),"idta_organisation_type=4");
			$this->ttidtm_organisation->dataBind();
			
			$this->bindListVerteiler();
		}
	}
			
	public function bindListVerteiler(){
		$this->VerteilerListe->VirtualItemCount = count(VerteilerRecord::finder()->findAll());
                $this->VerteilerListe->DataSource=VerteilerRecord::finder()->findAll();
                $this->VerteilerListe->dataBind();
    }
    
	public function searchVerteiler(){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition="ver_name LIKE :suchbedingung1";
    		$criteria->Parameters[':suchbedingung1'] = "%".$this->find_verteiler->Text."%";
			
    		$this->VerteilerListe->VirtualItemCount = count(VerteilerRecord::finder()->findAll($criteria));
			
			$criteria->setLimit($this->VerteilerListe->PageSize);
			$criteria->setOffset($this->VerteilerListe->PageSize * $this->VerteilerListe->CurrentPageIndex);
			$this->VerteilerListe->DataKeyField = 'idtm_verteiler';
			
			$this->VerteilerListe->VirtualItemCount = count(VerteilerRecord::finder()->findAll());
			$this->VerteilerListe->DataSource=VerteilerRecord::finder()->findAll($criteria);
			$this->VerteilerListe->dataBind();
    }
    
	public function TDeleteButtonClicked($sender,$param){
    	$tempus=$this->primarykey;
    	$AEditRecord = VerteilerRecord::finder()->findByPK($this->$tempus->Text);
    	$AEditRecord->delete();
    	$this->bindListVerteiler();
    	$this->TNewButtonClicked($sender,$param);
    }
    
    public function load_verteiler($sender,$param){
    	
    	$item = $param->Item;
    	$myitem=VerteilerRecord::finder()->findByPK($item->lst_idtm_verteiler->Text);
    	
    	$tempus = $this->primarykey;
		$monus = $this->primarykey;
		
		$this->$tempus->Text = $myitem->$monus;
		
    	//HIDDEN
		foreach ($this->hiddenfields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->datfields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->boolfields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->fields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->verteiler_edit_status->Text = 1;
		$this->ParticipantbindList();
		//$this->addParticipant->setVisible(true);
    }
    
	public function TSavedButtonClicked($sender,$param){
		
		$tempus=$this->primarykey;
		
		if($this->verteiler_edit_status->Text == '1'){
			$AEditRecord = VerteilerRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$AEditRecord = new VerteilerRecord;
		}
	
		//HIDDEN
		foreach ($this->hiddenfields as $recordfield){
			$edrecordfield = $recordfield;
			$AEditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->datfields as $recordfield){
			$edrecordfield = $recordfield;
			$AEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->boolfields as $recordfield){
			$edrecordfield = $recordfield;
			$AEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->fields as $recordfield){
			$edrecordfield = $recordfield;
			$AEditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$AEditRecord->save();
			
		$this->bindListVerteiler();
		$this->ParticipantbindList();
		$this->idtm_verteiler->Text=$AEditRecord->idtm_verteiler;
	}

	public function TNewButtonClicked($sender,$param){
    	
		$tempus = $this->primarykey;
		
		$this->$tempus->Text = '0';
		
    	//HIDDEN
		foreach ($this->hiddenfields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->setValue('0');
		}
		
		//DATUM
		foreach ($this->datfields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}
		
		//BOOL
		foreach ($this->boolfields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->setChecked(0);
		}
		
		//NON DATUM
		foreach ($this->fields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->Text = '0';
		}
		
		$this->verteiler_edit_status->Text = '0';
		$this->ParticipantbindList();
	}

	public function TSendButtonClicked(){
		$mail = new PHPMailer();
		$mail->From = "pf@com-x-cha.com";  
		$mail->FromName = "risklogIQ";  
		$mail->Host = "smtp.1und1.de";  
		$mail->Mailer = "smtp";
		$mail->SMTPAuth = true;
		$mail->Username = "pf@com-x-cha.com";
		$mail->Password = "anna100877";
		$mail->AddAddress("philipp.frenzel@winterheller.com","pepe");
		$mail->Subject = "Test";
		$mail->Body = "Test";
		if(!$mail->Send()){  
			$this->PFMAILER->TEXT = "There was an error sending the message";  
		}else{
			$this->PFMAILER->TEXT = "..done..";
		}
	}
	
	//ANFANG DER FUNKTIONEN FUER DIE LISTE Participant
		
	public function removeParticipant($sender,$param){
		//#todo, hier muss noch eine Pruefung hin, ob der mitarbeiter bereits inkludiert ist
		$deleteRecord = VerteilerOrganisationRecord::finder();
		$deleteRecord->deleteByPk($param->Item->lstpart_idtm_verteiler_organisation->Text);
		$this->ParticipantbindList();
	}
		
	public function addParticipant(){
				
		$myRecord = new VerteilerOrganisationRecord;
		
		$myRecord->idtm_verteiler = $this->idtm_verteiler->Text;
		$myRecord->idtm_organisation = $this->ttidtm_organisation->Text;
		
		$myRecord->save();
		$this->ParticipantbindList();
	}
		
	private function ParticipantbindList(){
		$criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "idtm_verteiler = :suchtext";
    		$criteria->Parameters[':suchtext'] = $this->idtm_verteiler->Text;
		$criteria->OrdersBy["idtm_organisation"] = 'asc';
    		$this->ParticipantListe->VirtualItemCount = count(VerteilerOrganisationView::finder()->findAll($criteria));
       		$this->ParticipantListe->DataKeyField = 'idtm_verteiler';
		$this->ParticipantListe->DataSource=VerteilerOrganisationView::finder()->findAll($criteria);
		$this->ParticipantListe->dataBind();
	}

        public function participantList_PageIndexChanged($sender,$param)
		{
			$this->ParticipantListe->CurrentPageIndex = $param->NewPageIndex;
			$this->ParticipantbindList();
		}
	//ENDE DER FUNKTIONEN FUER DIE LISTE Participant
	
}
?>