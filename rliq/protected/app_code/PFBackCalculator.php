<?php

/**
 * Description of PFBackCalculator
 *
 * @author pfrenz
 */
class PFBackCalculator extends TPage {
//put your code here

    /* Folgende Parameter sind zur Berechnung der Werte notwendig...
     * @param idta_periode -> die interne Periodenbezeichnung -> 10001 für 1. Jahr oder 1 für 1 Monat (Bsp)
     * @param idtm_struktur -> die Struktur ID, auf der die Werte nachher gespreichert werden sollen
     * @param w_dimkey -> der Schlüssel, der angehängt werden soll...
     * @param assoc_array(feldbezug=>wert) -> array mit den Werten, die als "neu" betrachtet werden sollen...
     * @param idta_variante, damit auch der richtige pool bearbeitet wird
     */

    private $DBConnection;
    public $Perioden=array();
    private $NumPerIntern = array(); //Anzahl der internen Perioden
    private $ChangedPeriod=array();
    private $StartNode;
    private $StrukturType;
    private $ChildrenNodes = array();
    private $w_werte = array();
    private $GLOBALVARIANTE=1;
    public $Stammdatensicht = 1;
    private $mySQL='';
    private $Operator = 'SUM';

    private $FunktionsFelder=array();
    private $TTWERTE = array(); //das array, in dem alle werte gespeichert sind...
    private $DIMKEY; //der String, in dem die Dimension abgelegt wird
    //the information required for the calculation of the scheme
    private $calcOB = 0;
    private $calcOBID = 0;

    //hier speicher ich den Tatsächlichen Feldtyp, damit ich nicht dauernd eine neue Verbindung auf die DB setzen muss
    private $FeldfunktionType = array();
    //private $SinglePeriode = 0;
    private $WerteListe = array();

    private $ResetCalcpayables = 0; //brauch ich fuer den ersten reset der werte

    public function setVariante($idta_variante) {
        $this->GLOBALVARIANTE = $idta_variante;
    }

    private function update_w_wert($jahr,$monat,$local_ff,$local_id,$value=0) {
        $SaveString = $jahr."xxx".$monat."xxx".$local_ff."xxx".$local_id;
        $this->TTWERTE[$SaveString] = $value;
    }

    public function PFBackCalculator() {
        $this->DBConnection = new TDbConnection($this->Application->getModule('db1')->database->getConnectionString(),$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        $this->DBConnection->Active = true;
    }

    public function initTTWerte($idtm_struktur,$idta_struktur_type) {
        $SQL = "SELECT * FROM ta_feldfunktion WHERE idta_struktur_type = '".$idta_struktur_type."'";
        $Fields = FeldfunktionRecord::finder()->findAllBySQL($SQL);
        foreach($Fields As $Field) {
            array_push($this->FunktionsFelder,$Field->idta_feldfunktion);
            foreach ($this->Perioden AS $tmpPeriode) {
                if($tmpPeriode[0]>10000) {
                    $jahr = $tmpPeriode[0];
                    $monat= $tmpPeriode[0];
                }else {
                    $jahr = $this->getYearByMonth($tmpPeriode[0]);
                    $monat = $tmpPeriode[0];
                }
                if(count(WerteRecord::finder()->findAllBySql("SELECT idtt_werte FROM tt_werte WHERE w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND idta_feldfunktion = '".$Field->idta_feldfunktion."' AND idtm_struktur = '".$idtm_struktur."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1"))) {
                //echo "treffer";
                }else {
                    $this->init_tt_werte($idtm_struktur,$Field->idta_feldfunktion,$jahr,$monat);
                }
                $NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$idtm_struktur."' AND idta_feldfunktion = '".$Field->idta_feldfunktion."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                $SaveString = $jahr."xxx".$monat."xxx".$Field->idta_feldfunktion."xxx".$idtm_struktur;
                $this->TTWERTE[$SaveString]=$NEWWerteRecord->w_wert;
            }
        }
    }

    public function run() {
        $this->initTTWerte($this->StartNode,$this->StrukturType);
        $this->get_ff_type($this->StrukturType);
        if(count($this->w_werte)>=1) {
            foreach($this->w_werte as $key=>$value) {
                if($this->ChangedPeriod[0][0]>10000) {
                    $jahr = $this->ChangedPeriod[0][0];
                    $monat= $this->ChangedPeriod[0][0];
                }else {
                    $jahr = $this->getYearByMonth($this->ChangedPeriod[0][0]);
                    $monat = $this->ChangedPeriod[0][0];
                }
                $value = $value*1;
                $this->valueChanged($jahr,$monat,$this->StrukturType,$key,$this->StartNode,$value);
            }
            $this->saveValues();
        }
    }

    public function setStartPeriod($Periode) {
        $Result = PeriodenRecord::finder()->findByper_Intern($Periode);
        array_push($this->Perioden, array($Result->per_intern,$Result->per_extern));
        $this->ChangedPeriod = $this->Perioden; //hier nehme ich die ausgangsperiode, damit ich die Berechnungen nachher alle sauber fahren kann
        if($Periode < 10000) {
            $Records = PeriodenRecord::finder()->findAllByparent_idta_perioden(PeriodenRecord::finder()->findByidta_perioden($Result->parent_idta_perioden)->idta_perioden);
            $YearRecords = PeriodenRecord::finder()->findAllByidta_perioden(PeriodenRecord::finder()->findByidta_perioden($Result->parent_idta_perioden)->idta_perioden);
            foreach($YearRecords As $YearRecord) {
                array_push($this->Perioden, array($YearRecord->per_intern,$YearRecord->per_extern));
            }
        }else {
            $Records = PeriodenRecord::finder()->findAllByparent_idta_perioden($Result->idta_perioden);
        }
        foreach($Records As $Record) {
            if($Record->per_intern <> $Result->per_intern) {
                array_push($this->Perioden, array($Record->per_intern,$Record->per_extern));
            }
        }
    }

    public function setNewValues($w_werte) {
        $this->w_werte=$w_werte;
    }

    public function build_DIMKEY($strukturID) {
        $this->DIMKEY=""; //leeren, eines bereits vorhandenen dimkeys
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
        $NEWWerteRecord->w_dimkey=$this->DIMKEY;
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

    private function saveValues($options="ALL") {
        $myDimKey = $this->build_DIMKEY($this->StartNode);

        $jahr = 0;
        $monat = 0;

        //hier werden jetzt die einzelnen Werte geladen
        foreach($this->Perioden AS $tmpPerioden) {
            if(preg_match('/^\d\d\d\d/',$tmpPerioden[1])) {
                $jahr = $tmpPerioden[0];
                $monat = $jahr;
            }else {
                $jahr = $this->getYearByMonth($tmpPerioden[0]);
                $monat = $tmpPerioden[0];
            }

            //jetzt laden wir die einzelnen Werte
            foreach($this->FunktionsFelder AS $funkID) {
                $myUniquID=$jahr.'xxx'.$monat.'xxx'.$funkID.'xxx'.$this->StartNode;
                $NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$this->StartNode."' AND idta_feldfunktion = '".$funkID."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                if(count($NEWWerteRecord)==1) {
                    $NEWWerteRecord->w_wert = isset($this->TTWERTE[$myUniquID])?$this->TTWERTE[$myUniquID]:0;
                    $NEWWerteRecord->w_id_variante=$this->GLOBALVARIANTE;
                    $NEWWerteRecord->w_dimkey=$myDimKey;
                    $NEWWerteRecord->save();
                }else {
                    $this->init_tt_werte($this->StartNode,$funkID,$jahr,$monat);
                    $NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT * FROM tt_werte WHERE idtm_struktur = '".$this->StartNode."' AND idta_feldfunktion = '".$funkID."' AND w_jahr = '".$jahr."' AND w_monat = '".$monat."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                    $NEWWerteRecord->w_wert = isset($this->TTWERTE[$myUniquID])?$this->TTWERTE[$myUniquID]:0;
                    $NEWWerteRecord->w_id_variante=$this->GLOBALVARIANTE;
                    $NEWWerteRecord->w_dimkey=$myDimKey;
                    $NEWWerteRecord->save();
                }
            }
        //echo "SAVER";
        }
        if($options=="ALL"){
            foreach ($this->Perioden AS $tmpPeriode) {
                if(preg_match('/^\d\d\d\d/',$tmpPeriode[0])) {
                    $myRunner = new PFBackCalculator();
                    $myRunner->GLOBALVARIANTE = $this->GLOBALVARIANTE;
                    $myRunner->runStructureCollector($this->StartNode,$tmpPeriode[0],$this->GLOBALVARIANTE);
                }
            }
        }
    }

    public function setStartNode($idtm_struktur) {
        $this->StartNode = $idtm_struktur;
        $Node=StrukturRecord::finder()->findByidtm_struktur($this->StartNode);
        $this->StrukturType = $Node->idta_struktur_type;
        $this->getChildren($Node);
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
            array_push($this->ChildrenNodes,$Node->idtm_struktur);
        }
    }

    public function check_forChildren($Node) {
        $Result = count(StrukturRecord::finder()->findAllByparent_idtm_struktur($Node->idtm_struktur));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
    }

    public function getYearByMonth($periode_intern) {
        $Result = PeriodenRecord::finder()->findByper_Intern($periode_intern);
        if($Result->parent_idta_perioden != 0) {
            $Result2 = PeriodenRecord::finder()->findByidta_perioden($Result->parent_idta_perioden);
            return $Result2->per_intern;
        }else {
            return $periode_intern;
        }
    }

    public function valueChanged($local_jahr,$local_monat,$local_type,$local_ff,$local_id,$newValue) {
    //hier holen wir die Werte vom Feld:

    //if($this->SinglePeriode == 0){ -- die Kontrolle muss ich hier nicht machen, da immer alle Perioden vorhanden sind...
        $local_month_fields = $this->get_month_array($local_ff,$local_id);
        //print_r($local_month_fields);
        $local_month_weight = $this->get_month_weigth($local_month_fields,$this->get_ff_type($local_ff));
        //}

        if($local_monat < 10000) {
        //here comes the bottum up stuff
        //here comes the bottum up stuff
        //here comes the bottum up stuff

            $td_startvalue = $newValue; //davor wurde hier der Wert aus dem feld in der maske ausgelesen...

            switch($this->get_ff_type($local_ff)) {
                case 0:
                //here comes the part for the addition scheme SUM
                    $this->update_w_wert($local_jahr,$local_monat,$local_ff,$local_id,$td_startvalue);
                    $valuefillin = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_ff,$local_id,$valuefillin);
                    $this->check_collector($local_ff,$local_monat,$local_jahr,$local_id);
                    break;
                case 7:
                    //here comes the part for the addition scheme SPLASHER DOWN
                    $this->update_w_wert($local_jahr,$local_monat,$local_ff,$local_id,$td_startvalue);
                    $valuefillin = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_ff,$local_id,$valuefillin);
                    $this->calc_splasher($local_ff,$local_monat,$local_jahr,$local_id,$td_startvalue);
                    break;
                case 2:
                //here comes the part for the calculation scheme COLLECTOR
                    $this->run_back_collector($local_ff,$local_monat,$local_jahr,$local_id,$local_monat);
                    $valuefillin = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_ff,$local_id,$valuefillin);
                    $this->check_collector($local_ff,$local_monat,$local_jahr,$local_id);
                    break;
                case 5:
                //here comes the part for the calculation scheme CONTINUANCE
                    $this->run_back_collector($local_ff,$local_monat,$local_jahr,$local_id,$local_monat);
                    $valuefillin = $this->sum_up($local_jahr,$local_monat,$local_ff,$local_id);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_ff,$local_id,$valuefillin);
                    $this->check_collector($local_ff,$local_monat,$local_jahr,$local_id);
                    break;
                case 4:
                //here comes the part for the calculation scheme OPENING BALANCE
                    $this->calcOpeningBalance($local_type,$local_ff,$local_id);
                    break;
                case 6:
                //here comes the part for the calculation scheme ACCOUNTS PAYABLE
                    $this->calcPayables($local_type,$local_ff,$local_id,$local_jahr);
                    break;
                default:
                //here comes the part for the division scheme
                    $this->update_w_wert($local_jahr,$local_monat,$local_ff,$local_id,$td_startvalue);
                    $this->update_w_wert($local_jahr,$local_jahr,$local_ff,$local_id,$this->get_avg_header($local_month_fields));
                    $this->check_collector($local_ff,$local_monat,$local_jahr,$local_id);
            }

            $this->update_w_wert($local_jahr,$local_monat,$local_ff,$local_id,$td_startvalue);

        //end of buttom up stuff
        }else {
        //here comes the top down stuff
        //here comes the top down stuff
        //here comes the top down stuff

            $td_startvalue = $newValue;

            switch($this->get_ff_type($local_ff)) {
                case 2:
                    foreach($local_month_fields as $textfillin) {
                        $tempmonth = preg_split("/xxx/",$textfillin);
                        $mymonth = $tempmonth[1];
                        $valuefillin = $td_startvalue*$local_month_weight[$textfillin];
                        $this->update_w_wert($local_jahr,$mymonth,$local_ff,$local_id,$valuefillin);
                        $this->run_back_collector($local_ff,$mymonth,$local_jahr,$local_id,$local_monat);
                        $this->check_collector($local_ff,$mymonth,$local_jahr,$local_id);
                    }
                    break;
                case 5:
                //here comes the part for the calculation scheme CONTINUANCE
                    foreach($local_month_fields as $textfillin) {
                        $tempmonth = preg_split("/xxx/",$textfillin);
                        $mymonth = $tempmonth[2];
                        $valuefillin = $td_startvalue*$local_month_weight[$textfillin];
                        $this->update_w_wert($local_jahr,$mymonth,$local_ff,$local_id,$valuefillin);
                        $this->run_back_collector($local_ff,$mymonth,$local_jahr,$local_id,$local_monat);
                        $this->check_collector($local_ff,$mymonth,$local_jahr,$local_id);
                    }
                    break;
                case 4:
                //here comes the part for the calculation scheme OPENING BALANCE
                    $this->calcOpeningBalance($local_type,$local_ff,$local_id);
                    break;
                case 6:
                //here comes the part for the calculation scheme ACCOUNTS PAYABLE
                    $this->calcPayables($local_ff,$local_id,$local_jahr);
                    break;
                default:
                    foreach($local_month_fields as $textfillin) {
                        $tempmonth = preg_split("/xxx/",$textfillin);
                        $mymonth = $tempmonth[1];
                        $valuefillin = $td_startvalue*$local_month_weight[$textfillin];
                        $this->update_w_wert($local_jahr,$mymonth,$local_ff,$local_id,$valuefillin);
                        $this->check_collector($local_ff,$mymonth,$local_jahr,$local_id);
                    }
            }

            $this->update_w_wert($local_jahr,$local_jahr,$local_ff,$local_id,$td_startvalue);
        }

        if($this->calcOB) {
            $ttresult = CollectorRecord::finder()->findBySql("SELECT ta_collector.idta_feldfunktion,col_idtafeldfunktion,col_operator,ta_feldfunktion.ff_type AS ff_type FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.col_idtafeldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.idta_feldfunktion = '".$this->calcOBID."' AND ff_type=4 LIMIT 1");
            $this->calcOpeningBalance($ttresult->col_idtafeldfunktion, $local_id);
        }
    }

    private function calc_splasher($local_ff,$local_monat,$local_jahr,$local_id,$newValue){
        //die eingabe erfolgt auf monatsebene
        //Holen der Stammdateninfo
        $StrukturWechselKnoten = StrukturRecord::finder()->findByPK($local_id);
        $StammdatenID = $StrukturWechselKnoten->idtm_stammdaten;
        //Ermitteln des Gesamtwertes der entsprechenden Verteilung
        $sql = "SELECT spl_monat, sum(spl_faktor) AS spl_faktor FROM tt_splasher WHERE idta_variante = '".$this->GLOBALVARIANTE."' AND idtm_stammdaten='".$StammdatenID."' AND spl_monat='".$local_monat."'";
        $Gesamtwert = TTSplasherRecord::finder()->findBySql($sql)->spl_faktor;
        //Ermitteln der Faktoren für die Verteilung, dabei holen wir auch die Zieldimension
        $sql = "SELECT to_idtm_stammdaten,spl_monat,idta_feldfunktion, sum(spl_faktor) AS spl_faktor FROM tt_splasher WHERE idta_variante = ".$this->GLOBALVARIANTE." AND idtm_stammdaten=".$StammdatenID." AND spl_monat=".$local_monat." GROUP BY to_idtm_stammdaten,spl_monat,idta_feldfunktion";
        $Einzelwerte = TTSplasherRecord::finder()->findAllBySql($sql);
        //wenn wir einen treffer haben, koennen wir nach der standardverteilung arbeiten
        if(is_array($Einzelwerte) AND $Gesamtwert!=0){
            foreach($Einzelwerte AS $Einzelwert){
                $sql = "SELECT idtm_struktur FROM tm_struktur WHERE idtm_stammdaten = '".$Einzelwert->to_idtm_stammdaten."'";
                $sql .= " AND (struktur_lft BETWEEN ".$StrukturWechselKnoten->struktur_lft." AND ".$StrukturWechselKnoten->struktur_rgt.")";
                $UpdateStrukturId = StrukturRecord::finder()->findBySQL($sql)->idtm_struktur;
                $Einzelfaktor = $Einzelwert->spl_faktor;
                $td_startvalue = ($Einzelfaktor/$Gesamtwert)*$newValue;
                //hier startet jetzt der Part, wo ich nur eine Periode habe -> entweder SubJahr oder Jahr...
                $PFBackCalculator = new PFBackCalculator();
                /* Folgende Parameter sind zur Berechnung der Werte notwendig...
                 * @param idta_periode -> die interne Periodenbezeichnung -> 10001 für 1. Jahr oder 1 für 1 Monat (Bsp)
                 * @param idtm_struktur -> die Struktur ID, auf der die Werte nachher gespreichert werden sollen
                 * @param w_dimkey -> der Schlüssel, der angehängt werden soll...
                 * @param assoc_array(feldbezug=>wert) -> array mit den Werten, die als "neu" betrachtet werden sollen...
                 */
                $PFBackCalculator->setStartPeriod($Einzelwert->spl_monat);
                $PFBackCalculator->setStartNode($UpdateStrukturId);
                //vorbereiten des Wertearrays, damit die bestehenden Werte in der Datenbank, mit den neuen Uerberschrieben werden koennen
                //jetzt laden wir die einzelnen Werte
                $w_wert[$Einzelwert->idta_feldfunktion] = $td_startvalue;
                $PFBackCalculator->setNewValues($w_wert);
                $PFBackCalculator->setVariante($this->GLOBALVARIANTE);
                $PFBackCalculator->run();
                unset($PFBackCalculator);
                unset($UpdateStrukturId);
            }
        }else{
            unset ($EinzelWerte);
            //zuerst muss ich die Anzahl der Knoten ermitteln, die in der Zieldimensionsgruppe vorhanden sind...
            $idta_stammdaten_group = StammdatenRecord::finder()->findByPK($StammdatenID)->idta_stammdaten_group;
            $SplasherInfo = SplasherRecord::finder()->find('from_idta_stammdaten_group = ? AND from_idta_feldfunktion = ?',$idta_stammdaten_group,$local_ff);
            $to_idta_stammdaten_group = $SplasherInfo->to_idta_stammdaten_group;
            $to_idta_feldfunktion = $SplasherInfo->to_idta_feldfunktion;
            //hier ermitteln wir die anzahl der zielwerte
            $AnzahlZielWerte = StammdatenRecord::finder()->count('idta_stammdaten_group = ?',$to_idta_stammdaten_group);
            $sql = "SELECT idtm_stammdaten FROM tm_stammdaten WHERE idta_stammdaten_group = '".$to_idta_stammdaten_group."'";
            $Einzelwerte = StammdatenRecord::finder()->findAllBySql($sql);
            if(is_array($Einzelwerte)){
                foreach($Einzelwerte AS $Einzelwert){
                    $sql = "SELECT idtm_struktur FROM tm_struktur WHERE idtm_stammdaten = '".$Einzelwert->idtm_stammdaten."'";
                    $sql .= " AND (struktur_lft BETWEEN ".$StrukturWechselKnoten->struktur_lft." AND ".$StrukturWechselKnoten->struktur_rgt.")";
                    $UpdateStrukturId = StrukturRecord::finder()->findBySQL($sql)->idtm_struktur;
                    $Einzelfaktor = 1/$AnzahlZielWerte;
                    $td_startvalue = $Einzelfaktor*$newValue;
                    //hier startet jetzt der Part, wo ich nur eine Periode habe -> entweder SubJahr oder Jahr...
                    $PFBackCalculator = new PFBackCalculator();
                    /* Folgende Parameter sind zur Berechnung der Werte notwendig...
                     * @param idta_periode -> die interne Periodenbezeichnung -> 10001 für 1. Jahr oder 1 für 1 Monat (Bsp)
                     * @param idtm_struktur -> die Struktur ID, auf der die Werte nachher gespreichert werden sollen
                     * @param w_dimkey -> der Schlüssel, der angehängt werden soll...
                     * @param assoc_array(feldbezug=>wert) -> array mit den Werten, die als "neu" betrachtet werden sollen...
                     */
                    $PFBackCalculator->setStartPeriod($local_monat);
                    $PFBackCalculator->setStartNode($UpdateStrukturId);
                    //vorbereiten des Wertearrays, damit die bestehenden Werte in der Datenbank, mit den neuen Uerberschrieben werden koennen
                    //jetzt laden wir die einzelnen Werte
                    $w_wert[$to_idta_feldfunktion] = $td_startvalue;
                    $PFBackCalculator->setNewValues($w_wert);
                    $PFBackCalculator->setVariante($this->GLOBALVARIANTE);
                    $PFBackCalculator->run();
                    unset($PFBackCalculator);
                    unset($UpdateStrukturId);
                }
            }
        }
    }
    
    private function run_back_collector($local_ff,$local_monat,$local_jahr,$local_id,$local_start_monat) {

        $tresult = CollectorRecord::finder()->findAllBySql("SELECT col_idtafeldfunktion,col_operator,idta_collector FROM ta_collector WHERE idta_feldfunktion = '".$local_ff."' ORDER BY idta_collector DESC");
        $fields = array();
        $operators = array();
        $fixed = array();
        $feldfunktion = array();

        $myUniquID=$local_jahr."xxx".$local_monat."xxx".$local_ff."xxx".$local_id;
        $tempresult = $this->TTWERTE[$myUniquID];

        $i=0;
        foreach($tresult as $trecord) {
            $myWerteString = $local_jahr."xxx".$local_monat."xxx".$trecord->col_idtafeldfunktion."xxx".$local_id;
            $fields[$i] = $this->TTWERTE[$myWerteString];
            $operators[$i] = $trecord->col_operator;
            $feldfunktion[$i] = $trecord->col_idtafeldfunktion;
            $myfeldinfo = FeldfunktionRecord::finder()->findByPK($trecord->col_idtafeldfunktion);
            $fixed[$i] = $myfeldinfo->ff_fix;
            $i++;
        }
        $j=0;
        foreach($fields AS $myfield) {
            if($fixed[$j]) {
                switch($operators[$j]) {
                    case '+':
                        $tempresult -= $myfield;
                        break;
                    case '-':
                        $tempresult += $myfield;
                        break;
                    case '*':
                        $tempresult /= $myfield;
                        break;
                    case '/':
                        $tempresult *= $myfield;
                        break;
                    default:
                        $tempresult = $myfield;
                        break;
                }
            }
            //$this->valueChanged($local_jahr,$local_monat,$this->StrukturType,$feldfunktion[$j],$local_id,$tempresult*1);
            $j++;
        }        
    }

    private function get_month_array($ff,$id) {
        /*
         * $TmpCurrentString = $my_jahr."xxx".$monat."xxx".$local_ff."xxx".$local_id;
         * $tmpcurrent_value = number_format($this->TTWERTE[$TmpCurrentString],0,'.','');
         *
         */
        $returnarray = array();
        $jahr = 0;
        $monat = 0;
        foreach($this->Perioden AS $tmpPerioden) {
            if(preg_match('/^\d\d\d\d/',$tmpPerioden[1])) {
                $jahr = $tmpPerioden[0];
                $monat = $jahr;
            }else {
                $jahr = $this->getYearByMonth($tmpPerioden[0]);
                $monat = $tmpPerioden[0];
                $fieldstr = $jahr."xxx".$monat."xxx".$ff."xxx".$id;
                array_push($returnarray,$fieldstr);
            }
        }
        return $returnarray;
    }

    private function get_month_weigth($fields,$fftype='0') {
        $returnarray=array();
        $sum=0;
        $count=0;
        $countsum=0;
        foreach($fields as $myfield) {
            $sum+=$this->TTWERTE[$myfield]*1;
            if(!$this->TTWERTE[$myfield]==0) {
                $count++;
            }
        }

        //hier muss hinterlegt werden, wenn aufsummiert werden muss - auch collector
        if($count==0 && ($fftype==0 || $fftype ==2)) {
            foreach($fields as $myfield) {
                $countsum++;
            }
        }

        if($count>0 AND $sum != 0) {
            $avg = $sum/$count;
        }else {
            $avg=1;
        }
        //calculation for avg
        // print_r($fftype);
        switch($fftype) {
            case 1:
                foreach($fields as $fielda) {
                    if(($this->TTWERTE[$fielda]*1)!=0) {
                        $returnarray[$fielda]=$this->TTWERTE[$fielda]*1/$avg;
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
                    if(($this->TTWERTE[$fieldb]*1)!= 0) {
                        $returnarray[$fieldb]=$this->TTWERTE[$fieldb]*1/$sum;
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

    public function calcPayables($local_ff,$local_id,$local_jahr) {
        
        $counter=0;
        //reset of the existing values -> filled with value in w_endwert default zero if values from former periods, they will be filled in
        foreach ($this->Perioden AS $tmpPeriode) {
            $my_jahr = $this->getYearByMonth($tmpPeriode[0]);
            $follow_value_record = WerteRecord::finder()->findBySql("SELECT w_endwert FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$local_ff."' AND w_jahr = '".$my_jahr."' AND w_monat = '".$tmpPeriode[0]."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
            $this->update_w_wert($my_jahr,$tmpPeriode[0],$local_ff,$local_id,$follow_value_record->w_endwert);
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
        
        foreach ($this->Perioden AS $tmpPeriode) {
            $counter++; // hier erhoehen wir die info, dass die erste periode vorbei ist
            if($tmpPeriode[0]<10000) {
                $key=$tmpPeriode[0];
                $my_jahr = $this->getYearByMonth($tmpPeriode[0]);
                $CurrentString = $my_jahr."xxx".$tmpPeriode[0]."xxx".$local_ff."xxx".$local_id;
                $current_value = $this->TTWERTE[$CurrentString];
                $days_for_factor = 360/$this->getNumberPerIntern($my_jahr);
                //DB: 30 echo $days_for_factor;
                //here we get the value of the field, that contains the value for the base factor
                $tresult = CollectorRecord::finder()->findBySql("SELECT col_idtafeldfunktion,col_operator FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.col_idtafeldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.idta_feldfunktion = '".$local_ff."' AND ff_type='3' LIMIT 1"); //3 ist der struktursammler
                $BaseString = $my_jahr."xxx".$tmpPeriode[0]."xxx".$tresult->col_idtafeldfunktion."xxx".$local_id;
                $base_value = $this->TTWERTE[$BaseString];
                $factor_per_day=$base_value/$days_for_factor;
                $ttresult = CollectorRecord::finder()->findBySql("SELECT col_idtafeldfunktion,col_operator FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.col_idtafeldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.idta_feldfunktion = '".$local_ff."' AND ff_type<>'3' LIMIT 1"); //3 ist der struktursammler
                $DayString = $my_jahr."xxx".$tmpPeriode[0]."xxx".$ttresult->col_idtafeldfunktion."xxx".$local_id;
                $day_value = $this->TTWERTE[$DayString];
                $temp_compare = $day_value/$days_for_factor;

                $untergrenze = 0; //der untere laufer
                $obergrenze = 1; //der obere laufwert
                $monat = $tmpPeriode[0];
                //print_r("O:".$obergrenze."U:".$untergrenze."V:".$temp_compare);

                for($ii=0;$ii<=count($this->Perioden);$ii++) {
                    if($temp_compare >= $untergrenze AND $temp_compare < $obergrenze) {
                        $TmpCurrentString = $my_jahr."xxx".$monat."xxx".$local_ff."xxx".$local_id;
                        if(isset($this->TTWERTE[$TmpCurrentString])){
                            $tmpcurrent_value = $this->TTWERTE[$TmpCurrentString];
                        }else{
                            $tmpcurrent_value = 0;
                        }
                        $faktor_periode = $obergrenze - $temp_compare;
                        $valuefillin = $faktor_periode * $base_value;
                        $counter==1?'':$valuefillin+=$tmpcurrent_value;
                        $this->update_w_wert($my_jahr,$monat,$local_ff,$local_id,$valuefillin);
                        if($faktor_periode<1 AND $faktor_periode>0) {
                            $monat++;
                            $target_year = $this->getYearByMonth($monat);//this is new because a value needs to be passed to the following year
                            $valuefillin = (1-$faktor_periode) * $base_value;
                            $FollowString = $target_year."xxx".$monat."xxx".$local_ff."xxx".$local_id;
                            if($target_year == $my_jahr){
                                $follow_value = $this->TTWERTE[$FollowString];
                                $counter==1?'':$valuefillin+=$follow_value;
                                $this->update_w_wert($my_jahr,$monat,$local_ff,$local_id,$valuefillin);
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
                $this->check_collector($local_ff,$key,$local_jahr,$local_id);
            }
        }
        //echo "Sp6x";
        $this->update_w_wert($local_jahr,$local_jahr,$local_ff,$local_id,$this->sum_up($local_jahr,$local_jahr,$local_ff,$local_id));
    }

    public function calcOpeningBalance($local_ff,$local_id) {
        $key = 0; //init of the variable
        $temp_periode = 0; //init of the variable
        foreach ($this->Perioden AS $tmpPeriode) {
            $key = $tmpPeriode[0];
            $my_jahr = $this->getYearByMonth($key);
            $temp_periode = $this->getPeriodeBefore($key);
            if($temp_periode<10000 AND $key<10000) {
                $trecord = FeldfunktionRecord::finder()->findByidta_feldfunktion($local_ff);
                $jahr = $this->getYearByMonth($temp_periode);
                $PreviousString = $jahr."xxx".$temp_periode."xxx".$trecord->pre_idta_feldfunktion."xxx".$local_id;
                if(isset($this->TTWERTE[$PreviousString]) AND ($jahr == $my_jahr)){
                    $valuefillin = number_format($this->TTWERTE[$PreviousString],2,'.','');
                }else{
                    $valuefillin = WerteRecord::finder()->findAllBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$trecord->pre_idta_feldfunktion."' AND w_jahr = '".$jahr."' AND w_monat = '".$temp_periode."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                }
                $this->update_w_wert($my_jahr,$key,$local_ff,$local_id,$valuefillin);
                $this->check_collector($local_ff,$key,$my_jahr,$local_id);
            }else {
                $jahr = $temp_periode;//pruefung ob 10001 nicht vergessen
                if($my_jahr==10001) {
                    if($key>12 AND $key<10000) {
                        $jahr+=1;
                        $temp_periode+=1;
                    }
                    $PreviousString = $jahr."xxx".$temp_periode."xxx".$local_ff."xxx".$local_id;
                    if(isset($this->TTWERTE[$PreviousString])){
                        $valuefillin = number_format($this->TTWERTE[$PreviousString],2,'.','');
                    }else{
                        $valuefillin = number_format(0,2,'.','');
                    }
                    //echo "Sp8x";
                    $this->update_w_wert($my_jahr,$key,$local_ff,$local_id,$valuefillin);
                    $this->check_collector($local_ff,$key,$my_jahr,$local_id);
                }else {
                    $jahr = $this->getYearByMonth($temp_periode);
                    $ttrecord = FeldfunktionRecord::finder()->findByidta_feldfunktion($local_ff);
                    $myWerteString = $jahr."xxx".$temp_periode."xxx".$local_ff."xxx".$local_id;
                    if(isset($this->TTWERTE[$myWerteString]) AND ($jahr == $my_jahr)){
                        $valuefillin = number_format($this->TTWERTE[$myWerteString],2,'.','');
                    }else{
                        $ResultWert = WerteRecord::finder()->findBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$local_id."' AND idta_feldfunktion = '".$ttrecord->pre_idta_feldfunktion."' AND w_jahr = '".$jahr."' AND w_monat = '".$jahr."' AND w_id_variante = '".$this->GLOBALVARIANTE."' LIMIT 1");
                        if(is_object($ResultWert)){
                            $valuefillin = number_format($ResultWert->w_wert,2,'.','');
                        }else{
                            $valuefillin = number_format(0,2,'.','');
                        }
                    }
                    //echo "Sp9x";
                    $this->update_w_wert($my_jahr,$key,$local_ff,$local_id,$valuefillin);
                    $this->check_collector($local_ff,$key,$my_jahr,$local_id);
                }
            }
        }
    }

    private function check_collector($local_ff,$local_monat,$local_jahr,$local_id) {
        $myfieldfunk='';
        $myfieldfunkrev='';
        $tresult = CollectorRecord::finder()->findAllBySql("SELECT ta_collector.idta_feldfunktion,col_idtafeldfunktion,col_operator,ta_feldfunktion.ff_type AS ff_type FROM ta_collector INNER JOIN ta_feldfunktion ON ta_collector.idta_feldfunktion = ta_feldfunktion.idta_feldfunktion WHERE ta_collector.col_idtafeldfunktion = '".$local_ff."'");
        foreach($tresult as $trecord) {
            if(FeldfunktionRecord::finder()->findByPK($trecord->idta_feldfunktion)->ff_calcopening) {
                $this->calcOB=1;
                $this->calcOBID=$trecord->idta_feldfunktion;
            }
            if($trecord->ff_type==6) {
                $this->calcPayables($trecord->idta_feldfunktion, $local_id, $local_jahr);
            }elseif($trecord->ff_type==4) {
                $this->calcOpeningBalance($trecord->idta_feldfunktion,$local_id);
            }else {
                $this->run_collector($trecord->idta_feldfunktion,$local_monat,$local_jahr,$local_id);
                $myfieldfunk = $trecord->idta_feldfunktion;
                if($myfieldfunk!='') { //AND $myfieldfunk!='3'){
                    if(FeldfunktionRecord::finder()->findByPK($myfieldfunk)->ff_type==5) {
                        $mymonth = $this->getMaxPerIntern($local_jahr);
                        $SaveString = $local_jahr."xxx".$mymonth."xxx".$myfieldfunk."xxx".$local_id;
                        //echo "Sp1x";
                        $this->update_w_wert($local_jahr,$local_jahr,$myfieldfunk,$local_id,$this->TTWERTE[$SaveString]);
                    }else {
                    //echo "Sp2x";
                        $this->update_w_wert($local_jahr,$local_jahr,$myfieldfunk,$local_id,$this->sum_up($local_jahr,$local_monat,$myfieldfunk,$local_id));
                    }
                }
            }
        }
    }

    private function run_collector($local_ff,$local_monat,$local_jahr,$local_id) {

        $tresult = CollectorRecord::finder()->findAllBySql("SELECT col_idtafeldfunktion,col_operator FROM ta_collector WHERE idta_feldfunktion = '".$local_ff."'");
        $fields = array();
        $operators = array();
        $tempresult = 0;

        $i=0;
        foreach($tresult as $trecord) {
            $myWerteString = $local_jahr."xxx".$local_monat."xxx".$trecord->col_idtafeldfunktion."xxx".$local_id;
            $fields[$i] = isset($this->TTWERTE[$myWerteString])?$this->TTWERTE[$myWerteString]:0;
            $operators[$i] = $trecord->col_operator;
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
        }
        //echo "Sp10x";
        $this->update_w_wert($local_jahr,$local_monat,$local_ff,$local_id,$tempresult);
    }

    private function sum_up($local_jahr,$local_month,$local_ff,$local_id) {
        $returnresult = 0;
        foreach ($this->Perioden AS $tmpPeriode) {
            if(!preg_match('/^\d\d\d\d/',$tmpPeriode[1])) {
                $monat = $tmpPeriode[0];
                $myWerteString = $local_jahr."xxx".$monat."xxx".$local_ff."xxx".$local_id;
                $returnresult += isset($this->TTWERTE[$myWerteString])?$this->TTWERTE[$myWerteString]*1:0;
            }
        }
        return $returnresult;
    }

    private function get_avg_header($fields) {
        $sum=0;
        $count=0;
        foreach($fields as $myfield) {
            $sum+=$this->TTWERTE[$myfield]*1;
            //echo $sum;
            if(!$this->TTWERTE[$myfield]==0) {
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

    public function buildStructureCollectorSQL($idtm_struktur_to) {
        $this->mySQL = "SELECT tm_struktur_tm_struktur.idtm_struktur_to AS Name ";
        foreach($this->Perioden As $Periode) {
            $this->mySQL .= ", ".$this->Operator."(CASE WHEN w_monat='".$Periode[0]."' THEN w_wert ELSE 0 END) AS '".$Periode[0]."' ";
        }
        $this->mySQL .= "FROM tt_werte INNER JOIN tm_struktur_tm_struktur ON tm_struktur_tm_struktur.idtm_struktur_from = tt_werte.idtm_struktur ";
        //$this->mySQL .= "INNER JOIN tm_struktur ON tm_struktur_tm_struktur.idtm_struktur_from = tm_struktur.idtm_struktur ";
        $this->mySQL .= "WHERE tm_struktur_tm_struktur.idtm_struktur_to = '".$idtm_struktur_to."' AND w_id_variante = '".$this->GLOBALVARIANTE."' ";
        if(count($this->FunktionsFelder) > 0) {
            $this->mySQL .= "AND (";
            $counter=0;
            foreach($this->FunktionsFelder As $Node) {
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

    public function findFirstCompany($idtm_struktur) {
        $StartNode = StrukturRecord::finder()->findByidtm_struktur($idtm_struktur);
        if($StartNode->idta_struktur_type == 1) {
            return $StartNode->idtm_struktur;
        }else {
            if($this->check_forParent($StartNode)) {
                $Record = StrukturRecord::finder()->findByidtm_struktur($StartNode->parent_idtm_struktur);
                return $this->findFirstCompany($Record->idtm_struktur);
            }else {
                return 1;
            }
        }
    }

    private function getMaxPerIntern($local_jahr) {
        return PeriodenRecord::finder()->findBySQL("SELECT MAX(per_intern) AS per_intern FROM ta_perioden WHERE parent_idta_perioden = '".PeriodenRecord::finder()->findByper_intern($local_jahr)->idta_perioden."'")->per_intern;
    }

    public function runStructureCollector($idtm_struktur,$periode='10001',$variante='1') {
        //diese info benoetige ich, damit keine doppelten felder geladen werden
        $existingFieldfunktionen = array();
        $existingFieldfunktionen = $this->FunktionsFelder;
        
        $Records = StrukturStrukturRecord::finder()->findAllBySQL("SELECT idtm_struktur_to FROM tm_struktur_tm_struktur WHERE idtm_struktur_from = '".$idtm_struktur."' GROUP BY idtm_struktur_to");
        $counter=0;
        foreach($Records AS $Record) {
            //leeren der bestehenden Funktionsfelder
            $this->FunktionsFelder=array();

            $counter++;
            $this->TTWERTE=array();
            $this->WerteListe=array();
            //hier leere ich das Array, sonst klappt das nicht
            $Company = $this->findFirstCompany($Record->idtm_struktur_to);            
            $counter==1?$this->setStartNode($Company):'';
            $counter==1?$this->setStartPeriod($periode):'';

            //hier muss ich checken, ob aus einer struktur 2 werte auf den gleichen Punkt verweisen...
            foreach(StrukturStrukturRecord::finder()->findAllByidtm_struktur_to($Record->idtm_struktur_to)as $temp) {
                array_push($this->FunktionsFelder,$temp->idta_feldfunktion);
            }
            //here we run the SQL-Statement
            $this->buildStructureCollectorSQL($Record->idtm_struktur_to);
            $this->setStartNode($Record->idtm_struktur_to);
            
            //diese info benoetige ich, damit keine doppelten felder geladen werden
             $this->FunktionsFelder = $existingFieldfunktionen;

            $command = $this->DBConnection->createCommand($this->mySQL);
            $dataReader=$command->query();
            $this->WerteListe = $dataReader->readAll();

            //hier befuellen wir den ram record
            $this->TTWERTE = array();
            //DB: leeres array print_r($this->TTWERTE);
            $this->initTTWerte($Record->idtm_struktur_to, StrukturRecord::finder()->findByPK($Record->idtm_struktur_to)->idta_struktur_type);
            $Result = StrukturRecord::finder()->findByPK($Record->idtm_struktur_to);
            $SQL = "SELECT * FROM ta_feldfunktion WHERE idta_struktur_type = '".$Result->idta_struktur_type."' AND ff_type='3'";
            $FieldToChange = FeldfunktionRecord::finder()->findBySQL($SQL);
            foreach ($this->Perioden AS $tmpPeriode) {
                if($tmpPeriode[0]>10000) {
                    $jahr = $tmpPeriode[0];
                    $monat= $tmpPeriode[0];
                }else {
                    $jahr = $this->getYearByMonth($tmpPeriode[0]);
                    $monat = $tmpPeriode[0];
                }
                //$this->valueChanged($jahr,$monat,$Result->idta_struktur_type,$FieldToChange->idta_feldfunktion,$Record->idtm_struktur_to,$this->WerteListe[0][$monat]);
                $this->update_w_wert($jahr,$monat,$FieldToChange->idta_feldfunktion,$Record->idtm_struktur_to,$this->WerteListe[0][$monat]);
            }
            $this->mySQL='';
            //former saveValues()
            $this->valueChanged($jahr,$jahr,$Result->idta_struktur_type,$FieldToChange->idta_feldfunktion,$Record->idtm_struktur_to,$this->WerteListe[0][$jahr]);
            $this->check_collector($FieldToChange->idta_feldfunktion,$jahr,$jahr,$Record->idtm_struktur_to);
            $this->saveValues("NOSC");
        }        
    }

    private function getNumberPerIntern($local_jahr) {
        if(isset($this->NumPerIntern[$local_jahr])){
            if($this->NumPerIntern[$local_jahr]==0){
                $this->NumPerIntern[$local_jahr] = count(PeriodenRecord::finder()->findAllBySQL("SELECT per_intern FROM ta_perioden WHERE parent_idta_perioden = '".PeriodenRecord::finder()->findByper_intern($local_jahr)->idta_perioden."'"));
            }
        }else{
            $this->NumPerIntern[$local_jahr] = count(PeriodenRecord::finder()->findAllBySQL("SELECT per_intern FROM ta_perioden WHERE parent_idta_perioden = '".PeriodenRecord::finder()->findByper_intern($local_jahr)->idta_perioden."'"));
        }
        return $this->NumPerIntern[$local_jahr];
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
                    return $tester-1;
                }else {
                    return $periode_intern-1;
                }
            }
        }
    }

}
?>
