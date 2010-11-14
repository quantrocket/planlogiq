<?php
class AufgabenRendererEdit extends TDataListItemRenderer{

    public function initPullDown(){
        $this->ttidtm_ressource->DataSource=PFH::build_SQLPullDown(RessourceRecord::finder(),"tm_ressource",array("idtm_ressource","res_name"));
        $this->ttidtm_ressource->dataBind();
        $this->Tedidta_aufgaben_type->DataSource=PFH::build_SQLPullDown(AufgabenTypeRecord::finder(),"ta_aufgaben_type",array("idta_aufgaben_type","auf_type_name"));
        $this->Tedidta_aufgaben_type->dataBind();
    }

    public function addRessource($sender,$param){
        //auf welche dimension sollen die werte zugeordnet werden
        $rIndecies = $this->ttidtm_ressource->SelectedIndices;
        foreach($rIndecies as $index)
        {
            $myRecord = new AufgabeRessourceRecord();
            $myRecord->idtm_aufgabe = $this->Tedidtm_aufgaben->Text;
            $myRecord->idtm_ressource = $this->ttidtm_ressource->Items[$index]->Value;
            $myRecord->auf_res_dauer = $this->ttauf_res_dauer->Text;
            $myRecord->save();
        }
        $this->bindListRessource();
    }

    public function removeRessource($sender,$param){
        AufgabeRessourceRecord::finder()->deleteByPk($param->Item->lstpart_idtm_aufgabe_ressource->Text);
        $this->bindListRessource();
    }

    public function bindListRessource(){
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition = "idtm_aufgabe = :suchtext";
            $criteria->Parameters[':suchtext'] = $this->Tedidtm_aufgaben->Text;
            $criteria->OrdersBy["idtm_ressource"] = 'asc';
            $this->RessourceListe->DataSource=AufgabeRessourceView::finder()->findAll($criteria);
            $this->RessourceListe->dataBind();
    }
}
?>