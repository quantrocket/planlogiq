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

class PTimeSelection extends TTemplateControl implements IActiveControl, ICallbackEventHandler{

    public function onPreRender($writer) {
       //$this->registerClientScripts();
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
        $this->setText($this->XXXTimeInput->Text);
	return $this->getViewState('Text', '');
    }

    public function setText($text){
        $my_time = explode(':',$text);
        if(count($my_time)>1){
            $my_time_text = $my_time[0].':'.$my_time[1];
        }else{
            $my_time_text = '00:00';
        }
	if($this->page->isCallback || $this->page->isPostBack){
            $this->XXXTimeInput->Text = $my_time_text;
        }
        return $this->setViewState('Text', $my_time_text, '');
    }

    public function callback_MyACTCallback($sender,$param){
        if($this->page->isCallback){
            $theObjectContainingParameters = $param->CallbackParameter;
            $this->setText($theObjectContainingParameters->idtm_activity);
        }
    }

    protected function registerClientScripts() {
        $id=$this->getClientID();
        $this->getPage()->getClientScript()->registerStyleSheetFile('datepicker','../rliq/protected/pages/components/css/datepicker.css');
        $this->getPage()->getClientScript()->registerScriptFile('datepicker',$this->publishAsset("./js/datepicker.js"));
        $this->getPage()->getClientScript()->registerScriptFile('prototype-date-extensions',$this->publishAsset("./js/prototype-date-extensions.js"));
    }

//    public function renderControl($writer) {

//        $this->renderChildren($writer);
//
//        if(!$this->getEnabled())
//            return;
//
//        $PlaceHolder = $this->XXXTimeInput->getClientID();
//        $actionScript = $this->MyACTCallback->ActiveControl->Javascript;
//        
//        $js= <<< EOD
//
//<script type="text/javascript" charset="UTF-8">
//
//        var picker = new Control.DatePicker("$PlaceHolder", {icon: '/rliq/themes/basic/gfx/16x16/actions/clock.png', datePicker: false, timePicker: true, timePickerAdjacent: true});
//
//
//    function ttdatepicker(nodeId){
//        treeACT.saveOpenStates();
//        var id = nodeId;
//        var request = $actionScript;
//        var param = {'idtm_activity' : id};
//        request.setCallbackParameter(param);
//        request.dispatch();
//        return true;
//    }
//
//</script>
//EOD;
//
//        $writer->write($js);
    //$this->processChildren($writer);
//    }

}
?>
