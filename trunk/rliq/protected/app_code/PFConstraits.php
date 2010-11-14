<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PFConstraits
 *
 * @author pf
 */
class PFConstraits {
//put your code here

    private $PSPBasisElements;
    private $Constraits = array();

    public function getConstraits($Nodes) {
        $this->PSPBasisElements = $Nodes;
        //zuerst holen wir uns das startelement
        foreach($this->PSPBasisElements as $Datensatz) {
            if(!$this->check_forPrevious($Datensatz)) {
                $MyStartID = $Datensatz->idtm_activity;
                $this->walkChildren($Datensatz);
            }
        }
        return $this->Constraits;
    }

    public function check_forPrevious($Node) {
        $SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."'";
        $Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
    }

    public function check_forPSPChildren($Node) {
        $SQL = "SELECT * FROM ta_activity_activity WHERE pre_idtm_activity = '".$Node->idtm_activity."'";
        $Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
    }

    private function walkChildren($Node) {
        foreach($this->get_PSPChildren($Node) as $Result) {
            array_push($this->Constraits,$this->return_RelType($Result, $Node->idtm_activity));
        }
        foreach($this->get_PSPChildren($Node) as $Result) {
            $tempNode = $this->return_ActivityByPK($Result->idtm_activity);
            $this->check_forPSPChildren($tempNode)?$this->walkChildren($tempNode):'';
        }
    }

    private function return_RelType($Node,$CurrentID) {
        $temp=array();
        $SQL = "SELECT * FROM ta_activity_activity WHERE idtm_activity = '".$Node->idtm_activity."' AND pre_idtm_activity ='".$CurrentID."'";
        $result = ActivityActivityRecord::finder()->findBySQL($SQL);
        if(count(ActivityActivityRecord::finder()->findAllBySQL($SQL))==1) {
            switch ($result->actact_type) {
                case 1:
                    $labType = "CONSTRAIN_STARTSTART";
                    break;
                case 2:
                    $labType = "CONSTRAIN_STARTEND";
                    break;
                case 3:
                    $labType = "CONSTRAIN_ENDEND";
                    break;
                default:
                    $labType = "CONSTRAIN_ENDSTART";
            }
            $temp=array($Node->idtm_activity,$CurrentID,$labType);
            return $temp;
        }else {
            $temp=array();
            return $temp;
        }
    }

    private function return_ActivityByPK($id) {
        foreach($this->PSPBasisElements as $Node) {
            if($Node->idtm_activity == $id) {
                return $Node;
                break;
            }
        }
        return 0;
    }

    public function get_PSPChildren($Node) {
        $SQL = "SELECT * FROM ta_activity_activity WHERE pre_idtm_activity = '".$Node->idtm_activity."'";
        $Result = count(ActivityActivityRecord::finder()->findAllBySQL($SQL));
        $SSQL = "SELECT * FROM tm_activity WHERE ";
        $counter = 0;
        if($Result>=1) {
            foreach(ActivityActivityRecord::finder()->findAllBySQL($SQL) as $Results) {
                $counter==0?$SSQL.="idtm_activity = '".$Results->idtm_activity."'":$SSQL.=" OR idtm_activity = '".$Results->idtm_activity."'";
                $counter++;
            }
        }else {
            $SSQL.="idtm_activity = '0'";
        }
        return ActivityRecord::finder()->findAllBySQL($SSQL);
    }

}
?>
