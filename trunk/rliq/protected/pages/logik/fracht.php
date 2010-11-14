<?php

class fracht extends TPage{
	
	public function onInit($param){
		
		parent::onInit($param);
		
		if(!$this->isPostBack){
		
		switch ($this->Request['modus']){
			case 0:
				$sql = "SELECT idtm_fahrzeug_kategorie, fahrzeug_kategorie_name FROM tm_fahrzeug_kategorie";
				$data = PFH::convertdbObjectArray(FahrzeugKategorieRecord::finder()->findAllBySql($sql),array("idtm_fahrzeug_kategorie","fahrzeug_kategorie_name"));
				$this->idtm_fahrzeug_kategorie->DataSource=$data;
				$this->idtm_fahrzeug_kategorie->dataBind();
				
				//$this->bindList();
				break;
			/*case 1:
				$sql = "SELECT idtm_waren_kategorie, waren_kategorie_name FROM tm_waren_kategorie";
				$data = PFH::convertdbObjectArray(WarenKategorieRecord::finder()->findAllBySql($sql),array("idtm_waren_kategorie","waren_kategorie_name"));
				$this->edidtm_waren_kategorie->DataSource=$data;
				$this->edidtm_waren_kategorie->dataBind();
				
				$sql = "SELECT idtm_preis_kategorie, preis_kategorie_name FROM tm_preis_kategorie";
				$data = PFH::convertdbObjectArray(PreisKategorieRecord::finder()->findAllBySql($sql),array("idtm_preis_kategorie","preis_kategorie_name"));
				$this->edidtm_preis_kategorie->DataSource=$data;
				$this->edidtm_preis_kategorie->dataBind();
				
				$this->fillValues($this->getSelected($this->Request['idta_waren']));
				$this->bindListed();
				break;*/
			default:
				break; 		
		}
				
		$this->viewPanel->ActiveViewIndex=$this->Request['modus'];
		}
	}
	
	protected function fillValues($item){
		
		$fields = array("waren_artikelnummer","waren_ean","waren_bezeichnung","waren_menge","waren_gewicht","waren_preis","waren_typ","idtm_waren_kategorie","idtm_preis_kategorie","idta_adresse");
		$this->edidta_waren->Value = $item->idta_waren;
		
		//ACHTUNG DATUM
		$this->edwaren_dat_lb->setDate(date($item->waren_dat_lb));
		
		foreach ($fields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$this->$edrecordfield->Text = $item->$recordfield;
		}
		
	}
	
	protected function getSelected($key){
		$finder = WarenRecord::finder();
		$item = $finder->findByPk($key);
		return $item;
	}
	
	private function bindList(){
		$SQL1 = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN ta_partei_has_ta_adresse ON ta_adresse.idta_adresse = ta_partei_has_ta_adresse.idta_adresse INNER JOIN ta_partei ON ta_partei_has_ta_adresse.idta_partei = ta_partei.idta_partei WHERE ta_partei.idtm_user = ".$this->User->getUserId($this->User->Name);
		$SQL1 .= " LIMIT ".$this->AdresseListe->PageSize;
		$SQL1 .= " OFFSET ".$this->AdresseListe->PageSize * $this->AdresseListe->CurrentPageIndex;
				
		
		$this->AdresseListe->DataKeyField = 'lstidta_adresse';
		$validate = PFH::checkCountStatement(AdresseRecord::finder()->findBySql($SQL1));
		if($validate){	
			$this->edAdresseListe->VirtualItemCount = AdresseRecord::finder()->findBySql($SQL1)->Count();
		}else{
			$this->edAdresseListe->VirtualItemCount = 0;
		}
		$this->AdresseListe->DataSource=$adressen = AdresseRecord::finder()->findAllBySql($SQL1);
		$this->AdresseListe->dataBind();
	}
	
	private function bindListed(){
		$SQL1 = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN ta_partei_has_ta_adresse ON ta_adresse.idta_adresse = ta_partei_has_ta_adresse.idta_adresse INNER JOIN ta_partei ON ta_partei_has_ta_adresse.idta_partei = ta_partei.idta_partei WHERE ta_partei.idtm_user = ".$this->User->getUserId($this->User->Name);
		$SQL1 .= " LIMIT ".$this->edAdresseListe->PageSize;
		$SQL1 .= " OFFSET ".$this->edAdresseListe->PageSize * $this->edAdresseListe->CurrentPageIndex;
				
		$this->edAdresseListe->DataKeyField = 'edlstidta_adresse';
		
		$validate = PFH::checkCountStatement(AdresseRecord::finder()->findBySql($SQL1));
		if($validate){	
			$this->edAdresseListe->VirtualItemCount = AdresseRecord::finder()->findBySql($SQL1)->Count();
		}else{
			$this->edAdresseListe->VirtualItemCount = 0;
		}
		$this->edAdresseListe->DataSource=$adressen = AdresseRecord::finder()->findAllBySql($SQL1);
		$this->edAdresseListe->dataBind();
	}
	
	public function editButtonClicked($sender,$param){
		
		$warenRecord = WarenRecord::finder()->findByPK($this->edidta_waren->Data);
	
		$fields = array("waren_artikelnummer","waren_ean","waren_bezeichnung","waren_menge","waren_gewicht","waren_preis","waren_typ","idtm_waren_kategorie","idtm_preis_kategorie","idta_adresse");
		
		//ACHTUNG DATUM
		$warenRecord->waren_dat_lb = date("Y-m-d",$this->edwaren_dat_lb->TimeStamp);
		
		foreach ($fields as $recordfield){
			$edrecordfield = 'ed'.$recordfield;
			$warenRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$warenRecord->save();
			
		$this->Response->redirect($this->getRequest()->constructUrl('page',"logik.partei"));
	}
	
	public function insertButtonClicked($sender,$param){
		
		$warenRecord = new WarenRecord;
		
		$fields = array("waren_artikelnummer","waren_ean","waren_bezeichnung","waren_menge","waren_gewicht","waren_preis","waren_typ","idtm_waren_kategorie","idtm_preis_kategorie","idta_adresse");
		
		//ACHTUNG DATUM
		$warenRecord->waren_dat_lb = date("Y-m-d",$this->waren_dat_lb->TimeStamp);
		
		foreach ($fields as $recordfield){
			$warenRecord->$recordfield = $this->$recordfield->Text;
		}

		$warenRecord->save();
			
		$this->Response->redirect($this->getRequest()->constructUrl('page',"logik.partei"));
	}
	
	public function dtgList_PageIndexChanged($sender,$param)
		{
			$this->AdresseListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindList();
		}
		
	public function eddtgList_PageIndexChanged($sender,$param)
		{
			$this->edAdresseListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListed();
		}
	
	public function dtgList_EditCommand($sender,$param)
		{
			$item=$param->Item;
			$this->idta_adresse_label->Text=$item->lst_adresse_street->Text;
			$this->idta_adresse->Data=$item->lst_idta_adresse->Text;
			$this->bindList();
		}	
		
	public function eddtgList_EditCommand($sender,$param)
		{
			$item=$param->Item;
			$this->edidta_adresse_label->Text=$item->edlst_adresse_street->Text;
			$this->edidta_adresse->Data=$item->edlst_idta_adresse->Text;
			$this->bindListed();
		}
		
}

?>