<?php

class ressourcenbelegung extends TPage {

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

            $this->idta_ressource_type->DataSource=PFH::build_SQLPullDown(RessourceTypeRecord::finder(),"ta_ressource_type",array("idta_ressource_type","res_type_name"));
            $this->idta_ressource_type->dataBind();
            $this->idta_ressource_type->Text = 4;

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
        if($this->page->isPostBack){
                $criteria->Condition = "idta_ressource_type = :suchtext1";
                $criteria->Parameters[':suchtext1'] = $this->idta_ressource_type->Text;
        }else{
            $criteria->Condition = "idta_ressource_type = :suchtext1";
            $criteria->Parameters[':suchtext1'] = $this->idta_ressource_type->Text;
        }
        $this->Belegungsplan->DataSource = RessourceRecord::finder()->findAll($criteria);
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

    public function checkRessourceStatus($ResZeitID,$idtm_ressource){
        $filterDate = date('Y-m-d',$this->res_selected_date->TimeStamp);

        //the calculation of the time-string
        $hourresult = round((($ResZeitID-1)/4)-0.26,0);
        $minuteresult = round((($ResZeitID-1)/4-$hourresult)*60+1,0);

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "ter_starttime <= :suchtext1 AND ter_endtime >= :suchtext2 AND idtm_ressource = :suchtext3 AND ter_startdate = DATE(:suchtext4)";
        $criteria->Parameters[':suchtext1'] = $hourresult.":".$minuteresult;
        $criteria->Parameters[':suchtext2'] = $hourresult.":".$minuteresult;
        $criteria->Parameters[':suchtext3'] = $idtm_ressource;
        $criteria->Parameters[':suchtext4'] = $filterDate;
        
        $Results = TerminRessourceOrganisationView::finder()->findAll($criteria);

        if(count($Results)>=1){
            return "resFull";
        }else{
            return "resFree";
        }
    }

}
?>