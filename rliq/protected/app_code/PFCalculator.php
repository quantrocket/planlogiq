<?php
class PFCalculator extends TPage {

    private $DBConnection;
    public $Perioden = array();
    public $Varianten = array();
    public $Stammdatensicht = 1;
    public $FeldFunktion = 0;
    private $StrukturType = 0;
    private $StartNode = 1;
    public $ChildrenNodes = array();
    public $ChildrenNodesString = "";
    private $WerteListe = array();
    private $WerteListeFormatted = array();
    private $mySQL = '';
    private $StrukturPfad = '';
    private $WerteTitle = array();
    private $Operator = 'SUM';
    private $PivotCondition=array();
    public $dimension = "0";
    public $GLOBALVARIANTE = '';
    private $calcOB = 0;
    private $calcOBID = 0;
    private $TTWERTE = array();
    private $FunktionsFelder=array();
    private $idtm_struktur_to = 0;
    public $ColumnObj;
    public $NumberOfColumns;

    //hier kommen die beiden variablen fuer den subfilter und den gemeinsamen filter
    public $ReportOperator = array();
    public $ReportIDsLeft = array();
    public $ReportIDsRight = array();
    public $ReportIDsInString = array();
    public $ReportIDsInStringAll = "";

    //hier kommt der spezialwert, in dem ich die einzelnen spalten beschreibe
    public $idta_struktur_bericht_spalten=array();

    private $allowedIDs=array(); //inside this array we store all allowed ids the user can see
    private $subcats = array();//list of all subcats

    private $InputReport=0; //here is defined, wheater the report is used for input or not...

    private $UserStartId = 1;

    //hier speicher ich den Tatsächlichen Feldtyp, damit ich nicht dauernd eine neue Verbindung auf die DB setzen muss
    private $FeldfunktionType = array();

    //die SQL Strings...
    private $mySQLSTR = "";
    private $AVGmySQLSTR= "";


    public function PFCalculator() {
        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_struktur"));
        $this->StartNode=$this->UserStartId;
    }

    public function setDBConnection($DBConnection) {
        $this->DBConnection = $DBConnection;
    }

    public function load_all_cats() {
        $rows = StrukturRecord::finder()->findAll();
        foreach($rows as $row) {
            $this->subcats[$row->parent_idtm_struktur][]=$row->idtm_struktur;
        }
    }

    public function getsubcats() {
        return $this->subcats;
    }

    public function setsubcats($subcats) {
        $this->subcats = $subcats;
        $this->subCategory_list($this->subcats, $this->UserStartId);//the two must be replaced with the value from the usermanager
    }

    private function subCategory_list($subcats,$catID) {
        $this->allowedIDs[] = $catID; //id des ersten Startelements...
        if(array_key_exists($catID,$subcats)) {
            foreach($subcats[$catID] as $subCatID) {
                $this->allowedIDs[] = $this->subCategory_list($subcats, $subCatID);
            }
        }
    }

    public function calcColumns() {
        $this->NumberOfColumns = 0;
        foreach($this->ColumnObj AS $Column) {
            foreach($this->Perioden[$Column->idta_struktur_bericht_spalten] AS $Periode){
                $this->NumberOfColumns++;
            }
        }
    }

    public function setColumns($ColumnRecords){
        $this->ColumnObj = $ColumnRecords;
    }

    public function setInputReport($IsInputReport) {
        $this->InputReport = $IsInputReport;
    }

    public function saveValues($idtm_struktur,$idta_struktur_type) {

        $jahr = 0;
        $monat = 0;

        //hier werden jetzt die einzelnen Werte geladen
        foreach ($this->Perioden AS $tmpPeriode) {
            if($tmpPeriode[0]>10000) {
                $jahr = $tmpPeriode[0];
                $monat= $tmpPeriode[0];
            }else {
                $jahr = $this->getYearByMonth($tmpPeriode[0]);
                $monat = $tmpPeriode[0];
            }
            $SQL = "SELECT * FROM ta_feldfunktion WHERE idta_struktur_type = '".$idta_struktur_type."'";
            $Fields = FeldfunktionRecord::finder()->findAllBySQL($SQL);
            foreach($Fields As $Field) {
                $myUniquID=$jahr.'xxx'.$monat.'xxx'.$Field->idta_feldfunktion.'xxx'.$idtm_struktur;
                $NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$idtm_struktur."' AND idta_feldfunktion = '".$Field->idta_feldfunktion."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."'LIMIT 1");
                $NEWWerteRecord->w_wert = $this->TTWERTE[$myUniquID];
                count($NEWWerteRecord)==1?$NEWWerteRecord->save():'';
            }
        }
    }

    public function setVariante($idta_variante) {
        $this->GLOBALVARIANTE = $idta_variante;
    }

    public function setVarianten($varianten_array) {
        $this->$Varianten = $varianten_array;
    }

    public function setOperator($NewOperator) {
        $NewOperator!=''?$this->Operator = $NewOperator:'';
    }

    public function findFirstCompany($idtm_struktur) {
        $StartNode = StrukturRecord::finder()->findByPK($idtm_struktur);
        if($StartNode->idta_struktur_type == 1) {
            $idtm_struktur_company = $StartNode->idtm_struktur;
            return $idtm_struktur_company;
        }else {
            if($this->check_forParents($StartNode)) {
                $SQL = "SELECT * FROM tm_struktur WHERE idtm_struktur = '".$Node->parent_idtm_struktur."'";
                $Records = StrukturRecord::finder()->findAllBySQL($SQL);
                foreach($Records AS $Record) {
                    $this->findFirstCompany($Record->idtm_struktur);
                }
            }else {
                return 1;
            }
        }

    }

    public function getCurrentPath($idtm_struktur) {
        $this->StrukturPfad="";
        $this->StartNode=$idtm_struktur;
        $Node=StrukturRecord::finder()->findByidtm_struktur($this->StartNode);
        if(count($Node)>1){
            $this->buildCurrentPath(1);
        }else{
            $this->buildCurrentPath($Node);
        }
        return $this->StrukturPfad;
    }

    public function buildCurrentPath($Node) {
        if(!$this->check_forParents($Node)) {
            $this->StrukturPfad .=" > ".$Node->struktur_name;
        }else {
            $SQL = "SELECT * FROM tm_struktur WHERE idtm_struktur = '".$Node->parent_idtm_struktur."'";
            $Records = StrukturRecord::finder()->findAllBySQL($SQL);
            foreach($Records AS $Record) {
                $this->buildCurrentPath($Record);
            }
            $this->StrukturPfad .=" > ".$Node->struktur_name;
        }
    }

    public function destroy() {
        $this->DBConnection->Active = false;
    }

    public function executeSQL($label="Name",$detail=1) {
        if($detail>0) {
            $this->buildSQL();
            $command = $this->DBConnection->createCommand($this->mySQL);
            $dataReader=$command->query();
            $this->WerteListe = $dataReader->readAll();
            $this->FormatWerte();
            $this->buildTitle($label);
            $this->addTitle();
        }
        $this->buildSUMSQL();
        $command = $this->DBConnection->createCommand($this->mySQL);
        $dataReader=$command->query();
        $this->WerteListe = $dataReader->readAll();
    }

    public function executeDimensionSQL($label="Name",$detail=0,$inputreport=0) {
        if($detail>0 OR $inputreport>0) {
            $this->buildDimensionSQL();
            $command = $this->DBConnection->createCommand($this->mySQL);
            $dataReader=$command->query();
            $this->WerteListe = $dataReader->readAll();
            $this->FormatWerte();
        }
        if($inputreport==0){
            $this->buildDimensionSUMSQL($label);
            $command = $this->DBConnection->createCommand($this->mySQL);
            $dataReader=$command->query();
            $this->WerteListe = $dataReader->readAll();
            $this->FormatWerte();
        }
    }

    public function executePIVOTSQL($label="Name",$detail=1) {
        if($detail>0) {
            if(count($this->ChildrenNodes) > 0) {
                foreach($this->ChildrenNodes As $Node) {
                    $this->buildPIVOTSQL($Node);
                    //print_r($this->mySQL);
                    $command = $this->DBConnection->createCommand($this->mySQL);
                    $dataReader=$command->query();
                    $temp = $dataReader->readAll();
                    if(count($temp)>0) {
                        $this->WerteListe = $temp;
                    }else {
                        $tmpArray=array();
                        $myArray=array();
                        $myArray['Name']=$Node->stammdaten_name;
                        foreach($this->Perioden As $Periode) {
                            $myArray[$Periode[0]]=0;
                        }
                        array_push($tmpArray,$myArray);
                        $this->WerteListe = $tmpArray;
                    }
                    $this->FormatWerte();
                }
            }
        //$this->buildTitle($label);
        //$this->addTitle();
        }
        $this->buildSUMPIVOTSQL();
        $command = $this->DBConnection->createCommand($this->mySQL);
        $dataReader=$command->query();
        $temp = $dataReader->readAll();
        if(count($temp)>0) {
            $this->WerteListe = $temp;
        }else {
            $tmpArray=array();
            $myArray=array();
            $myArray['Name']='Total';
            foreach($this->Perioden As $Periode) {
                $myArray[$Periode[0]]=0;
            }
            array_push($tmpArray,$myArray);
            $this->WerteListe = $tmpArray;
        }
    }

    public function executeSQLNOTITLE($label="Summe",$detail=1) {
        if($detail>0) {
            $this->buildSQL();
            $command = $this->DBConnection->createCommand($this->mySQL);            
            $dataReader=$command->query();
            $this->WerteListe = $dataReader->readAll();
            $this->FormatWerte();
        }
        $this->buildSUMSQL($label);
        $commandsum = $this->DBConnection->createCommand($this->mySQL);
        $dataReadersum=$commandsum->query();
        $this->WerteListe = $dataReadersum->readAll();
        //$this->FormatWerte();
    }

    public function executeSUMSQL($label="Summe") {
        $this->buildSUMSQL($label);
        $command = $this->DBConnection->createCommand($this->mySQL);
        $dataReader=$command->query();
        $this->WerteListe = $dataReader->readAll();
        //$this->FormatWerte();
    }

    public function getValues() {
        return $this->WerteListeFormatted;
    }

    public function getPlainValues() {
        return $this->WerteListe;
    }

    public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

    public function setStartNode($idtm_struktur,$Nested=0) {
        $this->ChildrenNodes=array();
        if(in_array($idtm_struktur,$this->allowedIDs) AND $idtm_struktur != '') {
            //print_r($this->ChildrenNodes);
            $this->StartNode = $idtm_struktur;
            if($Nested==0){
                $Node=StrukturRecord::finder()->findByidtm_struktur($this->StartNode);
                $this->getChildren($Node);
            }
        }else {
            $this->StartNode = $this->UserStartId;
            if($Nested==0){
                $Node=StrukturRecord::finder()->findByidtm_struktur($this->StartNode);
                $this->getChildren($Node);
            }
        }
    }

    public function loadDimension($Node) {
        $this->ChildrenNodes = $this->get_PivotChildren($Node);
    }

    private function getChildren($Node) {
        if($this->check_forChildren($Node)) {
            $SQL = "SELECT idtm_struktur,parent_idtm_struktur FROM tm_struktur WHERE parent_idtm_struktur = '".$Node->idtm_struktur."'";
            $Records = StrukturRecord::finder()->findAllBySQL($SQL);
            foreach($Records AS $Record) {
                $this->getChildren($Record);
            }
        }else {
            $this->ChildrenNodes[]=$Node->idtm_struktur;
        }
    }

    public function check_forChildren($Node) {
        $Result = count(StrukturRecord::finder()->findByparent_idtm_struktur($Node->idtm_struktur));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
    }

    public function setFeldFunktion($idta_feldfunktion) {
        $this->FeldFunktion = $idta_feldfunktion;
        $FFRecord = FeldfunktionRecord::finder()->findByPK($this->FeldFunktion);
        if(count($FFRecord)==1){
            $this->StrukturType = $FFRecord->idta_struktur_type;
        }else{
            $this->StrukturType = 0;
        }
    }

    public function setStartPeriod($Periode,$SinglePeriode=0) {
        if(count($this->Perioden)>0){
            $this->Perioden = array();
        }
        $Result = PeriodenRecord::finder()->findByper_Intern($Periode);
        array_push($this->Perioden, array($Result->per_intern,$Result->per_extern));
        if($SinglePeriode==0) {
            $Records = PeriodenRecord::finder()->findAllByparent_idta_perioden($Result->idta_perioden);
            foreach($Records As $Record) {
                array_push($this->Perioden, array($Record->per_intern,$Record->per_extern));
            }
        }
    }

    public function buildSQLStrukturFilter($AggrAbfrage=0,$label=""){
        $MYSCounter = 0;
        if(count($this->ReportIDsLeft)>=1){
            //das brauche ich als Hilfe, damit die einschraenkung wieder passt
            $AggrAbfrage==1?$this->AVGmySQLSTR = "SELECT '".$label."' AS Name":''; //NEW AGGREGATION
            $this->mySQLSTR = '';
            $left_min = 0;
            $right_max = 0;
            foreach($this->ColumnObj AS $Column) {
                $tmp_variante = $this->Varianten[$Column->idta_struktur_bericht_spalten];
                if(!$Column->sbs_idta_variante_fix) {
                    $tmp_variante = $this->GLOBALVARIANTE;
                }
                foreach($this->Perioden[$Column->idta_struktur_bericht_spalten] AS $Periode) {
                    $this->mySQL .= ",".$this->ReportOperator[$Column->idta_struktur_bericht_spalten]."(CASE WHEN w_monat='".$Periode[0]."' AND w_id_variante = '".$tmp_variante."' ";
                    if(count($this->ReportIDsLeft[$Column->idta_struktur_bericht_spalten]) > 0) {
                          if($MYSCounter==0){
                                $left_min = $this->ReportIDsLeft[$Column->idta_struktur_bericht_spalten];
                                $right_max = $this->ReportIDsRight[$Column->idta_struktur_bericht_spalten];
                                $MYSCounter++;
                          }else{
                                $this->ReportIDsLeft[$Column->idta_struktur_bericht_spalten]<$left_min?$left_min=$this->ReportIDsLeft[$Column->idta_struktur_bericht_spalten]:'';
                                $this->ReportIDsRight[$Column->idta_struktur_bericht_spalten]>$right_max?$right_max=$this->ReportIDsRight[$Column->idta_struktur_bericht_spalten]:'';
                          }
                        $this->mySQL .= "AND tm_struktur.struktur_lft BETWEEN ".$this->ReportIDsLeft[$Column->idta_struktur_bericht_spalten]." AND ".$this->ReportIDsRight[$Column->idta_struktur_bericht_spalten]." ";
                    }
                    $this->mySQL .= "THEN w_wert ELSE null END) AS '".$Periode[0].'R'.$Column->idta_struktur_bericht_spalten."' ";
                    //I need to build this syntax in order to get the right sum...
                    $AggrAbfrage==1?$this->AVGmySQLSTR .= ", SUM(".$Periode[0].'R'.$Column->idta_struktur_bericht_spalten.") AS ".$Periode[0].'R'.$Column->idta_struktur_bericht_spalten." ":'';
                }
            }
            $this->mySQLSTR .= "AND (tm_struktur.struktur_lft BETWEEN ".$left_min." AND ".$right_max.") ";
        }else{
            //das brauche ich als Hilfe, damit die einschraenkung wieder passt
            $this->mySQLSTR = '';
            $this->mySQLSTR .= "AND tm_struktur.idtm_struktur IN (".$this->ReportIDsInStringAll.") ";
            foreach($this->ColumnObj AS $Column) {
                $tmp_variante = $this->Varianten[$Column->idta_struktur_bericht_spalten];
                if(!$Column->sbs_idta_variante_fix) {
                    $tmp_variante = $this->GLOBALVARIANTE;
                }
                foreach($this->Perioden[$Column->idta_struktur_bericht_spalten] AS $Periode) {
                    $this->mySQL .= ",".$this->Operator."(CASE WHEN w_monat='".$Periode[0]."' AND w_id_variante = '".$tmp_variante."' ";
                    if(count($this->ChildrenNodes[$Column->idta_struktur_bericht_spalten]) > 0) {
                        $this->mySQL .= "AND tm_struktur.idtm_struktur IN (".$this->ReportIDsInString[$Column->idta_struktur_bericht_spalten].") ";
                    }
                    $this->mySQL .= "THEN w_wert ELSE null END) AS '".$Periode[0].'R'.$Column->idta_struktur_bericht_spalten."' ";
                    //I need to build this syntax in order to get the right sum...
                    $AggrAbfrage==1?$this->AVGmySQLSTR .= ", SUM(".$Periode[0].'R'.$Column->idta_struktur_bericht_spalten.") AS ".$Periode[0].'R'.$Column->idta_struktur_bericht_spalten." ":'';
                }
            }
        }
    }

    public function buildSQL() {
        if($this->InputReport==0) {
            $this->mySQL = "SELECT struktur_name AS Name ";
        }else {
            $this->mySQL = "SELECT CONCAT('xxx',struktur_name,'xxx',tm_struktur.idtm_struktur,'xxx') AS Name ";
        }

        $this->buildSQLStrukturFilter();

        $this->mySQL .= "FROM tt_werte INNER JOIN tm_struktur on tm_struktur.idtm_struktur = tt_werte.idtm_struktur ";
        $this->mySQL .= "WHERE idta_struktur_type = '".$this->StrukturType."' AND idta_feldfunktion = '".$this->FeldFunktion."' ";        
        $this->mySQL.= $this->mySQLSTR;
        $this->mySQL .= "GROUP BY Name";
        $this->InputReport==1?$this->mySQL.=",tm_struktur.idtm_struktur":'';
    }

    public function buildDimensionSQL() {
        if($this->InputReport==0) {
            $this->mySQL = "SELECT struktur_name AS Name ";
        }else {
            $this->mySQL = "SELECT CONCAT('xxx',struktur_name,'xxx',tm_struktur.idtm_struktur,'xxx') AS Name ";
        }
       
        $this->buildSQLStrukturFilter();

        $this->mySQL .= "FROM tt_werte INNER JOIN tm_struktur on tm_struktur.idtm_struktur = tt_werte.idtm_struktur ";
        $this->mySQL .= "WHERE idta_struktur_type = '".$this->StrukturType."' AND idta_feldfunktion = '".$this->FeldFunktion."' ";
        $this->mySQL .= "AND ";
        $this->mySQL .= "w_dimkey LIKE '%x".$this->dimension."x%' ";
        $this->mySQL.= $this->mySQLSTR;
        $this->mySQL .= "GROUP BY Name";
        $this->InputReport==1?$this->mySQL.=",tm_struktur.idtm_struktur":'';
    }

    public function buildDimensionSUMSQL($label) {
        //$this->mySQL = "SELECT '".$label."' AS Name ";
        $this->mySQL = "SELECT struktur_name AS Name ";//changed this to get other aggregations to work
        
        $this->buildSQLStrukturFilter(1,$label);

        $this->mySQL .= "FROM tt_werte INNER JOIN tm_struktur on tm_struktur.idtm_struktur = tt_werte.idtm_struktur ";
        $this->mySQL .= "WHERE idta_struktur_type = '".$this->StrukturType."' AND idta_feldfunktion = '".$this->FeldFunktion."' ";
        $this->mySQL .= "AND ";
        $this->mySQL .= "w_dimkey LIKE '%x".$this->dimension."x%' ";
        $this->mySQL .= $this->mySQLSTR;
        $this->mySQL .= "GROUP BY Name";
        $this->AVGmySQLSTR .= "FROM (".$this->mySQL.") AS tempTabelle";
        $this->mySQL = $this->AVGmySQLSTR;
    }

    public function buildStructureCollectorSQL($idtm_struktur_to) {
        $this->mySQL = "SELECT tm_struktur_tm_struktur.idtm_struktur_to AS Name ";
        foreach($this->Perioden As $Periode) {
            $this->mySQL .= ", ".$this->Operator."(CASE WHEN w_monat='".$Periode[0]."' THEN w_wert ELSE 0 END) AS '".$Periode[0]."' ";
        }
        $this->mySQL .= "FROM tt_werte INNER JOIN tm_struktur_tm_struktur ON tm_struktur_tm_struktur.idtm_struktur_from = tt_werte.idtm_struktur ";
        //$this->mySQL .= "INNER JOIN tm_struktur ON tm_struktur_tm_struktur.idtm_struktur_from = tm_struktur.idtm_struktur ";
        $this->mySQL .= "WHERE tm_struktur_tm_struktur.idtm_struktur_to = '".$idtm_struktur_to."' AND w_id_variante = '".$this->GLOBALVARIANTE."' ";
        if(count($this->FeldFunktion) > 0) {
            $this->mySQL .= "AND (";
            $counter=0;
            foreach($this->FeldFunktion As $Node) {
                $counter==0?$this->mySQL .= "tt_werte.idta_feldfunktion = '".$Node."' ":$this->mySQL .= "OR tt_werte.idta_feldfunktion = '".$Node."' ";
                $counter++;
            }
            $this->mySQL .= ") ";
        }
        if(count($this->ChildrenNodes) > 0) {
            $this->mySQL .= "AND (";
            $counter=0;
            foreach($this->ChildrenNodes As $Node) {
                $counter==0?$this->mySQL .= "tm_struktur_tm_struktur.idtm_struktur_from = '".$Node."' ":$this->mySQL .= "OR tm_struktur_tm_struktur.idtm_struktur_from = '".$Node."' ";
                $counter++;
            }
            $this->mySQL .= ") ";
        }
        $this->mySQL .= "GROUP BY Name LIMIT 1";
    }

    public function buildPivotCondition($PivotString) {
        $this->PivotCondition=explode("xx",$PivotString);
    }

    public function buildPIVOTSQL($Node) {
        $this->mySQL = "SELECT '".$Node->stammdaten_name."' AS Name ";
        foreach($this->Perioden As $Periode) {
            $this->mySQL .= ",".$this->Operator."(CASE WHEN w_monat='".$Periode[0]."' THEN w_wert ELSE 0 END) AS '".$Periode[0]."' ";
        }
        $this->mySQL .= "FROM tt_werte a INNER JOIN tm_struktur b ON b.idtm_struktur = a.idtm_struktur ";
        $this->mySQL .= "INNER JOIN tm_stammdaten c ON b.idtm_stammdaten = c.idtm_stammdaten ";
        $this->mySQL .= "WHERE idta_feldfunktion = '".$this->FeldFunktion."' ";
        if(count($this->PivotCondition)>0) {
            $this->mySQL .= "AND ";
            $counter=0;
            foreach($this->PivotCondition AS $pvc) {
                $counter==0?$this->mySQL .= "w_dimkey LIKE '%x".$pvc."x%' ":$this->mySQL .= "AND w_dimkey LIKE '%x".$pvc."x%' ";
                $counter++;
            }
        }
        $this->mySQL .= "AND ";
        $this->mySQL .= "w_dimkey LIKE '%x".$Node->idtm_stammdaten."x%' AND w_id_variante = '".$this->GLOBALVARIANTE."' ";
        $this->mySQL .= "GROUP BY Name";
    }

    public function buildSUMPIVOTSQL() {
        $this->mySQL = "SELECT 'Total' AS Name ";
        foreach($this->Perioden As $Periode) {
            $this->mySQL .= ",".$this->Operator."(CASE WHEN w_monat='".$Periode[0]."' THEN w_wert ELSE 0 END) AS '".$Periode[0]."' ";
        }
        $this->mySQL .= "FROM tt_werte a INNER JOIN tm_struktur b ON b.idtm_struktur = a.idtm_struktur ";
        $this->mySQL .= "INNER JOIN tm_stammdaten c ON b.idtm_stammdaten = c.idtm_stammdaten ";
        $this->mySQL .= "INNER JOIN ta_stammdaten_group d ON c.idta_stammdaten_group = d.idta_stammdaten_group ";
        $this->mySQL .= "WHERE idta_feldfunktion = '".$this->FeldFunktion."' ";
        if(count($this->PivotCondition)>0) {
            $this->mySQL .= "AND ";
            $counter=0;
            foreach($this->PivotCondition AS $pvc) {
                $counter==0?$this->mySQL .= "w_dimkey LIKE '%x".$pvc."x%' ":$this->mySQL .= "AND w_dimkey LIKE '%x".$pvc."x%' ";
                $counter++;
            }
        }
        if(count($this->ChildrenNodes) > 0) {
            $this->mySQL .= "AND (";
            $counter=0;
            foreach($this->ChildrenNodes As $Node) {
                $counter==0?$this->mySQL .= "w_dimkey LIKE '%x".$Node->idtm_stammdaten."x%' ":$this->mySQL .= "OR w_dimkey LIKE '%x".$Node->idtm_stammdaten."x%' ";
                $counter++;
            }
            $this->mySQL .= ") ";
        }
        $this->mySQL .= "AND w_id_variante = '".$this->GLOBALVARIANTE."' GROUP BY d.idta_stammdaten_group";
    }

    public function buildSUMSQL($label="Summe") {
        //$this->mySQL = "SELECT '".$label."' AS Name ";
        $this->mySQL = "SELECT struktur_name AS Name ";//changed this to get other aggregations to work
        
        $this->buildSQLStrukturFilter(1,$label);
        
        $this->mySQL .= "FROM tt_werte INNER JOIN tm_struktur on tm_struktur.idtm_struktur = tt_werte.idtm_struktur ";
        $this->mySQL .= "WHERE idta_struktur_type = '".$this->StrukturType."' AND idta_feldfunktion = '".$this->FeldFunktion."' ";
        $this->mySQL.=  $this->mySQLSTR;
        $this->mySQL .= "GROUP BY Name";
        $this->AVGmySQLSTR .= "FROM (".$this->mySQL.") AS tempTabelle";
        $this->mySQL =  $this->AVGmySQLSTR;
    }

    public function FormatWerte($Excel=0) {
        foreach ($this->WerteListe as $row) {
            $temparray = array();
            array_push($temparray,$row['Name']);
            if(count($this->ColumnObj)>0) {
                foreach($this->ColumnObj AS $Column){
                    foreach($this->Perioden[$Column->idta_struktur_bericht_spalten] As $Periode) {
                        if($this->InputReport==0) {
                            if($Excel==0){
                                array_push($temparray,number_format($row[$Periode[0].'R'.$Column->idta_struktur_bericht_spalten], 0, ',', '.'));
                            }else{
                                array_push($temparray,number_format($row[$Periode[0].'R'.$Column->idta_struktur_bericht_spalten], 2, '.', ''));
                            }
                        }else{
                            array_push($temparray,number_format($row[$Periode[0].'R'.$Column->idta_struktur_bericht_spalten], 2, '.', ''));
                        }
                    }
                }
             }else {
                if($this->InputReport==0) {
                    if($Excel==0){
                        array_push($temparray,number_format($row[$Periode[0].'R'.$Column->idta_struktur_bericht_spalten], 0, ',', '.'));
                    }else{
                        array_push($temparray,number_format($row[$Periode[0].'R'.$Column->idta_struktur_bericht_spalten], 2, '.', ''));
                    }
                }else{
                    array_push($temparray,number_format($row[$Periode[0]], 2, '.', ''));
                }
            }                                
            array_push($this->WerteListeFormatted,$temparray);
        }
    }

    public function buildTitle($label="Name") {
        $temparray = array();
        array_push($temparray,$label);
        foreach($this->ColumnObj AS $Column){
            foreach($this->Perioden[$Column->idta_struktur_bericht_spalten] As $Periode) {
                if($Column->sbs_struktur_switch_type==1){
                    $dummy=StrukturRecord::finder()->findByidtm_struktur($Column->sbs_idtm_struktur)->struktur_name.'x'.$Periode[1];
                }else{
                    $dummy=$Periode[1].'x'.$Periode[0];
                }
                array_push($temparray,$dummy);
            }
        }
        $this->WerteTitle=$temparray;
    }

    public function buildPivotTitle($label="Name") {
        $temparray = array();
        array_push($temparray,$label);
        foreach($this->Perioden As $Periode) {
            $dummy=$Periode[1];
            array_push($temparray,$dummy);
        }
        $this->WerteTitle=$temparray;
    }

    public function addTitle() {
        array_push($this->WerteListeFormatted,$this->WerteTitle);
    }

    public function getTitle() {
        return $this->WerteTitle;
    }    

    public function check_forParents($Node) {
        $SQL = "SELECT idtm_struktur,parent_idtm_struktur FROM tm_struktur WHERE idtm_struktur = '".$Node->parent_idtm_struktur."'";
        $Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
    }

    public function get_PivotChildren($Node) {
        $SQL = "SELECT * FROM tm_stammdaten WHERE idta_stammdaten_group = '".$Node->idta_stammdaten_group."'";
        $Result = count(StammdatenRecord::finder()->findAllBySQL($SQL));
        $SSQL = "SELECT * FROM tm_stammdaten WHERE ";
        $counter = 0;
        if($Result>=1) {
            foreach(StammdatenRecord::finder()->findAllBySQL($SQL) as $Results) {
                $counter==0?$SSQL.="idtm_stammdaten = '".$Results->idtm_stammdaten."'":$SSQL.=" OR idtm_stammdaten = '".$Results->idtm_stammdaten."'";
                $counter++;
            }
        }else {
            $SSQL.="idtm_stammdaten = '0'";
        }
        return StammdatenRecord::finder()->findAllBySQL($SSQL);
    }

    public function getYearByMonth($periode_intern) {
        $Result = PeriodenRecord::finder()->findByper_Intern($periode_intern);
        if(is_Object($Result)){
            if($Result->parent_idta_perioden != 0) {
                $Result2 = PeriodenRecord::finder()->findByidta_perioden($Result->parent_idta_perioden);
                return $Result2->per_intern;
            }else {
                return $periode_intern;
            }
        }else {
            return $periode_intern;
        }
    }

}
?>