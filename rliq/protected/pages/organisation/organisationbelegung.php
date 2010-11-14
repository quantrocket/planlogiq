<?php

class organisationbelegung extends TPage {

    private $StartZeitStrahl = 6;
    private $EndZeitStrahl = 22;

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {
        date_default_timezone_set('Europe/Berlin');
        parent::onLoad($param);

        if(!$this->page->isPostBack && !$this->page->isCallback) {
            $tmpstartdate = new DateTime();
            $this->res_selected_date->setDate($tmpstartdate->format("Y-m-d"));

            $this->bindListBelegungsplan();
        }
    }

    public function dataBindRessourceStundenRepeater($sender,$param){
        if($sender->Id == 'Belegungsplan'){
            $item=$param->Item;
            if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
                $data = array();
                for($ii=$this->StartZeitStrahl;$ii<$this->EndZeitStrahl;$ii++){
                    $data[]=$ii;
                }
                $item->RessourceStundenRepeater->DataSource = $data;
                $item->RessourceStundenRepeater->dataBind();
                $item->RessourceViertelRepeater->DataSource = $data;
                $item->RessourceViertelRepeater->dataBind();
            }
        }
    }

    public function bindListBelegungsplan(){
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idtm_user > 0 ";
        $this->Belegungsplan->DataSource = OrganisationRecord::finder()->findAll($criteria);
        $this->Belegungsplan->dataBind();
    }

    public function UpdateBelegung($sender,$param){
        $this->bindListBelegungsplan();
    }

    /**
     *
     * @param <int> $ResZeitID The time id, passed by the repeater: 61 => 15:00 -> 15*4+1
     * @param <int> $idtm_ressource The Key-Field for the Ressource
     */

    public function checkRessourceStatus($ResZeitID,$idtm_organisation){
        $filterDate = date('Y-m-d',$this->res_selected_date->TimeStamp);

        //the calculation of the time-string
        $hourresult = round((($ResZeitID-1)/4)-0.26,0);
        $minuteresult = round((($ResZeitID-1)/4-$hourresult)*60+1,0);

        $mySQL = "SELECT tm_termin.* FROM tm_termin INNER JOIN vv_termin_organisation ON tm_termin.idtm_termin = vv_termin_organisation.idtm_termin WHERE ter_starttime <= '".$hourresult.":".$minuteresult."'";
        $mySQL .= " AND ter_endtime >= '".$hourresult.":".$minuteresult."' AND idtm_organisation = ".$idtm_organisation." AND ter_startdate = DATE('".$filterDate."')";
        //print_r($mySQL);
        $Results = TerminRecord::finder()->findAllBySQL($mySQL);

        if(count($Results)>=1){
            return "resFull";
        }else{
            return "resFree";
        }
    }

}
?>