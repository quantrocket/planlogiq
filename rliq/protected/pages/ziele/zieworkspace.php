<?php

Prado::using('Application.app_code.PFHDSource');

class zieworkspace extends TPage {

    private $rqidtm_ziele = 0;

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {

            if($this->Request['rqidtm_ziele']!='') {
                $this->rqidtm_ziele = $this->Request['rqidtm_ziele'];
            }

            $HRKEYtop = new PFHierarchyPullDown();
            $HRKEYtop->setStructureTable("tm_ziele");
            $HRKEYtop->setRecordClass(ZieleRecord::finder());
            $HRKEYtop->setPKField("idtm_ziele");
            $HRKEYtop->setField("zie_name");
            $HRKEYtop->setSQLCondition("idta_ziele_type=1");
            $HRKEYtop->letsrun();
            $this->idtm_ziele->DataSource=$HRKEYtop->myTree;
            $this->idtm_ziele->dataBind();

            $NEWRECORD = $this->NewRecord;
            $NEWRECORD->setText("neues Element anlegen");
            $NEWRECORD->setToPage("ziele.zieleview");
            $NEWRECORD->setGetVariables('modus=0');

            $NEWSTRECORD = $this->NewStepRecord;
            $NEWSTRECORD->setText("operatives Ziel anlegen");
            $NEWSTRECORD->setToPage("ziele.ttzieleview");
            $NEWSTRECORD->setGetVariables('modus=0');

            $this->bindListOrgListe();
        }

    }

    public function bindListOrgListe($StartNode='0') {
        $HRKEYTest = new PFHDSource();
        $HRKEYTest->setStructureTable("tm_ziele");
        $HRKEYTest->setRecordClass(ZieleRecord::finder());
        $HRKEYTest->setPKField("idtm_ziele");
        $HRKEYTest->setField("zie_name");
        $HRKEYTest->setStartNode($StartNode);
        $HRKEYTest->letsrun();

        $this->OrgListe->DataSource=$HRKEYTest->myTree;
        $this->OrgListe->dataBind();
    }

    public function applyTargetFilter($sender,$param) {
        if($sender->Id=="idtm_ziele"){
            $filter = $this->idtm_ziele->Text;
            $this->bindListOrgListe($filter);
        }
    }

    public function dtgList_viewChildren($sender,$param,$test=0) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idtm_ziele = :suchtext1";
        if ($test==0) {
            $criteria->Parameters[':suchtext1'] = $param->Item->lst_zie_idtm_ziele->Text;
        }else {
            $criteria->Parameters[':suchtext1'] = $sender->lst_zie_parent->Text;
        }
        $this->selected_idtm_ziele->Text = $criteria->Parameters[':suchtext1'];

        $this->ProStepListe->DataSource=TTZieleRecord::finder()->findAll($criteria);
        $this->ProStepListe->dataBind();

        if ($test==0) {
            $item=$param->Item;
            $item->setBackColor("#ababab");
        }
    }

    public function bindListProStepListe($sender,$param) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idtm_ziele = :suchtext1";
        $criteria->Parameters[':suchtext1'] = $this->selected_idtm_ziele->Text;
        $this->ProStepListe->DataSource=TTZieleRecord::finder()->findAll($criteria);
        $this->ProStepListe->dataBind();
    }

    public function dtgList_PageIndexChanged($sender,$param) {
        $this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListOrgListe();
    }

    public function ProStepListeChanged($sender,$param) {
        $this->ProStepListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListProStepListe($sender,$param);
    }

    public function dtgList_deleteCommand($sender,$param) {
        $item=$param->Item;
        $finder = ZieleRecord::finder();
        $finder->deleteAll('idtm_ziele = ?',$item->lst_org_idtm_ziele->Text);
        $this->bindListOrgListe();
    }

    public function searchOrg($sender,$param) {

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="zie_name LIKE :suchtext";
        $criteria->Parameters[':suchtext'] = "%".$this->find_org->Text."%";
        $criteria->setLimit($this->OrgListe->PageSize);
        $criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
        $this->OrgListe->DataKeyField = 'idtm_ziele';

        $this->OrgListe->VirtualItemCount = count(ZieleRecord::finder()->withzieletype()->find($criteria));
        $this->OrgListe->DataSource=ZieleRecord::finder()->withzieletype()->findAll($criteria);
        $this->OrgListe->dataBind();

    }

    public function clearProStep($agent_x) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idtm_ziele = :suchtext";
        $criteria->Parameters[':suchtext'] = $agent_x;
        $criteria->setLimit($this->ProStepListe->PageSize);
        $criteria->setOffset($this->ProStepListe->PageSize * $this->ProStepListe->CurrentPageIndex);
        $this->ProStepListe->DataKeyField = 'idtt_ziele';

        $this->ProStepListe->VirtualItemCount = count(TTZieleRecord::finder()->find($criteria));
        $this->ProStepListe->DataSource=TTZieleRecord::finder()->findAll($criteria);
        $this->ProStepListe->dataBind();

    }

    public function dtgList_editCommand($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"ziele.zieleview",array('modus'=>'1','idtm_ziele'=>$param->Item->lst_zie_idtm_ziele->Text));
        $this->Response->redirect($url);
    }

    public function ziestepList_editCommand($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"ziele.ttzieleview",array('modus'=>'1','idtt_ziele'=>$sender->CommandParameter));
        $this->Response->redirect($url);
    }
}
?>