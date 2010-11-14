<?php

class ApplyToDimensionContainer extends TTemplateControl {

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack || !$this->page->IsCallback) {
            $this->initPullDowns();
        }
        $this->initParams();
    }

    public function initPullDowns(){
        $this->Tedidta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
        $this->Tedidta_stammdaten_group->dataBind();
    }

    public function initParams(){
        $this->Tedsend_tabelle->Text = $this->page->Tedsend_tabelle->Text;
        $this->Tedsend_id->Text = $this->page->Tedsend_id->Text;
        $this->Tedsend_field->Text = $this->page->Tedsend_field->Text;
    }

    public function SyncWithDimension($sender,$param){
        $cleanmodul = '';
        $SQL = "SELECT * FROM ".$this->Tedsend_tabelle->Text." WHERE parent_id".$this->Tedsend_tabelle->Text." = ". $this->Tedsend_id->Text;
        $cleanmodul = preg_replace("/(^t[a-z]\_)/", "", $this->Tedsend_tabelle->Text);
        preg_match("/(_[a-z])/", $cleanmodul, $matches);
        if(count($matches)>=1){
            $cleanmodul = preg_replace("/(_[a-z])/", ucfirst(substr($matches[1], 1, 1)), $cleanmodul);
        }
        $finderclass = ucfirst($cleanmodul)."Record";       
        $AllRecordsToWrite = TActiveRecord::finder($finderclass)->findAllBySql($SQL);
        $fieldToTransfer = $this->Tedsend_field->Text;
        if(count($AllRecordsToWrite)>0){
            foreach($AllRecordsToWrite AS $SingleRecord){
                $criteria = new TActiveRecordCriteria;
                $criteria->Condition = 'idta_stammdaten_group = :stammdaten_group AND stammdaten_key_extern = :key_extern';
                $criteria->Parameters[':stammdaten_group'] = $this->Tedidta_stammdaten_group->Text;
                $criteria->Parameters[':key_extern'] = $this->Tedsend_tabelle->Text.$SingleRecord->{'id'.$this->Tedsend_tabelle->Text};
                $RecordToChange = StammdatenRecord::finder()->find($criteria);
                if(count($RecordToChange)==0){
                    $RecordToChange = new StammdatenRecord();
                }
                $RecordToChange->idta_stammdaten_group = $this->Tedidta_stammdaten_group->Text;
                $RecordToChange->stammdaten_key_extern = $this->Tedsend_tabelle->Text.$SingleRecord->{'id'.$this->Tedsend_tabelle->Text};
                $RecordToChange->stammdaten_name = $SingleRecord->{$fieldToTransfer};
                $RecordToChange->save();
                unset($RecordToChange);
            }
        }
    }
}

?>