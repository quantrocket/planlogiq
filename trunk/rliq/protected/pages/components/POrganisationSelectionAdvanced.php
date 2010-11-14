<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of POrganisationSelection
 *
 * @author PFRENZ
 */

Prado::using('System.Web.UI.TTemplateControl');
prado::using ('System.Web.UI.ActiveControls.TActiveControlAdapter');

class POrganisationSelectionAdvanced extends TTemplateControl implements IActiveControl, ICallbackEventHandler{

    public function __construct(){
        parent::__construct();
        $this->setAdapter(new TActiveControlAdapter($this));
    }

    /**
     * @return TBaseActiveControl basic active control options.
     */
    public function getActiveControl()
    {
            return $this->getAdapter()->getBaseActiveControl();
    }

    /**
     *
     * Raises the callback event. This method is required by {@link
     * ICallbackEventHandler} interface. It will raise {@link OnMenuItemSelected
     * OnMenuItemSelected} event first and then the {@link onCallback OnCallback} event.
     * This method is mainly used by framework and control developers.
     * @param TCallbackEventParameter the event parameter
     */
    public function raiseCallbackEvent($param)
    {
            $this->raisePostBackEvent(implode(',',$param->getCallbackParameter()));
            $this->onCallback($param);
    }

    /**
     * This method is invoked when a callback is requested. The method raises
     * 'OnCallback' event to fire up the event handlers. If you override this
     * method, be sure to call the parent implementation so that the event
     * handler can be invoked.
     * @param TCallbackEventParameter event parameter to be passed to the event handlers
     */
    public function onCallback($param)
    {
            $this->raiseEvent('OnCallback', $this, $param);
    }

    public function OnInit($param){
        parent::OnInit($param);
    }

    public function getID(){
        $id = $this->getViewState('ID', '');
        if($id != '')
            return $id;
        $id = $this->getViewState('ID',TPropertyValue::ensureString($id));
        return $id;
    }

    public function setID($value){
        $this->setViewState('ID',TPropertyValue::ensureString($value),'');
    }

    public function getText(){
	return $this->getViewState('Text', '');
    }

    public function setText($text){
        $SelectedOrga = OrganisationRecord::finder()->findByPK($text);
        $this->XXXsuggest_idtm_organisation->Text = $SelectedOrga->org_vorname . ' '.$SelectedOrga->org_name;
	return $this->setViewState('Text', $text, '');
    }

    public function clearSuggestBox($sender,$param){
        $this->setText(0);
    }

    // The part for the suggestion stuff
    public function XXXsuggestOrganisation($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $mySQL = "SELECT idtm_organisation,org_name,org_vorname FROM tm_organisation WHERE UPPER(org_name) LIKE '%".strtoupper($token)."%'";
        $sender->DataSource=PFH::convertdbObjectSuggest(TActiveRecord::finder('OrganisationRecord')->findAllBySQL($mySQL),array('idtm_organisation','org_name','org_vorname'));
        $sender->dataBind();
    }

    public function XXXsuggestionSelectedOne($sender,$param) {
        $id=$sender->Suggestions->DataKeys[ $param->selectedIndex ];
        $this->setViewState('Text', $id, '');
        $this->OnCallback($param);
    }

    public function showSuggestBox($sender,$param) {
        $id=$this->mpnlOrganisationContainer->getClientID();
        $this->initPullDown();
        $this->WINOrgaidta_organisation_type->Text = -1;
        $this->bindListOrgListe();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function XXOrgaSelected($sender,$param){
        $id=$this->mpnlOrganisationContainer->getClientID();
        $this->setText($param->Item->xxidtm_organisation->Text);
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.close('$id',true);");
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
