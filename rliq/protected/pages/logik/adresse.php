<?php

class adresse extends TPage{
	
	public function onInit($param){
		
		parent::onInit($param);
		
		if(!$this->isPostBack){
		
		switch ($this->Request['modus']){
			case 0:
				$sql = "SELECT idtm_country, country_ful FROM tm_country";
				$data = PFH::convertdbObjectArray(CountryRecord::finder()->findAllBySql($sql),array("idtm_country","country_ful"));
				$this->Country->DataSource=$data;
				$this->Country->dataBind();
				$this->idta_partei->Data = $this->Request['idta_partei'];
				break;
			case 1:
				$sql = "SELECT idtm_country, country_ful FROM tm_country";
				$data = PFH::convertdbObjectArray(CountryRecord::finder()->findAllBySql($sql),array("idtm_country","country_ful"));
				$this->edCountry->DataSource=$data;
				$this->edCountry->dataBind();
				$this->fillValues($this->getSelected($this->Request['idta_adresse']));
				break;
			default:
				break; 		
		}
				
		$this->viewPanel->ActiveViewIndex=$this->Request['modus'];
		}
	}
	
	protected function fillValues($item){
		$this->edStreet->Text = $item->adresse_street;
		$this->edTown->Text = $item->adresse_town;
		$this->edZip->Text = $item->adresse_zip;
		$this->edCountry->Text = $item->idtm_country;
		$this->edidta_adresse->Data = $item->idta_adresse;
	}
	
	protected function getSelected($key){
		$finder = AdresseRecord::finder();
		$item = $finder->findByPk($key);
		return $item;
	}
	
	public function insertButtonClicked($sender,$param){
		
		$adresseRecord = new AdresseRecord;
		
		$adresseRecord->adresse_street = $this->Street->Text;
		$adresseRecord->adresse_town = $this->Town->Text;
		$adresseRecord->adresse_zip = $this->Zip->Text;
		
		//lets add the coordinates
                $myGTranslator = new GoogleAdressTranslator();
                $mapparams=$myGTranslator->getLatAndLong(implode(",",array($this->Street->Text,$this->Town->Text)));
                $myLatandLong = explode(",",$mapparams);

                //here we check, if the coordinates have been found
                if($myLatandLong[1]!=0) {

                    $adresseRecord->adresse_lat = $myLatandLong[1];
                    $adresseRecord->adresse_long = $myLatandLong[0];

                }
                else {
                    $adresseRecord->adresse_lat = "48.189950";
                    $adresseRecord->adresse_long = "16.377319";
                }
		
		$adresseRecord->idtm_country = $this->Country->Text;
		
		$adresseRecord->save();
		
		//einbinden der zusaetzlichen infos
		
		$parteiadresseRecord = new ParteiAdresseRecord;
		$parteiadresseRecord->idta_partei = $this->idta_partei->Data;
		$parteiadresseRecord->idta_adresse = $adresseRecord->idta_adresse;
		
		$parteiadresseRecord->save();
		
		$this->Response->redirect($this->getRequest()->constructUrl('page',"logik.partei"));
	}
	
	public function editButtonClicked($sender,$param){
		
		$ar = AdresseRecord::finder()->findByPK($this->edidta_adresse->Data);
		
		$ar->adresse_street = $this->edStreet->Text;
		$ar->adresse_town = $this->edTown->Text;
		$ar->adresse_zip = $this->edZip->Text;
		$ar->idtm_country = $this->edCountry->Text;
		$ar->idta_adresse = $this->edidta_adresse->Data;
		
		//lets add the coordinates
		$myGTranslator = new GoogleAdressTranslator();
        $mapparams=$myGTranslator->getLatAndLong(implode(",",array($this->edStreet->Text,$this->edTown->Text)));
        $myLatandLong = explode(",",$mapparams);
		
        
        //here we check, if the coordinates have been found
        if($myLatandLong[1]!=0){
        
			$ar->adresse_lat = $myLatandLong[1];
			$ar->adresse_long = $myLatandLong[0];
			
        }
        else{
        	$ar->adresse_lat = "48.189950";
        	$ar->adresse_long = "16.377319";
        }
		
		
		
		$ar->save();
		
		$this->Response->redirect($this->getRequest()->constructUrl('page',"logik.partei"));
	}
	
}

?>