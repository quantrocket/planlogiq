<?php

Prado::using('Application.app_code.PFCalculator');
Prado::using('Application.app_code.PFBackCalculator');
Prado::using('Application.3rdParty.Classes.PHPExcel');
Prado::using('Application.3rdParty.Classes.PHPExcel.Writer.*');

class WBK_StrukturBerichtViewer extends TPage {

    private $Periode = '10001';
    private $Perioden;
    private $Variante = '1';
    private $idta_struktur_bericht = 1;
    private $zwischenergebnisse = array();
    private $SinglePeriode = 0;
    private $InputBericht = 0;
    private $ReportPerioden = array();

    private $ReportOperator = array();  //der Operator fuer die jeweiligen Spalten

    private $ReportIDsLeft = array();
    private $ReportIDsRight = array();

    private $ReportIDsInString = array();
    private $ReportIDsInStringAll = "";
    private $GraphOffset = 0;
    private $idtm_struktur = 0;
    private $STRcounter=0;
    private $Nested = 1;

    private $workbook; //das komplette dokument
    private $sheet; //das aktuelle Sheet
    private $sheetrow = 2;
    private $sheetleftgap = 2; //der abstand von links
    private $ColumnArray = array(1=>"A",2=>"B",3=>"C",4=>"D",5=>"E",6=>"F",7=>"G",8=>"H",9=>"I",10=>"J",11=>"K",12=>"L",13=>"M",14=>"N",15=>"O",16=>"P",17=>"Q",18=>"R",19=>"S",20=>"T",21=>"U",22=>"V",23=>"W",24=>"X",25=>"Y",26=>"Z",27=>"AA",28=>"AB",29=>"AC",30=>"AD",31=>"AE",32=>"AF",33=>"AG",34=>"AH",35=>"AI",36=>"AJ");
    private $config=array('indent'=>true, 'output-xhtml'=>true);
    private $encoding='utf8';


    public function onPreInit($param) {

        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);

        parent::onPreInit($param);

        $this->workbook = new PHPExcel();

        $this->sheet = $this->workbook->getActiveSheet();        

        $this->sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT);
        $this->sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);



        if($this->Request['periode']!='') {
            $this->Periode = $this->Request['periode'];
        }

        if($this->Request['idtm_struktur']!='') {
            $this->idtm_struktur = $this->Request['idtm_struktur'];
        }else{
            $this->idtm_struktur = $this->user->getStartNode($this->user->getUserId($this->user->Name),"idtm_struktur");
            $this->idtm_struktur==''?$this->idtm_struktur=1:'';
        }


        if($this->Request['per_single']!='') {
            $this->SinglePeriode = $this->Request['per_single'];
        }
        if($this->Request['idta_variante']!='') {
            $this->Variante = $this->Request['idta_variante'];
        }
        if($this->Request['idta_struktur_bericht']!='') {
            $this->idta_struktur_bericht = $this->Request['idta_struktur_bericht'];
        }else{
            $this->idta_struktur_bericht = StrukturBerichtRecord::finder()->findBysb_startbericht(1)->idta_struktur_bericht;
        }

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

        $local_column=$this->sheetleftgap;
        $local_cell='';

        $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
        $this->sheet->setCellValue($local_cell,$mySubcatsCalculator->getCurrentPath($this->idtm_struktur));
            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->setSize(14);
            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->getColor()->setRGB('999900');
        $this->sheetrow++;

        //Adjusting the size
        $this->sheet->getColumnDimension($this->ColumnArray[$local_column])->setWidth(30);
        $local_column++;
        for($ii=$local_column;$ii<count($this->ColumnArray);$ii++){
            $this->sheet->getColumnDimension($this->ColumnArray[$ii])->setWidth(12);
        }
        
        //here we build the statements nthat a stored inside the database
        $Report = StrukturBerichtRecord::finder()->findByPK($this->idta_struktur_bericht);
        $this->sheet->setTitle($Report->pivot_struktur_name);

        $ReportColumns = StrukturBerichtSpaltenRecord::finder()->findAllByidta_struktur_bericht($this->idta_struktur_bericht);

        $idta_struktur_bericht_spalten=array();

        foreach($ReportColumns as $ReportColumn) {
            $this->GraphOffset++;
            $this->InputBericht = $ReportColumn->sbs_input;
            //$this->StrukturBerichtSaveButton->Visible = $this->InputBericht;

            //wenn die Variante fix uebergeben wurde
            if($ReportColumn->sbs_idta_variante_fix) {
                $this->Variante = $ReportColumn->idta_variante;
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
                ${$SingleRow->idta_struktur_bericht_zeilen."RObj"}->setInputReport($this->InputBericht); //hier immer auf 1, da sonst die formate nicht passen...
                //Perioden muss vor CALCCOLUMNS kommen, sonst ergebnis nicht richtig
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
                    //$this->draw_graph(${$SingleRow->idta_struktur_bericht_zeilen."RObj"},$SingleRow->sbz_label,$SingleRow->sbz_spacer_label);
                }
            }
        }
        $this->generate('Excel5','planlogIQ');
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


            $local_column=$this->sheetleftgap;
            $local_cell='';

            $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
            $local_column++;
            $this->sheet->setCellValue($local_cell,$label);
            $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->getStartColor()->setRGB('898989');
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->setSize(10);
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->getColor()->setRGB('ffffff');
    
            $counter = count ($tempResult);
            //$counter += $local_column; //hier kann es auch verkehrt sein... evtl minus eins
            for($ii=0;$ii<$counter;$ii++) {
                $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
                $local_column++;
                $this->sheet->setCellValue($local_cell,number_format($tempResult[$ii], 0, '.', ''));
                $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                    $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $tempResult[$ii]<0?$this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->getStartColor()->setRGB('ff4444'):$this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->getStartColor()->setRGB('898989');
                    $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->setSize(10);
                    $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->getColor()->setRGB('ffffff');
            }
            //$FirstRow->setCssClass('calculated');
            $this->sheetrow++;
            $this->zwischenergebnisse[$label]=$tempResult;
        }
    }

    private function load_header($PFCALCULATOR) {
        $PFCALCULATOR->buildTitle("Zeit");
        $local_column=$this->sheetleftgap;
        $local_cell='';
        foreach($PFCALCULATOR->getTitle() AS $value) {
            $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->getStartColor()->setRGB('565656');
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->setSize(11);
                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->getColor()->setRGB('FFFFFF');
            $local_column++;
            $mylabel = explode('x',$value);
            $this->sheet->setCellValue($local_cell,$mylabel[0]);
        }
        $this->sheetrow++;
    }

    private function draw_spacer($PFCALCULATOR,$title="Graph") {
        $local_column=$this->sheetleftgap;
        $local_cell='';
        $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
        $rowspan = 0;
        $rowspan = $PFCALCULATOR->NumberOfColumns+$this->sheetleftgap;
        $until_cell = $this->ColumnArray[$rowspan].$this->sheetrow;
        $this->sheet->mergeCells($local_cell.':'.$until_cell);
        $this->sheet->setCellValue($local_cell,$title);
            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->getStartColor()->setRGB('9a9a9a');
            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->setSize(10);
            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->getColor()->setRGB('FFFFFF');
        $this->sheetrow++;
    }

    private function draw_cells($PFCALCULATOR,$name="Summe",$details=1) {
        $PFCALCULATOR->executeSQLNOTITLE($name,$details);
        $PFCALCULATOR->FormatWerte();
        $ROWS = $PFCALCULATOR->getValues();
        $ROWSPLAIN = $PFCALCULATOR->getPlainValues();
        $ALTERNATING = 0;

        $temparray = array(); //hier speicher ich die werte fuer das zwischenergebnis

        if(count($ROWS)>0){
            foreach($ROWS as $row) {

                $ALTERNATING++;
                $RowCounter=1;
                //hier bauen wir die einzelnen Zeilen
                if($ALTERNATING<count($ROWS) AND count($row)>0) {
                    $local_column=$this->sheetleftgap;
                    $local_cell='';
                    foreach($row as $value) {
                        $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
                        $local_column++;
                        if($this->InputBericht==0 OR $RowCounter==1) {
                            if($this->InputBericht==1) {
                                $tmpText = array();
                                $tmpText = preg_split("/xxx/",$value);
                                $this->sheet->setCellValue($local_cell,$tmpText[1]);
                            }else {
                                $this->sheet->setCellValue($local_cell,preg_replace("/\./","",$value));
                                $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                            }
                        }else{
                            if($this->InputBericht==0){
                                $this->sheet->setCellValue($local_cell,preg_replace("/\./","",$value));
                                $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                            }else{
                                $this->sheet->setCellValue($local_cell,preg_replace("/\./",",",$value));
                                $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                            }
                        }
                        //fmod($ALTERNATING,2)==0?$WorkRow->setCssClass('listalternating'):$WorkRow->setCssClass('listnonealternating');
                        $RowCounter++;                        
                    }
                }else {
                    $local_column=$this->sheetleftgap;
                    $local_cell='';
                    if($this->InputBericht==0 AND count($row)>0) {
                        foreach($row as $value) {
                            $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
                            $local_column++;
                            $this->sheet->setCellValue($local_cell,preg_replace("/\./","",$value));
                            $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->getStartColor()->setRGB('ccccff');
                                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->setSize(10);
                                $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->getColor()->setRGB('000000');
                            //$cell->setCssClass('calculatedsumme');
                        }                        
                    }
                }
                $this->sheetrow++;
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
            $DetailChecker = 0;
        }else{
            $DetailChecker = 1;
        }
        $PFCALCULATOR->executeDimensionSQL($name,$DetailChecker,$this->InputBericht); //davor this->inputbericht...
        $ROWS = $PFCALCULATOR->getValues();
        $ROWSPLAIN = $PFCALCULATOR->getPlainValues();
        $ALTERNATING = $this->sheetrow;
        $ROWCOUNTER = 0;

        $temparray = array(); //hier speicher ich die werte fuer das zwischenergebnis

        foreach($ROWS as $row) {
            $ALTERNATING++;
            $ROWCOUNTER++;
            $DDRowCounter=1;
            //hier bauen wir die einzelnen Zeilen
            if($this->InputBericht==1) {
                $walkerCheck = count($ROWS)>2?count($ROWS)-1:count($ROWS);
            }else {
                $walkerCheck = count($ROWS);
            }
            if($ROWCOUNTER<$walkerCheck OR ($DetailChecker==0 AND $this->InputBericht==0) OR $this->InputBericht==1) {
                $local_column=$this->sheetleftgap;
                $local_cell='';
                foreach($row as $value) {
                    $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
                    $local_column++;
                    $cell = new TTableCell();
                    $cell->EnableViewState = false;
                    if($DDRowCounter==1) {
                        if($this->InputBericht==1) {
                            $tmpText = array();
                            $tmpText = preg_split("/xxx/",$value);
                            $this->sheet->setCellValue($local_cell,$tmpText[1]);
                        }else {
                            if(preg_match("/xxx/",$value)){
                                $tmpText = array();
                                $tmpText = preg_split("/xxx/",$value);
                                $this->sheet->setCellValue($local_cell,preg_replace("/\./","",$tmpText[1]));
                            }else{
                                $this->sheet->setCellValue($local_cell,preg_replace("/\./","",$value));
                                $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                            }
                        }
                    }else {
                        if($this->InputBericht==0){
                            $this->sheet->setCellValue($local_cell,preg_replace("/\./","",$value));
                            $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                        }else{
                            $this->sheet->setCellValue($local_cell,preg_replace("/\./",",",$value));
                            $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                        }
                    }
                    //fmod($ALTERNATING,2)==0?$WorkRow->setCssClass('listalternating'):$WorkRow->setCssClass('listnonealternating');
                    $DDRowCounter++;
                }                
            }else {
                if($this->InputBericht==0) {
                    $local_column=$this->sheetleftgap;
                    $local_cell='';
                    foreach($row as $value) {
                        $local_cell = $this->ColumnArray[$local_column].$this->sheetrow;
                        $local_column++;
                        $this->sheet->setCellValue($local_cell,preg_replace("/\./","",$value));
                        $this->sheet->getStyle($local_cell)->getNumberFormat()->setFormatCode("_-* #.##0_-;-* #.##0_-;_-* "-"_-;_-@_-");
                            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFill()->getStartColor()->setRGB('ccccff');
                            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->setSize(10);
                            $this->workbook->getActiveSheet()->getStyle($local_cell)->getFont()->getColor()->setRGB('000000');
                        //$cell->setCssClass('calculatedsumme');
                    }
                }
            }
            $this->sheetrow++;
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

    public function generate($format = "Excel5", $docName = "Tabelle"){
        switch($format){
            case 'Excel2007' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/Excel2007.php';
                $writer = new PHPExcel_Writer_Excel2007($this->workbook);
                $ext  = 'xlsx';
                $header = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                //supprime le pr�-calcul
                $writer->setPreCalculateFormulas(false);
                break;
             case 'Excel2003' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/Excel2007.php';
                $writer = new PHPExcel_Writer_Excel2007($this->workbook);
                $writer->setOffice2003Compatibility(true);
                $ext  = 'xlsx';
                //supprime le pr�-calcul
                $header = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                $writer->setPreCalculateFormulas(false);
                break;
            case 'Excel5' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/Excel5.php';
                $writer = new PHPExcel_Writer_Excel5($this->workbook);
                $ext = 'xls';
                $header = 'application/vnd.ms-excel';
                break;
            case 'CSV' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/CSV.php';
                $writer  = new PHPExcel_Writer_CSV($this->workbook);
                $writer->setDelimiter(",");//l'op�rateur de s�paration est la virgule
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'csv';
                break;
            case 'PDF' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/PDF.php';
                $writer  = new PHPExcel_Writer_PDF($this->workbook);
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'pdf';
                break;
            case 'HTML' :
                include dirname(__FILE__).'/../../../3rdParty/Classes/PHPExcel/Writer/HTML.php';
                $writer  = new PHPExcel_Writer_HTML($this->workbook);
                $writer->setSheetIndex(0);//Une seule feuille possible
                $ext = 'html';
                break;

        }

        $this->getResponse()->appendHeader("Content-Type:".$header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$docName.'.'.$ext);

        $writer->save('php://output');
        exit;
      }

}
?>