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

class PActivitySelection extends TTemplateControl implements IActiveControl, ICallbackEventHandler{

    public function onPreRender($writer) {
        $this->registerClientScripts();
    }

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
        $MyActivity = ActivityRecord::finder()->findByPK($text);
        $this->XXXsuggest_idtm_activity->Text = $MyActivity->act_name;
	return $this->setViewState('Text', $text, '');
    }

    public function getStartPunkt(){
	return $this->getViewState('StartPunkt', '');
    }

    public function setStartPunkt($text){
	return $this->setViewState('StartPunkt', $text, '');
    }

    public function getStartKnoten(){
	return $this->getViewState('StartKnoten', '');
    }

    public function setStartKnoten($text){
	return $this->setViewState('StartKnoten', $text, '');
    }

    public function viewTreeBox($sender,$param){
        if($this->myTreeBox->Display=="None"){
            $this->myTreeBox->setDisplay("Dynamic");
        }else{
            $this->myTreeBox->setDisplay("None");
        }
    }

    // The part for the suggestion stuff
    public function XXXsuggestOrganisation($sender,$param) {
        $ADDCondition='';
        if(!$this->getStartPunkt()=='' AND !$this->getStartPunkt()==0){
            $ADDCondition = " AND idta_activity_type = ".$this->getStartPunkt();
        }
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $mySQL = "SELECT idtm_activity,act_name FROM tm_activity WHERE UPPER(act_name) LIKE '".str_replace('*', '%', strtoupper($token))."%'".$ADDCondition;
        $sender->DataSource=PFH::convertdbObjectSuggest(TActiveRecord::finder('ActivityRecord')->findAllBySQL($mySQL),array('idtm_activity','act_name'));
        $sender->dataBind();
    }

    public function XXXsuggestionSelectedOne($sender,$param) {
        $id=$sender->Suggestions->DataKeys[ $param->selectedIndex ];
        $this->setViewState('Text', $id, '');
        $this->OnCallback($param);
    }

    public function callback_MyACTCallback($sender,$param){
        if($this->page->isCallback){
            $theObjectContainingParameters = $param->CallbackParameter;
            $this->setText($theObjectContainingParameters->idtm_activity);
        }
    }

    protected function registerClientScripts() {
        $id=$this->getClientID();

        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxtree','../rliq/protected/3rdParty/dhtmlxTree/codebase/dhtmlxtree_pro.css');
        //$this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxmenu_standard','../../rliq/protected/3rdParty/dhtmlxMenu/codebase/skins/dhtmlxmenu_standard.css');
//        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxmenu_clear_silver','../../rliq/protected/3rdParty/dhtmlxMenu/codebase/skins/dhtmlxmenu_dhx_skyblue.css');
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree',$this->publishAsset("../../3rdParty/dhtmlxTree/codebase/dhtmlxtree_pro.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_srnd',$this->publishAsset("../../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_srnd.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_xw',$this->publishAsset("../../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_xw.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_kn',$this->publishAsset("../../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_kn.js"));
        //$this->getPage()->getClientScript()->registerScriptFile('dhtmlxcommonmenu',$this->publishAsset("../../3rdParty/dhtmlxMenu/codebase/dhtmlxcommon.js"));
//        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxmenu',$this->publishAsset("../../3rdParty/dhtmlxMenu/codebase/dhtmlxmenu.js"));
//        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxmenu_ext',$this->publishAsset("../../3rdParty/dhtmlxMenu/codebase/ext/dhtmlxmenu_ext.js"));
    }

    public function renderControl($writer) {
        $this->renderChildren($writer);

        if(!$this->getEnabled())
            return;

        $PlaceHolder = $this->myTreeBox->getClientID();
        $actionScript = $this->MyACTCallback->ActiveControl->Javascript;
        
        $StrukturTreeConnector = $this->parent->getRequest()->constructUrl('page','activity.TreeActivityConnector');
        $TreeFilter='';
        if(!$this->getStartPunkt()=='' AND !$this->getStartPunkt()==0){
            $TreeFilter .= "&idta_activity_type=".$this->getStartPunkt();
        }
        if(!$this->getStartKnoten()=='' AND !$this->getStartKnoten()==0){
            $TreeFilter .= "&idtm_activity_start=".$this->getStartKnoten();
        }

        $findItem = "";
        
        $js= <<< EOD

<script type="text/javascript" charset="UTF-8">

        var treeACT=new dhtmlXTreeObject('$PlaceHolder',"100%","100%",0);
        treeACT.setImagePath('/rliq/themes/basic/imgs/');
        treeACT.setItemStyle('position:static');
        treeACT.enableKeyboardNavigation(true);
        treeACT.enableKeySearch(true);
        treeACT.enableDragAndDrop(false);
        treeACT.enableCheckBoxes(false);
        treeACT.setOnClickHandler(onACTNodeSelect);
        treeACT.setXMLAutoLoading("$StrukturTreeConnector$TreeFilter")
        treeACT.loadXML('$StrukturTreeConnector$TreeFilter',function(){
            treeACT.loadOpenStates()
        });

    function onACTNodeSelect(nodeId){
        treeACT.saveOpenStates();
        var id = nodeId;
        var request = $actionScript;
        var param = {'idtm_activity' : id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }

</script>
EOD;

        $writer->write($js);
    //$this->processChildren($writer);
    }

}
?>
