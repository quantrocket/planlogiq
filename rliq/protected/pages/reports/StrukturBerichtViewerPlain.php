<?php

Prado::using('Application.app_code.PFCalculator');
Prado::using('Application.app_code.PFBackCalculator');
Prado::using('Application.app_code.PFPeriodPullDown');
Prado::using('Application.app_code.PFStrukturGenerator');

class StrukturBerichtViewerPlain extends TPage {
    
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    private $Periode = '10001';
    private $Perioden;
    private $Variante = '1';
    private $Varianten;
    private $Stammdatensicht;
    private $idta_struktur_bericht = 1;
    private $zwischenergebnisse = array();
    private $SinglePeriode = 0;
    private $InputBericht = 0;
    private $ReportPerioden = array(); //die Perioden information fuer die jeweilige Spalten

    private $ReportOperator = array();  //der Operator fuer die jeweiligen Spalten

    private $ReportIDsLeft = array();
    private $ReportIDsRight = array();

    private $ReportIDsInString = array();
    private $ReportIDsInStringAll = "";
    private $GraphOffset = 0;
    private $idtm_struktur = 0;
    private $STRcounter=0;
    private $Nested = 1;
   
    //inside this variable, i will store the number of lines, this will be used for the collapse function...
    private $sheetrow = 0;

    private $session; //the variable for the session
    private $dynamicControlList; //the variable the grid is stored into

    public function onInit($param) {
        $this->session = $this->Application->getSession();
        if(!$this->page->isCallback && !$this->page->isPostBack) {
            $finder = UserFelderRecord::finder();
            $finder->deleteAll('user_id = ?', $this->User->getUserId());
            $this->initTable();
            $this->session['dynamicControlList'] = $this->dynamicControlList;            
        }else{
            $this->reRenderTable();
        }
    }

    public function onLoad($param){       
        if($this->Application->Parameters['QlikView']=='1'){
//            $QVTestSecure = new QVTicketSecure();
//            $RolandTicket = $QVTestSecure->getTicketHTMLSQL($this->User->Name);
            if(!$this->page->isPostBack && !$this->page->isCallback){
                $this->QlikViewInline->FrameUrl=("http://".$this->Application->Parameters['QlikViewHost']."/webplanner_ajax/SH02.htm?LB15=".strtoupper($this->User->Name));
             //qlikview/AjaxZfc/dms_ajax/?userid=".$RolandTicket);
            }//ende der postback schleife
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

    public function initTable(){

        //this needs to be loaded before the period, because we need the variant_id for the startperiod
        if($this->Request['idta_variante']!='') {
            $this->Variante = $this->Request['idta_variante'];
        }else{
            $this->Variante=VarianteRecord::finder()->findByvar_default(1)->idta_variante;
            $this->Variante==""?$this->Variante=1:'';
        }

        if($this->Request['periode']!='') {
            $this->Periode = $this->Request['periode'];
        }else{
            $sec_per = $this->Periode;
            $this->Periode = VarianteRecord::finder()->findByidta_variante($this->Variante)->idta_perioden;
            $this->Periode==''?$this->Periode=$sec_per:'';
        }

        if($this->Request['idta_stammdatensicht']!=""){
            $this->Stammdatensicht=$this->Request['idta_stammdatensicht'];
        }else{
            $this->Stammdatensicht==""?$this->Stammdatensicht=1:'';
        }

        if($this->Request['idtm_struktur']!='') {
            $this->idtm_struktur = $this->Request['idtm_struktur'];
        }else{
            $this->idtm_struktur = $this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_struktur");
            $this->idtm_struktur==''?$this->idtm_struktur=1:'';
        }


        if($this->Request['per_single']!='') {
            $this->SinglePeriode = $this->Request['per_single'];
        }
        
        if($this->Request['idta_struktur_bericht']!='') {
            $this->idta_struktur_bericht = $this->Request['idta_struktur_bericht'];
        }else{
            $this->idta_struktur_bericht = StrukturBerichtRecord::finder()->findBysb_startbericht(1)->idta_struktur_bericht;
        }

//        //the parameters for the commentsystem
//        $this->ccom_id->Text = $this->idtm_struktur;
//        $this->cidta_variante->Text = $this->Variante;
//        $this->cidta_periode->Text = $this->Periode;

        //setting up the db-connection
        $myDBConnection = new TDbConnection($this->Application->getModule('db1')->database->getConnectionString(),$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        $myDBConnection->Active = true;

        //this calculator is used to build the subcats
        $mySubcatsCalculator = new PFCalculator();
        $mySubcatsCalculator->setDBConnection($myDBConnection);
        $mySubcatsCalculator->setStartNode($this->idtm_struktur,1);
        if(!$this->Nested){
            $mySubcatsCalculator->load_all_cats();
            $mySubcatsCalculator->setsubcats($mySubcatsCalculator->getsubcats());
        }

        //THE REPORT HEADER

        $this->sheetrow++;

        $this->StrukturPfad->Text = $mySubcatsCalculator->getCurrentPath($this->idtm_struktur);
        
        //here we build the statements nthat a stored inside the database
        $Report = StrukturBerichtRecord::finder()->findByPK($this->idta_struktur_bericht);
        $this->ttpivot_struktur_name->Text = $Report->pivot_struktur_name;

        $ColumnCriteria = new TActiveRecordCriteria();
        $ColumnCriteria->Condition = "idta_struktur_bericht = :suchtextcolumn";
        $ColumnCriteria->Parameters[':suchtextcolumn'] = $this->idta_struktur_bericht;
        $ColumnCriteria->OrdersBy['sbs_order']='ASC';

        $ReportColumns = StrukturBerichtSpaltenRecord::finder()->findAll($ColumnCriteria);

        $idta_struktur_bericht_spalten=array();

        foreach($ReportColumns as $ReportColumn) {
            $this->GraphOffset++;
            $this->InputBericht = $ReportColumn->sbs_input;
//            $this->StrukturBerichtSaveButton->Visible = $this->InputBericht;
//            $this->StrukturBerichtSaveImageButton->Visible = $this->InputBericht;

            //wenn die Variante fix uebergeben wurde
            if($ReportColumn->sbs_idta_variante_fix) {
                $this->Varianten[$ReportColumn->idta_struktur_bericht_spalten] = $ReportColumn->idta_variante;
            }else{
                $this->Varianten[$ReportColumn->idta_struktur_bericht_spalten] = $this->Variante;
            }

            //hier befuelle ich die variablenliste pro

            if($ReportColumn->sbs_struktur_switch_type==1){
                $mySubcatsCalculator->setStartNode($ReportColumn->sbs_idtm_struktur,$this->Nested);
            }else{
                $mySubcatsCalculator->setStartNode($this->idtm_struktur,$this->Nested);
            }

            //das brauchen wir, fuer den fall dass wir die nested informationen noch nicht im modell haben
            if(!$this->Nested){
                $ChildrenNodes[$ReportColumn->idta_struktur_bericht_spalten]=$mySubcatsCalculator->ChildrenNodes;
                $tmpInString='';
                if(count($ChildrenNodes[$ReportColumn->idta_struktur_bericht_spalten]) > 0) {
                    $counter=0;
                    foreach($ChildrenNodes[$ReportColumn->idta_struktur_bericht_spalten] As $key=>$value) {
                        if($value!=''){
                            $counter==0?$tmpInString .= "'".$value."' ":$tmpInString .= ",'".$value."' ";
                            $counter++;
                            $this->STRcounter==0?$this->ReportIDsInStringAll .= "'".$value."' ":$this->ReportIDsInStringAll .= ",'".$value."' ";
                            $this->STRcounter++;
                        }
                    }
                }
                $this->ReportIDsInString[$ReportColumn->idta_struktur_bericht_spalten]=$tmpInString;
            }else{
                if($ReportColumn->sbs_struktur_switch_type==1){
                    $StartRecord = StrukturRecord::finder()->findByidtm_struktur($ReportColumn->sbs_idtm_struktur);
                }else{
                    $StartRecord = StrukturRecord::finder()->findByidtm_struktur($this->idtm_struktur);
                }
                $this->ReportIDsLeft[$ReportColumn->idta_struktur_bericht_spalten] = $StartRecord->struktur_lft;
                $this->ReportIDsRight[$ReportColumn->idta_struktur_bericht_spalten] = $StartRecord->struktur_rgt;
            }

            //falls eine zeitliche abweichung festgestellt wurde
            if($ReportColumn->sbs_perioden_fix==1 OR (!$ReportColumn->idta_perioden_gap==0 AND !$ReportColumn->idta_perioden_gap=='')){
                $mySubcatsCalculator->setStartPeriod($ReportColumn->idta_perioden_gap,$ReportColumn->sbs_perioden_fix);
                $this->ReportPerioden[$ReportColumn->idta_struktur_bericht_spalten]=$mySubcatsCalculator->Perioden;                
            }else{
                $mySubcatsCalculator->setStartPeriod($this->Periode,$this->SinglePeriode);
                $this->ReportPerioden[$ReportColumn->idta_struktur_bericht_spalten]=$mySubcatsCalculator->Perioden;
            }

            $this->ReportOperator[$ReportColumn->idta_struktur_bericht_spalten]=$ReportColumn->sbs_bericht_operator;

        }

        foreach($this->ReportPerioden AS $tempPerioden){
            foreach($tempPerioden AS $key=>$value){
                $this->Perioden[] = $value[0];
            }
        }
        //print_r($this->Perioden);
        
        if($this->InputBericht || (!$this->page->isCallback && !$this->page->isPostBack)) {

            $criteria = new TActiveRecordCriteria();
            $criteria->Condition="idta_struktur_bericht = :suchbedingung1";
            $criteria->Parameters[':suchbedingung1'] = $this->idta_struktur_bericht;
            $criteria->OrdersBy['sbz_order']="ASC";
            $ReportRows = StrukturBerichtZeilenRecord::finder()->findAll($criteria);

            $rowcounter = 0;
            foreach($ReportRows As $SingleRow) {
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"} = new PFCalculator();
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setDBConnection($myDBConnection);
                //${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setsubcats($mySubcats);
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setInputReport($this->InputBericht);
                //Perioden muss vor CALCCOLUMNS kommen, sonst ergebnis nicht richtig
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->Varianten=$this->Varianten; //aus der zeile
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->Perioden=$this->ReportPerioden; //aus der zeile
                //this needs to be set, before we calc the number of columns
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setColumns($ReportColumns);
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->calcColumns();
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setFeldFunktion($SingleRow->idta_feldfunktion);
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setVariante($this->Variante); //muss noch aus der definition der spalte geholt werden                
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->idta_struktur_bericht_spalten=$idta_struktur_bericht_spalten; //aus der zeile
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->ReportOperator=$this->ReportOperator; //uebergabe des operators an den calculator
                //hier uebergeben wir die entsprechenden spaltenwerte...
                if($this->Nested){
                    ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->ReportIDsLeft=$this->ReportIDsLeft;
                    ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->ReportIDsRight=$this->ReportIDsRight;
                }else{
                    ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->ReportIDsInString=$this->ReportIDsInString;
                    ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->ReportIDsInStringAll=$this->ReportIDsInStringAll;
                    ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->ChildrenNodes=$ChildrenNodes; //aus der zeile
                }        
                //${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setStartNode($this->Request['idtm_struktur']);
                if($rowcounter==0) {
                    $this->load_header(${$SingleRow->idta_struktur_bericht_zeilen."RObj"});
                    $rowcounter++;
                }
                if($SingleRow->sbz_spacer_label!="" OR !$SingleRow->sbz_spacer_label==0) {
                    if($SingleRow->sbz_type==4) {
                        ;
                    }else {
                        $this->draw_spacer(${$SingleRow->idta_struktur_bericht_zeilen."RObj"},$SingleRow->sbz_spacer_label);
                    }
                }
                if($SingleRow->sbz_type==0) { //type == 0 bedeutet die normale listenabfrage
                    $this->draw_cells(${$SingleRow->idta_struktur_bericht_zeilen."RObj"},$SingleRow->sbz_label,$SingleRow->sbz_detail);
                }
                if($SingleRow->sbz_type==1) { //type == 1 bedeutet die dimensionsabfrage
                    ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->dimension = $SingleRow->idtm_stammdaten;
                    $this->draw_cells_dimension(${$SingleRow->idta_struktur_bericht_zeilen."RObj"},$SingleRow->sbz_label,$SingleRow->sbz_order,$SingleRow->sbz_detail);
                }
                if($SingleRow->sbz_type==3) {  //type == 3 Bedeutet eine Berechnung auf bereits existierende Objekte
                    $this->build_difference_zwischenergebnisse($SingleRow->idta_struktur_bericht_zeilen,$SingleRow->sbz_label);
                }
                if($SingleRow->sbz_type==4) { //type == 4 bedeutet einen graphen
                    $this->draw_graph(${$SingleRow->idta_struktur_bericht_zeilen."RObj"},$SingleRow->sbz_label,$SingleRow->sbz_spacer_label);
                }
            }
        }
    }

    public function generateExcel2007($sender,$param){
        $parameter['modus']=0;
        $parameter['periode']=$this->strukturportlet->DWH_idta_perioden->Text;
        $parameter['per_single']=$this->strukturportlet->DWH_per_single->Checked?1:0;
        $parameter['idta_variante']=$this->strukturportlet->DWH_idta_variante->Text;
        $parameter['idta_struktur_bericht']=$this->strukturportlet->DWH_idta_struktur_bericht->Text;
        $parameter['idtm_struktur']=$this->strukturportlet->DWH_idtm_struktur->Text;
        $parameter['idta_struktur_type']=$this->Request['idta_struktur_type'];
        //$anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
        $url = $this->getApplication()->getRequest()->constructUrl('page','reports.workbook.WBK_StrukturBerichtViewer', $parameter);
        $this->Response->redirect($url);
    }

    public function OpenStrukturBerichtContainer($sender,$param) {
        $id=$this->mpnlStrukturBerichtContainer->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function OpenFortschreibungContainer($sender,$param) {
        $id=$this->mpnlFortschreibungContainer->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    private function build_difference_zwischenergebnisse($idta_struktur_bericht_zeilen,$label="TTResult") {
        $tempResult = array(); //the inital result-array, that is used for further calculations
        $ttResults = SBZCollectorRecord::finder()->findAllByidta_struktur_bericht_zeilen($idta_struktur_bericht_zeilen);
        if(count($ttResults)>=2) { //this makes only sence, if we have more than one value...
            foreach($ttResults as $tResult) {
                $myCalcField = StrukturBerichtZeilenRecord::finder()->findByPK($tResult->row_idta_struktur_bericht_zeilen);
                if(isset($this->zwischenergebnisse[$myCalcField->sbz_label])){
                    $counter = count ($this->zwischenergebnisse[$myCalcField->sbz_label]);
                    for($ii=0;$ii<$counter;$ii++) {
                        if(!isset($tempResult[$ii])){
                            $tempResult[$ii]=0;
                        }
                        $PreFaktor = 0;
                        $PreFaktor = isset($this->zwischenergebnisse[$myCalcField->sbz_label][$ii])?$this->zwischenergebnisse[$myCalcField->sbz_label][$ii]:0;
                        switch($tResult->sbz_collector_operator) {
                            case '+':
                                $tempResult[$ii] += $PreFaktor;
                                break;
                            case '-':
                                $tempResult[$ii] -= $PreFaktor;
                                break;
                            case '*':
                                $tempResult[$ii] *= $PreFaktor;
                                break;
                            case '/':
                                $tempResult[$ii] /= $PreFaktor;
                                break;
                            default:
                                $tempResult[$ii] = $PreFaktor;
                                break;
                        }
                    }
                }
            }

            $FirstRow = new TActiveTableRow; //after the calculation is done, we need to draw it
            $FirstRow->setID("R".$this->sheetrow."G"); //new for grouping
            $this->resulttable->Rows[]=$FirstRow;

            $ControlListCell=array(); //clean the children
            $cell=new TActiveTableCell;
            $cell->setID("R".$this->sheetrow."C1");
            $cell->Text=$label;
            $cell->EnableViewState = true;
            $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C1","OnCallback"=>"","CommandParameter"=>"","children"=>"");
            $FirstRow->Cells[]=$cell;
            $counter = count ($tempResult);
            for($ii=0;$ii<$counter;$ii++) {
                $cell=new TActiveTableCell;
                $cell->setID("R".$this->sheetrow."C".($ii+2)); 
                $cell->Text=number_format($tempResult[$ii], 0, ',', '.');
                $cell->EnableViewState = true;
                $tempResult[$ii]<0?$cell->setCssClass('negativecell'):'';
                $FirstRow->Cells[]=$cell;
                $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".($ii+2),"OnCallback"=>"","CommandParameter"=>"","children"=>"");
            }
            $FirstRow->setCssClass('calculated');
            $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>"R".$this->sheetrow."G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);
            $this->zwischenergebnisse[$label]=$tempResult;
        }
        $this->sheetrow++; //now all cells are fixed, so i can count the row up
    }

    private function load_header($PFCALCULATOR) {
        $PFCALCULATOR->buildTitle("Zeit");

        $FirstRow = new TActiveTableRow;
        $FirstRow->setID("R".$this->sheetrow."G"); //new for grouping
        $this->resulttable->Rows[]=$FirstRow;
        $ii = 1; //the startpoint of the columncounter                
        
        $ControlListCell=array(); //clean the children

        foreach($PFCALCULATOR->getTitle() AS $value) {
            //i need to check, if it´s original level or not
            preg_match('/.x([0-9]+$)/',$value,$matches);
            if(isset($matches[1])){
                $per_intern = $matches[1];
            }else{
                $per_intern = '';
            }

            $cell=new TActiveTableCell();
            $ControlListCellChildren=array();//clean the children
            if($per_intern>=9999){
                $cell->setID("R".$this->sheetrow."C".$ii."G");                
                    $imagebutton = new TActiveImageButton();
                    $imagebutton->setID("R".$this->sheetrow."C".$ii."GIB");
                    $imagebutton->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif");
                    $imagebutton->setText("collapse");
                    $imagebutton->onCallback="page.hideColumnGroup";
                    $imagebutton->setCommandParameter($ii);
                    $cell->Controls->add($imagebutton);
                $ControlListCellChildren[]=Array("class"=>"TActiveImageButton","id"=>"R".$this->sheetrow."C".$ii."GIB","OnCallback"=>"page.hideColumnGroup","CommandParameter"=>$ii);
                //$cell->Text=$title;
                    $activeLabel = new TActiveLabel();
                    $activeLabel->setID("R".$this->sheetrow."C".$ii."GAL");
                    $yearlabel = explode('x',$value);
                    $activeLabel->setText("<b> ".$yearlabel[0]."</b>");
                    $cell->Controls->add($activeLabel);
                $ControlListCellChildren[]=Array("class"=>"TActiveLabel","id"=>"R".$this->sheetrow."C".$ii."GAL","OnCallback"=>"","CommandParameter"=>"");
                $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$ii."G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
            }else{
                $monthlabel = explode('x',$value);
                if($value=='Zeit'){
                    $cell->setID("R".$this->sheetrow."CGG".$ii);
                    $imagebutton = new TActiveImageButton();
                    $imagebutton->setID("R".$this->sheetrow."CGG".$ii."IB");
                    $imagebutton->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif");
                    $imagebutton->setText("collapse");
                    $imagebutton->onCallback="page.hideAllRows";
                    $imagebutton->setCommandParameter($ii);
                    $cell->Controls->add($imagebutton);
                        $ControlListCellChildren[]=Array("class"=>"TActiveImageButton","id"=>"R".$this->sheetrow."CGG".$ii."IB","OnCallback"=>"page.hideAllRows","CommandParameter"=>$ii);
                    $activeLabel = new TActiveLabel();
                    $activeLabel->setID("R".$this->sheetrow."CGG".$ii."AL");
                    $activeLabel->setText("<b> ".$monthlabel[0]."</b>");
                    $cell->Controls->add($activeLabel);
                        $ControlListCellChildren[]=Array("class"=>"TActiveLabel","id"=>"R".$this->sheetrow."CGG".$ii."AL","OnCallback"=>"","CommandParameter"=>"");
                    $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."CGG".$ii,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
                }else{
                    $cell->setID("R".$this->sheetrow."C".$ii);
                    $yearKennung = $PFCALCULATOR->getYearByMonth($monthlabel[1]);
                    $yearLabelObj = PeriodenRecord::finder()->findByper_intern($yearKennung);
                    if(is_Object($yearLabelObj)){
                        $yearLabel = $yearLabelObj->per_extern;
                    }else{
                        $yearLabel = '   ';
                    }
                    $cell->Text="<b> ".strtoupper($monthlabel[0])." ".substr($yearLabel,2,2)."</b>";
                    $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$ii,"OnCallback"=>"","CommandParameter"=>"","children"=>"");
                }
            }
            $cell->EnableViewState = true;
            $FirstRow->Cells[]=$cell;
            $ii++; //increment of the columncounter
        }
        $FirstRow->setCssClass('thead');
        $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>"R".$this->sheetrow."G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);
        $this->sheetrow++;
    }

    private function draw_spacer($PFCALCULATOR,$title="Graph") {
        $FirstRow = new TActiveTableRow;
        $FirstRow->setID("R".$this->sheetrow."G"); //new for grouping
        $this->resulttable->Rows[]=$FirstRow;

        $ControlListCell=array(); //clean the children
        $ControlListCellChildren=array();//clean the children

        $cell=new TActiveTableCell;
        //id and button to expand and collapse
        $cell->setID("R".$this->sheetrow."C1G");
            $imagebutton = new TActiveImageButton();
            $imagebutton->setID("R".$this->sheetrow."C1GIB");
            $imagebutton->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif");
            $imagebutton->setText("collapse");
            $imagebutton->onCallback="page.hideRowGroup";
            $imagebutton->setCommandParameter($this->sheetrow);
            $cell->Controls->add($imagebutton);
            $ControlListCellChildren[]=Array("class"=>"TActiveImageButton","id"=>"R".$this->sheetrow."C1GIB","OnCallback"=>"page.hideRowGroup","CommandParameter"=>$this->sheetrow);

        //$cell->Text=$title;
        $activeLabel = new TActiveLabel;
        $activeLabel->setID("R".$this->sheetrow."C1GAL");
        $activeLabel->setText(" ".$title);
        $cell->Controls->add($activeLabel);
        $ControlListCellChildren[]=Array("class"=>"TActiveLabel","id"=>"R".$this->sheetrow."C1GAL","OnCallback"=>"","CommandParameter"=>"");

        $cell->EnableViewState = true;
        $cell->setCssClass('listheader');
        //echo (count($PFCALCULATOR->Perioden)*$PFCALCULATOR->NumberOfColumns)+1;
        $cell->setColumnSpan($PFCALCULATOR->NumberOfColumns+1);
        $FirstRow->Cells[]=$cell;
        
        $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C1G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
        $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>"R".$this->sheetrow."G","OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);

        $this->sheetrow++; //inccrement of the sheetrow
    }

    private function draw_graph($PFCALCULATOR,$title="Graph",$GraphSource="DBI") {
        $FirstRow = new TActiveTableRow;
        $this->resulttable->Rows[]=$FirstRow;

        $cell=new TActiveTableCell;
        $cell->EnableViewState = true;
        $cell->Text=$title;
        $cell->setCssClass('listalternating');
        $FirstRow->Cells[]=$cell;

        $celltt=new TActiveTableCell;
        $celltt->EnableViewState = true;
        $celltt->setCssClass('PSPFAZ');
        $celltt->setColumnSpan($PFCALCULATOR->NumberOfColumns+1);

        //here comes the image component
        $GraphImage = new TActiveImage;
        $GraphImage->ID=$title;
        $GraphImage->setImageAlign("center");
        $GraphImage->setImageUrl($this->generateGraph($this->zwischenergebnisse[$GraphSource],$title));

        $celltt->Controls->add($GraphImage);

        $FirstRow->Cells[]=$celltt;
    }

    private function draw_cells($PFCALCULATOR,$name="Summe",$details=1) {
        $PFCALCULATOR->executeSQLNOTITLE($name,$details);
        $PFCALCULATOR->FormatWerte();
        $ROWS = $PFCALCULATOR->getValues();
        $ROWSPLAIN = $PFCALCULATOR->getPlainValues();
        $ALTERNATING = 1;

        //here comes the part for the inputfield
        if($this->InputBericht==1) {
            $inp_idta_feldfunktion = $PFCALCULATOR->FeldFunktion;
            $inp_idta_variante = $this->Variante;
        }

        $temparray = array(); //hier speicher ich die werte fuer das zwischenergebnis
        $WorkRowID=""; //empty and init the var

        if(count($ROWS)>0){
            foreach($ROWS as $row) {
                
                $RowCounter=1;
                //hier bauen wir die einzelnen Zeilen
                $WorkRow=new TActiveTableRow;

                $ControlListCell=array(); //clean the children                
                
                if($ALTERNATING<count($ROWS) AND count($row)>0) {
                    $jj = 1;
                    $WorkRowID="R".$this->sheetrow; //new for grouping
                    $this->resulttable->Rows[]=$WorkRow;
                    $ColumnCounter=0;
                    foreach($row as $value) {                        
                        $ControlListCellChildren=array();//clean the children
                        $cell = new TActiveTableCell();
                        $cell->setID("R".$this->sheetrow."C".$jj);
                        $jj++;
                        $cell->EnableViewState = true;
                        if($this->InputBericht==0 OR $RowCounter==1) {
                            if($this->InputBericht==1) {
                                $tmpText = preg_split("/xxx/",$value);
                                $cell->Text = $PFCALCULATOR->getCurrentPath($tmpText[2]);
                                $inp_idtm_struktur = $tmpText[2];
                                $inp_idta_struktur_type = StrukturRecord::finder()->findByidtm_struktur($inp_idtm_struktur)->idta_struktur_type;
                            }else {
                                $cell->Text = $value;
                            }
                        }else {
                            $inp_per_month = $this->Perioden[$ColumnCounter-1];
                            $inp_per_year = $PFCALCULATOR->getYearByMonth($inp_per_month);
                            $inputfield = new TActiveTextBox();
                            $UniqueID = 'xxx'.$inp_per_year.'xxx'.$inp_per_month.'xxx'.$inp_idta_struktur_type.'xxx'.$inp_idta_feldfunktion.'xxx'.$inp_idtm_struktur.'xxx'.$inp_idta_variante.'xxx';
                            if(!$this->page->isPostBack && !$this->page->isCallback && $this->InputBericht==1) {
                                $inputfield->setText($value);
                                $MyUserFelderRecord = new UserFelderRecord();
                                $MyUserFelderRecord->user_id = $this->User->getUserId($this->User->Name);
                                $MyUserFelderRecord->tuf_feldname = $UniqueID;
                                $MyUserFelderRecord->save();
                            }
                            //$local_jahr."xxx".$local_jahr."xxx".$local_type."xxx".$local_ff."xxx".$local_id;
                            $inputfield->setId($UniqueID);
                            $inputfield->setCssClass("inputgrid");
                            //$this->page->registerObject($UniqueID,$inputfield);
                            $cell->Controls->add($inputfield);
                            $ControlListCellChildren[]=Array("class"=>"TActiveTextBox","id"=>$UniqueID,"OnCallback"=>"","CommandParameter"=>"");                            
                        }
                        $WorkRow->Cells[]=$cell;
                        $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$jj,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
                        $RowCounter++;
                        $ColumnCounter++;
                    }
                    fmod($ALTERNATING,2)==0?$WorkRow->setCssClass('listalternating'):$WorkRow->setCssClass('listnonealternating');
                    $ALTERNATING++;
                }else {
                    $jj = 1;
                    $WorkRowID="R".$this->sheetrow."G"; //new for grouping
                    $this->resulttable->Rows[]=$WorkRow;
                    if($this->InputBericht==0 AND count($row)>0) {
                        $ControlListCellChildren=array();//clean the children
                        foreach($row as $value) {
                            $cell = new TActiveTableCell();
                            $cell->setID("R".$this->sheetrow."C".$jj);
                            $jj++;
                            $cell->EnableViewState = true;
                            $cell->Text = $value;
                            $cell->setCssClass('calculatedsumme');
                            $WorkRow->Cells[]=$cell;
                            $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$jj,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
                        }
                    }
                    $ALTERNATING++;
                }
                $WorkRow->setID($WorkRowID);
                $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>$WorkRowID,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);
                $this->sheetrow++; //increment rowcounter
            }
        }

        $ALTERNATING = 0;
        $labelcounter=0;
        foreach($ROWSPLAIN as $rowplain) {
            $ALTERNATING++;
            if($ALTERNATING==count($ROWSPLAIN)) {
                foreach($rowplain as $valueplain) {
                    $labelcounter==0?$labelcounter++:array_push($temparray,$valueplain*1);
                }
            }
        }
        $this->zwischenergebnisse[$name]=$temparray;
    }

    private function draw_cells_dimension($PFCALCULATOR,$name="Summe",$ORDER = 0,$DetailRow="0") {
        if($DetailRow==0){
            //$TESTDETAIL = StammdatenGroupRecord::finder()->findByidta_stammdaten_group((StammdatenRecord::finder()->findByidtm_stammdaten($PFCALCULATOR->dimension)->idta_stammdaten_group))->stammdaten_group_original;
            //$DetailChecker = $TESTDETAIL==1?1:0;
            $DetailChecker = 0;
        }else{
            $DetailChecker = 1;
        }
        $PFCALCULATOR->executeDimensionSQL($name,$DetailChecker,$this->InputBericht);
        $ROWS = $PFCALCULATOR->getValues();
        $ROWSPLAIN = $PFCALCULATOR->getPlainValues();
        $ALTERNATING = $this->sheetrow;
        $ROWCOUNTER = 0;

        //here comes the part for the iniputfield
        if($this->InputBericht==1) {
            $inp_idta_feldfunktion = $PFCALCULATOR->FeldFunktion;
            $inp_idta_variante = $this->Variante;
        }

        $temparray = array(); //hier speicher ich die werte fuer das zwischenergebnis
        $WorkRowID = "";//empty an init var

        foreach($ROWS as $row) {
            $jj=1;            
            $ROWCOUNTER++;
            $DDRowCounter=1;
            //hier bauen wir die einzelnen Zeilen

            $ControlListCell=array(); //clean the children        

            $WorkRow=new TActiveTableRow;
            if($this->InputBericht==1) {
                $walkerCheck = count($ROWS)>2?count($ROWS)-1:count($ROWS);
            }else {
                $walkerCheck = count($ROWS);
            }

            if($ROWCOUNTER<$walkerCheck OR ($DetailChecker==0 AND $this->InputBericht==0) OR $this->InputBericht==1) {
                $WorkRowID="R".$this->sheetrow; //new for grouping
                $this->resulttable->Rows[]=$WorkRow;
                $ColumnCounter=0;
                foreach($row as $value) {
                    $ControlListCellChildren=array();//clean the children
                    $cell = new TActiveTableCell();
                    $cell->setID("R".$this->sheetrow."C".$jj);
                    $jj++;
                    $cell->EnableViewState = true;
                    if($this->InputBericht==0 OR $DDRowCounter==1) {
                        if($this->InputBericht==1) {
                            $tmpText = preg_split("/xxx/",$value);
                            $cell->Text = $tmpText[1];
                            $inp_idtm_struktur = $tmpText[2];
                            $inp_idta_struktur_type = StrukturRecord::finder()->findByidtm_struktur($inp_idtm_struktur)->idta_struktur_type;
                        }else {
                            $cell->Text = $value;
                        }
                    }else {
                        $inp_per_month = $this->Perioden[$ColumnCounter-1];
                        $inp_per_year = $PFCALCULATOR->getYearByMonth($inp_per_month);
                        $inputfield = new TActiveTextBox();
                        $UniqueID = 'xxx'.$inp_per_year.'xxx'.$inp_per_month.'xxx'.$inp_idta_struktur_type.'xxx'.$inp_idta_feldfunktion.'xxx'.$inp_idtm_struktur.'xxx'.$inp_idta_variante.'xxx';
                        if(!$this->page->isPostBack && !$this->page->isCallback && $this->InputBericht==1) {
                            $inputfield->setText($value);
                            $MyUserFelderRecord = new UserFelderRecord();
                            $MyUserFelderRecord->user_id = $this->User->getUserId($this->User->Name);
                            $MyUserFelderRecord->tuf_feldname = $UniqueID;
                            $MyUserFelderRecord->save();
                        }
                        //$local_jahr."xxx".$local_jahr."xxx".$local_type."xxx".$local_ff."xxx".$local_id;
                        $inputfield->setId($UniqueID);
                        $inputfield->setCssClass("inputgrid");
                        //$this->page->registerObject($UniqueID,$inputfield);
                        $cell->Controls->add($inputfield);
                        $ControlListCellChildren[]=Array("class"=>"TActiveTextBox","id"=>$UniqueID,"OnCallback"=>"","CommandParameter"=>"");
                    }
                    $WorkRow->Cells[]=$cell;
                    $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$jj,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
                    $DDRowCounter++;
                    $ColumnCounter++;
                }
                fmod($ALTERNATING,2)==0?$WorkRow->setCssClass('listalternating'):$WorkRow->setCssClass('listnonealternating');
                $ALTERNATING++;
            }else{
                $WorkRowID="R".$this->sheetrow."G"; //new for grouping
                $this->resulttable->Rows[]=$WorkRow;
                $jj = 1;
                if($this->InputBericht==0) {
                    $ControlListCellChildren = array();
                    foreach($row as $value) {
                        $cell = new TActiveTableCell();
                        $cell->setID("R".$this->sheetrow."C".$jj);
                        $jj++;
                        $cell->EnableViewState = true;
                        $cell->Text = $value;
                        $cell->setCssClass('calculatedsumme');
                        $WorkRow->Cells[]=$cell;
                        $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>"R".$this->sheetrow."C".$jj,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
                    }
                }
            }
        $WorkRow->setID($WorkRowID);
        $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>$WorkRowID,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);
        $this->sheetrow++; //increment rowcounter
        }

        $ALTERNATING = 0;
        $labelcounter=0;
        foreach($ROWSPLAIN as $rowplain) {
            $ALTERNATING++;
            if($ALTERNATING==count($ROWSPLAIN)) {
                foreach($rowplain as $valueplain) {
                    $labelcounter==0?$labelcounter++:array_push($temparray,$valueplain*1);
                }
            }
        }
        $this->zwischenergebnisse[$name]=$temparray;

    }

    private function generateGraph($MyArray,$ObjID) {

        $ydata1 = array();
        $xdata = array();
        $width = array();
        $height = array();
        $ytitle = array($ObjID);
        $title = array("Periode");
        $title = implode(',', $title);
        $legend = array("Erfolgsgrafik");
        $legend = implode(',', $legend);

        $counter = count($MyArray);
        //$counter==1?$ii=0:$ii=1;
        //$ii += $this->GraphOffset-1;
        $ii = 0;

        foreach($this->Perioden AS $Periode){
                if($Periode<10000){
                    $xdata[] = $Periode;
                    $ydata1[] = $MyArray[$ii];
                    if($ii > 30) {
                            break;
                    }
                }
                $ii++;
        }

        $width[] = "600";
        $height[] = "250";
        $width = implode(',', $width);
        $height = implode(',', $height);

        $ydata1 = implode(',', $ydata1);
        $xdata = implode(',', $xdata);
        $ytitledata = implode(',', $ytitle);
        return $this->getRequest()->constructUrl('graph', 3, array( 'height' => $height, 'legend' => $legend, 'title' => $title,'width' => $width,'xdata' => $xdata, 'ydata1' => $ydata1, 'ytitle' => $ytitledata), false);
    }

    public function saveStrukturBericht($sender,$param) {
        $mySession = "";
        $mySession = UserFelderRecord::finder()->findAllBy_user_id($this->User->getUserId($this->User->Name));
        foreach($mySession as $myFieldRecord) {
            $arr_newValues = array(); //very important!!! otherwise we have values which dont exist
            $arr_MyFields = array();
            $uniqueID = $myFieldRecord->tuf_feldname;
            //lets start the saving
            //$UniqueID = 'xxx'.$inp_per_year.'xxx'.$inp_per_month.'xxx'.$inp_idta_struktur_type.'xxx'.$inp_idta_feldfunktion.'xxx'.$inp_idtm_struktur.'xxx'.$inp_idta_variante.'xxx';
            //$SaveString = $jahr."xxx".$monat."xxx".$local_ff."xxx".$local_id; definition of the string to pass
            $arr_MyFields = preg_split("/xxx/",$uniqueID);
            $tt_per_year = $arr_MyFields[1];
            $tt_per_month = $arr_MyFields[2];
            $tt_idta_struktur_type = $arr_MyFields[3];
            $tt_idta_feldfunktion = $arr_MyFields[4];
            $tt_idtm_struktur = $arr_MyFields[5];
            $tt_idta_variante = $arr_MyFields[6];
            
            //abrufen des aktuellen DB-Wertes
            $ExistingValue = WerteRecord::finder()->findBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$tt_idtm_struktur."' AND idta_feldfunktion = '".$tt_idta_feldfunktion."' AND w_jahr = '".$tt_per_year."' AND w_monat = '".$tt_per_month."' AND w_id_variante = '".$tt_idta_variante."' LIMIT 1");
            //formatieren, damit es mit dem inhalt aus der zelle vergleichbar wird
            //CHECKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK
            if(is_Object($ExistingValue)){
                $CompareValue = number_format($ExistingValue->w_wert, 2, '.', '');
            }else{
                $CompareValue = "0.00";
            }
            //here I start the magic server calculation;)
            if($CompareValue === $this->page->ACTPanel->FindControl($uniqueID)->Text){

            }else{
                $ObjSaver = new PFBackCalculator();
                $ObjSaver->setVariante($tt_idta_variante);
                $ObjSaver->setStartPeriod($tt_per_month);
                $ObjSaver->setStartNode($tt_idtm_struktur);
                //$SaveString = $tt_per_year."xxx".$tt_per_month."xxx".$tt_idta_feldfunktion."xxx".$tt_idtm_struktur;
                //$NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$tt_idtm_struktur."' AND idta_feldfunktion = '".$tt_idta_feldfunktion."' AND w_jahr = '".$tt_per_year."' AND w_monat = '".$tt_per_month."' AND w_id_variante = '".$tt_idta_variante."' LIMIT 1");
                $arr_newValues[$tt_idta_feldfunktion]=$this->page->ACTPanel->FindControl($uniqueID)->Text;
                $ObjSaver->setNewValues($arr_newValues);
                $ObjSaver->run();
                unset($ObjSaver);
            }
        }
        $sender->Text = "Saved";
    }

    public function hideRowGroup($sender,$param){
        $startValue = $sender->CommandParameter + 1;
        for($ii=$startValue;$ii<=100000;$ii++){
            $collapseRow = "R".$ii;
            if($this->resulttable->FindControl($collapseRow)){
                if($this->resulttable->FindControl($collapseRow)->Visible){
                    $this->resulttable->FindControl($collapseRow)->setVisible(false);
                    $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-expand.gif"):'';
                }else{
                    $this->resulttable->FindControl($collapseRow)->setVisible(true);
                    $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif"):'';
                }
            }else{
                break;
            }
        }
    }

    public function hideAllRows($sender,$param){
        $startValue = $sender->CommandParameter + 1;
        for($ii=$startValue;$ii<=100000;$ii++){
            $collapseRow = "R".$ii;
            if($this->resulttable->FindControl($collapseRow)){
                if($this->resulttable->FindControl($collapseRow)->Visible){
                    $this->resulttable->FindControl($collapseRow)->setVisible(false);
                    $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-expand.gif"):'';
                }else{
                    $this->resulttable->FindControl($collapseRow)->setVisible(true);
                    $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif"):'';
                }
            }else{
                $collapseRow = "R".$ii."G";
                if($this->resulttable->FindControl($collapseRow)){
                    ;
                }else{
                    break;
                }
            }
        }
    }

    public function hideColumnGroup($sender,$param){
        $startValue = $sender->CommandParameter + 1;
        for($ii=$startValue;$ii<=1000;$ii++){
            for($jj=1;$jj<=100;$jj++){
                $isGroupColumn = "R".$jj."C1G";
                if($this->resulttable->FindControl($isGroupColumn)){
                    if($this->resulttable->FindControl($isGroupColumn)->Visible){
                        $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-expand.gif"):'';                        
                    }else{
                        $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif"):'';
                    }
                }else{
                    $collapseColumn = "R".$jj."C".$ii;
                    if($this->resulttable->FindControl($collapseColumn)){ //hier muss noch eine pruefung hin, ob es eine group colum ist, dann direkt next...
                        if($this->resulttable->FindControl($collapseColumn)->Visible){
                            $this->resulttable->FindControl($collapseColumn)->setVisible(false);
                            $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-expand.gif"):'';                            
                        }else{
                            $this->resulttable->FindControl($collapseColumn)->setVisible(true);
                            $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif"):'';
                        }
                    }else{
                        break 0;
                    }
                }
            }
        }
    }
    
}
?>