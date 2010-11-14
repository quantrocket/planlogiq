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
class prtsearchwindow extends TPage{
    //put your code here

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->IsPostBack && !$this->IsCallBack) {
            //falls etwas zum start benoetigt wuerde
        }
    }

    public function initPullDown(){
        
    }

    public function dtgList_PageIndexChanged($sender,$param) {
        $this->PrtListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListPrtListe();
    }

    public function bindListPrtListe() {
        $criteria = new TActiveRecordCriteria();
        if($this->WINprtdet_topic->Text!=""){
            $criteria->Condition ="prtdet_topic LIKE :suchtext1";
            $criteria->Parameters[':suchtext1'] = '%'.$this->WINprtdet_topic->Text.'%';
            var_dump($criteria);
            $this->PrtListe->DataSource=ProtokollDetailAufgabeView::finder()->findAll($criteria);
            $this->PrtListe->dataBind();
        }
    }

    public function dtgList_sortCommand($sender,$param) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idta_organisation_type = :suchtext1";
        $criteria->Parameters[':suchtext1'] = $this->WINOrgaidta_organisation_type->Text;
        $criteria->OrdersBy[$param->SortExpression]='ASC';
        $this->PrtListe->DataSource=OrganisationRecord::finder()->findAll($criteria);
        $this->PrtListe->dataBind();
    }
}
?>
