<?php
class ProtokollRendererEdit extends TDataListItemRenderer
{

    public function initPrtAufgaben(){
        $this->prtAufgabenContainer->bindListPrtAufgaben();
        $this->prtAufgabenContainer->__destruct();
    }

    public function getStartKnoten(){
        $CheckStart = TerminRecord::finder()->findByPK($this->page->edidtm_termin->Text)->idtm_activity;
        if($CheckStart>0){
            return $CheckStart;
        }else{
            return 0;
        }
    }

    public function initPullDown(){
        $this->idta_protokoll_ergebnistype->dataSource=PFH::build_SQLPullDown(ProtokollErgebnistypeRecord::finder(),"ta_protokoll_ergebnistype",array("idta_protokoll_ergebnistype","prt_ergtype_name"));
        $this->idta_protokoll_ergebnistype->dataBind();

        $HRKEYTest = new PFHierarchyPullDown();
        $HRKEYTest->setStructureTable("tm_activity");
        $HRKEYTest->setRecordClass(ActivityRecord::finder());
        $HRKEYTest->setPKField("idtm_activity");
        $HRKEYTest->setField("act_name");
        $CheckStart = TerminRecord::finder()->findByPK($this->page->edidtm_termin->Text)->idtm_activity;
        if($CheckStart>0){
            $HRKEYTest->setStartNode($CheckStart);
    }
        $HRKEYTest->letsrun();
        $this->idtm_activity->DataSource=$HRKEYTest->myTree;
        $this->idtm_activity->dataBind();
    }

    public function DisplayMyTaskPanel($sender,$param){
        if($this->idta_protokoll_ergebnistype->Text<3){
            $this->Tedauf_visible->Text="Dynamic";
        }else{
            $this->Tedauf_visible->Text="None";
        }
        $this->initPrtAufgaben();
    }
}
?>