<?php

class newpartei extends TPage{
	
	public function onInit($param){
		parent::onInit($param);
	}
	
	public function saveButtonClicked($sender,$param){
		
		$parteiRecord = new ParteiRecord;
		
		$parteiRecord->partei_name = $this->Name1->Text;
		$parteiRecord->partei_name2 = $this->Name2->Text;
		$parteiRecord->partei_name3 = $this->Name3->Text;
		$parteiRecord->partei_vorname = $this->Vorname->Text;
		
		$parteiRecord->idtm_user = $this->User->getUserId($this->User->Name);
		
		$parteiRecord->save();
		
		$this->Response->redirect($this->getRequest()->constructUrl('page',"logik.partei"));
	}
	
}

?>