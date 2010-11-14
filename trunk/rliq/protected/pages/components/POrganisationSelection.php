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

class POrganisationSelection extends TTemplateControl implements IActiveControl, ICallbackEventHandler{

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
    
}
?>
