<?php

Prado::using('Application.app_code.PFCalculator');
Prado::using('Application.app_code.PFBackCalculator');
Prado::using('Application.app_code.PFPeriodPullDown');

class streingabemaske extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    private $STRprimarykey = "idtm_struktur";
    private $STRcurrentID = '';
    private $piq_idta_struktur_type = '0';
    private $FunktionsFelder = array();
    private $dynamicControlList; //the variable the grid is stored into
    private $ControlFieldList; //the variable the grid is stored into


    //hier speicher ich den Tatsächlichen Feldtyp, damit ich nicht dauernd eine neue Verbindung auf die DB setzen muss
    private $FeldfunktionType = array();

    private $TableHeaderRL;
    private $Perioden = array();
    private $Periode = '10001';
    private $SinglePeriode = 0;
    private $session;
    private $GLOBALVARIANTE = '1';
    private $DIMKEY = NULL;
    private $FieldPrefix = '';
    private $calcOB = 0;
    private $calcOBID = 0;

    //inside this variable, i will store the number of lines, this will be used for the collapse function...
    private $sheetrow = 0;

    private $ResetCalcpayables = 0;
    private $NumberOfDigits = 2;

    private $RAMValues=array();


    public function onInit($param) {
        // hier sammel ich die periode aus dem datawarehouse
        if($this->Request['periode']!='') {
            $this->Periode = $this->Request['periode'];
        }
        if($this->Request['per_single']!='') {
            $this->SinglePeriode = $this->Request['per_single'];
        }
        $this->load_ta_perioden($this->Periode,$this->SinglePeriode);
        //struktur Info
        $this->idtm_struktur->Text = $this->Request[$this->STRprimarykey];
        $this->STRcurrentID = $this->idtm_struktur->Text;
        //idta_struktur_type
        $this->piq_idta_struktur_type = $this->Request['idta_struktur_type'];
        //always load the relevant fields
        $this->get_ff_type($this->piq_idta_struktur_type);


        //smart loading of the fieldfunction, requiered for faster handling
        $this->GLOBALVARIANTE = $this->Request['idta_variante'];
        //before we start, we need alle columns that exist
        $this->load_ta_feldfunktion();
        
        $this->session = $this->Application->getSession();

        if(!isset($this->session['requestStage'])) {
            $this->session['requestStage'] = 1; // Now you'll know that this is the first request and nothing was created yet.
        }else {
            $this->session['requestStage'] += 1; // If this is the second request, the value is now 2
        }

        if(!$this->page->isCallback || !$this->page->isPostBack) {
            $this->initTable();
            $this->session['dynamicControlList'] = $this->dynamicControlList;
        }else{
            $this->reRenderTable();
        }
        parent::onInit($param);
    }
    
    public function initTable() {

            //now we check, if we already have values inside
            $this->check_tt_werte();

            $jahr = 0;
            $monat = 0;

            if(count($this->FunktionsFelder)>0) {
            //hier werden jetzt die einzelnen Werte geladen
                foreach($this->Perioden AS $key => $value) {
                    if(preg_match('/^\d\d\d\d/',$value)) {
                        $jahr = $key;
                        $monat = $key;
                    }else {
                        $jahr = $this->getYearByMonth($key);
                        $monat = $key;
                    }

                    //jetzt laden wir die einzelnen Werte
                    foreach($this->FunktionsFelder AS $funkID) {
                        $myUniquID="RLIQ".'XXX'.$jahr.'XXX'.$monat.'XXX'.$this->piq_idta_struktur_type.'XXX'.$funkID.'XXX'.$this->STRcurrentID;
                        $myttvalue = WerteRecord::finder()->findAllBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$this->STRcurrentID."' AND idta_feldfunktion = '".$funkID."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' LIMIT 1");
                        foreach($myttvalue AS $myttrecord) {
                            $readonly = FeldfunktionRecord::finder()->findByPK($funkID)->ff_readonly;
                            $this->ControlFieldList[]=array('class' => "TActiveTextBox", 'id' => $myUniquID, 'OnCallback' => 'onTextChanged','requestStage' => $this->session['requestStage'],'readonly'=>$readonly);
                        }
                    }
                }
                $this->load_header();
                $this->draw_cells();
            }
    }

    public function reRenderTable(){
        $test = array();
        $test = $this->session['dynamicControlList'];
        if(is_array($test)){
            foreach($test as $key=>$controlDescription){//render rows
                $controlClass = $controlDescription['class'];
                $newControl = new $controlClass;
                $newControl->setID($controlDescription['id']);
                if($controlDescription['OnCallback']!=""){
                    $newControl->OnCallback = $controlDescription['OnCallback'];
                }
                if($controlDescription['CommandParameter']!=""){
                    $newControl->setCommandParameter($controlDescription['CommandParameter']);
                }
                $this->resulttable->Rows[]=$newControl;
                foreach($controlDescription['children'] as $cellkey=>$cellcontrolDescription){ //render cells
                    $cellcontrolClass = $cellcontrolDescription['class'];
                    $newCellControl = new $cellcontrolClass;
                    $newCellControl->setID($cellcontrolDescription['id']);
                    if($cellcontrolDescription['OnCallback']!=""){
                        $newCellControl->OnCallback = $cellcontrolDescription['OnCallback'];
                    }
                    if($cellcontrolDescription['CommandParameter']!=""){
                        $newCellControl->setCommandParameter($cellcontrolDescription['CommandParameter']);
                    }
                    if(is_array($cellcontrolDescription['children'])){
                        foreach($cellcontrolDescription['children'] as $cellcokey=>$cellcocontrolDescription){ //render content of cells
                            $cellcocontrolClass = $cellcocontrolDescription['class'];
                            $newCellcoControl = new $cellcocontrolClass;
                            $newCellcoControl->setID($cellcocontrolDescription['id']);
                            if($cellcocontrolDescription['OnCallback']!=""){
                                $newCellcoControl->OnCallback = $cellcocontrolDescription['OnCallback'];
                            }
                            if($cellcocontrolDescription['CommandParameter']!=""){
                                $newCellcoControl->CommandParameter=$cellcocontrolDescription['CommandParameter'];
                            }
                            $newCellControl->Controls->add($newCellcoControl);
                        }
                    }
                    $newControl->Cells[]=$newCellControl;
                }
            }
        }
    }

    private function draw_cells() {

        $jahr = 0;
        $monat = 0;

        //hier werden jetzt die einzelnen Werte geladen
        foreach ($this->Perioden AS $key => $value) {
        //hier bauen wir die einzelnen Zeilen
            $jj=1;
            $WorkRow=new TActiveTableRow;
            $WorkRowID="R".$this->sheetrow; //new for grouping
            $this->resulttable->Rows[]=$WorkRow;            

            if($key<10000) {
                fmod($key,2)==0?$WorkRow->setCssClass('inputgrid'):$WorkRow->setCssClass('inputgridalternating');
            }

            if(preg_match('/^\d\d\d\d/',$value)) {
                $jahr = $key;
                $monat = $jahr;
            //$WorkRow->setBackColor('#cdcdcd');
            }else {
                $jahr = $this->getYearByMonth($key);
                $monat = $key;
            }

            $ControlListCell=array(); //clean the children
            
            //hier kommt die Beschriftung
            $cell = new TActiveTableCell();
            $cell->Text = $value;
            $WorkRow->Cells[]=$cell;

            //jetzt laden wir die einzelnen Werte
            foreach($this->FunktionsFelder AS $funkID) {
                $ControlListCellChildren=array();//clean the children
                $myUniquID="RLIQ".'XXX'.$jahr.'XXX'.$monat.'XXX'.$this->piq_idta_struktur_type.'XXX'.$funkID.'XXX'.$this->STRcurrentID;

                foreach($this->ControlFieldList as $controlDescription) {
                    if($controlDescription['id']==$myUniquID) {
                        $controlClass = $controlDescription['class'];
                        $newControl = new $controlClass;
                        $newControl->ID = $controlDescription['id'];
                        //list($name,$method) = explode('.',$controlDescription['OnCallback']);
                        if($controlDescription['readonly']) {
                            $newControl->setReadOnly(1);
                        }else {
                            $newControl->setReadOnly(0);
                        }
                        $newControl->AutoPostback=true; //former true
                        $newControl->OnCallback = "page.onTextChanged";
                        fmod($key,2)==0?$newControl->CssClass="inputgrid":$newControl->CssClass="inputgridalternating";
                        $newControl->Text = '0.00';
                        $cell = new TActiveTableCell;
                        $cell->Controls->add($newControl);
                        $ControlListCellChildren[]=Array("class"=>"TActiveTextBox","id"=>$controlDescription['id'],"OnCallback"=>"page.onTextChanged","CommandParameter"=>"");
                        $WorkRow->Cells[]=$cell;
                        $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$jj,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
                    }
                $jj++;
                }
            }
        $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>$WorkRowID,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);
        $this->sheetrow++;
        }
    }

    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {
        //hier schreiben wir den aktuellen Pfad, damit der User erkennen kann, wo er sich befindet
            $this->id_struktur->Text = $this->STRcurrentID;
            $this->idta_struktur_type->Text = $this->piq_idta_struktur_type;

            $wfl_values = array(1=>"offen",2=>"planen",3=>"pruefen",4=>"genehmigt",5=>"geschlossen");
            $this->wfl_status->dataSource=$wfl_values;
            $this->wfl_status->dataBind();

            //build the path
            $pfad = new PFCalculator();
            $this->StrukturPfad->Text = $pfad->getCurrentPath($this->Request['idtm_struktur']);

            //laden der Werte
            $this->getLatestWorkflow();
            $this->load_values();
        }
    }

    public function getLatestWorkflow() {
        $SQL = "SELECT * FROM tt_workflow WHERE wfl_modul='idtm_struktur' AND wfl_id=".$this->STRcurrentID." AND idta_variante=".$this->GLOBALVARIANTE." AND idta_periode=".$this->Periode." ORDER BY wfl_cdate DESC LIMIT 1";
        $WorkFlowRecord = WorkflowRecord::finder()->findBySQL($SQL);
        if(count($WorkFlowRecord)==1){
            $this->wfl_status->Text = $WorkFlowRecord->wfl_status;
        }else{
            $this->wfl_status->Text = 1;
        }
    }

    public function OpenFeldfunktionContainer($sender,$param) {
        $id=$this->mpnlFeldfunktionContainer->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function OpenStrukturStrukturContainer($sender,$param) {
        $id=$this->mpnlStrukturStrukturContainer->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function OpenSaisonContainer($sender,$param) {
        $id=$this->mpnlSaisonalisierung->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    private function init_tt_werte($id,$ff,$jahr,$monat) {

        $myFunk = FeldfunktionRecord::finder()->findByPK($ff);
        $tmp = $myFunk->ff_default;

        //hier schreiben wir nullwerte...
        $NEWWerteRecord = new WerteRecord();
        $NEWWerteRecord->w_jahr=$jahr;
        $NEWWerteRecord->w_monat=$monat;
        $this->getInitialValue($ff, StrukturRecord::finder()->findByPK($id)->idtm_stammdaten, $monat, $this->GLOBALVARIANTE, $tmp);
        $NEWWerteRecord->w_wert=$tmp;
        $NEWWerteRecord->w_endwert=0;
        $NEWWerteRecord->idta_feldfunktion=$ff;
        $NEWWerteRecord->idtm_struktur=$id;
        $NEWWerteRecord->w_id_variante=$this->GLOBALVARIANTE;
        $NEWWerteRecord->save();

    }

    private function getInitialValue($idta_feldfunktion, $idtm_stammdaten, $monat, $idta_variante, &$StartValue) {
    //First we check for the single month::
        $Results = TTStammdatenRecord::finder()->find('idtm_stammdaten = ? AND idta_periode = ? AND idta_variante = ? AND idta_feldfunktion = ?',$idtm_stammdaten,PeriodenRecord::finder()->findByper_Intern($monat)->idta_perioden,$idta_variante,$idta_feldfunktion);
        if(count($Results)==1) {
            $StartValue=$Results->tt_stammdaten_value;
        }else {
            $Result = TTStammdatenRecord::finder()->find('idtm_stammdaten = ? AND idta_periode = ? AND idta_variante = ? AND idta_feldfunktion = ?',$idtm_stammdaten,PeriodenRecord::finder()->findByper_Intern($this->getYearByMonth($monat))->idta_perioden,$idta_variante,$idta_feldfunktion);
            if(count($Result)==1) {
                $StartValue=$Result->tt_stammdaten_value;
            }
        }
    }

    private function update_w_wert($jahr,$monat,$local_type,$local_ff,$local_id,$value='0') {
        $myUniquID="RLIQ".'XXX'.$jahr.'XXX'.$monat.'XXX'.$local_type.'XXX'.$local_ff.'XXX'.$local_id;
        $this->RAMValues[$myUniquID]=$value;
    }

    public function saveValues() {

    //if(count(WorkflowRecord::finder()->find("wfl_modul='idtm_struktur' AND wfl_id=? AND wfl_status=?",$this->STRcurrentID,$this->wfl_status->Text))==0){
        $WFRecord = new WorkflowRecord;
        $WFRecord->wfl_modul="idtm_struktur";
        $WFRecord->wfl_id=$this->STRcurrentID;
        $WFRecord->wfl_status = $this->wfl_status->Text;
        $WFRecord->idtm_user=$this->User->GetUserId($this->User->Name);
        $WFRecord->idta_variante=$this->GLOBALVARIANTE;
        $WFRecord->idta_periode=$this->Periode;
        $WFRecord->save();
        //}

        $myDimKey = $this->build_DIMKEY($this->STRcurrentID);
        
        if($this->SinglePeriode==0 AND count($this->Perioden)>1) {
            $this->SaveButton->Text="saved";

            $jahr = 0;
            $monat = 0;

            //hier werden jetzt die einzelnen Werte geladen
            foreach ($this->Perioden AS $key => $value) {

                if(preg_match('/^\d\d\d\d/',$value)) {
                    $jahr = $key;
                    $monat = $key;
                }else {
                    $jahr = $this->getYearByMonth($key);
                    $monat = $key;
                }

                //jetzt laden wir die einzelnen Werte
                foreach($this->FunktionsFelder AS $funkID) {
                    $myUniquID="RLIQ".'XXX'.$jahr.'XXX'.$monat.'XXX'.$this->piq_idta_struktur_type.'XXX'.$funkID.'XXX'.$this->STRcurrentID;
                    $NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$this->STRcurrentID."' AND idta_feldfunktion = '".$funkID."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                    $NEWWerteRecord->w_wert = $this->page->ACTPanel->FindControl($myUniquID)->Text;
                    $NEWWerteRecord->w_id_variante=$this->GLOBALVARIANTE;
                    $NEWWerteRecord->w_dimkey=$myDimKey;
                    $NEWWerteRecord->save();
                }
            }
            foreach ($this->Perioden AS $key => $value) {
                if(preg_match('/^\d\d\d\d/',$value)) {
                    //echo "Hier startet der Backcalculator";
                    $myRunner = new PFBackCalculator();
                    $myRunner->setVariante($this->GLOBALVARIANTE);
                    $myRunner->runStructureCollector($this->STRcurrentID,$key,$this->GLOBALVARIANTE);
                }
            }
        }else{
            foreach($this->Perioden AS $key => $value) {
            //hier laden wir die variablen, die wir benoetigen um die letzten Werte auszulesen
                if(preg_match('/^\d\d\d\d/',$value)) {
                    $jahr = $key;
                    $monat = $key;
                }else {
                    $jahr = $this->getYearByMonth($key);
                    $monat = $key;
                }
                //hier startet jetzt der Part, wo ich nur eine Periode habe -> entweder SubJahr oder Jahr...
                $PFBackCalculator = new PFBackCalculator();
                /* Folgende Parameter sind zur Berechnung der Werte notwendig...
                 * @param idta_periode -> die interne Periodenbezeichnung -> 10001 für 1. Jahr oder 1 für 1 Monat (Bsp)
                 * @param idtm_struktur -> die Struktur ID, auf der die Werte nachher gespreichert werden sollen
                 * @param w_dimkey -> der Schlüssel, der angehängt werden soll...
                 * @param assoc_array(feldbezug=>wert) -> array mit den Werten, die als "neu" betrachtet werden sollen...
                 */
                $PFBackCalculator->setStartPeriod($monat);
                $PFBackCalculator->setStartNode($this->STRcurrentID);
                //vorbereiten des Wertearrays, damit die bestehenden Werte in der Datenbank, mit den neuen Uerberschrieben werden koennen
                //jetzt laden wir die einzelnen Werte
                foreach($this->FunktionsFelder AS $funkID) {
                    $myUniquID="RLIQ".'XXX'.$jahr.'XXX'.$monat.'XXX'.$this->piq_idta_struktur_type.'XXX'.$funkID.'XXX'.$this->STRcurrentID;
                    $NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$this->STRcurrentID."' AND idta_feldfunktion = '".$funkID."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                    if($NEWWerteRecord->w_wert<>$this->page->ACTPanel->FindControl($myUniquID)->Text) {
                        $w_wert[$funkID] = $this->page->ACTPanel->FindControl($myUniquID)->Text;
                    }
                }
                $PFBackCalculator->setNewValues($w_wert);
                $PFBackCalculator->setVariante($this->GLOBALVARIANTE);
                $PFBackCalculator->run();
                $this->SaveButton->Text = "saved";
            }
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
        if(count($Result)==1){
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

    private function check_tt_werte() {

        $jahr = 0;
        $monat = 0;

        foreach ($this->Perioden AS $key => $value) {

            if(preg_match('/^\d\d\d\d/',$value)) {
                $jahr = $key;
                $monat = $key;
            }else {
                $jahr = $this->getYearByMonth($key);
                $monat = $key;
            }

            foreach($this->FunktionsFelder AS $funkID) {

                if(count(WerteRecord::finder()->findAllBySql("SELECT idtt_werte FROM tt_werte WHERE w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND idta_feldfunktion = '".$funkID."' AND idtm_struktur = '".$this->STRcurrentID."' AND w_id_variante = '".$this->GLOBALVARIANTE."'"))) {
                //echo "treffer";
                }else {
                    $this->init_tt_werte($this->STRcurrentID,$funkID,$jahr,$monat);
                }
            }
        }
    }

    private function load_ta_perioden($Periode,$SinglePeriode=0) {
        $Result = PeriodenRecord::finder()->findByper_Intern($Periode);
        $this->Perioden[$Result->per_intern]=$Result->per_extern;
        if($SinglePeriode==0) {
            $Records = PeriodenRecord::finder()->findAllByparent_idta_perioden($Result->idta_perioden);
            foreach($Records As $Record) {
                $this->Perioden[$Record->per_intern]=$Record->per_extern;
            }
        }
    }

    private function load_ta_feldfunktion() {
    //hier laden wir die liste unserer feldfunktionen
        $myfefunk = FeldfunktionRecord::finder()->findAllBySql("SELECT * FROM ta_feldfunktion WHERE idta_struktur_type = '".$this->piq_idta_struktur_type."' ORDER BY ff_order");
        if(count($myfefunk)>0) {
            foreach($myfefunk AS $record) {
                $this->TableHeaderRL[$record->idta_feldfunktion]=$record->ff_name;
                array_push($this->FunktionsFelder,$record->idta_feldfunktion);
            }
        }
    }

    private function load_header() {
        //lets init the label row
        $FirstRow = new TActiveTableRow;
        $FirstRow->setCssClass('thead');
        $this->resulttable->Rows[]=$FirstRow;

        $ControlListCell=array(); //clean the children
        $ControlListCellChildren=array(); //clean the children

        $cell = new TActiveTableCell;
        $cell->Text="Zeit";
        $FirstRow->Cells[]=$cell;
        $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C0G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);

        $ii = 1;
        foreach($this->TableHeaderRL AS $key=>$value) {
            $ControlListCellChildren=array(); //clean the children
            $cell=new TActiveTableCell;
            $activeLabel = new TActiveLabel();
            $activeLabel->setID("R".$this->sheetrow."C".$ii."GAL");
            $activeLabel->setText("<b> ".$value."</b>");
            $cell->Controls->add($activeLabel);
            $ControlListCellChildren[]=Array("class"=>"TActiveLabel","id"=>"R".$this->sheetrow."C".$ii."GAL","OnCallback"=>"","CommandParameter"=>"");
            $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$ii."G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
            $FirstRow->Cells[]=$cell;
            $ii++;
        }
        $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>"R".$this->sheetrow."G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);
        $this->sheetrow++;
    }

    private function load_values() {

        $jahr = 0;
        $monat = 0;

        //hier werden jetzt die einzelnen Werte geladen
        foreach ($this->Perioden AS $key => $value) {

            if(preg_match('/^\d\d\d\d/',$value)) {
                $jahr = $key;
                $monat = $key;
            }else {
                $jahr = $this->getYearByMonth($key);
                $monat = $key;
            }

            //jetzt laden wir die einzelnen Werte
            foreach($this->FunktionsFelder AS $funkID) {
                $myUniquID="RLIQ".'XXX'.$jahr.'XXX'.$monat.'XXX'.$this->piq_idta_struktur_type.'XXX'.$funkID.'XXX'.$this->STRcurrentID;
                $myttvalue = WerteRecord::finder()->findAllBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$this->STRcurrentID."' AND idta_feldfunktion = '".$funkID."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");

                foreach($myttvalue AS $myttrecord) {
                    $this->page->ACTPanel->FindControl($myUniquID)->Text=number_format($myttrecord->w_wert,$this->NumberOfDigits,'.','');
                }
            }
        }
    }

    public function calcOpeningBalance($local_type,$local_ff,$local_id) {
        $myids = preg_split("/XXX/",$_POST['PRADO_POSTBACK_TARGET']);
        $local_field_id = $_POST['PRADO_POSTBACK_TARGET'];
        preg_match('/(.+\$.+\$).+/',$local_field_id,$matches);
        $local_prefix = $matches[1];
        
        foreach ($this->Perioden AS $key => $value) {
            $my_jahr = $this->getYearByMonth($key);
            $current_field = "RLIQXXX".$my_jahr."XXX".$key."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
            $tmp_periode = $this->getPeriodeBefore($key);
            if($tmp_periode<10000 AND $key<10000) {//hier muss noch eine weitere bedingung hin, damit der jahreswert auch richtig geladen wird
                $trecord = FeldfunktionRecord::finder()->findByPK($local_ff);
                $jahr = $this->getYearByMonth($tmp_periode);
                $previous_field = "RLIQXXX".$jahr."XXX".$tmp_periode."XXX".$local_type."XXX".$trecord->pre_idta_feldfunktion."XXX".$local_id;
                if($this->page->ACTPanel->FindControl($previous_field)){
                    $valuefillin = number_format($this->page->ACTPanel->FindControl($previous_field)->Text,$this->NumberOfDigits,'.','');
                }else{
                    $valuefillin = WerteRecord::finder()->findAllBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$trecord->pre_idta_feldfunktion."' AND w_jahr = '".$jahr."' AND w_monat = '".$tmp_periode."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                }
                $this->update_w_wert($my_jahr,$key,$local_type,$local_ff,$local_id,$valuefillin);
                $this->page->ACTPanel->FindControl($current_field)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                $this->check_collector($local_ff,$current_field,$local_prefix,$key,$my_jahr,$local_id);
            }else {
                $jahr = $tmp_periode;//pruefung ob 10001 nicht vergessen
                if($my_jahr==10001) {
                    //hier passiert doch ebbes...
                    if($key>12 AND $key<10000) {
                        $jahr+=1;
                        $tmp_periode+=1;
                    }
                    $previous_field = "RLIQXXX".$jahr."XXX".$tmp_periode."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                    if($this->page->ACTPanel->FindControl($previous_field)){
                        $valuefillin = number_format($this->page->ACTPanel->FindControl($previous_field)->Text,$this->NumberOfDigits,'.','');
                    }else{
                        $valuefillin = number_format(0,$this->NumberOfDigits,'.','');
                    }
                    $this->update_w_wert($my_jahr,$key,$local_type,$local_ff,$local_id,$valuefillin);
                    $this->page->ACTPanel->FindControl($current_field)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                    $this->check_collector($local_ff,$current_field,$local_prefix,$key,$my_jahr,$local_id);
                }else {
                    $jahr = $this->getYearByMonth($tmp_periode);
                    $trecord = FeldfunktionRecord::finder()->findByPK($local_ff);
                    $previous_field = "RLIQXXX".$jahr."XXX".$tmp_periode."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                    if($this->page->ACTPanel->FindControl($previous_field)){
                        $valuefillin = number_format($this->page->ACTPanel->FindControl($previous_field)->Text,$this->NumberOfDigits,'.','');
                    }else{
                        $ResultWert = WerteRecord::finder()->findBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$trecord->pre_idta_feldfunktion."' AND w_jahr = '".$jahr."' AND w_monat = '".$jahr."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                        $valuefillin = isset($ResultWert->w_wert)?$ResultWert->w_wert:0;
                    }
                    $this->update_w_wert($my_jahr,$key,$local_type,$local_ff,$local_id,$valuefillin);
                    $this->page->ACTPanel->FindControl($current_field)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                    $this->check_collector($local_ff,$current_field,$local_prefix,$key,$my_jahr,$local_id);
                }
            }
        }
    }

    public function calcPayables($local_type,$local_ff,$local_id,$local_jahr,$local_prefix) {        
        $counter=0;

        //reset of the existing values -> filled with value in w_endwert default zero if values from former periods, they will be filled in
        foreach ($this->Perioden AS $key => $value) {
            $my_jahr = $this->getYearByMonth($key);
            $current_field = "RLIQXXX".$my_jahr."XXX".$key."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
            $follow_value_record = WerteRecord::finder()->findBySql("SELECT w_endwert FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$local_ff."' AND w_jahr = '".$my_jahr."' AND w_monat = '".$key."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
            $this->page->ACTPanel->FindControl($current_field)->Text = number_format($follow_value_record->w_endwert,$this->NumberOfDigits,'.','');
        }

        //I need to reset the values for the following Period
        if($this->ResetCalcpayables==0){
            $cleanyear = $my_jahr+1;
            $ResetRecords=WerteRecord::finder()->findAllBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$local_ff."' AND w_jahr = '".$cleanyear."' AND w_id_variante = '".$this->GLOBALVARIANTE."'");
            foreach($ResetRecords AS $ResetRecord){
                $ResetRecord->w_endwert=0;
                $ResetRecord->save();
            }
            $this->ResetCalcpayables++;
        }

        foreach ($this->Perioden AS $key => $value) {            
            $counter++; // hier erhoehen wir die info, dass die erste periode vorbei ist
            if($key<10000) {
                $my_jahr = $this->getYearByMonth($key);
                $current_field = "RLIQXXX".$my_jahr."XXX".$key."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                $current_value = number_format($this->page->ACTPanel->FindControl($current_field)->Text,$this->NumberOfDigits,'.','');
                $days_for_factor = 360/$this->getNumberPerIntern($my_jahr);
                //here we get the value of the field, that contains the value for the base factor
                $tresult = CollectorRecord::finder()->findBySql("SELECT col_idtafeldfunktion,col_operator FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.col_idtafeldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.idta_feldfunktion = '".$local_ff."' AND ff_type='3' LIMIT 1"); //3 ist der struktursammler
                $base_field = "RLIQXXX".$my_jahr."XXX".$key."XXX".$local_type."XXX".$tresult->col_idtafeldfunktion."XXX".$local_id;
                $base_value = number_format($this->page->ACTPanel->FindControl($base_field)->Text,$this->NumberOfDigits,'.','');
                $factor_per_day=$base_value/$days_for_factor;
                $ttresult = CollectorRecord::finder()->findBySql("SELECT col_idtafeldfunktion,col_operator FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.col_idtafeldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.idta_feldfunktion = '".$local_ff."' AND ff_type<>'3' LIMIT 1"); //3 ist der struktursammler
                $day_field = "RLIQXXX".$my_jahr."XXX".$key."XXX".$local_type."XXX".$ttresult->col_idtafeldfunktion."XXX".$local_id;
                $day_value = $this->page->ACTPanel->FindControl($day_field)->Text;
                $temp_compare = $day_value/$days_for_factor;

                $untergrenze = 0; //der untere laufer
                $obergrenze = 1; //der obere laufwert
                $monat = $key;

                for($ii=0;$ii<10000;$ii++) {
                    if($temp_compare >= $untergrenze AND $temp_compare < $obergrenze) {
                        $tmpcurrent_field = "RLIQXXX".$my_jahr."XXX".$monat."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                        if($this->page->ACTPanel->FindControl($tmpcurrent_field)){
                            $tmpcurrent_value = number_format($this->page->ACTPanel->FindControl($tmpcurrent_field)->Text,$this->NumberOfDigits,'.','');
                        }else{
                            $tmpcurrent_value = 0;
                        }
                        $faktor_periode = $obergrenze - $temp_compare;                        
                        $valuefillin = $faktor_periode * $base_value;
                        $counter==1?'':$valuefillin+=$tmpcurrent_value;
                        $this->update_w_wert($my_jahr,$monat,$local_type,$local_ff,$local_id,$valuefillin);
                        $this->page->ACTPanel->FindControl($tmpcurrent_field)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                        if($faktor_periode<1 AND $faktor_periode>0) {
                            $monat++;
                            $target_year = $this->getYearByMonth($monat);//this is new because a value needs to be passed to the following year
                            $valuefillin = (1-$faktor_periode) * $base_value;
                            $follow_field = "RLIQXXX".$target_year."XXX".$monat."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                            if($target_year == $my_jahr){
                                $follow_value = number_format($this->page->ACTPanel->FindControl($follow_field)->Text,$this->NumberOfDigits,'.','');
                                $counter==1?'':$valuefillin+=$follow_value;
                                $this->update_w_wert($target_year,$monat,$local_type,$local_ff,$local_id,$valuefillin);
                                $this->page->ACTPanel->FindControl($follow_field)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                                break;
                            }else{                                
                                $follow_value_record = WerteRecord::finder()->findBySql("SELECT idtt_werte FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$local_ff."' AND w_jahr = '".$target_year."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");                                
                                if(count($follow_value_record)>0){
                                    $MyWerteRecord = WerteRecord::finder()->findByidtt_werte($follow_value_record->idtt_werte);
                                    $follow_value = $MyWerteRecord->w_endwert*1;
                                    $MyWerteRecord->w_endwert = $valuefillin;
                                    $MyWerteRecord->save();
                                    break;
                                }else{
                                    $tmp=0;
                                    $NEWWerteRecord = new WerteRecord();
                                    $NEWWerteRecord->w_jahr=$target_year;
                                    $NEWWerteRecord->w_monat=$monat;
                                    $this->getInitialValue($local_ff, StrukturRecord::finder()->findByPK($local_id)->idtm_stammdaten, $monat, $this->GLOBALVARIANTE, $tmp);
                                    $NEWWerteRecord->w_wert=$tmp;
                                    $NEWWerteRecord->w_endwert=$valuefillin;
                                    $NEWWerteRecord->idta_feldfunktion=$local_ff;
                                    $NEWWerteRecord->idtm_struktur=$local_id;
                                    $NEWWerteRecord->w_id_variante=$this->GLOBALVARIANTE;
                                    $NEWWerteRecord->save();
                                    break;
                                }
                            }                            
                        }
                    }
                    $untergrenze++;
                    $obergrenze++;
                    $monat++;
                }
                $this->check_collector($local_ff,$current_field,$local_prefix,$key,$local_jahr,$local_id);
            }
        }
        $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$this->sum_up($local_jahr,$local_jahr,$local_ff,$local_id));
        $resultField="RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
        $this->page->ACTPanel->FindControl($resultField)->Text = number_format($this->sum_up($local_jahr,$local_jahr,$local_ff,$local_id),$this->NumberOfDigits,'.','');
        //hier muss die berechnung der erroeffnungsbilanz hin
    }

    public function onTextChanged($sender,$param) {

        $this->SaveButton->Text="please SAVE";

        $myids = preg_split("/XXX/",$_POST['PRADO_POSTBACK_TARGET']);
        $local_field_id = $_POST['PRADO_POSTBACK_TARGET'];
        preg_match('/(.+\$.+\$).+/',$local_field_id,$matches);
        $local_prefix = $matches[1];
        $this->FieldPrefix = $local_prefix;

        //hier holen wir die Werte vom Feld:
        $local_jahr = $myids['1'];  //Jahr
        $local_monat = $myids['2']; //Monat
        $local_type = $myids['3']; //idta_struktur_type
        $local_ff = $myids['4']; //Feldfunktion
        $local_id = $myids['5']; //idta_struktur

        if($this->SinglePeriode == 0) {
            $local_month_fields = $this->get_month_array($local_jahr,$local_type,$local_ff,$local_id);
            $local_month_weight = $this->get_month_weigth($local_month_fields,$local_prefix,$this->get_ff_type($local_ff));
        }

        if($local_monat < 10000) {
        //here comes the bottum up stuff
        //here comes the bottum up stuff
        //here comes the bottum up stuff

            $td_startvalue = $_POST[$local_field_id];
            
            switch($this->get_ff_type($local_ff)) {
                case 0:
                //here comes the part for the addition scheme SUM
                    $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                    $this->update_w_wert($local_jahr,$local_monat,$local_type,$local_ff,$local_id,$td_startvalue);
                    $valuefillin = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$valuefillin);
                    $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                    $this->check_collector($local_ff,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    break;
                case 2:
                //here comes the part for the calculation scheme COLLECTOR
                    $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                    $this->run_back_collector($local_ff,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    $valuefillin = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$valuefillin);
//                    $valuefillin2 = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
//                    $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$valuefillin2);
                    $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');;
                    $this->check_collector($local_ff,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    break;
                case 5:
                //here comes the part for the calculation scheme CONTINUANCE
                    $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                    $this->run_back_collector($local_ff,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    $valuefillin = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$valuefillin);
//                    $valuefillin2 = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
//                    $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$valuefillin2);
                    $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');;
                    $this->check_collector($local_ff,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    break;
                case 4:
                //here comes the part for the calculation scheme OPENING BALANCE
                    $this->calcOpeningBalance($local_type,$local_ff,$local_id);
                    break;
                case 6:
                //here comes the part for the calculation scheme ACCOUNTS PAYABLE
                    $this->calcPayables($local_type,$local_ff,$local_id,$local_jahr,$local_prefix);
                    break;
                default:
                //here comes the part for the division scheme
                    $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                    $this->update_w_wert($local_jahr,$local_monat,$local_type,$local_ff,$local_id,$td_startvalue);
                    $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($this->get_avg_header($local_month_fields,$local_prefix),$this->NumberOfDigits,'.','');
                    $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$this->get_avg_header($local_month_fields,$local_prefix));
                    $this->check_collector($local_ff,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
            }

            $this->update_w_wert($local_jahr,$local_monat,$local_type,$local_ff,$local_id,$td_startvalue);

        //end of buttom up stuff
        }else {
        //here comes the top down stuff
        //here comes the top down stuff
        //here comes the top down stuff

            $td_startvalue = $_POST[$local_field_id];

            if($this->SinglePeriode==0) {
                switch($this->get_ff_type($local_ff)) {
                    case 2:
                        foreach($local_month_fields as $textfillin) {
                            $tempmonth = preg_split("/XXX/",$textfillin);
                            $mymonth = $tempmonth[2];
                            $valuefillin = $td_startvalue*$local_month_weight[$textfillin];
                            $this->page->ACTPanel->FindControl($textfillin)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                            $this->update_w_wert($local_jahr,$mymonth,$local_type,$local_ff,$local_id,$valuefillin);
                            $this->run_back_collector($local_ff,$textfillin,$local_prefix,$mymonth,$local_jahr,$local_id);
                            $this->check_collector($local_ff,$textfillin,$local_prefix,$mymonth,$local_jahr,$local_id);
                        }
                        break;
                    case 5:
                    //here comes the part for the calculation scheme CONTINUANCE
                        foreach($local_month_fields as $textfillin) {
                            $tempmonth = preg_split("/XXX/",$textfillin);
                            $mymonth = $tempmonth[2];
                            $valuefillin = $td_startvalue*$local_month_weight[$textfillin];
                            $this->page->ACTPanel->FindControl($textfillin)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                            $this->update_w_wert($local_jahr,$mymonth,$local_type,$local_ff,$local_id,$valuefillin);
                            $this->run_back_collector($local_ff,$textfillin,$local_prefix,$mymonth,$local_jahr,$local_id);
                            $this->check_collector($local_ff,$textfillin,$local_prefix,$mymonth,$local_jahr,$local_id);
                        }
                        break;
                    case 4:
                    //here comes the part for the calculation scheme OPENING BALANCE
                        $this->calcOpeningBalance($local_type,$local_ff,$local_id);
                        break;
                    case 6:
                    //here comes the part for the calculation scheme ACCOUNTS PAYABLE
                        $this->calcPayables($local_type,$local_ff,$local_id,$local_jahr,$local_prefix);
                        break;
                    default:
                        foreach($local_month_fields as $textfillin) {
                            $tempmonth = preg_split("/XXX/",$textfillin);
                            $mymonth = $tempmonth[2];
                            $valuefillin = $td_startvalue*$local_month_weight[$textfillin];
                            $this->page->ACTPanel->FindControl($textfillin)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                            $this->update_w_wert($local_jahr,$mymonth,$local_type,$local_ff,$local_id,$valuefillin);
                            $this->check_collector($local_ff,$textfillin,$local_prefix,$mymonth,$local_jahr,$local_id);
                        }
                }

                $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$td_startvalue);
            }else {//END of Non single periode...
            //end of top down stuff
                $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$local_type."XXX".$local_ff."XXX".$local_id;
                switch($this->get_ff_type($local_ff)) {
                    case 2:
                    //the scheme for the collector
                        $this->update_w_wert($local_jahr,$local_monat,$local_type,$local_ff,$local_id,$td_startvalue);
                        $this->run_back_collector($local_ff,$lf_year,$local_prefix,$local_jahr,$local_jahr,$local_id);
                        $this->check_collector($local_ff,$lf_year,$local_prefix,$local_jahr,$local_jahr,$local_id);
                        break;
                    case 5:
                        $this->update_w_wert($local_jahr,$local_monat,$local_type,$local_ff,$local_id,$td_startvalue);
                        $this->run_back_collector($local_ff,$lf_year,$local_prefix,$local_jahr,$local_jahr,$local_id);
                        $this->check_collector($local_ff,$lf_year,$local_prefix,$local_jahr,$local_jahr,$local_id);
                        break;
                    case 4:
                    //here comes the part for the calculation scheme OPENING BALANCE
                        $this->calcOpeningBalance($local_type,$local_ff,$local_id);
                        break;
                    case 6:
                    //here comes the part for the calculation scheme ACCOUNTS PAYABLE
                        $this->calcPayables($local_type,$local_ff,$local_id,$local_jahr,$local_prefix);
                        break;
                    default:
                        $update_w_wert = $this->update_w_wert($local_jahr,$local_monat,$local_type,$local_ff,$local_id,$td_startvalue);
                        $this->check_collector($local_ff,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                        break;
                }

                $this->update_w_wert($local_jahr,$local_jahr,$local_type,$local_ff,$local_id,$td_startvalue);
            }

        }

        if($this->calcOB) {
            $ttresult = CollectorRecord::finder()->findBySql("SELECT ta_collector.idta_feldfunktion,col_idtafeldfunktion,col_operator,ta_feldfunktion.ff_type AS ff_type FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.col_idtafeldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.idta_feldfunktion = '".$this->calcOBID."' AND ff_type=4 LIMIT 1");
            $this->calcOpeningBalance($this->piq_idta_struktur_type, $ttresult->col_idtafeldfunktion, $local_id);
        }

    }

    private function get_ff_type($idta_struktur_type) {
        if (count($this->FeldfunktionType)>=1) {
            return $this->FeldfunktionType[$idta_struktur_type];
        //this is a trick, because for init, we need the structure type, later never again;)
        }else {
            $myttvalue = FeldfunktionRecord::finder()->findAll("idta_struktur_type = ?",$idta_struktur_type);
            // works...print_r($myttvalue);
            foreach($myttvalue as $record) {
            //array_push($this->FeldfunktionType,array($record->idta_feldfunktion,$record->ff_type));
                $this->FeldfunktionType[$record->idta_feldfunktion]=$record->ff_type;
            }
            if(isset($this->FeldfunktionType[$idta_struktur_type])){
                return $this->FeldfunktionType[$idta_struktur_type];
            }else{
                return 0;
            }
        }
    }

    private function get_month_array($year,$type,$ff,$id) {
        $returnarray = array();
        $jahr = 0;
        $monat = 0;
        foreach($this->Perioden AS $key => $value) {
            if(preg_match('/^\d\d\d\d/',$value)) {
                $jahr = $key;
                $monat = $jahr;
            }else {
                $jahr = $this->getYearByMonth($key);
                $monat = $key;
                $fieldstr = "RLIQXXX".$jahr."XXX".$monat."XXX".$type."XXX".$ff."XXX".$id;
                array_push($returnarray,$fieldstr);
            }
        }
        return $returnarray;
    }

    private function get_month_weigth($fields,$ffprefix,$fftype='0') {
        $returnarray=array();
        $sum=0;
        $count=0;
        $countsum=0;
        foreach($fields as $myfield) {
            $sum+=$_POST[$ffprefix.$myfield]*1;
            if(!($_POST[$ffprefix.$myfield]*1)==0) {
                $count++;
            }
        }

        //hier muss hinterlegt werden, wenn aufsummiert werden muss - auch collector
        if($count==0 && ($fftype==0 || $fftype ==2)) {
            foreach($fields as $myfield) {
                $countsum++;
            }
        }

        if($count>0) {
            $avg = $sum/$count;
        }else {
            $avg=1;
        }
        //calculation for avg
        switch($fftype) {
            case 1:
                foreach($fields as $fielda) {
                    if(($_POST[$ffprefix.$fielda]*1)!=0) {
                        $returnarray[$fielda]=$_POST[$ffprefix.$fielda]*1/$avg;
                    }else {
                        if($count == 0) {
                            $returnarray[$fielda]='1';
                        }else {
                            $returnarray[$fielda]='0';
                        }
                    }
                }
                break;
            //calculation for sum
            default:
                foreach($fields as $fieldb) {
                    if(($_POST[$ffprefix.$fieldb]*1)!= 0) {
                        $returnarray[$fieldb]=$_POST[$ffprefix.$fieldb]*1/$sum;
                    }else {
                        if(!$countsum==0) {
                            $returnarray[$fieldb]=1/$countsum;
                        }else {
                            $returnarray[$fieldb]=0;
                        }
                    }
                }
        }
        return $returnarray;
    }

    private function get_avg_header($fields,$ffprefix) {
        $sum=0;
        foreach($fields as $myfield) {
            $sum+=$_POST[$ffprefix.$myfield]*1;
            if(!$_POST[$ffprefix.$myfield]==0) {
                $count++;
            }
        }
        if($count>0) {
            $avg = $sum/$count;
        }else {
            $avg=1;
        }
        return $avg;
    }

    private function run_collector($ffid,$field,$local_prefix,$month,$local_jahr,$local_id) {
        $tresult = CollectorRecord::finder()->findAllBySql("SELECT ta_collector.idta_feldfunktion,col_idtafeldfunktion,col_operator,ta_feldfunktion.ff_type AS ff_type FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.idta_feldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.idta_feldfunktion = '".$ffid."'");
        $fields = array();
        $operators = array();
        $types = array();
        $feldfunktion = array();
        $tempresult = 0;
        $i=0;
        $resultField="RLIQXXX".$local_jahr."XXX".$month."XXX".$this->piq_idta_struktur_type."XXX".$ffid."XXX".$local_id;
        foreach($tresult as $trecord) {
                /*$myWerteRecord = WerteRecord::finder()->findBySql("SELECT w_wert FROM tt_werte WHERE w_jahr = '".$local_jahr."' AND w_monat = '".$month."' AND idta_feldfunktion = '".$trecord->col_idtafeldfunktion."' AND idtm_struktur = '".$local_id."' AND w_id_variante = '".$this->GLOBALVARIANTE."'");
                $fields[$i] = $myWerteRecord->w_wert;
                */
            $myUniquID="RLIQ".'XXX'.$local_jahr.'XXX'.$month.'XXX'.$this->piq_idta_struktur_type.'XXX'.$trecord->col_idtafeldfunktion.'XXX'.$local_id;
            $fields[$i] = $this->page->ACTPanel->FindControl($myUniquID)->Text;
            $operators[$i] = $trecord->col_operator;
            $types[$i] = $trecord->ff_type; //hier wurde gemoggelt, eigentlich ffeldfunktion...
            $feldfunktion[$i] = $trecord->idta_feldfunktion;
            $i++;
        }
        $j=0;
        foreach($fields AS $myfield) {
            switch($operators[$j++]) {
                case '+':
                    $tempresult += $myfield;
                    break;
                case '-':
                    $tempresult -= $myfield;
                    break;
                case '*':
                    $tempresult *= $myfield;
                    break;
                case '/':
                    $tempresult /= $myfield;
                    break;
                default:
                    $tempresult = $myfield;
                    break;
            }
            $this->page->ACTPanel->FindControl($resultField)->Text = number_format($tempresult,$this->NumberOfDigits,'.','');
            $this->update_w_wert($local_jahr,$month,$this->piq_idta_struktur_type,$ffid,$local_id,$tempresult);
        }
    }

    private function run_back_collector($ffid,$field,$local_prefix,$month,$local_jahr,$local_id) {

        $tresult = CollectorRecord::finder()->findAllBySql("SELECT col_idtafeldfunktion,col_operator FROM ta_collector WHERE idta_feldfunktion = '".$ffid."'");
        $fields = array();
        $operators = array();
        $types = array();
        $tempresult = 0;
        $i=0;
        $myfaktor = 0;

        //the new value
        $local_field_id = $_POST['PRADO_POSTBACK_TARGET'];
        $myids = preg_split("/XXX/",$_POST['PRADO_POSTBACK_TARGET']);
        $td_startvalue = $_POST[$local_field_id];
        $local_monat = $myids['2'];
        //        $local_ff = $myids['4'];

        $td_oldvalue=0;
        //hier muss die pruefung hin...
        //im folgenden wird der ursprungswert berechnet...
        if($local_monat<10000 OR $this->SinglePeriode>0) {
            foreach($tresult as $trecord) {
                $myfeldinfo = FeldfunktionRecord::finder()->findByPK($trecord->col_idtafeldfunktion);
                $helperField="RLIQXXX".$local_jahr."XXX".$month."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                if($td_oldvalue!=0) {
                    switch($trecord->col_operator) {
                        case '+':
                            $td_oldvalue += $this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        case '-':
                            $td_oldvalue -= $this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        case '*':
                            $td_oldvalue *= $this->page->ACTPanel->FindControl($helperField)->Text==0?1:$this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        case '/':
                            $td_oldvalue /= $this->page->ACTPanel->FindControl($helperField)->Text==0?1:$this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        default:
                            $td_oldvalue = $this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                    }
                }else {
                    $td_oldvalue = $this->page->ACTPanel->FindControl($helperField)->Text;
                }
            }
            //the old value
            $td_oldvalue==0?$td_oldvalue=1:$td_oldvalue;
            $myUniquID="RLIQ".'XXX'.$local_jahr.'XXX'.$month.'XXX'.$this->piq_idta_struktur_type.'XXX'.$ffid.'XXX'.$local_id;
            $td_newvalue = $this->page->ACTPanel->FindControl($myUniquID)->Text;
            $myfaktor = $td_newvalue/$td_oldvalue;

            //lets look for the column to change
            foreach($tresult as $trecord) {
                $myfeldinfo = FeldfunktionRecord::finder()->findByPK($trecord->col_idtafeldfunktion);
                $resultField="RLIQXXX".$local_jahr."XXX".$month."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                if($myfeldinfo->ff_fix == 0) {
                    $this->page->ACTPanel->FindControl($resultField)->Text = number_format($this->page->ACTPanel->FindControl($resultField)->Text==0?$myfaktor:$this->page->ACTPanel->FindControl($resultField)->Text*$myfaktor,$this->NumberOfDigits,'.','');
                    if($myfeldinfo->ff_type == 0) {
                        if($this->SinglePeriode==0){
                            $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                            $valuefillin = $this->sum_up($local_jahr,$month,$myfeldinfo->idta_feldfunktion,$local_id);
                            $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                        }
                        $this->check_collector($myfeldinfo->idta_feldfunktion,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    }
                    else {
                        if($this->SinglePeriode==0){
                            $local_month_fields = $this->get_month_array($local_jahr,$this->piq_idta_struktur_type,$myfeldinfo->idta_feldfunktion,$local_id);
                            $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                            $valuefillin = $this->get_avg_header($local_month_fields,$local_prefix);
                            $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                        }
                        $this->check_collector($myfeldinfo->idta_feldfunktion,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    }
                }
            }

        }
        else {
        //start wenn jahreswert
            $td_oldvalue=0;
            foreach($tresult as $trecord) {
                $myfeldinfo = FeldfunktionRecord::finder()->findByPK($trecord->col_idtafeldfunktion);
                $helperField="RLIQXXX".$local_jahr."XXX".$month."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                if($td_oldvalue!=0) {
                    switch($trecord->col_operator) {
                        case '+':
                            $td_oldvalue += $this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        case '-':
                            $td_oldvalue -= $this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        case '*':
                            $td_oldvalue *= $this->page->ACTPanel->FindControl($helperField)->Text==0?1:$this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        case '/':
                            $td_oldvalue /= $this->page->ACTPanel->FindControl($helperField)->Text==0?1:$this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                        default:
                            $td_oldvalue = $this->page->ACTPanel->FindControl($helperField)->Text;
                            break;
                    }
                }else {
                    $td_oldvalue = $this->page->ACTPanel->FindControl($helperField)->Text;
                }
            }
            $td_oldvalue==0?$td_oldvalue=1:$td_oldvalue;
            //the old value
            $myUniquID="RLIQ".'XXX'.$local_jahr.'XXX'.$month.'XXX'.$this->piq_idta_struktur_type.'XXX'.$ffid.'XXX'.$local_id;
            $td_newvalue = $this->page->ACTPanel->FindControl($myUniquID)->Text;
            $myfaktor = $td_newvalue/$td_oldvalue;

            //lets look for the column to change
            foreach($tresult as $trecord) {
                $myfeldinfo = FeldfunktionRecord::finder()->findByPK($trecord->col_idtafeldfunktion);
                $resultField="RLIQXXX".$local_jahr."XXX".$month."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                if($myfeldinfo->ff_fix == 0) {
                    $this->page->ACTPanel->FindControl($resultField)->Text = number_format($this->page->ACTPanel->FindControl($resultField)->Text==0?$myfaktor:$this->page->ACTPanel->FindControl($resultField)->Text*$myfaktor,$this->NumberOfDigits,'.','');
                    if($myfeldinfo->ff_type == 0 ||$myfeldinfo->ff_type == 2) {
                        $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                        $valuefillin = $this->sum_up($local_jahr,$local_monat,$myfeldinfo->idta_feldfunktion,$local_id);
                        $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                        $this->check_collector($myfeldinfo->idta_feldfunktion,$lf_year,$local_prefix,$local_monat,$local_jahr,$local_id);
                    }else {
                        if($this->SinglePeriode==0) {
                            $local_month_fields = $this->get_month_array($local_jahr,$this->piq_idta_struktur_type,$myfeldinfo->idta_feldfunktion,$local_id);
                            $lf_year = "RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$this->piq_idta_struktur_type."XXX".$myfeldinfo->idta_feldfunktion."XXX".$local_id;
                            $valuefillin = $this->get_avg_header($local_month_fields,$local_prefix);
                            $this->page->ACTPanel->FindControl($lf_year)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                        }
                    }
                }
            }

        }

    }

    private function check_collector($ffid,$field,$local_prefix,$month,$local_jahr,$local_id) {
        $myfieldfunk='';
        $myfieldfunkrev='';
        $tresult = CollectorRecord::finder()->findAllBySql("SELECT ta_collector.idta_feldfunktion,col_idtafeldfunktion,col_operator,ta_feldfunktion.ff_type AS ff_type FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.idta_feldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.col_idtafeldfunktion = '".$ffid."'");
        foreach($tresult as $trecord) {
            if(FeldfunktionRecord::finder()->findByPK($trecord->idta_feldfunktion)->ff_calcopening) {
                $this->calcOB=1;
                $this->calcOBID=$trecord->idta_feldfunktion;
            }
            if($trecord->ff_type==6) {
                $this->calcPayables($this->piq_idta_struktur_type, $trecord->idta_feldfunktion, $local_id, $local_jahr, $local_prefix);
            }elseif($trecord->ff_type==4) {
                $this->calcOpeningBalance($this->piq_idta_struktur_type,$trecord->idta_feldfunktion,$local_id);
            }else {
                $this->run_collector($trecord->idta_feldfunktion,$field,$local_prefix,$month,$local_jahr,$local_id);
                $myfieldfunk = $trecord->idta_feldfunktion;
                if($myfieldfunk!='') { //AND $myfieldfunk!='3'){
                    if(FeldfunktionRecord::finder()->findByPK($myfieldfunk)->ff_type==5) {
                        $mymonth = $this->getMaxPerIntern($local_jahr);
                        $previousField="RLIQXXX".$local_jahr."XXX".$mymonth."XXX".$this->piq_idta_struktur_type."XXX".$myfieldfunk."XXX".$local_id;
                        $valuefillin = $this->page->ACTPanel->FindControl($previousField)->Text;
                        $this->update_w_wert($local_jahr,$local_jahr,$this->piq_idta_struktur_type,$myfieldfunk,$local_id,$valuefillin);
                        $resultField="RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$this->piq_idta_struktur_type."XXX".$myfieldfunk."XXX".$local_id;
                        $this->page->ACTPanel->FindControl($resultField)->Text = number_format($valuefillin,$this->NumberOfDigits,'.','');
                    }else {
                        if($this->SinglePeriode==0) {
                            $this->update_w_wert($local_jahr,$local_jahr,$this->piq_idta_struktur_type,$myfieldfunk,$local_id,$this->sum_up($local_jahr,$month,$myfieldfunk,$local_id));
                            //hier muss ich definieren, ob der wert in summe oben an kommt, oder nur der absolute wert
                            $resultField="RLIQXXX".$local_jahr."XXX".$local_jahr."XXX".$this->piq_idta_struktur_type."XXX".$myfieldfunk."XXX".$local_id;
                            $this->page->ACTPanel->FindControl($resultField)->Text = number_format($this->sum_up($local_jahr,$month,$myfieldfunk,$local_id),$this->NumberOfDigits,'.','');
                        }
                    }
                }
            }
        }
    }

    private function getMaxPerIntern($local_jahr) {
        return PeriodenRecord::finder()->findBySQL("SELECT MAX(per_intern) AS per_intern FROM ta_perioden WHERE parent_idta_perioden = '".PeriodenRecord::finder()->findByper_intern($local_jahr)->idta_perioden."'")->per_intern;
    }

    private function getNumberPerIntern($local_jahr) {
        return count(PeriodenRecord::finder()->findAllBySQL("SELECT per_intern FROM ta_perioden WHERE parent_idta_perioden = '".PeriodenRecord::finder()->findByper_intern($local_jahr)->idta_perioden."'"));
    }

    private function sum_up($local_jahr,$local_month,$ffid,$local_id) {
        $returnresult = 0;
        foreach ($this->Perioden AS $key => $value) {
            if(!preg_match('/^\d\d\d\d/',$value)) {
                $monat = $key;
                $myUniquID="RLIQ".'XXX'.$local_jahr.'XXX'.$monat.'XXX'.$this->piq_idta_struktur_type.'XXX'.$ffid.'XXX'.$local_id;
                $returnresult += $this->page->ACTPanel->FindControl($myUniquID)->Text;
            }
        }
        return $returnresult;
    }

    private function getYearByMonth($periode_intern) {
        $Result = PeriodenRecord::finder()->findByper_Intern($periode_intern);
        if($Result->parent_idta_perioden != 0) {
            $Result2 = PeriodenRecord::finder()->findByidta_perioden($Result->parent_idta_perioden);
            return $Result2->per_intern;
        }else {
            return $periode_intern;
        }
    }

    private function getPeriodeBefore($periode_intern) {
        if($periode_intern==1 OR $periode_intern==10001) {
            return 10001;
        }else {
            if($periode_intern<10000) {
                $tester = $periode_intern-1;
                //achtung, wenn nicht monate sondern andere detaillierung
                if(fmod($tester,12)==0) {
                    return 10000+(round($periode_intern/12,0));
                }else {
                    return $periode_intern-1;
                }
            }else {
                $criteria = new TActiveRecordCriteria();
                $criteria->Condition="per_intern < 10000";
                if(count(PeriodenRecord::finder()->findAll($criteria))>0) {
                    $tester = ($periode_intern-10001)*12; //achtung, wenn detaillierter als monat geplant wird, dann stimmt das nicht mehr
                    return $tester;
                }else {
                    return $periode_intern-1;
                }
            }
        }
    }

}
?>