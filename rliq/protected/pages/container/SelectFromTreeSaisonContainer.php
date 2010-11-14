<?php

class SelectFromTreeSaisonContainer extends TTemplateControl {

    /*
     * To implement the container, use the following tags
     *  <com:Application.pages.container.AufgabenContainerOrganisation ID="AufgabenContainerOrganisation"/>
     *  <com:TActiveTextBox id="SFTauf_tabelle" Text="tm_struktur" visible="false" />
     *  <com:TActiveTextBox id="SFTstart_id" Text="0" visible="false" />
     *
     */


    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            $this->initParameters();
            $this->bindListChildren($this->SFTstart_id->Text);
        }
    }

    public function initParameters(){
        $this->SFTauf_tabelle->Text = $this->page->SaisonContainer->SFTauf_tabelle->Text;
        $this->SFTstart_id->Text = $this->page->SaisonContainer->SFTstart_id->Text;
    }

    public function bindListChildren($id){
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "parent_idtm_struktur = :idtm_struktur";
        $criteria->Parameters['idtm_struktur']=$id;
        $criteria->OrdersBy['idta_struktur_type'] = 'ASC';
        $criteria->OrdersBy['struktur_name'] = 'ASC';
        $Records = StrukturRecord::finder()->findAll($criteria);
        $this->SelectFromTreeGrid->DataSource=$Records;
        $this->SelectFromTreeGrid->dataBind();
    }

    public function ChangeLevel($sender,$param){
        $this->bindListChildren($sender->CommandParameter);
    }

    public function ChooseLevel($sender,$param){
        $this->page->SaisonContainer->{$this->SFTauf_tabelle->Text}->Text =$sender->CommandParameter;
    }

    public function check_forChildren($Id) {
        $Result = count(StrukturRecord::finder()->findAllByparent_idtm_struktur($Id));
        if($Result>=1) {
            return "true";
        }else {
            return "false";
        }
    }

}

?>