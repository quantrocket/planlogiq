<?php

class PFTerminplan extends TWebControl{
	
	private $PSPRecord;
	private $PSPFinder;
	private $AllPSPRecords=array();
	private $PSPBasisElements=array();
	private $PSPBasisElementsTemporary=array();
        private $subcats=array();
        private $allowedIDs=array();
	
	public function load_PSP($idtm_activity="0"){
		$this->PSPRecord = new ActivityRecord();
		$this->PSPFinder = $this->PSPRecord->finder();
		$this->AllPSPRecords = $this->initValues($idtm_activity);
		$this->calc_PSPBasisElements();
		$this->calc_PSPforward();
	}

        public function initValues($idtm_activity){
            $this->allowedIDs = array();
            $this->load_all_cats();
            $this->subCategory_list($this->subcats,$idtm_activity);
            $SQL = "SELECT * FROM tm_activity WHERE idtm_activity IN (";
            $counter=0;
            foreach($this->allowedIDs AS $tmp_id){
                if($tmp_id!=''){
                    $counter==0?$SQL.="'".$tmp_id."'":$SQL.=",'".$tmp_id."'";
                    $counter++;
                }
            }
            $SQL.=")";
            return ActivityRecord::finder()->findAllBySql($SQL);
        }

        public function load_all_cats() {
            $rows = ActivityRecord::finder()->findAll();
            foreach($rows as $row) {
                $this->subcats[$row->parent_idtm_activity][]=$row->idtm_activity;
            }
        }

        private function subCategory_list($subcats,$catID) {
            $this->allowedIDs[] = $catID; //id des ersten Startelements...
            if(array_key_exists($catID,$subcats)) {
                foreach($subcats[$catID] as $subCatID) {
                    $this->allowedIDs[] = $this->subCategory_list($subcats,$subCatID);
                }
            }
        }
	
	public function get_PSPElements(){
		return $this->AllPSPRecords;
	}
	
	public function calc_PSPStep(){
		
	}
	
	public function check_forChildren($Node){
		$SQL = "SELECT * FROM tm_activity WHERE parent_idtm_activity = '".$Node->idtm_activity."'";
		$Result = count($this->PSPFinder->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}
	
	public function calc_PSPBasisElements(){
		foreach($this->AllPSPRecords as $Datensatz){
			if($this->check_forPrevious($Datensatz)){
				$Datensatz->ttact_hasPrevious=1;
			}else{
				$Datensatz->ttact_hasPrevious=0;
			}
			if(!$this->check_forChildren($Datensatz)){
				array_push($this->PSPBasisElements,$Datensatz);
			}
		}
	}
	
	public function get_PSPBasisElements(){
		return $this->PSPBasisElements;
	}
	
	public function check_forPrevious($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."'";
		$Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}

        public function check_ifLatest($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."'";
		$Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return false;
		}else{
			return true;
		}
	}
	
	public function check_forPSPChildren($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE pre_idtm_activity = '".$Node->idtm_activity."'";
		$Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}

        public function check_forPSPParents($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."'";
		$Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}

        private function getMinElement($data){
            for ($i = count($data) - 1; $i >= 0; $i--) {
		$swapped = false;
		for ($j = 0; $j < $i; $j++) {
			if ( $data[$j]->ttact_saz > $data[$j + 1]->ttact_saz ) {
				$tmp = $data[$j];
                                $data[$j] = $data[$j + 1];
                                $data[$j + 1] = $tmp;
                                $swapped = true;
                        }
		}
                if (!$swapped) {
			return $data[0];
		}
            }            
        }

        public function getMaxSEZ($data){
            for ($i = count($data) - 1; $i >= 0; $i--) {
		$swapped = false;
		for ($j = 0; $j < $i; $j++) {
			if ( $data[$j]->ttact_sez < $data[$j + 1]->ttact_sez ) {
				$tmp = $data[$j];
                                $data[$j] = $data[$j + 1];
                                $data[$j + 1] = $tmp;
                                $swapped = true;
                        }
		}
                if (!$swapped) {
			return $data[0];
		}
            }
        }

        private function getMinPuffer($data){
            for ($i = count($data) - 1; $i >= 0; $i--) {
		$swapped = false;
		for ($j = 0; $j < $i; $j++) {
			if ( $data[$j]->ttact_gp > $data[$j + 1]->ttact_gp ) {
				$tmp = $data[$j];
                                $data[$j] = $data[$j + 1];
                                $data[$j + 1] = $tmp;
                                $swapped = true;
                        }
		}
                if (!$swapped) {
			return $data[0];
		}
            }
        }
	
	public function calc_PSPforward(){
		//zuerst holen wir uns das startelement
		foreach($this->PSPBasisElements as $Datensatz){
			if(!$this->check_forPrevious($Datensatz)){
				$MyStartID = $Datensatz->idtm_activity;
				$this->calc_StartEndDate($Datensatz,1);
                $this->walkChildren($Datensatz);
			}
		}
     }
     
        private function walkChildren($Node){
            foreach($this->get_PSPChildren($Node) as $Result){
               $tempNode = $this->return_ActivityByPK($Result->idtm_activity);
               $this->calc_StartEndDate($tempNode,0,0,$this->return_RelType($Result, $Node->idtm_activity));
            }
            foreach($this->get_PSPChildren($Node) as $Result){
               $tempNode = $this->return_ActivityByPK($Result->idtm_activity);
               $this->check_forPSPChildren($tempNode)?$this->walkChildren($tempNode):'';
            }
        }

        public function get_PSPChildren($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE pre_idtm_activity = '".$Node->idtm_activity."'";
		$Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
                $SSQL = "SELECT * FROM tm_activity WHERE ";
                $counter = 0;
                if($Result>=1){
                    foreach(ActivityActivityRecord::finder()->findAllBySQL($SQL) as $Results){
                        $counter==0?$SSQL.="idtm_activity = '".$Results->idtm_activity."'":$SSQL.=" OR idtm_activity = '".$Results->idtm_activity."'";
                        $counter++;
                    }
                }else{
                    $SSQL.="idtm_activity = '0'";
                }
                return ActivityRecord::finder()->findAllBySQL($SSQL);
	}

        public function get_PSPParents($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."'";
		$Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
                    $SSQL = "SELECT * FROM tm_activity WHERE ";
                    $counter = 0;
                    foreach(ActivityActivityRecord::finder()->findAllBySQL($SQL) as $Results){
                         $counter==0?$SSQL.="idtm_activity = '".$Results->pre_idtm_activity."'":$SSQL.=" OR idtm_activity = '".$Results->pre_idtm_activity."'";
                         $counter++;
                    }
                    return ActivityRecord::finder()->findAllBySQL($SSQL);
                }
	}
	
	private function calc_SAZnSEZ($Node,$start=0,$faz=0,$RELTYPE=""){
                if($start==1){
			$Node->ttact_sez=$Node->ttact_fez;
			$Node->ttact_saz=$Node->ttact_sez-$Node->act_dauer;
		}
		else{
                   switch ($RELTYPE['RELTYPE']){
                        case 1:                           
                            $tmpNode = $this->return_ActivityByPREID($RELTYPE['PREID']);
                            $Node->ttact_saz=$tmpNode->ttact_saz-$RELTYPE['MINZ']-$RELTYPE['MAXZ'];
                            $Node->ttact_sez=$Node->ttact_saz+$Node->act_dauer;
                            break;
                        case 2:
                            $tmpNode = $this->return_ActivityByPREID($RELTYPE['PREID']);//der Nachfolger
                            $Node->ttact_saz=$tmpNode->ttact_sez-$RELTYPE['MINZ']-$RELTYPE['MAXZ'];
                            $Node->ttact_sez=$Node->ttact_saz+$Node->act_dauer;
                            break;
                        case 3:
                            $tmpNode = $this->return_ActivityByPREID($RELTYPE['PREID']);
                            $Node->ttact_sez=$tmpNode->ttact_sez-$RELTYPE['MINZ']-$RELTYPE['MAXZ'];
                            $Node->ttact_saz=$Node->ttact_sez-$Node->act_dauer;
                            break;
                        default:
                            $tmpNode = $this->return_ActivityByPREID($RELTYPE['PREID']);
                            $mySAZ = $this->getMinSAZfromChildren($Node);
                            $Node->ttact_sez=$mySAZ-$RELTYPE['MINZ']-$RELTYPE['MAXZ'];
                            $Node->ttact_saz=$Node->ttact_sez-$Node->act_dauer;
                    }
		}
	}

        private function getMinSAZfromChildren($Node){
            foreach($this->get_PSPChildren($Node) as $Result){
               $tempNode[] = $this->return_ActivityByPK($Result->idtm_activity);
            }
            $test = $this->getMinElement($tempNode);
            return $test->ttact_saz;
        }

        private function getMinGPfromChildren($Node){
            if($this->check_forPSPChildren($Node)){
                foreach($this->get_PSPChildren($Node) as $Result){
                   $tempNode[] = $this->return_ActivityByPK($Result->idtm_activity);
                }
                $test = $this->getMinPuffer($tempNode);
                return $test->ttact_gp;
            }else{
                return 0;
            }
        }

        private function calc_StartEndDate($Node,$start=0,$faz=0,$RELTYPE=""){
            if($start==1){
                if(!$this->isMilestone($Node)){
                        $Node->ttact_startdate=$Node->act_startdate;
                        $myDate = new DateTime($Node->ttact_startdate);
                        $myDate->modify($Node->act_dauer."days");
                        $Node->ttact_enddate=$myDate->format("Y-m-d");
                }else{
                        $Node->ttact_startdate=$Node->act_startdate;
                        $myDate = new DateTime($Node->ttact_startdate);
                        $Node->ttact_enddate=$myDate->format("Y-m-d");
                }
            }
            else{
             if(!$this->isMilestone($Node)){
                    $tmpNode = $this->return_ActivityByPREID($RELTYPE['PREID']);
                    $Node->ttact_startdate=$tmpNode->ttact_enddate;
                    $myDate = new DateTime($Node->ttact_startdate);
                    $myDate->modify($Node->act_dauer."days");
                    $Node->ttact_enddate=$myDate->format("Y-m-d");                    
            }else{
                    //$tmpNode = $this->return_ActivityByPREID($RELTYPE['PREID']);
                    $Node->ttact_startdate<=$this->get_maxEndDate($Node)?$Node->ttact_startdate=$this->get_maxEndDate($Node):'';
                    //$Node->ttact_startdate=$tmpNode->ttact_enddate;
                    $myDate = new DateTime($Node->ttact_startdate);
                    $Node->ttact_enddate=$myDate->format("Y-m-d");
            }
		}
	}
	
	private function get_maxEndDate($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."'";
		$Result = ActivityActivityRecord::finder()->findAllBySQL($SQL);
		$FEZ = $Node->ttact_enddate;
		if(count(ActivityActivityRecord::finder()->findAllBySQL($SQL))>0){
			foreach($Result as $Record){
                $RELTYPE = $this->return_RelType($Node,$Record->pre_idtm_activity);
				$ActRecord=$this->return_ActivityByPK($Record->pre_idtm_activity);
                if($ActRecord!=0){
					if($ActRecord->ttact_enddate >= $FEZ){
						$FEZ = $ActRecord->ttact_enddate;
					}
				}
                $ActRecord->ttact_iscalculated=1;
			}
		}
		return $FEZ;
	}
	
	private function return_ActivityByPK($id){
		foreach($this->PSPBasisElements as $Node){
			if($Node->idtm_activity == $id){
				return $Node;
				break;
			}
		}
		return 0;
	}

        private function return_ActivityByPREID($id){
		foreach($this->PSPBasisElements as $Node){
			if($Node->idtm_activity == $id){
				return $Node;
				break;
			}
		}
		return 0;
	}

        private function return_RelType($Node,$CurrentID){
            $temp=array();
            $SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."' AND pre_idtm_activity ='".$CurrentID."'";
            $result = ActivityActivityRecord::finder()->findBySQL($SQL);
            if(count(ActivityActivityRecord::finder()->findAllBySQL($SQL))==1){
                $temp['RELTYPE']=$result->actact_type;
                $temp['MINZ']=$result->actact_minz;
                $temp['MAXZ']=$result->actact_maxz;
                $temp['PREID']=$CurrentID;
            }else{
                $temp['RELTYPE']=0;
                $temp['MINZ']=0;
                $temp['MAXZ']=0;
                $temp['PREID']=0;
            }
            return $temp;
        }

        private function return_RelTypeParents($Node,$CurrentID){
            $temp=array();
            $SQL = "SELECT * FROM ta_activity_activity WHERE pre_idtm_activity = ".$Node->idtm_activity." AND idtm_activity =".$CurrentID;
            $result = ActivityActivityRecord::finder()->findBySQL($SQL);
            if(count(ActivityActivityRecord::finder()->findAllBySQL($SQL))==1){
                $temp['RELTYPE']=$result->actact_type;
                $temp['MINZ']=$result->actact_minz;
                $temp['MAXZ']=$result->actact_maxz;
                $temp['PREID']=$CurrentID;
            }else{
                $temp['RELTYPE']=0;
                $temp['MINZ']=0;
                $temp['MAXZ']=0;
                $temp['PREID']=0;
            }
            return $temp;
        }

         private function isMilestone($Node){
            if($Node->idta_activity_type == 1){
                return true;
            }else{
                return false;
            }
        }
	
}

?>