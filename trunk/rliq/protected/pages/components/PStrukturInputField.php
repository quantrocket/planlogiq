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

class PStrukturInputField extends TTemplateControl implements IActiveControl, ICallbackEventHandler{

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

    public function onLoad($param){
        parent::onLoad($param);
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
        $this->XXXInputField->Text = $text;
	return $this->setViewState('Text', $text, '');
    }

    public function showInputBox($sender,$param){
        $this->initPullDowns($sender,$param);
        $this->applyValues($sender,$param);
        $id=$this->mpnlPStrukturInputContainer->getClientID();
        $this->MyPStrukturInputContainer->setDisplay("Dynamic");
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function showCommentBox($sender,$param){
        $this->initComments($sender,$param);
        $id=$this->mpnlKommentarContainer->getClientID();
        $this->MympnlKommentarContainer->setDisplay("Dynamic");
        $this->getPage()->getClientScript()->registerEndScript('XX',"Windows.show('$id',true);");
    }

    public function initComments($sender,$param){
        $tabelle = "tm_custommask";
        $this->KommentarContainerNOP->bindParameterListComments($tabelle,$this->getID());
    }

    public function applyValues($sender,$param){
        $CMFRecord = CustomMaskFieldRecord::finder()->find("cuf_maskenname = ? AND cuf_maskenid = ?",$this->page->cuf_maskenname->Text,$this->Id);
        if(count($CMFRecord)<1){
            $CMFRecord = new CustomMaskFieldRecord();
            $CMFRecord->cuf_maskenname = $this->page->cuf_maskenname->Text;
            $CMFRecord->cuf_maskenid = $this->Id;
            $CMFRecord->save();
        }
        $this->idta_variante->Text = $CMFRecord->idta_variante;
        $this->idta_perioden->Text = $CMFRecord->idta_perioden;
        $this->idta_feldfunktion->Text = $CMFRecord->idta_feldfunktion;
        $this->idtm_stammdaten->Text = $CMFRecord->idtm_stammdaten;
    }

    public function pageAction($sender,$param){
        $CMFRecord = CustomMaskFieldRecord::finder()->find("cuf_maskenname = ? AND cuf_maskenid = ?",$this->page->cuf_maskenname->Text,$this->Id);
        $CMFRecord->idta_variante = $this->idta_variante->Text;
        $CMFRecord->idta_feldfunktion = $this->idta_feldfunktion->Text;
        $CMFRecord->idta_perioden = $this->idta_perioden->Text;
        $CMFRecord->idtm_stammdaten = $this->idtm_stammdaten->Text;
        $CMFRecord->cuf_numberformat = $this->cuf_numberformat->Text;
        $CMFRecord->save();
    }

    public function initPullDowns($sender, $param){
        $this->idta_variante->DataSource = VarianteRecord::finder()->findAll();
        $this->idta_variante->dataBind();

        $this->idta_perioden->DataSource = PeriodenRecord::finder()->findAll();
        $this->idta_perioden->dataBind();

        $this->idta_feldfunktion->DataSource = FeldfunktionRecord::finder()->findAll();
        $this->idta_feldfunktion->dataBind();

        $this->cuf_numberformat->DataSource = array("0"=>"Ganzzahl","1"=>"Prozent","2"=>"Zahl");
        $this->cuf_numberformat->dataBind();

        $criteria = new TActiveRecordCriteria;
        $criteria->OrdersBy['idta_stammdaten_group'] = 'desc';
        $this->idtm_stammdaten->DataSource = StammdatenRecord::finder()->findAll($criteria);
        $this->idtm_stammdaten->dataBind();
    }
    
}
?>
