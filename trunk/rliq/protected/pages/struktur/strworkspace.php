<?php

Prado::using('Application.app_code.PFHDSource');

class strworkspace extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {

            $HRKEYtop = new PFHierarchyPullDown();
            $HRKEYtop->setStructureTable("tm_struktur");
            $HRKEYtop->setRecordClass(StrukturRecord::finder());
            $HRKEYtop->setPKField("idtm_struktur");
            $HRKEYtop->setField("struktur_name");
            $HRKEYtop->setSQLCondition("idta_struktur_type=1");
            $HRKEYtop->letsrun();
            $this->idtm_struktur->DataSource=$HRKEYtop->myTree;
            $this->idtm_struktur->dataBind();

            $this->bindListOrgListe();
        }
    }

    public function newStructureRecord($sender,$param){
        $page = "struktur.strukturview";
        $parameter['modus']=0;
        //$anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
        $url = $this->getApplication()->getRequest()->constructUrl('page',$page, $parameter);// . $anchor;
        $this->Response->redirect($url);
    }

    public function applyTargetFilter($sender,$param) {
        $filter = $this->idtm_struktur->Text;
        $this->bindListOrgListe($filter);
    }

    public function bindListOrgListe($StartNode='0') {
        if($StartNode == 1){
            $StartNode = 0;
        }
        $HRKEYTest = new PFHDSource();
        $HRKEYTest->setStructureTable("tm_struktur");
        $HRKEYTest->setRecordClass(StrukturRecord::finder());
        $HRKEYTest->setPKField("idtm_struktur");
        $HRKEYTest->setField("struktur_name");
        $HRKEYTest->setStartNode($StartNode);
        $HRKEYTest->setSQLOrder("struktur_name ASC");
        $HRKEYTest->letsrun();
        $this->OrgListe->DataSource=$HRKEYTest->myTree;
        $this->OrgListe->dataBind();

    }

    public function dtgList_PageIndexChanged($sender,$param) {
        $this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListOrgListe($this->idtm_struktur->Text);
    }

    public function dtgList_deleteCommand($sender,$param) {
        $item=$param->Item;
        $finder = StrukturRecord::finder();
        $finder->deleteAll('idtm_struktur = ?',$item->lst_org_idtm_struktur->Text);
        $this->bindListOrgListe($this->idtm_struktur->Text);
    }

    public function searchOrg($sender,$param) {

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="struktur_name LIKE :suchtext";
        $criteria->Parameters[':suchtext'] = "%".$this->find_org->Text."%";
        $criteria->setLimit($this->OrgListe->PageSize);
        $criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
        $this->OrgListe->DataKeyField = 'idtm_struktur';

        $this->OrgListe->VirtualItemCount = count(StrukturRecord::finder()->withstrtype()->find($criteria));
        $this->OrgListe->DataSource=StrukturRecord::finder()->withstrtype()->findAll($criteria);
        $this->OrgListe->dataBind();

    }

    public function dtgList_editCommand($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"struktur.strukturview",array('modus'=>'1','idtm_struktur'=>$param->Item->lst_str_idtm_struktur->Text,'periode'=>'10001'));
        $this->Response->redirect($url);
    }

    public function dtgList_updateCommand($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"struktur.streingabemaskeram",array('modus'=>'0','idtm_struktur'=>$param->Item->lst_str_idtm_struktur->Text,'idta_struktur_type'=>$param->Item->lst_str_type->Text));
        $this->Response->redirect($url);
    }

}
?>