<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of orgextwindow
 *
 * @author pfrenz
 */
class orgextwindow extends TPage{
    //put your code here

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->IsPostBack && !$this->IsCallBack) {
            $this->initPullDown();
            $this->WINOrgaidta_organisation_type->Text = -1;
            $this->bindListOrgListe();
        }
    }

    public function initPullDown(){
        $sql = "SELECT idta_organisation_type, org_type_name FROM ta_organisation_type ORDER BY org_type_name";
        $data = PFH::convertdbObjectArray(OrganisationTypeRecord::finder()->findAllBySql($sql),array("idta_organisation_type","org_type_name"));
        $data[-1]="alle";
        $this->WINOrgaidta_organisation_type->DataSource=$data;
        $this->WINOrgaidta_organisation_type->dataBind();
    }

    public function dtgList_PageIndexChanged($sender,$param) {
        $this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListOrgListe();
    }

    public function bindListOrgListe() {
        $criteria = new TActiveRecordCriteria();
        if($this->WINOrgaidta_organisation_type->Text>=1){
            $criteria->Condition ="idta_organisation_type = :suchtext1";
            $criteria->Parameters[':suchtext1'] = $this->WINOrgaidta_organisation_type->Text;
        }
        if($this->WINOrgaorg_name->Text!='' && $this->WINOrgaidta_organisation_type->Text>=1){
            $criteria->Condition .= " AND org_name LIKE :suchtext2";
            $criteria->Parameters[':suchtext2'] = str_replace('*', '%', $this->WINOrgaorg_name->Text);
        }
        if($this->WINOrgaorg_name->Text!='' && $this->WINOrgaidta_organisation_type->Text<1){
            $criteria->Condition = "org_name LIKE :suchtext2";
            $criteria->Parameters[':suchtext2'] = str_replace('*', '%', $this->WINOrgaorg_name->Text);
        }
        $criteria->OrdersBy['org_name']='ASC';
        $criteria->OrdersBy['org_fk_internal']='ASC';
       
        $this->OrgListe->DataSource=OrganisationRecord::finder()->findAll($criteria);
        $this->OrgListe->dataBind();
    }

    public function dtgList_sortCommand($sender,$param) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idta_organisation_type = :suchtext1";
        $criteria->Parameters[':suchtext1'] = $this->WINOrgaidta_organisation_type->Text;
        $criteria->OrdersBy[$param->SortExpression]='ASC';
        $this->OrgListe->DataSource=OrganisationRecord::finder()->findAll($criteria);
        $this->OrgListe->dataBind();
    }
}
?>
