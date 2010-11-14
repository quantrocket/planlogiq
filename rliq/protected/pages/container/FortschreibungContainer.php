<?php

class FortschreibungContainer extends TTemplateControl {

    public $Perioden = array();
    public $DIMKEY = "";

    private function load_ta_perioden($From_Periode,$To_Periode) {
        $MyGap = $To_Periode-$From_Periode;
        if($MyGap >= 1){
            for($ii=1;$ii<=$MyGap;$ii++){
                $Result = PeriodenRecord::finder()->findByper_Intern($From_Periode+$ii-1);
                $ResultTo = PeriodenRecord::finder()->findByper_Intern($From_Periode+$ii);
                $this->Perioden[$Result->per_intern]=$ResultTo->per_intern;
                $Records = PeriodenRecord::finder()->findAllByparent_idta_perioden($Result->idta_perioden);
                $NumOfPeriods=count($Records);
                foreach($Records As $Record) {
                    $this->Perioden[$Record->per_intern]=$Record->per_intern+$NumOfPeriods;
                }
            }
        }
    }

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            $this->bindListBox();
            $this->bindListFortschreibung();
        }
    }

    public function bindListBox() {
        $this->for_idta_feldfunktion->DataSource = PFH::build_SQLPullDownAdvanced(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name","idta_struktur_type"));
        $this->for_idta_feldfunktion->DataBind();

        $this->for_from_idta_periode->DataSource=PFH::build_SQLPullDown(PeriodenRecord::finder(),"ta_perioden",array("per_intern","per_extern"));
        $this->for_from_idta_periode->dataBind();

        $this->for_to_idta_periode->DataSource=PFH::build_SQLPullDown(PeriodenRecord::finder(),"ta_perioden",array("per_intern","per_extern"));
        $this->for_to_idta_periode->dataBind();

        $this->for_idta_variante->DataSource=PFH::build_SQLPullDown(VarianteRecord::finder(),"ta_variante",array("idta_variante","var_descr"));
        $this->for_idta_variante->dataBind();

        //definition der wachstumstypen...
        // 1 = year to year
        $growthtypes = array(1=>'year to year');
        $this->for_idta_fortschreibungs_type->DataSource=$growthtypes;
        $this->for_idta_fortschreibungs_type->dataBind();
    }

    public function bindListFortschreibung() {
    //here i load all values from FortschreibungRecord
        $this->FortschreibungListe->DataSource=FortschreibungRecord::finder()->findAll();
        $this->FortschreibungListe->dataBind();
    }

    private $RCprimarykey = "idta_fortschreibung";
    private $RCfields = array("for_faktor","for_name","idtm_struktur","idta_feldfunktion","from_idta_periode","to_idta_periode","idta_variante","idta_fortschreibungs_type");
    private $RCdatfields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array();

    public function FCClosedButtonClicked($sender, $param) {
        $this->page->mpnlFortschreibung->Hide();
    }


    public function load_fortschreibung($sender,$param) {

        $item = $param->Item;
        $myitem=FortschreibungRecord::finder()->findByPK($item->lst_idta_fortschreibung->Text);

        $tempus = 'for_'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->fortschreibung_edit_status->Text = 1;
    }

    public function FCSavedButtonClicked($sender,$param) {

        $tempus='for_'.$this->RCprimarykey;

        if($this->fortschreibung_edit_status->Text == '1') {
            $RCEditRecord = FortschreibungRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new FortschreibungRecord;
        }

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }
        $RCEditRecord->save();
        $this->bindListFortschreibung();
    }

    public function FCNewButtonClicked($sender,$param) {

        $tempus = 'for_'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->Checked(0);
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'for_'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->fortschreibung_edit_status->Text = '0';
    }


    public function FortschreibungListe_PageIndexChanged($sender,$param) {
        $this->FortschreibungListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListFortschreibung();
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

    private function getIdtafeldfunktion($Field) {
        preg_match('/XXX([0-9]*)XXX([0-9]*)XXX.*/',$Field,$matches);
        $month = $matches[2];
        return PeriodenRecord::finder()->findByper_intern($month)->idta_perioden;
    }

    /**
     *
     * @param <type> $sender - the sender of the Click event
     * @param <type> $param - parameters, but sender->commandparameter
     * @method from this point the complete seasonality will be written
     * @author Philipp Frenzel pf@com-x-cha.com
     * @@copyright Frenzel GmbH 2009
     *
     */

    private $allowedIDs=array(); //inside this array we store all allowed ids the user can see
    private $subcats = array();//list of all subcats


    public function FCRunSeasonsButtonClicked($sender,$param) {
        //Step One, find all relevant IDs
        $FieldstToChange = array(); //inside this array, the fields that needed to be changed are listed
        $FieldsToKeep = array(); //inside this array, the fields that needed to be keept are listed

        $idta_variante = $this->Request['idta_variante'];
        $to_idta_variante = $this->for_idta_variante->Text;

        $this->load_ta_perioden($this->for_from_idta_periode->Text, $this->for_to_idta_periode->Text);

        $idta_struktur_type = FeldfunktionRecord::finder()->findByidta_feldfunktion($this->for_idta_feldfunktion->Text)->idta_struktur_type;
        $idta_feldfunktion = $this->for_idta_feldfunktion->Text;

        $FieldsToChange[] = $idta_feldfunktion; //this value needs always to be included

        $this->load_all_cats();
        $SQLINCondition = $this->subCategory_Inlist($this->subcats, $this->for_idtm_struktur->Text);//the two must be replaced with the value from the usermanager

        $sql = "SELECT idtm_struktur FROM tm_struktur WHERE idtm_struktur IN (".$SQLINCondition.") AND idta_struktur_type = ".$idta_struktur_type;

        //here I recieve the array of values containing the elements to be changed
        $ElementsToChange = StrukturRecord::finder()->findAllBySQL($sql);       

        //before the change can start, I need to identify the affected rows
        $FieldsToChangeBrutto = CollectorRecord::finder()->findAllBycol_idtafeldfunktion($idta_feldfunktion);

        foreach($FieldsToChangeBrutto As $TmpField) {
            $FieldsToChange[] = $TmpField->idta_feldfunktion;
        }

        $FieldsToKeepBrutto = FeldfunktionRecord::finder()->findAllByidta_struktur_type($idta_struktur_type);
        foreach($FieldsToKeepBrutto AS $MyTmpField){
            if(!in_array($MyTmpField->idta_feldfunktion,$FieldsToChange)){
                $FieldsToKeep[]=$MyTmpField->idta_feldfunktion;
            }
        }

        foreach($ElementsToChange AS $Element) {
            foreach($this->Perioden AS $key=>$value) {
                foreach($FieldsToChange AS $Field) {
                    $year_idta_periode=$this->getYearByMonth($key);
                    $sqlYEAR = "SELECT w_wert FROM tt_werte WHERE w_jahr = ".$year_idta_periode." AND w_monat = ".$key." AND idtm_struktur = ".$Element->idtm_struktur." AND w_id_variante = ".$idta_variante." AND idta_feldfunktion = ".$Field;
                    $YEARValue = WerteRecord::finder()->findBySQL($sqlYEAR)->w_wert;
                    $newValue =  $YEARValue * (1+($this->for_for_faktor->Text/100));
                    //$sqlMONTH = "SELECT w_wert FROM tt_werte WHERE w_jahr = ".$year_idta_periode." AND w_monat = ".PeriodenRecord::finder()->findByPK($Season->idta_periode)->per_intern." AND idtm_struktur = ".$Element->idtm_struktur." AND w_id_variante = ".$idta_variante." AND idta_feldfunktion = ".$Field;
                    $year_to_idta_periode=$this->getYearByMonth($value);
                    if(count(WerteRecord::finder()->find('w_jahr = ? AND w_monat = ? AND idtm_struktur = ? AND w_id_variante = ? AND idta_feldfunktion = ?',$year_to_idta_periode,$value,$Element->idtm_struktur,$to_idta_variante,$Field))) {
                        $RecordToUpdate = WerteRecord::finder()->find('w_jahr = ? AND w_monat = ? AND idtm_struktur = ? AND w_id_variante = ? AND idta_feldfunktion = ?',$year_to_idta_periode,$value,$Element->idtm_struktur,$to_idta_variante,$Field);
                        if($RecordToUpdate->w_wert != $newValue){
                            $RecordToUpdate->w_wert = $newValue;
                            $RecordToUpdate->save();
                        }
                    }else{
                        $RecordToWrite = new WerteRecord();
                        $RecordToWrite->w_jahr = $year_to_idta_periode;
                        $RecordToWrite->w_monat = $value;
                        $RecordToWrite->idtm_struktur = $Element->idtm_struktur;
                        $RecordToWrite->w_id_variante = $to_idta_variante;
                        $RecordToWrite->w_wert = $newValue;
                        $RecordToWrite->idta_feldfunktion = $Field;
                        $RecordToWrite->w_dimkey = $this->build_DIMKEY($Element->idtm_struktur);
                        $RecordToWrite->save();
                    }
                }
                foreach($FieldsToKeep AS $Field) {
                    $year_idta_periode=$this->getYearByMonth($key);
                    $sqlYEAR = "SELECT w_wert FROM tt_werte WHERE w_jahr = ".$year_idta_periode." AND w_monat = ".$key." AND idtm_struktur = ".$Element->idtm_struktur." AND w_id_variante = ".$idta_variante." AND idta_feldfunktion = ".$Field;
                    $YEARValue = WerteRecord::finder()->findBySQL($sqlYEAR)->w_wert;
                    $newValue =  $YEARValue;
                    //$sqlMONTH = "SELECT w_wert FROM tt_werte WHERE w_jahr = ".$year_idta_periode." AND w_monat = ".PeriodenRecord::finder()->findByPK($Season->idta_periode)->per_intern." AND idtm_struktur = ".$Element->idtm_struktur." AND w_id_variante = ".$idta_variante." AND idta_feldfunktion = ".$Field;
                    $year_to_idta_periode=$this->getYearByMonth($value);
                    if(count(WerteRecord::finder()->find('w_jahr = ? AND w_monat = ? AND idtm_struktur = ? AND w_id_variante = ? AND idta_feldfunktion = ?',$year_to_idta_periode,$value,$Element->idtm_struktur,$to_idta_variante,$Field))) {
                        $RecordToUpdate = WerteRecord::finder()->find('w_jahr = ? AND w_monat = ? AND idtm_struktur = ? AND w_id_variante = ? AND idta_feldfunktion = ?',$year_to_idta_periode,$value,$Element->idtm_struktur,$to_idta_variante,$Field);
                        $RecordToUpdate->w_wert = $newValue;
                        $RecordToUpdate->save();
                    }else{
                        $RecordToWrite = new WerteRecord();
                        $RecordToWrite->w_jahr = $year_to_idta_periode;
                        $RecordToWrite->w_monat = $value;
                        $RecordToWrite->idtm_struktur = $Element->idtm_struktur;
                        $RecordToWrite->w_id_variante = $to_idta_variante;
                        $RecordToWrite->w_wert = $newValue;
                        $RecordToWrite->idta_feldfunktion = $Field;
                        $RecordToWrite->w_dimkey = $this->build_DIMKEY($Element->idtm_struktur);
                        $RecordToWrite->save();
                    }
                }
            }
        }
    }

    private function load_all_cats() {
        $rows = StrukturRecord::finder()->findAll();
        foreach($rows as $row) {
            $this->subcats[$row->parent_idtm_struktur][]=$row->idtm_struktur;
        //$this->parentcats[$row->idtm_struktur]=$row->parent_idtm_struktur;
        }
    }

    private function subCategory_list($subcats,$catID) {
        $this->allowedIDs[] = $catID; //id des ersten Startelements...
        if(array_key_exists($catID,$subcats)) {
            foreach($subcats[$catID] as $subCatID) {
                $this->allowedIDs[] = $this->subCategory_list($subcats, $subCatID);
            }
        }
    }

    private function subCategory_Inlist($subcats,$catID) {
        $lst = $catID; //id des ersten Startelements...
        if(array_key_exists($catID,$subcats)) {
            foreach($subcats[$catID] as $subCatID) {
                $lst .= ",".$this->subCategory_Inlist($subcats, $subCatID);
            }
        }
        return $lst;
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

    public function check_forParent($Node) {
        $SQL = "SELECT * FROM tm_struktur WHERE idtm_struktur = '".$Node->idtm_struktur."'";
        $Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
        if($Result>=1) {
            return true;
        }else {
            return false;
        }
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
}

?>