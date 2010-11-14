<?php

class RechnungContainer extends TTemplateControl {

    /*
     * To implement the container, use the following tags
     *  <com:Application.pages.container.AufgabenContainerOrganisation ID="AufgabenContainerOrganisation"/>
     *  <com:TActiveTextBox id="Teddeb_tabelle" Text="tm_activity" visible="false" />
     *  <com:TActiveTextBox id="Teddeb_id" Text="0" visible="false" />
     *
     */

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack && !$this->page->IsCallback) {
            $this->initPullDowns();
        }
    }

    public function initParameters(){
        $this->Teddeb_tabelle->Text = $this->page->Teddeb_tabelle->Text;
        $this->Teddeb_id->Text = $this->page->Teddeb_id->Text;
    }

    public function showCommentDialog($sender,$param) {
        $this->CommentDialog->setDisplay("Dynamic");
        $this->initPullDowns();
        $sender->Visible = false;
    }

    public function hideCommentDialog($sender,$param) {
        $this->CommentDialog->setDisplay("None");
        $this->KommentarSichtButton->Visible = true;
    }

    public function ccActions($sender,$param){
        if($sender->id === "newInvoice"){

        }
    }

    public function initPullDowns(){
//        $this->deb_inout->DataSource = $this->deb_inout_values;
//        $this->deb_inout->dataBind();
    }

    public function bindRechnungListe(){
        $SQL = "SELECT * FROM tm_rechnung";// WHERE idtm_organisation_to = '".$this->Teddeb_tabelle->Text."' AND deb_deleted = 0 AND deb_id = ".$this->Teddeb_id->Text ." ORDER BY deb_order";
        
        $this->CCRechnungListe->DataSource=RechnungRecord::finder()->findAllBySQL($SQL);
        $this->CCRechnungListe->dataBind();
    }


    public function recalcSumme($sender,$param){
        $item=$sender->parent;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
            {
                $DetailRecord = DetailBelegRecord::finder()->findByPK($item->idtm_detail_beleg->Text);
                $DetailRecord->deb_inout = $item->deb_inout->Checked?1:0;
                $DetailRecord->deb_order = $item->deb_order->Text;
                $DetailRecord->deb_descr = $item->deb_descr->Text;
                $DetailRecord->deb_nummer = $item->deb_nummer->Text;
                $DetailRecord->deb_konto = $item->deb_konto->Text;
                $DetailRecord->deb_date = date('Y-m-d',$item->deb_date->TimeStamp);

                $DetailRecord->deb_menge = floatval($item->deb_menge->Text);
                $DetailRecord->deb_preis = floatval($item->deb_preis->Text);
                $DetailRecord->deb_tax = floatval($item->deb_tax->Text);
                $DetailRecord->deb_summe = ($DetailRecord->deb_menge * $DetailRecord->deb_preis * $DetailRecord->deb_tax / 100)+$DetailRecord->deb_menge * $DetailRecord->deb_preis;
                $DetailRecord->save();
                $item->deb_summe->Text = number_format($DetailRecord->deb_summe,2,'.',',');
            }
         $this->calcInvoiceSum();
    }

    public function editItem($sender,$param){
        $this->loadPartei($sender,$param);

        $sender->parent->DetailBelegContainer->initParameters('tm_rechnung',$param->Item->idtm_rechnung->Text);
        $sender->parent->DetailBelegContainer->bindDetailBelegListe($sender,$param);
    }

    public function loadPartei($sender,$param){
        $MeinRecord=RechnungRecord::finder()->findByPK($param->Item->idtm_rechnung->Text);
        $OrganisationFrom = OrganisationRecord::finder()->findByPK($MeinRecord->idtm_organisation_from);
        $OrganisationTo = OrganisationRecord::finder()->findByPK($MeinRecord->idtm_organisation_to);

        $this->idtm_organisation_from->Text=$MeinRecord->idtm_organisation_from;
        $this->idtm_organisation_to->Text=$MeinRecord->idtm_organisation_to;

        $this->TO_org_uid->Text = $OrganisationTo->org_uid;
        $this->FROM_org_uid->Text = $OrganisationFrom->org_uid;
    }

    public function CDEBSaveButtonClicked($sender,$param) {
        $SaveRecord = new DetailBelegRecord();
        $SaveRecord->deb_tabelle = $this->Teddeb_tabelle->Text;
        $SaveRecord->deb_id = $this->Teddeb_id->Text;
        $SaveRecord->deb_deleted = 0;
        $SaveRecord->deb_date = date("Y-m-d");
        $SaveRecord->save();
        
        $this->bindDetailBelegListe();
    }

    public function CCOMNewButtonClicked($sender,$param) {
        $this->com_content->Text = "";
    }


     public function DetailBelegListeEdit($sender,$param){
        $this->CCDetailBelegListe->EditItemIndex=$param->Item->ItemIndex;
        $this->bindDetailBelegListe();
    }

    public function DetailBelegListe_pageIndexChanged($sender,$param){
        $this->CCDetailBelegListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindDetailBelegListe();
    }

    public function DetailBelegListeCancel($sender,$param)
    {
        $this->CCDetailBelegListe->EditItemIndex=-1;
        $this->bindDetailBelegListe();
    }

    public function DetailBelegListeSave($sender,$param){
        $item=$param->Item;

        $SaveRecord = DetailBelegRecord::finder()->findByidtm_detail_beleg($item->lst_idtm_detail_beleg->TextBox->Text);

//        $SaveRecord->deb_tabelle = $item->lst_deb_tabelle->Text;
//        $SaveRecord->deb_id = $item->lst_deb_id->Text;

        $SaveRecord->deb_order = $item->lst_deb_order->TextBox->Text;
        $SaveRecord->deb_nummer = $item->lst_deb_nummer->TextBox->Text;
        $SaveRecord->deb_descr = $item->lst_deb_descr->TextBox->Text;

        $SaveRecord->deb_inout = $item->lst_deb_inout->ATB_lst_deb_inout->Checked?1:0;
        $SaveRecord->deb_konto = $item->lst_deb_konto->TextBox->Text;

        $SaveRecord->deb_menge = $item->lst_deb_menge->TextBox->Text;
        $SaveRecord->deb_preis = $item->lst_deb_preis->TextBox->Text;
        $SaveRecord->deb_tax = $item->lst_deb_tax->TextBox->Text;

        $SaveRecord->deb_date = $item->lst_deb_date->TextBox->Text;

        $SaveRecord->deb_deleted = 0;

        $SaveRecord->save();

        $this->CCDetailBelegListe->EditItemIndex=-1;
        $this->bindDetailBelegListe();
    }

}

?>