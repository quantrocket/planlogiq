<?php

class a_Zeiterfassung_Mitarbeiter extends TPage
{

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public $TTColors = array(1=>"PSPSAZ","PSPFEZ","alternating","nonealternating");

    public $SummeDauer = 0;
    public $SummeKM = 0;
	
	public function onLoad($param){
            date_default_timezone_set('Europe/Berlin');
            parent::onLoad($param);
            if(!$this->isPostBack && !$this->isCallback){
                $this->RCedidtm_organisation->Text=$this->User->getUserOrgId($this->User->getUserId());

                $tmpstartdate = new DateTime();
                $tmpstartdate->modify("-30days");
                $this->zeiterfassung_datestart->setDate($tmpstartdate->format("Y-m-d"));
                $tmpstartdate->modify("45days");
                $this->zeiterfassung_dateende->setDate($tmpstartdate->format("Y-m-d"));
                $this->bindListRCValue();
                $idta_kostenstatus = PFH::build_SQLPullDown(KostenStatusRecord::finder(),"ta_kosten_status",array("idta_kosten_status","kst_status_name"));
                $idta_kostenstatus["Alle"]="Alle anzeigen";
                $this->idta_kosten_status->DataSource=$idta_kostenstatus;
                $this->idta_kosten_status->dataBind();
                $this->idta_kosten_status->Text="Alle";
                $idtm_activity = PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_name","act_pspcode"),"idta_activity_type = 2","act_name ASC");
                $idtm_activity["Alle"]="Alle anzeigen";
                $this->idtm_activity->DataSource=$idtm_activity;
                $this->idtm_activity->dataBind();
                $this->idtm_activity->Text="Alle";
            }
	}

        public function bindListRCValue() {
            $datestart = date('Y-m-d',$this->zeiterfassung_datestart->TimeStamp);
            $dateende = date('Y-m-d',$this->zeiterfassung_dateende->TimeStamp);
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition ="(zeit_date >= DATE(:suchtext2) AND zeit_date <= DATE(:suchtext3))";           
            $criteria->Parameters[':suchtext2'] = $datestart;
            $criteria->Parameters[':suchtext3'] = $dateende;
            if($this->RCedidtm_organisation->Text>=1){
                $criteria->Condition .= " AND idtm_organisation = :suchtext1";
                $criteria->Parameters[':suchtext1'] = $this->RCedidtm_organisation->Text;
            }
            if($this->idta_kosten_status->Text!="Alle"){
                $criteria->Condition .= " AND idta_kosten_status = :suchtext4";
                $criteria->Parameters[':suchtext4'] = $this->idta_kosten_status->Text;
            }
            if($this->idtm_activity->Text!="Alle"){
                $criteria->Condition .= " AND idtm_activity = :suchtext5";
                $criteria->Parameters[':suchtext5'] = $this->idtm_activity->Text;
            }
            $criteria->OrdersBy['idtm_activity']='asc';
            $criteria->OrdersBy['zeit_date']='asc';
            $criteria->OrdersBy['idta_kosten_status']='asc';
            //$this->ZeiterfassungListe->VirtualItemCount = count(ZeiterfassungRecord::finder()->findAll($criteria));
            $this->ZeiterfassungListe->DataSource=ZeiterfassungRecord::finder()->findAll($criteria);
            $this->ZeiterfassungListe->dataBind();
        }

        public function prepareForHtml($content){
            return preg_replace("/\n/", "<br/>\n", $content);
        }

        public function countDetailbelege($idtm_zeiterfassung){
            $SQL = "SELECT count(idtm_detail_beleg) AS idtm_detail_beleg FROM tm_detail_beleg WHERE deb_tabelle = 'tm_zeiterfassung' AND deb_deleted = 0 AND deb_id = ".$idtm_zeiterfassung ." GROUP BY deb_tabelle";
            $MyResult = DetailBelegRecord::finder()->findBySQL($SQL);
            if(is_object($MyResult)){
                return $MyResult->idtm_detail_beleg;
            }else{
                return 0;
            }
        }

        public function load_detailbelege($sender,$param){
            $item=$param->Item;
            if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
                $SQL = "SELECT * FROM tm_detail_beleg WHERE deb_tabelle = 'tm_zeiterfassung' AND deb_deleted = 0 AND deb_id = ".$item->Data->idtm_zeiterfassung;
                //TODO : Hier muss noch die einschraenkung beherzigt werden...
                $item->CCDetailBelegListe->DataSource=DetailBelegRecord::finder()->findAllBySQL($SQL);
                $item->CCDetailBelegListe->dataBind();
            }
        }

        public function updateStatusChecked($sender,$param){
            $ChangeRecord = ZeiterfassungRecord::finder()->findByidtm_zeiterfassung($param->CallbackParameter);
            $ChangeRecord->zeit_checked=$sender->Checked?1:0;
            $ChangeRecord->save();
        }

        public function updateStatusAbgerechnet($sender,$param){
            $ChangeRecord = ZeiterfassungRecord::finder()->findByidtm_zeiterfassung($param->CallbackParameter);
            $ChangeRecord->zeit_abgerechnet=$sender->Checked?1:0;
            $ChangeRecord->save();
        }
	
}
?>