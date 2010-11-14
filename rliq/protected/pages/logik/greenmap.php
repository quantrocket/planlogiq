<?php
class GreenMap extends TPage
{
	
	public function onInit($param){
		
		//first i like to get all records...
        	$mydata = array();
        	// populates post data into the repeater
            $companies = $this->buildData();

            foreach($companies as $companie){
            	
            	$criteria_p=new TActiveRecordCriteria;
        		$criteria_p->Condition = 'idta_partei = :idta_partei';
        		$criteria_p->Parameters[':idta_partei'] = $companie->idta_partei;
        	
        		$templisteadresse = ParteiAdresseRecord::finder()->findAll($criteria_p);
        		$listeadresse = (array)$templisteadresse;
        		//print_r($listeadresse);
        	
        		foreach($listeadresse as $walker){
        			$conditionx = new TActiveRecordCriteria;
        			$conditionx->Condition = 'idta_adresse = :idta_adresse';
        			$conditionx->Parameters[':idta_adresse'] = $walker->idta_adresse;
        			array_push($mydata,AdresseRecord::finder()->find($conditionx));
        		}
            }
            
            //print_r($mydata);
            $ii = 0;
            
        	foreach($mydata as $mylocaladress){
            	$marker = prado::createComponent('BActiveGoogleMapMarker');
				$marker->setID("p".$ii);
				//$marker->setTitle('Adress:'.$mylocaladress->adresse_town."-".$mylocaladress->adresse_street);
				$marker->setTitle('Standort');
                                $marker->setPoint(array($mylocaladress->adresse_lat,$mylocaladress->adresse_long));
				$marker->setVisible(true);
				$infobulle = prado::createComponent('TLabel');
				$infobulle->setID('greentradeinfo'.$mylocaladress->idta_adresse);
				$infobulle->setText("Klappt");
				$marker->addedControl($infobulle);
				$this->GoogleMap->addMarker($marker);
        		    	
            	if($ii==0){
            		$this->GoogleMap->setCenter("(".implode(",",array($mylocaladress->adresse_lat,$mylocaladress->adresse_long)).")");	
            	}
            	$ii++;
       		}
       		
	}
	
	private function buildData(){
		
		// Construts a query criteria
        $criteria_t=new TActiveRecordCriteria;
        $criteria_t->Condition = 'idtm_user = :idtm_user';
        $criteria_t->Parameters[':idtm_user'] = $this->User->getUserId($this->User->Name);
        $criteria_t->OrdersBy['partei_name']='asc';
        // query for the posts with the above criteria and with author information
        return ParteiRecord::finder()->findAll($criteria_t);
		
	}
	
}
?>