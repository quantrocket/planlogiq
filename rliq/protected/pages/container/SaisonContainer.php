<?php

class SaisonContainer extends TTemplateControl{

    public $Perioden = array();

	public function onInit($param)
	{
		parent::onInit($param);
	}

        private function load_ta_perioden($Periode,$SinglePeriode=0){
		$Result = PeriodenRecord::finder()->findByper_Intern($Periode);
                $this->Perioden[$Result->per_intern]=$Result->per_extern;
                if($SinglePeriode==0){
                    $Records = PeriodenRecord::finder()->findAllByparent_idta_perioden($Result->idta_perioden);
                    foreach($Records As $Record){
                        $this->Perioden[$Record->per_intern]=$Record->per_extern;
                    }
                }
	}

        public function onLoad($param)
	{
		parent::onLoad($param);
                if(!$this->page->IsPostBack && !$this->page->isCallback){
                    $this->bindListBox();
                    $this->bindListSaison();                    
                }
	}

        public function bindListBox(){
            $this->sai_idta_feldfunktion->DataSource = PFH::build_SQLPullDownAdvanced(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name","idta_struktur_type"));
            $this->sai_idta_feldfunktion->DataBind();
        }

        public function bindListSaison(){
            //here i load all values from SaisonRecord
            $this->SaisonListe->DataSource=SaisonRecord::finder()->findAll();
            $this->SaisonListe->dataBind();
        }

        public function bindListSaisonTT(){
            //here i load all values from SaisonRecord
            $this->SaisonTTListe->DataSource=TTSaisonRecord::finder()->findAllByidta_saisonalisierung($this->sai_idta_saisonalisierung->Text);
            $this->SaisonTTListe->dataBind();
        }

	private $RCprimarykey = "idta_saisonalisierung";
	private $RCfields = array("sai_name","idtm_struktur","idta_feldfunktion");
	private $RCdatfields = array();
	private $RChiddenfields = array();
	private $RCboolfields = array();

        public function SCClosedButtonClicked($sender, $param){
            $this->page->mpnlSaisonalisierung->Hide();
	}

        
        public function load_saison($sender,$param){
    	
            $item = $param->Item;
            $myitem=SaisonRecord::finder()->findByPK($item->lst_idta_saison->Text);

            $tempus = 'sai_'.$this->RCprimarykey;
            $monus = $this->RCprimarykey;
		
            $this->$tempus->Text = $myitem->$monus;
		
            //HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->saison_edit_status->Text = 1;
                $this->bindListSaisonTT();
    }
    
	public function SCSavedButtonClicked($sender,$param){
		
		$tempus='sai_'.$this->RCprimarykey;
		
		if($this->saison_edit_status->Text == '1'){
			$RCEditRecord = SaisonRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$RCEditRecord = new SaisonRecord;
		}
	
		//HIDDEN
		foreach ($this->RChiddenfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->RCdatfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->RCboolfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->RCfields as $recordfield){
			$edrecordfield = 'sai_'.$recordfield;
			$RCEditRecord->$recordfield = $this->$edrecordfield->Text;
		}
		$RCEditRecord->save();
		$this->bindListSaison();
	}

	public function SCNewButtonClicked($sender,$param){
    		
            $tempus = 'sai_'.$this->RCprimarykey;
            $monus = $this->RCprimarykey;

            $this->$tempus->Text = '0';

            //HIDDEN
            foreach ($this->RChiddenfields as $recordfield){
                    $edrecordfield = 'sai_'.$recordfield;
                    $this->$edrecordfield->setValue('0');
            }

            //DATUM
            foreach ($this->RCdatfields as $recordfield){
                    $edrecordfield = 'sai_'.$recordfield;
                    $this->$edrecordfield->setDate(date('Y-m-d',time()));
            }

            //BOOL
            foreach ($this->RCboolfields as $recordfield){
                    $edrecordfield = 'sai_'.$recordfield;
                    $this->$edrecordfield->Checked(0);
            }

            //NON DATUM
            foreach ($this->RCfields as $recordfield){
                    $edrecordfield = 'sai_'.$recordfield;
                    $this->$edrecordfield->Text = '0';
            }

            $this->saison_edit_status->Text = '0';
        }
    
    
	public function SaisonListe_PageIndexChanged($sender,$param)
		{
			$this->SaisonListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListSaison();
		}

        public function SaisonTTListe_PageIndexChanged($sender,$param)
		{
			$this->SaisonTTListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListSaisonTT();
		}

        public function SCLoadButtonClicked($sender, $param){
            $sender->Text = "Start Loading...";
            $this->load_ta_perioden($this->Request['periode']);
            $fields = $this->get_month_array(FeldfunktionRecord::finder()->findByidta_feldfunktion($this->sai_idta_feldfunktion->Text)->idta_struktur_type,$this->sai_idta_feldfunktion->Text,$this->page->idtm_struktur->Text);
            $monthly = $this->get_month_weigth($fields, FeldfunktionRecord::finder()->findByidta_feldfunktion($this->sai_idta_feldfunktion->Text)->idta_struktur_type);
            $this->saveTTSaison($monthly);
            $this->bindListSaisonTT();
            $sender->Text = "Loaded";
        }

        public function saveTTSaison($montharray){
            $DeleteRecords = TTSaisonRecord::finder();
            $DeleteRecords->deleteAll('idta_saisonalisierung = ?',$this->sai_idta_saisonalisierung->Text);
            foreach($montharray AS $key=>$value){
                $MySaveRecord = new TTSaisonRecord();
                $MySaveRecord->idta_saisonalisierung = $this->sai_idta_saisonalisierung->Text;
                $MySaveRecord->sai_wert = $value;
                $MySaveRecord->idta_periode=$this->getIdtafeldfunktion($key);
                $MySaveRecord->save();
            }
        }

        private function get_month_weigth($fields,$fftype='0'){
		$returnarray=array();
		$sum=0;
		$count=0;
		$countsum=0;
		foreach($fields as $myfield){
			$sum+=$this->page->ACTPanel->FindControl($myfield)->Text*1;
			if(!$this->page->ACTPanel->FindControl($myfield)->Text==0){
					$count++;
			}
		}

                //hier muss hinterlegt werden, wenn aufsummiert werden muss - auch collector
		if($count==0 && ($fftype==0 || $fftype ==2)){
                    foreach($fields as $myfield){
                            $countsum++;
                    }
		}

		if($count>0){
                    $avg = $sum/$count;
		}else{
                    $avg=1;
		}
		//calculation for avg
		switch($fftype){
                    case 1:
                            foreach($fields as $fielda){
                                if($this->page->ACTPanel->FindControl($fielda)->Text!=0){
                                        $returnarray[$fielda]=$this->page->ACTPanel->FindControl($fielda)->Text*1/$avg;
                                }else{
                                    if($count == 0){
                                            $returnarray[$fielda]='1';
                                    }else{
                                            $returnarray[$fielda]='0';
                                    }
                                }
                            }
                            break;
            //calculation for sum
                    default:
                            foreach($fields as $fieldb){
                                if($this->page->ACTPanel->FindControl($fieldb)->Text!= 0){
                                    $returnarray[$fieldb]=$this->page->ACTPanel->FindControl($fieldb)->Text*1/$sum;
                                }else{
                                    if(!$countsum==0){
                                            $returnarray[$fieldb]=1/$countsum;
                                    }else{
                                            $returnarray[$fieldb]=0;
                                    }
                                }
                            }
		}
		return $returnarray;
	}

        private function get_month_array($type,$ff,$id){
		$returnarray = array();
		$jahr = 0;
		$monat = 0;
		foreach($this->Perioden AS $key => $value){
			if(preg_match('/^\d\d\d\d/',$value)){
				$jahr = $key;
				$monat = $jahr;
			}else{
				$jahr = $this->getYearByMonth($key);
                                $monat = $key;
				$fieldstr = "RLIQXXX".$jahr."XXX".$monat."XXX".$type."XXX".$ff."XXX".$id;
				array_push($returnarray,$fieldstr);
			}
		}
		return $returnarray;
	}

         private function getYearByMonth($periode_intern){
            $Result = PeriodenRecord::finder()->findByper_Intern($periode_intern);
            if($Result->parent_idta_perioden != 0){
                $Result2 = PeriodenRecord::finder()->findByidta_perioden($Result->parent_idta_perioden);
                return $Result2->per_intern;
            }else{
                return $periode_intern;
            }
        }

        private function getIdtafeldfunktion($Field){
            preg_match('/XXX([0-9]*)XXX([0-9]*)XXX.*/',$Field,$matches);
            $month = $matches[2];
            return PeriodenRecord::finder()->findByper_intern($month)->idta_perioden;
        }

        /**
         *
         * @param <type> $sender - the sender of the Click event
         * @param <type> $param - parameters, but sender->commandparameter
         * @method from this point the complete seasonality will be written
         * @author Philipp Frenzel pf@com-x-cha.com
         * @@copyright Frenzel GmbH 2009
         * 
         */

        private $allowedIDs=array(); //inside this array we store all allowed ids the user can see
        private $subcats = array();//list of all subcats
  
        public function SCRunSeasonsButtonClicked($sender,$param){
            //Step One, find all relevant IDs
            $FieldstToChange = array(); //inside this array, the fields that needed to be changed are listed
            $idta_variante = $this->Request['idta_variante'];
            $idta_struktur_type = FeldfunktionRecord::finder()->findByidta_feldfunktion($this->sai_idta_feldfunktion->Text)->idta_struktur_type;
            $idta_feldfunktion = $this->sai_idta_feldfunktion->Text;

            $FieldsToChange[] = $idta_feldfunktion; //this value needs always to be included

            $this->load_all_cats();
            $SQLINCondition = $this->subCategory_Inlist($this->subcats, $this->sai_idtm_struktur->Text);//the two must be replaced with the value from the usermanager

            $sql = "SELECT idtm_struktur FROM tm_struktur WHERE idtm_struktur IN (".$SQLINCondition.") AND idta_struktur_type = ".$idta_struktur_type;

            //here I recieve the array of values containing the elements to be changed
            $ElementsToChange = StrukturRecord::finder()->findAllBySQL($sql);

            //before the change can start, I need to identify the affected rows
            $FieldsToChangeBrutto = CollectorRecord::finder()->findAllBycol_idtafeldfunktion($idta_feldfunktion);

            foreach($FieldsToChangeBrutto As $TmpField){
               $FieldsToChange[] = $TmpField->idta_feldfunktion;
            }
            $SeasonToWrite = TTSaisonRecord::finder()->findAllByidta_saisonalisierung($this->sai_idta_saisonalisierung->Text);
            foreach($ElementsToChange AS $Element){
                foreach($FieldsToChange AS $Field){
                    foreach($SeasonToWrite As $Season){
                        $year_idta_periode = PeriodenRecord::finder()->find('idta_perioden = ?',PeriodenRecord::finder()->findByPK($Season->idta_periode)->parent_idta_perioden)->per_intern;
                        $sqlYEAR = "SELECT w_wert FROM tt_werte WHERE w_jahr = ".$year_idta_periode." AND w_monat = ".$year_idta_periode." AND idtm_struktur = ".$Element->idtm_struktur." AND w_id_variante = ".$idta_variante." AND idta_feldfunktion = ".$Field;
                        $YEARValue = WerteRecord::finder()->findBySQL($sqlYEAR)->w_wert;
                        $newMonthValue = $YEARValue * $Season->sai_wert;
                        //$sqlMONTH = "SELECT w_wert FROM tt_werte WHERE w_jahr = ".$year_idta_periode." AND w_monat = ".PeriodenRecord::finder()->findByPK($Season->idta_periode)->per_intern." AND idtm_struktur = ".$Element->idtm_struktur." AND w_id_variante = ".$idta_variante." AND idta_feldfunktion = ".$Field;
                        if(count(WerteRecord::finder()->find('w_jahr = ? AND w_monat = ? AND idtm_struktur = ? AND w_id_variante = ? AND idta_feldfunktion = ?',$year_idta_periode,PeriodenRecord::finder()->findByPK($Season->idta_periode)->per_intern,$Element->idtm_struktur,$idta_variante,$Field))){
                            $RecordToUpdate = WerteRecord::finder()->find('w_jahr = ? AND w_monat = ? AND idtm_struktur = ? AND w_id_variante = ? AND idta_feldfunktion = ?',$year_idta_periode,PeriodenRecord::finder()->findByPK($Season->idta_periode)->per_intern,$Element->idtm_struktur,$idta_variante,$Field);
                            $RecordToUpdate->w_wert = $newMonthValue;
                            $RecordToUpdate->save();
                        }
                    }
                }
            }
        }

        private function load_all_cats(){
            $rows = StrukturRecord::finder()->findAll();
            foreach($rows as $row){
                $this->subcats[$row->parent_idtm_struktur][]=$row->idtm_struktur;
                //$this->parentcats[$row->idtm_struktur]=$row->parent_idtm_struktur;
            }
        }

        private function subCategory_list($subcats,$catID){
            $this->allowedIDs[] = $catID; //id des ersten Startelements...
            if(array_key_exists($catID,$subcats)){
                foreach($subcats[$catID] as $subCatID){
                    $this->allowedIDs[] = $this->subCategory_list($subcats, $subCatID);
                }
            }
        }

        private function subCategory_Inlist($subcats,$catID){
            $lst = $catID; //id des ersten Startelements...
            if(array_key_exists($catID,$subcats)){
                foreach($subcats[$catID] as $subCatID){
                    $lst .= ",".$this->subCategory_Inlist($subcats, $subCatID);
                }
            }
            return $lst;
        }
}

?>