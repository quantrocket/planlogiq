<?php

class wareninfo extends TPage{
	
	public function onInit($param){
		
		parent::onInit($param);
		
		if(!$this->isPostBack){
		
		switch ($this->Request['modus']){
			case 0:
				$sql = "SELECT idtm_waren_kategorie, waren_kategorie_name FROM tm_waren_kategorie";
				$data = PFH::convertdbObjectArray(WarenKategorieRecord::finder()->findAllBySql($sql),array("idtm_waren_kategorie","waren_kategorie_name"));
				$this->idtm_waren_kategorie->DataSource=$data;
				$this->idtm_waren_kategorie->dataBind();
				
				$sql = "SELECT idtm_preis_kategorie, preis_kategorie_name FROM tm_preis_kategorie";
				$data = PFH::convertdbObjectArray(PreisKategorieRecord::finder()->findAllBySql($sql),array("idtm_preis_kategorie","preis_kategorie_name"));
				$this->idtm_preis_kategorie->DataSource=$data;
				$this->idtm_preis_kategorie->dataBind();
				
				$this->fillValues($this->getSelected($this->Request['idta_waren']));
				$this->fillValuesPartei($this->getSelectedPartei($this->Request['idta_waren']));
				$this->fillValuesAdresse($this->getSelectedAdresse($this->Request['idta_waren']));
				break;
			default:
				break; 		
		}
				
		$this->viewPanel->ActiveViewIndex=$this->Request['modus'];
		}
	}
	
	protected function fillValues($item){
		
		$fields = array("waren_artikelnummer","waren_ean","waren_bezeichnung","waren_beschreibung","waren_menge","waren_gewicht","waren_preis","waren_typ","idtm_waren_kategorie","idtm_preis_kategorie");
		$this->idta_waren->Value = $item->idta_waren;
		$this->idta_adresse->Value = $item->idta_adresse;
		
		//ACHTUNG DATUM
		$this->waren_dat_lb->setText(date($item->waren_dat_lb));
		
		foreach ($fields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->Text = $item->$recordfield;
		}
		
	}
	
	protected function fillValuesPartei($item){
		
		$fields = array("partei_name","partei_name2","idta_partei");
		
		foreach ($fields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->Text = $item->$recordfield;
		}
		
	}
	
	protected function fillValuesAdresse($item){
		
		$fields = array("adresse_street","adresse_zip","adresse_town","adresse_lat","adresse_long");
		
		foreach ($fields as $recordfield){
			$edrecordfield = $recordfield;
			$this->$edrecordfield->Text = $item->$recordfield;
		}
		
	}
	
	protected function getSelected($key){
		$item = WarenRecord::finder()->withwarenadresse()->findByPk($key);
		return $item;
	}
	
	protected function getSelectedPartei($key){
		$SQL = "SELECT partei_name, partei_name2, ta_partei.idta_partei FROM ta_partei INNER JOIN ta_partei_has_ta_adresse ON ta_partei.idta_partei=ta_partei_has_ta_adresse.idta_partei INNER JOIN ta_adresse ON ta_adresse.idta_adresse = ta_partei_has_ta_adresse.idta_adresse INNER JOIN ta_waren ON ta_waren.idta_adresse = ta_adresse.idta_adresse WHERE ta_waren.idta_waren = ".$key;
		$item = ParteiRecord::finder()->findBySQL($SQL);
		return $item;
	}

	protected function getSelectedAdresse($key){
		$SQL = "SELECT adresse_lat, adresse_long, adresse_zip, adresse_town, adresse_street FROM ta_adresse INNER JOIN ta_waren ON ta_waren.idta_adresse = ta_adresse.idta_adresse WHERE ta_waren.idta_waren = ".$key;
		$item = AdresseRecord::finder()->findBySQL($SQL);
		return $item;
	}
	
	public function calcDistButtonClicked($sender,$param){
		
		//lets add the coordinates
		$myGTranslator = new GoogleAdressTranslator();
        $mapparams=$myGTranslator->getLatAndLong(implode(",",array($this->partner_street->Text,$this->partner_town->Text)));
        $myLatandLong = explode(",",$mapparams);
		
        $arraydistance = array(array($myLatandLong['1'],$myLatandLong['0']), array($this->adresse_lat->Text,$this->adresse_long->Text));
		$distance = PFH::getDistance($arraydistance);
		$baseweight = (double)$this->waren_gewicht->Text;
		$baseprice = (double)$this->waren_preis->Text;
		$this->est_waren_preis->Text = $distance*1.4/1000*2.5/2*$baseweight/10000 + $baseprice;
	}
	
}

?>