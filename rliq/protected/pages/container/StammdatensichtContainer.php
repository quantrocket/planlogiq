<?php

class StammdatensichtContainer extends TTemplateControl {


    public function onLoad($param) {
        $sender = "";
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            $this->bindstammdatensichtListe($sender,$param);
        }
    }

    public function bindstammdatensichtListe($sender, $param){
        $this->StammdatensichtListe->DataSource = StammdatensichtRecord::finder()->findAll();
        $this->StammdatensichtListe->dataBind();
    }

    public function pageAction($sender, $param) {
        if($sender->Id=="StammdatensichtListe"){
            $item=$param->Item;
            if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
                {
                    $StammdatensichtRecord = StammdatensichtRecord::finder()->findByPK($item->lst_idta_stammdatensicht->Text);
                    $this->RCedsts_name->Text = $StammdatensichtRecord->sts_name;
                    $this->RCedsts_aktiv->Checked = $StammdatensichtRecord->sts_aktiv;
                    $this->RCedsts_reporting->Checked = $StammdatensichtRecord->sts_reporting;
                    $this->RCedidta_stammdatensicht->Text = $StammdatensichtRecord->idta_stammdatensicht;
                    $this->RCedstammdatensicht_edit_status->Text = 1;
                }
        }
        if($param->CommandName==='new'){
            $this->RCedsts_name->Text = "";
            $this->RCedsts_aktiv->Checked = 1;
            $this->RCedsts_reporting->Checked = 0;
            $this->RCedidta_stammdatensicht->Text = 0;
            $this->RCedstammdatensicht_edit_status->Text = 0;
        }
        if($param->CommandName==='save'){
            if($this->RCedstammdatensicht_edit_status->Text === '0'){
                $StammsichtRecord = new StammdatensichtRecord();
            }else{
                $StammsichtRecord = StammdatensichtRecord::finder()->findByPK($this->RCedidta_stammdatensicht->Text);
            }
            $StammsichtRecord->sts_name = $this->RCedsts_name->Text;
            $StammsichtRecord->sts_aktiv = $this->RCedsts_aktiv->Checked;
            $StammsichtRecord->sts_reporting = $this->RCedsts_reporting->Checked;
            $StammsichtRecord->save();
            $this->RCedidta_stammdatensicht->Text = $StammsichtRecord->idta_stammdatensicht;
            $this->RCedstammdatensicht_edit_status->Text = 1;
            $this->bindstammdatensichtListe($sender, $param);
        }
        //$this->parent->parent->mpnlStammdatensicht->Hide();
    }
    
}

?>