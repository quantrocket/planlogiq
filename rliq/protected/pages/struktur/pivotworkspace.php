<?php

class pivotworkspace extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }


    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {
            $this->bindListOrgListe();
        }

    }

    public function OpenPivotBerichtContainer($sender,$param) {
        $this->mpnlPivotBericht->Show();
    }

    public function bindListOrgListe() {

        $criteria = new TActiveRecordCriteria();
        $criteria->setLimit($this->OrgListe->PageSize);
        $criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
        $this->OrgListe->DataKeyField = 'idta_pivot_bericht';

        $this->OrgListe->VirtualItemCount = count(PivotBerichtRecord::finder()->findAll());
        $this->OrgListe->DataSource=PivotBerichtRecord::finder()->findAll($criteria);
        $this->OrgListe->dataBind();

    }

    public function dtgList_PageIndexChanged($sender,$param) {
        $this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListOrgListe();
    }

    public function dtgList_deleteCommand($sender,$param) {
        $item=$param->Item;
        $finder = StrukturRecord::finder();
        $finder->deleteAll('idtm_struktur = ?',$item->lst_org_idtm_struktur->Text);
        $this->bindListOrgListe();
    }

    public function searchOrg($sender,$param) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="pivot_bericht_name LIKE :suchtext";
        $criteria->Parameters[':suchtext'] = "%".$this->find_org->Text."%";
        $criteria->setLimit($this->OrgListe->PageSize);
        $criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
        $this->OrgListe->DataKeyField = 'idta_pivot_bericht';

        $this->OrgListe->VirtualItemCount = count(PivotBerichtRecord::finder()->find($criteria));
        $this->OrgListe->DataSource=PivotBerichtRecord::finder()->findAll($criteria);
        $this->OrgListe->dataBind();
    }
    
    public function dtgList_editCommand($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"struktur.pivotview",array('modus'=>'1','idta_pivot_bericht'=>$param->Item->lst_idta_pivot_bericht->Text));
        $this->Response->redirect($url);
    }

    public function dtgList_updateCommand($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"struktur.streingabemaskeram",array('modus'=>'0','idtm_struktur'=>$param->Item->lst_str_idtm_struktur->Text,'idta_struktur_type'=>$param->Item->lst_str_type->Text));
        $this->Response->redirect($url);
    }

}
?>