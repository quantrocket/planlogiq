<?php
/**
 * AccountPortlet class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: AccountPortlet.php 1398 2006-09-08 19:31:03Z xue $
 */

Prado::using('Application.portlets.portlet');
Prado::using('Application.app_code.PFDBTools');

/**
 * AccountPortlet class
 *
 * @author Philipp Frenzel <philipp.frenzel@frenzel.net>
 * @link http://www.frenzel.net/
 * @copyright Copyright &copy; 2009 Frenzel GmbH
 * @license http://www.frenzel.net/license/
 */
class strukturportlet extends portlet {

    private $DIMKEY="";

    public function onLoad($param) {

        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            $this->DWH_idta_variante->DataSource=PFH::build_SQLPullDown(VarianteRecord::finder(),"ta_variante",array("idta_variante","var_descr"));
            $this->DWH_idta_variante->dataBind();

            $this->DWH_idta_stammdatensicht->DataSource=PFH::build_SQLPullDown(StammdatensichtRecord::finder(),"ta_stammdatensicht",array("idta_stammdatensicht","sts_name"));
            $this->DWH_idta_stammdatensicht->dataBind();


            $this->DWH_idta_struktur_bericht->DataSource=PFH::build_SQLPullDownAdvanced(StrukturBerichtRecord::finder(),"ta_struktur_bericht",array("idta_struktur_bericht","sb_order","pivot_struktur_name"),'','sb_order');
            $this->DWH_idta_struktur_bericht->dataBind();

            //first we need to check the szenario, because we recieve the startperiod from the variante...
            if($this->Request['idta_variante']!=""){
                $this->DWH_idta_variante->Text=$this->Request['idta_variante'];
            }else{
                $this->DWH_idta_variante->Text=VarianteRecord::finder()->findByvar_default(1)->idta_variante;
                $this->DWH_idta_variante->Text==""?$this->DWH_idta_variante->Text=1:'';
            }

            if($this->Request['idta_stammdatensicht']!=""){
                $this->DWH_idta_stammdatensicht->Text=$this->Request['idta_stammdatensicht'];
            }else{
                $this->DWH_idta_stammdatensicht->Text==""?$this->DWH_idta_stammdatensicht->Text=1:'';
            }
        
            if($this->Request['periode']!=""){
                $this->DWH_idta_perioden->Text=$this->Request['periode'];
            }else{
                $sec_per = "10001";
                $this->DWH_idta_perioden->Text = VarianteRecord::finder()->findByidta_variante($this->DWH_idta_variante->Text)->idta_perioden;
                $this->DWH_idta_perioden->Text==''?$this->DWH_idta_perioden->Text=$sec_per:'';
            }

            $this->Request['per_single']!=""?$this->DWH_per_single->setChecked($this->Request['per_single']):$this->DWH_per_single->setChecked(0);

            if($this->Request['idta_struktur_bericht']!='') {
                $this->DWH_idta_struktur_bericht->Text = $this->Request['idta_struktur_bericht'];
            }else{
                $this->DWH_idta_struktur_bericht->Text = StrukturBerichtRecord::finder()->findBysb_startbericht(1)->idta_struktur_bericht;
            }

            if($this->checkPeriodeArea($this->DWH_idta_struktur_bericht->Text)){
                $this->DWH_PERIODAREA->setVisible($this->checkPeriodeArea($this->DWH_idta_struktur_bericht->Text));
            }else{
                $this->DWH_PERIODAREA->setVisible($this->checkPeriodeArea($this->DWH_idta_struktur_bericht->Text));
                //$this->getPage()->getClientScript()->registerEndScript('XXX','dhxinTab.hideTab("tab2",true);');
            }

            $this->Request['idta_struktur_type']!=""?$this->Request['idta_struktur_type']=1:'';           

            if($this->Request['idtm_struktur']!='') {
                $this->DWH_idtm_struktur->Text = $this->Request['idtm_struktur'];
            }else{
                $this->DWH_idtm_struktur->Text = $this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_struktur");
                $this->DWH_idtm_struktur->Text==''?$this->DWH_idtm_struktur->Text=1:'';
            }
        }

    }

    private function checkPeriodeArea($idta_struktur_bericht){
        $checker = StrukturBerichtSpaltenRecord::finder()->findAllByidta_struktur_bericht($idta_struktur_bericht);
        if(count($checker)>=1){
            foreach($checker as $SingleRecord){
                if($SingleRecord->sbs_perioden_fix==0){
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function PeriodenSingle($sender,$param) {
        $page = $this->Request['page'];
        $parameter['modus']=0;
        $parameter['periode']=$this->DWH_idta_perioden->Text;
        $parameter['per_single']=$this->DWH_per_single->Checked?1:0;
        $parameter['idta_variante']=$this->DWH_idta_variante->Text;
        $parameter['idta_stammdatensicht']=$this->DWH_idta_stammdatensicht->Text;
        $parameter['idta_struktur_bericht']=$this->DWH_idta_struktur_bericht->Text;
        $parameter['idtm_struktur']=$this->DWH_idtm_struktur->Text;
        $parameter['idta_struktur_type']=$this->Request['idta_struktur_type'];
        $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
        $url = $this->getApplication()->getRequest()->constructUrl('page', $page, $parameter) . $anchor;
        $this->Response->redirect($url);
    }

    public function StrukturBerichtChanged($sender,$param) {
        $indices = $sender->SelectedIndices;
        foreach($indices as $index) {
            $item=$sender->Items[$index];
            $result=$item->Value;
        }
        $page = $this->Request['page'];
        $parameter['modus']=0;
        $parameter['periode']=$this->DWH_idta_perioden->Text;
        $parameter['per_single']=$this->DWH_per_single->Checked?1:0;
        $parameter['idta_variante']=$this->DWH_idta_variante->Text;
        $parameter['idta_stammdatensicht']=$this->DWH_idta_stammdatensicht->Text;
        $parameter['idta_struktur_bericht']=$result;
        $parameter['idtm_struktur']=$this->DWH_idtm_struktur->Text;
        $parameter['idta_struktur_type']=$this->Request['idta_struktur_type'];
        $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
        $url = $this->getApplication()->getRequest()->constructUrl('page', $page, $parameter) . $anchor;
        $this->Response->redirect($url);
    }

    public function ParameterChanged($sender,$param) {
        $indices = $sender->SelectedIndices;
        foreach($indices as $index) {
            $item=$sender->Items[$index];
            $result=$item->Value;
        }
        $page = $this->Request['page'];
        $parameter['modus']=0;
        $parameter['idta_variante']=$result;
        $parameter['idta_stammdatensicht']=$this->DWH_idta_stammdatensicht->Text;
        $parameter['idta_struktur_bericht']=$this->DWH_idta_struktur_bericht->Text;
        $parameter['periode']='';  //$this->DWH_idta_perioden->Text; I set this to empty, so the value for the period will be picked from the variant information
        $parameter['per_single']=$this->DWH_per_single->Checked?1:0;
        $parameter['idtm_struktur']=$this->DWH_idtm_struktur->Text;
        $parameter['idta_struktur_type']=$this->Request['idta_struktur_type'];
        $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
        $url = $this->getApplication()->getRequest()->constructUrl('page', $page, $parameter) . $anchor;
        $this->Response->redirect($url);
    }

    public function OpenVariantenContainer($sender,$param) {
        $id=$this->mpnlVariantenContainer->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function generateStructure($sender,$param) {

        $command=$param->getCommand();

        if($command=='Structure'){
            $Test = new PFStrukturGenerator(0,$this->DWH_idta_stammdatensicht->Text);
            unset($Test);
        }

        if($command=='Nested'){
            $Test = new PFStrukturGenerator(1,$this->DWH_idta_stammdatensicht->Text);
            unset($Test);
        }

        if($command=='CleanStructure'){
            $PFDBTools = new PFDBTools();
            $PFDBTools->cleanStrukturStruktur();
            unset($PFDBTools);
        }

        if($command=='InitLinks'){
            $PFDBTools = new PFDBTools();
            $PFDBTools->initStrukturLink();
            unset($PFDBTools);
        }

        if($command=='InitValues'){
            $PFDBTools = new PFDBTools();
            $idtm_struktur = $this->DWH_idtm_struktur->Text;
            $idta_variante = $this->DWH_idta_variante->Text;
            $idta_perioden = $this->DWH_idta_perioden->Text;
            $PFDBTools->InitDBValues($idtm_struktur, $idta_variante, $idta_perioden);
            unset($PFDBTools);
        }
    }

    public function OpenStammdatenGroupContainer($sender,$param) {
        $this->mpnlTestSG->Show();
    }

    public function OpenStammdatenContainer($sender,$param) {
        $this->mpnlTestS->Show();
    }

    public function getAnchor() {
        return $this->getViewState("Anchor", null);
    }

    public function callback_MyCallback($sender,$param){
        if($this->page->isCallback && $this->page->isPostBack){
            $theObjectContainingParameters = $param->CallbackParameter;
            $Record = StrukturRecord::finder()->findBy_idtm_struktur($theObjectContainingParameters->idtm_struktur);
            if($this->check_forChildren($Record)) {
                $page='reports.StrukturBerichtViewer';
            }else {
                $page='struktur.streingabemaske';
            }
            $parameter['periode']=$this->DWH_idta_perioden->Text;
            $parameter['idta_stammdatensicht']=$this->DWH_idta_stammdatensicht->Text;
            $parameter['idta_variante']=$this->DWH_idta_variante->Text;
            $parameter['idta_struktur_bericht']=$this->DWH_idta_struktur_bericht->Text;
            $parameter['per_single']=$this->DWH_per_single->Checked?1:0;
            $parameter['idtm_struktur']=$Record->idtm_struktur;
            $parameter['idta_struktur_type']=$Record->idta_struktur_type;
            $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
            $url = $this->getApplication()->getRequest()->constructUrl('page', $page, $parameter) . $anchor;
            $Record->idta_struktur_type!=''?$this->Response->redirect($url):'';
        }
    }

    public function callback_MyCallbackTime($sender,$param){
        if($this->page->isCallback && $this->page->isPostBack){
            $theObjectContainingParameters = $param->CallbackParameter;
            $Record = PeriodenRecord::finder()->findByPK($theObjectContainingParameters->idta_perioden);
            $StrRecord = StrukturRecord::finder()->findBy_idtm_struktur($this->DWH_idtm_struktur->Text);
            if($this->check_forChildren($StrRecord)) {
                $page='reports.StrukturBerichtViewer';
            }else {
                $page='struktur.streingabemaske';
            }
            $parameter['periode']=$Record->per_intern;
            $parameter['idta_stammdatensicht']=$this->DWH_idta_stammdatensicht->Text;
            $parameter['idta_variante']=$this->DWH_idta_variante->Text;
            $parameter['idta_struktur_bericht']=$this->DWH_idta_struktur_bericht->Text;
            $parameter['per_single']=$this->DWH_per_single->Checked?1:0;
            $parameter['idtm_struktur']=$this->DWH_idtm_struktur->Text;
            $parameter['idta_struktur_type']=$StrRecord->idta_struktur_type;
            $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
            $url = $this->getApplication()->getRequest()->constructUrl('page', $page, $parameter) . $anchor;
            $StrRecord->idta_struktur_type!=''?$this->Response->redirect($url):'';
        }
    }

    public function callback_MyCallbackReport($sender,$param){
        if($this->page->isCallback && $this->page->isPostBack){
            $theObjectContainingParameters = $param->CallbackParameter;
            $StrRecord = StrukturRecord::finder()->findBy_idtm_struktur($this->DWH_idtm_struktur->Text);
            if($this->check_forChildren($StrRecord)) {
                $page='reports.StrukturBerichtViewer';
            }else {
                $page='struktur.streingabemaske';
            }
            $parameter['idta_stammdatensicht']=$this->DWH_idta_stammdatensicht->Text;
            $parameter['periode']=$this->DWH_idta_perioden->Text;
            $parameter['idta_variante']=$this->DWH_idta_variante->Text;
            $parameter['idta_struktur_bericht']=$theObjectContainingParameters->idta_struktur_bericht;
            $parameter['per_single']=$this->DWH_per_single->Checked?1:0;
            $parameter['idtm_struktur']=$this->DWH_idtm_struktur->Text;
            $parameter['idta_struktur_type']=$StrRecord->idta_struktur_type;
            $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
            $url = $this->getApplication()->getRequest()->constructUrl('page', $page, $parameter) . $anchor;
            $StrRecord->idta_struktur_type!=''?$this->Response->redirect($url):'';
        }
    }

    public function callback_MyCallbackDouble($sender,$param) {
        if($this->page->isCallback) {
            $theObjectContainingParameters = $param->CallbackParameter;
            $page='struktur.strukturview';
            $parameter['modus']=1;
            $parameter['idtm_struktur']=$theObjectContainingParameters->idtm_struktur;
            $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
            $url = $this->getApplication()->getRequest()->constructUrl('page', $page, $parameter) . $anchor;
            $this->Response->redirect($url);
        }
    }

    public function callback_MyCallbackDrop($sender,$param) {
        if($this->page->isCallback) {
            $theObjectContainingParameters = $param->CallbackParameter;
            $Record = StrukturRecord::finder()->findBy_idtm_struktur($theObjectContainingParameters->idtm_struktur);
            if($this->check_forChildren($Record)) {
                $page='reports.StrukturBerichtViewer';
            }else {
                $page='struktur.streingabemaske';
            }
            $Record->parent_idtm_struktur = $theObjectContainingParameters->parent_idtm_struktur;
            $parameter['idta_stammdatensicht']=$this->DWH_idta_stammdatensicht->Text;
            $parameter['periode']=$this->DWH_idta_perioden->Text;
            $parameter['idta_variante']=$this->DWH_idta_variante->Text;
            $parameter['idta_struktur_bericht']=$this->DWH_idta_struktur_bericht->Text;
            $parameter['modus']=0;
            $parameter['per_single']=$this->DWH_per_single->Checked?1:0;
            $parameter['idtm_struktur']=$theObjectContainingParameters->parent_idtm_struktur;
            $parameter['idta_struktur_type']=$Record->idta_struktur_type;
            $Record->save();
            $newDimensions = $this->build_DIMKEY($theObjectContainingParameters->idtm_struktur);
            $this->update_DIMKEY($theObjectContainingParameters->idtm_struktur,$newDimensions);
        }
    }

    private function update_DIMKEY($strukturID,$dimKey) {
        $Results = WerteRecord::finder()->findAllByidtm_struktur($strukturID);
        foreach($Results AS $Row) {
            $Record = WerteRecord::finder()->findByidtt_werte($Row->idtt_werte);
            $Record->w_dimkey = $dimKey;
            $Record->save();
        }
    }

    private function build_DIMKEY($strukturID) {
        $Result = StrukturRecord::finder()->findByPK($strukturID);
        $Result->idtm_stammdaten!=''?$temp = "xx".$Result->idtm_stammdaten."xx":$temp='';
        $this->DIMKEY.=$temp;
        if($this->check_forParent($Result)) {
            $this->getParentID($Result);
        }
        return $this->DIMKEY;
    }

    private function getParentID($Node) {
        $Result = StrukturRecord::finder()->findByPK($Node->parent_idtm_struktur);
        if(is_object($Result)){
            $Result->idtm_stammdaten!=''?$temp = "xx".$Result->idtm_stammdaten."xx":$temp='';
            $this->DIMKEY.=$temp;
            if($this->check_forParent($Result)) {
                $this->getParentID($Result);
            }
        }
    }

    public function check_forParent($Node) {
        $SQL = "SELECT * FROM tm_struktur WHERE idtm_struktur = '".$Node->idtm_struktur."'";
        $Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
    }

    public function check_forChildren($Node) {
        $SQL = "SELECT * FROM tm_struktur WHERE parent_idtm_struktur = '".$Node->idtm_struktur."'";
        $Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
    }

}

?>