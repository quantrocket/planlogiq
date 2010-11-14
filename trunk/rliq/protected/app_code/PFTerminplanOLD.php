<?php

class PFTerminplan extends TWebControl{
	
	private $PSPRecord;
	private $PSPFinder;
	private $AllPSPRecords=array();
	private $PSPBasisElements=array();
	private $PSPBasisElementsTemporary=array();
	
	public function load_PSP(){
		$this->PSPRecord = new ActivityRecord();
		$this->PSPFinder = $this->PSPRecord->finder();
		$this->AllPSPRecords = $this->PSPFinder->findAll();
		$this->calc_PSPBasisElements();
                $this->calc_PSPTerminforward();
	}
	
	public function get_PSPElements(){
		return $this->AllPSPRecords;
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
	
	public function check_forPSPChildren($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE pre_idtm_activity = '".$Node->idtm_activity."'";
		$Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}
	
	public function calc_PSPTerminforward(){
		//zuerst holen wir uns das startelement
		foreach($this->PSPBasisElements as $Datensatz){
			if(!$this->check_forPrevious($Datensatz)){
				$MyStartID = $Datensatz->idtm_activity;
				$this->calc_StartDatenEndDate($Datensatz,1);
			}
		}
		foreach($this->PSPBasisElements as $Datensatz){
			if($this->check_forPrevious($Datensatz)){
				$this->calc_StartDatenEndDate($Datensatz);
			}
		}	
	}
	
	private function calc_StartDatenEndDate($Node,$start=0,$faz=0){
		if($start==1){
                    if(!$this->isMilestone($Node)){
                        $Node->ttact_startdate=$Node->act_startdate;
                        $myDate = new DateTime($Node->ttact_startdate);
                        $myDate->modify($Node->act_dauer."days");
                        $Node->ttact_enddate=$myDate->format("Y-m-d");
                    }else{
                        $Node->ttact_startdate=$Node->act_startdate;
                        $Node->ttact_enddate=$Node->act_enddate;
                    }
		}
		else{
                    if(!$this->isMilestone($Node)){
                        if($Node->ttact_startdate==0){
                            $Node->ttact_startdate=$this->calc_EndDatefromPrevious($Node);
                            $myDate = new DateTime($Node->ttact_startdate);
                            $myDate->modify($Node->act_dauer."days");
                            $Node->ttact_enddate=$myDate->format("Y-m-d");
                        }
                     }else{
                       $Node->ttact_startdate=$Node->act_startdate;
                       $Node->ttact_enddate=$Node->act_enddate;
                     }
		}
	}
	
	private function calc_EndDatefromPrevious($Node){
		$SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."'";
		$Result = ActivityActivityRecord::finder()->findAllBySQL($SQL);
                $myDate = new DateTime("now");
                $FEZ = $myDate->format("Y-m-d");
		if(count(ActivityActivityRecord::finder()->findAllBySQL($SQL))>0){
			foreach($Result as $Record){
				$ActRecord=$this->return_ActivityByPK($Record->pre_idtm_activity);
				if($ActRecord!=0){
					$this->calc_StartDatenEndDate($ActRecord); //this is the code where me make the recursive walk!
					$compDate = new DateTime($ActRecord->ttact_enddate);
                                        $fcompDate = $compDate->format("Y-m-d");
                                        if($FEZ<=$fcompDate){
						$tempDate = new DateTime($ActRecord->ttact_enddate);
                                                $FEZ = $tempDate->format("Y-m-d");
                                                //print_r($FEZ);
					}
				}
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
	
        private function isMilestone($Node){
            if($Node->idta_activity_type == 1){
                return true;
            }else{
                return false;
            }
        }

}

?>