<?php

Prado::using('Application.app_code.PFCalculator');
Prado::using('Application.app_code.PFBackCalculator');
Prado::using('Application.app_code.PFPeriodPullDown');
Prado::using('Application.app_code.PFDBTools');

class splasherworkspace extends TPage {

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }
    
    private $primarykey = "idta_splasher";
    private $MASTERRECORD = '';
    private $finder = '';
    private $fields = array('spl_name','from_idta_stammdaten_group','to_idta_stammdaten_group','from_idta_feldfunktion','to_idta_feldfunktion');
    private $listfields = array();
    private $datfields = array();
    private $timefields = array();
    private $hiddenfields = array();
    private $boolfields = array();
    private $exitURL = 'struktur.splasherworkspace';

    private $RAMRecord = array();

    private $session; //the variable for the session
    private $dynamicControlList; //the variable the grid is stored into

    private $sheetrow = 1;

    public function onInit($param) {
        $this->session = $this->Application->getSession();
        if($this->page->isCallback && $this->page->isPostBack) {
            $this->reRenderTable();
        }
        parent::onInit($param);
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

    public function onLoad($param) {
        date_default_timezone_set('Europe/Berlin');
        //Globale definition fuer dieses Dokument
        $this->finder = SplasherRecord::finder();
        $this->MASTERRECORD = new SplasherRecord;
        
        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {
            $this->initPullDowns();
            $this->bindListSplasher();
        }
    }

    public function initPullDowns(){
        $this->from_idta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
        $this->from_idta_stammdaten_group->dataBind();
        $this->to_idta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
        $this->to_idta_stammdaten_group->dataBind();

        $this->from_idta_feldfunktion->DataSource=PFH::build_SQLPullDown(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name"));
        $this->from_idta_feldfunktion->dataBind();
        $this->to_idta_feldfunktion->DataSource=PFH::build_SQLPullDown(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name"));
        $this->to_idta_feldfunktion->dataBind();

        $PeriodPullDown = new PFPeriodPullDown();
        $PeriodPullDown->setStructureTable("ta_perioden");
        $PeriodPullDown->setRecordClass(PeriodenRecord::finder());
        $PeriodPullDown->setPKField("idta_perioden");
        $PeriodPullDown->setSQLCondition("per_intern > 9999");
        $PeriodPullDown->setField("per_extern");
        $PeriodPullDown->letsrun();

        $this->DWH_idta_perioden->DataSource=$PeriodPullDown->myTree;
        $this->DWH_idta_perioden->dataBind();

        $this->DWH_idta_variante->DataSource=PFH::build_SQLPullDown(VarianteRecord::finder(),"ta_variante",array("idta_variante","var_descr"));
        $this->DWH_idta_variante->dataBind();
    }

    public function bindListSplasher() {
        $this->SplasherListe->DataSource=SplasherRecord::finder()->findAll();
        $this->SplasherListe->dataBind();
    }

    public function TDeleteButtonClicked($sender,$param) {
        $tempus=$this->primarykey;
        $AEditRecord = SplasherRecord::finder()->findByPK($this->$tempus->Text);
        $AEditRecord->delete();
        $this->bindListSplasher();
        $this->TNewButtonClicked($sender,$param);
    }

    public function RecalcYearSplasher($sender,$param){
        $PFDBTools = new PFDBTools();
        $PFDBTools->calculateSplasherYear();
        unset($PFDBTools);
    }

    public function load_splasher($sender,$param) {

        $item = $param->Item;
        $myitem=SplasherRecord::finder()->findByPK($item->lst_idta_splasher->Text);

        $tempus = $this->primarykey;
        $monus = $this->primarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->hiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->text= $myitem->$recordfield;
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->timefields as $recordfield) {
            $edrecordfield = $recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->splasher_edit_status->Text = 1;

        $this->bindList_From_StammdatenGroupList();
    }

    public function TSavedButtonClicked($sender,$param) {

        $tempus=$this->primarykey;

        if($this->splasher_edit_status->Text == '1') {
            $AEditRecord = SplasherRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $AEditRecord = new SplasherRecord;
        }

        //HIDDEN
        foreach ($this->hiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->timefields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $AEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $AEditRecord->save();

        $this->bindListSplasher();
        $this->splasher_edit_status->Text = 1;
        $this->idta_splasher->Text=$AEditRecord->idta_splasher;
    }

    public function TNewButtonClicked($sender,$param) {

        $tempus = $this->primarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->hiddenfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->datfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //LIST
        foreach ($this->listfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        //BOOL
        foreach ($this->boolfields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        foreach ($this->timefields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '00:00';
        }

        //NON DATUM
        foreach ($this->fields as $recordfield) {
            $edrecordfield = $recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->splasher_edit_status->Text = '0';
    }

    public function terminList_PageIndexChanged($sender,$param) {
        $this->TerminListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListSplasher();
    }

    public function bindList_From_StammdatenGroupList(){
        //$this->From_idtm_stammdaten->Text = $this->from_idta_stammdaten_group->Text;
        $this->From_StammdatenGroupListe->DataSource=StammdatenRecord::finder()->findAll('idta_stammdaten_group = ? AND stammdaten_aktiv = 0',$this->from_idta_stammdaten_group->Text);
        $this->From_StammdatenGroupListe->dataBind();
    }

    public function suggestOrganisation($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $mySQL = "SELECT idtm_organisation,org_name FROM tm_organisation WHERE org_name LIKE '%".$token."%'";
        $sender->DataSource=PFH::convertdbObjectSuggest(TActiveRecord::finder('OrganisationRecord')->findAllBySQL($mySQL),array('idtm_organisation','org_name'));
        $sender->dataBind();
    }

    public function checkOrganisationName($sender,$param) {
        // valid if the username is not found in the database
        $param->IsValid=OrganisationRecord::finder()->findByidtm_organisation($this->suggest_idtm_organisation->Text)===null;
    }

    public function suggestionSelectedOne($sender,$param) {
        $id=$sender->Suggestions->DataKeys[ $param->selectedIndex ];
        $this->ttidtm_organisation->Text=$id;
    }

    public function load_splasher_values(){
        //holen der perioden
        $Taschenrechner = new PFCalculator();
        $Taschenrechner->setStartPeriod($this->DWH_idta_perioden->Text);

        $MyRecords = StammdatenRecord::finder()->findAllByidta_stammdaten_group($this->to_idta_stammdaten_group->Text);

        foreach($Taschenrechner->Perioden AS $Periode){
            $PerMonat = $Periode[0];
            $PerJahr = $Taschenrechner->getYearByMonth($Periode[0]);
            foreach($MyRecords AS $StammdatenRC){
                $TTWerteRecord = TTSplasherRecord::finder()->find('spl_jahr = ? AND spl_monat=? AND idta_variante = ? AND idta_feldfunktion = ? AND idtm_stammdaten = ? AND to_idtm_stammdaten = ?',$PerJahr,$PerMonat,$this->DWH_idta_variante->Text,$this->to_idta_feldfunktion->Text,$this->From_idtm_stammdaten->Text,$StammdatenRC->idtm_stammdaten);
                $UniqueID = 'xxx'.$PerJahr .'xxx'. $PerMonat . 'xxx' . $this->From_idtm_stammdaten->Text.'xxx'. $StammdatenRC->idtm_stammdaten;
                if(count($TTWerteRecord)==1){
                    $this->RAMRecord[$UniqueID] = number_format($TTWerteRecord->spl_faktor, 2, '.', '');
                }else{
                    $TTSplasher = new TTSplasherRecord();
                    $TTSplasher->idta_feldfunktion = $this->to_idta_feldfunktion->Text;
                    $TTSplasher->idta_variante = $this->DWH_idta_variante->Text;
                    $TTSplasher->spl_jahr = $PerJahr;
                    $TTSplasher->spl_monat = $PerMonat;
                    $TTSplasher->idtm_stammdaten = $this->From_idtm_stammdaten->Text;
                    $TTSplasher->to_idtm_stammdaten = $StammdatenRC->idtm_stammdaten;
                    $TTSplasher->spl_faktor = number_format(0, 2, '.', '');
                    $TTSplasher->save();
                    $this->RAMRecord[$UniqueID] = number_format(0, 2, '.', '');
                }
            }
        }
    }

    public function load_splasher_table($sender,$param){        
        //init des von bezuges
        if($sender->ID == "From_StammdatenGroupListe"){
            $this->From_idtm_stammdaten->Text = $param->Item->lst_idtm_stammdaten->Data;
            $this->load_splasher_values();
        }
        
        //alternierende werte
        $Alternating = 1;

        //holen der perioden
        $Taschenrechner = new PFCalculator();
        $Taschenrechner->setStartPeriod($this->DWH_idta_perioden->Text);

        //holen der detialwerte der zielperiode
        $MyRecords = StammdatenRecord::finder()->findAll('idta_stammdaten_group = ? AND stammdaten_aktiv = 0',$this->to_idta_stammdaten_group->Text);
        //kopfzeile
        $WorkRow=new TActiveTableRow;
        $this->resulttable->Rows[]=$WorkRow;
        $WorkRow->setCssClass('thead');

        //Beschriftung
        $ii=1;
        
        $cell = new TActiveTableCell();
        $cell->setID("R".$this->sheetrow."C".$ii."GA");
        $cell->Text = "<b>Name</b>";
        $WorkRow->Cells[]=$cell;
        $ii++;
        
        //Datum als Titel
        foreach($Taschenrechner->Perioden AS $Periode){
            $cell = new TActiveTableCell();
            $cell->setID("R".$this->sheetrow."C".$ii."A");
            $activeLabel = new TActiveLabel();
            $activeLabel->setID("R".$this->sheetrow."C".$ii."AL");
            $activeLabel->setText($Periode[0]);
            $cell->Controls->add($activeLabel);
            $WorkRow->Cells[]=$cell;
            $ii++;
        }

        foreach($MyRecords AS $StammdatenRC){

            $ControlListCell=array(); //clean the children

            $WorkRow=new TActiveTableRow;
            $this->resulttable->Rows[]=$WorkRow;
            
            //Beschriftung
            $cell = new TActiveTableCell();
            $cell->Text = "<b>".$StammdatenRC->stammdaten_name."</b>";
            $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C1","OnCallback"=>"","CommandParameter"=>"","children"=>"");
            $WorkRow->Cells[]=$cell;

            //Datenfelder
            $jj=2;
            foreach($Taschenrechner->Perioden AS $Periode){
                $ControlListCellChildren=array();//clean the children
                $cell = new TActiveTableCell();
                //eingabefeld
                $inputfield = new TActiveTextBox();
                $inputfield->setCssClass("inputgrid");
                $UniqueID = 'xxx'.$Taschenrechner->getYearByMonth($Periode[0]) .'xxx'. $Periode[0] . 'xxx' . $this->From_idtm_stammdaten->Text.'xxx'. $StammdatenRC->idtm_stammdaten;
                $inputfield->setId($UniqueID);
                $inputfield->Text = $this->RAMRecord[$UniqueID];
                $inputfield->AutoPostback=true;
                $inputfield->OnCallback = "page.onTextChanged";
                $cell->Controls->add($inputfield);
                $ControlListCellChildren[]=Array("class"=>"TActiveTextBox","id"=>$UniqueID,"OnCallback"=>"page.onTextChanged","CommandParameter"=>"");
                $WorkRow->Cells[]=$cell;
                $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$jj,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
                $jj++;
            }

            //alternieren der zeilen
            fmod($ALTERNATING,2)==0?$WorkRow->setCssClass('listalternating'):$WorkRow->setCssClass('listnonealternating');

            $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>"R".$this->sheetrow,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);

            $ALTERNATING++;
            $this->sheetrow++;
        }

        $this->bindList_From_StammdatenGroupList();
        $param->Item->CssClass="PSPFAZ";
        unset($Taschenrechner);

        $this->session['dynamicControlList'] = $this->dynamicControlList;
    }

    public function saveSplashingValue($sender,$param){
         //holen der perioden
        $Taschenrechner = new PFCalculator();
        $Taschenrechner->setStartPeriod($this->DWH_idta_perioden->Text);

        $MyRecords = StammdatenRecord::finder()->findAllByidta_stammdaten_group($this->to_idta_stammdaten_group->Text);

        foreach($Taschenrechner->Perioden AS $Periode){
            $PerMonat = $Periode[0];
            $PerJahr = $Taschenrechner->getYearByMonth($Periode[0]);
            foreach($MyRecords AS $StammdatenRC){
                $TTWerteRecord = TTSplasherRecord::finder()->find('spl_jahr = ? AND spl_monat=? AND idta_variante = ? AND idta_feldfunktion = ? AND idtm_stammdaten = ? AND to_idtm_stammdaten = ?',$PerJahr,$PerMonat,$this->DWH_idta_variante->Text,$this->to_idta_feldfunktion->Text,$this->From_idtm_stammdaten->Text,$StammdatenRC->idtm_stammdaten);
                $UniqueID = 'xxx'.$PerJahr .'xxx'. $PerMonat . 'xxx' . $this->From_idtm_stammdaten->Text.'xxx'. $StammdatenRC->idtm_stammdaten;
                if(count($TTWerteRecord)==1){
                    $TTWerteRecord->spl_faktor = number_format($this->page->ACTPanel->FindControl($UniqueID)->Text, 2, '.', '');
                    $TTWerteRecord->save();
                    $this->RAMRecord[$UniqueID] = number_format($TTWerteRecord->spl_faktor, 2, '.', '');
                }
            }
        }
        $sender->Text = "saved";
    }

    public function onTextChanged($sender,$param){
        $MyIDs = preg_split("/xxx/",$sender->Id);
        $CJahr=$MyIDs[1];
        $CMonat=$MyIDs[2];
        $CFromStammdaten=$MyIDs[3];
        $CToStammdaten=$MyIDs[4];
        if($CMonat<=9999){
            $UniqueID = 'xxx'.$CJahr .'xxx'. $CJahr . 'xxx' . $CFromStammdaten.'xxx'. $CToStammdaten;
            $this->page->ACTPanel->FindControl($UniqueID)->Text = $this->sum_up($CJahr,$CMonat,$CFromStammdaten,$CToStammdaten);
        }else{
            $this->sum_down($CJahr,$CMonat,$CFromStammdaten,$CToStammdaten,$sender->Text);
        }
        $this->SplasherSaveButton->Text = "bitte speichern";
    }

    private function sum_up($local_jahr,$local_month,$local_from_stammdaten,$local_to_stammdaten) {
        //init des kalender
        $Taschenrechner = new PFCalculator();
        $Taschenrechner->setStartPeriod($this->DWH_idta_perioden->Text);

        $returnresult = 0;

        foreach($Taschenrechner->Perioden AS $Periode){
            $PerMonat = $Periode[0];
            if($PerMonat<9999){
                $UniqueID = 'xxx'.$local_jahr .'xxx'. $PerMonat . 'xxx' . $local_from_stammdaten.'xxx'. $local_to_stammdaten;
                $returnresult += $this->page->ACTPanel->FindControl($UniqueID)->Text;
            }
        }
        return $returnresult;
    }

    private function sum_down($local_jahr,$local_month,$local_from_stammdaten,$local_to_stammdaten,$NeuerJahresWert) {
        //init des kalender
        $JahresWert = $this->sum_up($local_jahr, $local_month, $local_from_stammdaten, $local_to_stammdaten);
        $Taschenrechner = new PFCalculator();
        $Taschenrechner->setStartPeriod($this->DWH_idta_perioden->Text);
        $returnresult = 0;
        foreach($Taschenrechner->Perioden AS $Periode){
            $PerMonat = $Periode[0];
            if($PerMonat<9999){
                $UniqueID = 'xxx'.$local_jahr .'xxx'. $PerMonat . 'xxx' . $local_from_stammdaten.'xxx'. $local_to_stammdaten;
                if($JahresWert<>0){
                    $this->page->ACTPanel->FindControl($UniqueID)->Text = $NeuerJahresWert * ($this->page->ACTPanel->FindControl($UniqueID)->Text*1/$JahresWert);
                }else{
                    $this->page->ACTPanel->FindControl($UniqueID)->Text = $NeuerJahresWert * (1/(count($Taschenrechner->Perioden)-1));
                }
            }
        }
        return true;
    }

    public function PeriodeChanged($sender,$param){
        $indices = $sender->SelectedIndices;
        foreach($indices as $index) {
            $item=$sender->Items[$index];
            $result=$item->Value;
        }
        $this->DWH_idta_perioden->Text = $result;
        $this->load_splasher_table($sender, $param);
    }

    public function VarianteChanged($sender,$param) {
        $indices = $sender->SelectedIndices;
        foreach($indices as $index) {
            $item=$sender->Items[$index];
            $result=$item->Value;
        }
        $this->DWH_idta_variante->Text = $result;
        $this->load_splasher_table($sender, $param);
    }

}
?>