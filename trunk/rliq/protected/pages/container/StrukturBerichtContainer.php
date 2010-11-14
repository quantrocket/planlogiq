<?php

class StrukturBerichtContainer extends TTemplateControl {

    public function onLoad($param) {
        parent::onLoad($param);
        date_default_timezone_set('Europe/Berlin');
        //$this->SBedidta_struktur_type->Text = $this->page->idta_struktur_type->Text;
        if(!$this->page->isPostBack && !$this->page->isCallback) {
        //$this->buildStrukturBerichtPullDown();
            $this->bindListStrukturBerichtValue();
            $this->buildStrukturBerichtZeilenPullDown();
            $this->buildStrukturBerichtSpaltenPullDown();

            $Usersql = "SELECT idtm_user, user_name FROM tm_user";
            $Userdata = PFH::convertdbObjectArray(UserRecord::finder()->findAllBySql($Usersql),array("idtm_user","user_name"));
            $this->idtm_user->DataSource=$Userdata;
            $this->idtm_user->dataBind();
        }
    }

    public function buildStrukturBerichtPullDown() {
    //$this->SBedff_operator->DataBind();
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $SBprimarykey = "idta_struktur_bericht";
    private $SBfields = array("idtm_user","pivot_struktur_name","pivot_struktur_cdate","sb_order");
    private $SBdatfields = array();
    private $SBhiddenfields = array();
    private $SBboolfields = array("sb_startbericht");

    public function SBClosedButtonClicked($sender, $param) {
        $this->page->mpnlStrukturBericht->Hide();
    }

    public function bindListStrukturBerichtValue() {
        $criteria = new TActiveRecordCriteria();
        //$criteria->Condition="idta_struktur_type = :suchbedingung1";
        //$criteria->Parameters[':suchbedingung1'] = $this->page->idta_struktur_type->Text;

        $this->StrukturBerichtListe->VirtualItemCount = count(StrukturBerichtRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->StrukturBerichtListe->PageSize);
        $criteria->setOffset($this->StrukturBerichtListe->PageSize * $this->StrukturBerichtListe->CurrentPageIndex);
        $this->StrukturBerichtListe->DataKeyField = 'idta_struktur_bericht';

        $this->StrukturBerichtListe->DataSource=StrukturBerichtRecord::finder()->findAll($criteria);
        $this->StrukturBerichtListe->dataBind();
    }

    public function SBDeleteButtonClicked($sender,$param) {

        $tempus='SBed'.$this->SBprimarykey;

        if($this->SBedstruktur_bericht_edit_status->Text == '1') {
            $SBeditRecord = StrukturBerichtRecord::finder()->findByPK($this->$tempus->Text);
            $SBeditRecord->delete();
        }
        $this->bindListStrukturBerichtValue();
    }

    public function load_StrukturBericht($sender,$param) {

        $item = $param->Item;
        $myitem=StrukturBerichtRecord::finder()->findByPK($item->SB_idta_struktur_bericht->Text);

        $tempus = 'SBed'.$this->SBprimarykey;
        $monus = $this->SBprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->SBhiddenfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->SBdatfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->SBboolfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->SBfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->SBedstruktur_bericht_edit_status->Text = 1;
        $this->SBedidta_struktur_bericht->Text = $item->SB_idta_struktur_bericht->Text;
        $this->SBZedidta_struktur_bericht->Text = $item->SB_idta_struktur_bericht->Text;
        $this->bindListStrukturBerichtZeilenValue();
        $this->SBSedidta_struktur_bericht->Text = $item->SB_idta_struktur_bericht->Text;
        $this->bindListStrukturBerichtSpaltenValue();

        $this->loadBerechtigung();
    }

    /* Speichern der Berichte unter...
     * @author : Philipp Frenzel
     * @contact : info@planlogiq.com
     * @function : Save Report As
     * @sender : the sender of the action
     * @param : possible parameters
     */

    public function SBSavedAsButtonClicked($sender,$param){
        //welcher Bericht soll gespeichert werden
        $idta_struktur_bericht = $this->SBedidta_struktur_bericht->Text;
        $ReportInformation = StrukturBerichtRecord::finder()->findByPK($idta_struktur_bericht);
        //als naechstes erzeuchen wir ein leeres Berichtsobjekt
        $NeuerBericht = new StrukturBerichtRecord();
        $NeuerBericht->idtm_user = $ReportInformation->idtm_user;
        $NeuerBericht->pivot_struktur_name = "KOPIE ".$ReportInformation->pivot_struktur_name;
        $NeuerBericht->sb_order = $ReportInformation->sb_order;
        $NeuerBericht->save(); //damit ich die neue ID verwenden kann
        //Als naechstes sollten wir uns alle Zeilen (ta_struktur_bericht_zeilen) aus der bestehenden Berichtsdefinition holen...
        //achtung im ersten schritt holen wir uns nur die felder ohne berechnung...
        //@TODO: Einbauen der Logiken fuer das speichern der berechneten Felder
        $BerichtszeilenORG = StrukturBerichtZeilenRecord::finder()->findAllByidta_struktur_bericht($idta_struktur_bericht);
        foreach($BerichtszeilenORG AS $BerichtszeileORG){
            $BerichtszeileNEW = new StrukturBerichtZeilenRecord();
            $BerichtszeileNEW->idta_feldfunktion = $BerichtszeileORG->idta_feldfunktion;
            $BerichtszeileNEW->sbz_spacer_label = $BerichtszeileORG->sbz_spacer_label;
            $BerichtszeileNEW->sbz_type = $BerichtszeileORG->sbz_type;
            $BerichtszeileNEW->sbz_detail = $BerichtszeileORG->sbz_detail;
            $BerichtszeileNEW->sbz_label = $BerichtszeileORG->sbz_label;
            $BerichtszeileNEW->idta_struktur_bericht = $NeuerBericht->idta_struktur_bericht;
            $BerichtszeileNEW->sbz_order = $BerichtszeileORG->sbz_order;
            $BerichtszeileNEW->sbz_input = $BerichtszeileORG->sbz_input;
            $BerichtszeileNEW->idtm_stammdaten = $BerichtszeileORG->idtm_stammdaten;
            $BerichtszeileNEW->save();
            unset($BerichtszeileNEW);
        }
        //als naechstes noch die Berichtsspalten - auch hier fehlt die berechnung
        $BerichtsspaltenORG = StrukturBerichtSpaltenRecord::finder()->findAllByidta_struktur_bericht($idta_struktur_bericht);
        foreach($BerichtsspaltenORG AS $BerichtsspalteORG){
            $BerichtsspalteNEW = new StrukturBerichtSpaltenRecord();
            $BerichtsspalteNEW->idta_perioden_gap=$BerichtsspalteORG->idta_perioden_gap;
            $BerichtsspalteNEW->sbs_perioden_fix=$BerichtsspalteORG->sbs_perioden_fix;
            $BerichtsspalteNEW->sbs_cumulated=$BerichtsspalteORG->sbs_cumulated;
            $BerichtsspalteNEW->idta_variante=$BerichtsspalteORG->idta_variante;
            $BerichtsspalteNEW->idta_struktur_bericht=$NeuerBericht->idta_struktur_bericht;
            $BerichtsspalteNEW->sbs_order=$BerichtsspalteORG->sbs_order;
            $BerichtsspalteNEW->sbs_input=$BerichtsspalteORG->sbs_input;
            $BerichtsspalteNEW->sbs_idta_variante_fix=$BerichtsspalteORG->sbs_idta_variante_fix;
            $BerichtsspalteNEW->sbs_idtm_struktur=$BerichtsspalteORG->sbs_idtm_struktur;
            $BerichtsspalteNEW->sbs_struktur_switch_type=$BerichtsspalteORG->sbs_struktur_switch_type;
            $BerichtsspalteNEW->sbs_bericht_operator=$BerichtsspalteORG->sbs_bericht_operator;
            $BerichtsspalteNEW->save();
            unset($BerichtsspalteNEW);
        }
        $this->bindListStrukturBerichtValue();
    }

    public function SBSavedButtonClicked($sender,$param) {

        $tempus='SBed'.$this->SBprimarykey;

        if($this->SBedstruktur_bericht_edit_status->Text == '1') {
            $SBeditRecord = StrukturBerichtRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $SBeditRecord = new StrukturBerichtRecord;
        }

        //HIDDEN
        foreach ($this->SBhiddenfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $SBeditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->SBdatfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $SBeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->SBboolfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $SBeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->SBfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $SBeditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $SBeditRecord->save();

        $this->bindListStrukturBerichtValue();
    }

    public function SBNewButtonClicked($sender,$param) {

    //$pivotbericht = $this->SBedidta_struktur_type->Text;

        $tempus = 'SBed'.$this->SBprimarykey;
        $monus = $this->SBprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->SBhiddenfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->SBdatfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->SBboolfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->SBfields as $recordfield) {
            $edrecordfield = 'SBed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->SBedidta_struktur_bericht->Text = 0;
        $this->SBedstruktur_bericht_edit_status->Text = '0';
        $this->bindListStrukturBerichtZeilenValue();
    }

    public function StrukturBerichtList_PageIndexChanged($sender,$param) {
        $this->StrukturBerichtListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStrukturBerichtValue();
    }


    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN

        /* here comes the part for the rows */
	/* here comes the part for the rows */
	/* here comes the part for the rows */
	/* here comes the part for the rows */

    /*
     * @author Philipp Frenzel
     * @param SBZedsbz_type '0'=>"Liste","1"=>"Dimension","2"=>"Berechnung","3"=>"Berechnung auf Berechnung","4"=>"Graph"
     */

    private $SBZprimarykey = "idta_struktur_bericht_zeilen";
    private $SBZfields = array("idta_feldfunktion","sbz_spacer_label","sbz_type","sbz_label","sbz_order","idtm_stammdaten","idta_struktur_bericht");
    private $SBZatfields = array();
    private $SBZhiddenfields = array();
    private $SBZboolfields = array("sbz_detail","sbz_input");

    public function buildStrukturBerichtZeilenPullDown() {
        $this->SBZedidta_feldfunktion->DataSource = PFH::build_SQLPullDown(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name"));
        $this->SBZedidta_feldfunktion->DataBind();

        $this->SBZedidtm_stammdaten->DataSource = PFH::build_SQLPullDown(StammdatenRecord::finder(),"tm_stammdaten",array("idtm_stammdaten","stammdaten_name"));
        $this->SBZedidtm_stammdaten->DataBind();

        $data=array('0'=>"Liste","1"=>"Dimension","3"=>"Berechnung","4"=>"Graph");
        $this->SBZedsbz_type->DataSource = $data;
        $this->SBZedsbz_type->DataBind();
    }

    public function bindListStrukturBerichtZeilenValue() {
        $this->SBZedidta_struktur_bericht->Text = $this->SBedidta_struktur_bericht->Text;
        //$this->buildStrukturBerichtZeilenPullDown();
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="idta_struktur_bericht = :suchbedingung1";
        $criteria->Parameters[':suchbedingung1'] = $this->SBZedidta_struktur_bericht->Text;
        $criteria->OrdersBy['sbz_order']="ASC";

        $this->StrukturBerichtZeilenListe->VirtualItemCount = count(StrukturBerichtZeilenRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->StrukturBerichtZeilenListe->PageSize);
        $criteria->setOffset($this->StrukturBerichtZeilenListe->PageSize * $this->StrukturBerichtZeilenListe->CurrentPageIndex);
        $this->StrukturBerichtZeilenListe->DataKeyField = 'idta_struktur_bericht_zeilen';

        $this->StrukturBerichtZeilenListe->DataSource=StrukturBerichtZeilenRecord::finder()->findAll($criteria);
        $this->StrukturBerichtZeilenListe->dataBind();
    }

    public function SBZDeleteButtonClicked($sender,$param) {
        $tempus='SBZed'.$this->SBZprimarykey;

        if($this->SBZedstruktur_bericht_zeilen_edit_status->Text == '1') {
            $SBZeditRecord = StrukturBerichtZeilenRecord::finder()->findByPK($this->$tempus->Text);
            $SBZeditRecord->delete();
        }
        $this->bindListStrukturBerichtZeilenValue();
    }

    public function load_StrukturBerichtZeilen($sender,$param) {

        $item = $param->Item;
        $myitem=StrukturBerichtZeilenRecord::finder()->findByPK($item->SBZ_idta_struktur_bericht_zeilen->Text);

        $tempus = 'SBZed'.$this->SBZprimarykey;
        $monus = $this->SBZprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->SBZhiddenfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->SBZatfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->SBZboolfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->SBZfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->SBZedstruktur_bericht_zeilen_edit_status->Text = 1;
        $this->SBZedidta_struktur_bericht_zeilen->Text = $item->SBZ_idta_struktur_bericht_zeilen->Text;
        $this->buildSBZCollectorPullDown();
        $this->bindListSBZCollectorValue();
    }

    public function SBZSavedButtonClicked($sender,$param) {

        $tempus='SBZed'.$this->SBZprimarykey;

        if($this->SBZedstruktur_bericht_zeilen_edit_status->Text == '1') {
            $SBZeditRecord = StrukturBerichtZeilenRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $SBZeditRecord = new StrukturBerichtZeilenRecord;
        }

        //HIDDEN
        foreach ($this->SBZhiddenfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $SBZeditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->SBZatfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $SBZeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->SBZboolfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $SBZeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->SBZfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $SBZeditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $SBZeditRecord->save();

        $this->bindListStrukturBerichtZeilenValue();
    }

    public function SBZNewButtonClicked($sender,$param) {

        $pivotbericht = $this->SBZedidta_struktur_bericht->Text;

        $tempus = 'SBZed'.$this->SBZprimarykey;
        $monus = $this->SBZprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->SBZhiddenfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->SBZatfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->SBZboolfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->SBZfields as $recordfield) {
            $edrecordfield = 'SBZed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->SBZedidta_struktur_bericht->Text = $pivotbericht;
        $this->SBZedstruktur_bericht_zeilen_edit_status->Text = '0';
    }


    public function StrukturBerichtZeilenList_PageIndexChanged($sender,$param) {
        $this->StrukturBerichtZeilenListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStrukturBerichtZeilenValue();
    }


    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN

    /* here comes the part for the columns */
	/* here comes the part for the columns */
	/* here comes the part for the columns */
	/* here comes the part for the columns */

    private $SBSprimarykey = "idta_struktur_bericht_spalten";
    private $SBSfields = array("idta_variante","idta_perioden_gap","sbs_order","idta_struktur_bericht","sbs_idtm_struktur","sbs_struktur_switch_type","sbs_bericht_operator");
    private $SBSatfields = array();
    private $SBShiddenfields = array();
    private $SBSboolfields = array("sbs_perioden_fix","sbs_input","sbs_cumulated","sbs_idta_variante_fix");

    public function buildStrukturBerichtSpaltenPullDown() {
        $this->SBSedidta_variante->DataSource = PFH::build_SQLPullDown(VarianteRecord::finder(),"ta_variante",array("idta_variante","var_descr"));
        $this->SBSedidta_variante->DataBind();

        $sbs_struktur_switch_type = array(0=>"none",1=>"fix",2=>"variable");
        $this->SBSedsbs_struktur_switch_type->DataSource=$sbs_struktur_switch_type;
        $this->SBSedsbs_struktur_switch_type->dataBind();

        $sbs_bericht_operator = array("SUM"=>"SUM","AVG"=>"AVG","COUNT"=>"COUNT","MAX"=>"MAX","MIN"=>"MIN","MEDIAN"=>"MEDIAN","STDDEV"=>"STDDEV");
        $this->SBSedsbs_bericht_operator->DataSource=$sbs_bericht_operator;
        $this->SBSedsbs_bericht_operator->dataBind();
    }

    public function bindListStrukturBerichtSpaltenValue() {
        $this->SBSedidta_struktur_bericht->Text = $this->SBedidta_struktur_bericht->Text;
        //$this->buildStrukturBerichtSpaltenPullDown();
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="idta_struktur_bericht = :suchbedingung1";
        $criteria->Parameters[':suchbedingung1'] = $this->SBSedidta_struktur_bericht->Text;

        $this->StrukturBerichtSpaltenListe->VirtualItemCount = count(StrukturBerichtSpaltenRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->StrukturBerichtSpaltenListe->PageSize);
        $criteria->setOffset($this->StrukturBerichtSpaltenListe->PageSize * $this->StrukturBerichtSpaltenListe->CurrentPageIndex);
        $this->StrukturBerichtSpaltenListe->DataKeyField = 'idta_struktur_bericht_spalten';

        $this->StrukturBerichtSpaltenListe->DataSource=StrukturBerichtSpaltenRecord::finder()->findAll($criteria);
        $this->StrukturBerichtSpaltenListe->dataBind();
    }

    public function SBSDeleteButtonClicked($sender,$param) {
        $tempus='SBSed'.$this->SBSprimarykey;

        if($this->SBSedstruktur_bericht_spalten_edit_status->Text == '1') {
            $SBSeditRecord = StrukturBerichtSpaltenRecord::finder()->findByPK($this->$tempus->Text);
            $SBSeditRecord->delete();
        }
        $this->bindListStrukturBerichtSpaltenValue();
    }

    public function load_StrukturBerichtSpalten($sender,$param) {

        $item = $param->Item;
        $myitem=StrukturBerichtSpaltenRecord::finder()->findByPK($item->SBS_idta_struktur_bericht_spalten->Text);

        $tempus = 'SBSed'.$this->SBSprimarykey;
        $monus = $this->SBSprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->SBShiddenfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->SBSatfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->SBSboolfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->SBSfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->SBSedstruktur_bericht_spalten_edit_status->Text = 1;
        $this->SBSedidta_struktur_bericht_spalten->Text = $item->SBS_idta_struktur_bericht_spalten->Text;

    }

    public function SBSSavedButtonClicked($sender,$param) {

        $tempus='SBSed'.$this->SBSprimarykey;

        if($this->SBSedstruktur_bericht_spalten_edit_status->Text == '1') {
            $SBSeditRecord = StrukturBerichtSpaltenRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $SBSeditRecord = new StrukturBerichtSpaltenRecord;
        }

        //HIDDEN
        foreach ($this->SBShiddenfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $SBSeditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->SBSatfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $SBSeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->SBSboolfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $SBSeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->SBSfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $SBSeditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $SBSeditRecord->save();

        $this->bindListStrukturBerichtSpaltenValue();
    }

    public function SBSNewButtonClicked($sender,$param) {

        $pivotbericht = $this->SBSedidta_struktur_bericht->Text;

        $tempus = 'SBSed'.$this->SBSprimarykey;
        $monus = $this->SBSprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->SBShiddenfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->SBSatfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->SBSboolfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->SBSfields as $recordfield) {
            $edrecordfield = 'SBSed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->SBSedidta_struktur_bericht->Text = $pivotbericht;
        $this->SBSedstruktur_bericht_spalten_edit_status->Text = '0';
    }

    public function StrukturBerichtSpaltenList_PageIndexChanged($sender,$param) {
        $this->StrukturBerichtSpaltenListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStrukturBerichtSpaltenValue();
    }


    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN

     /* here comes the part for the collector */
	/* here comes the part for the collector */
	/* here comes the part for the collector */
	/* here comes the part for the collector */

    private $SBZCOLprimarykey = "idta_sbz_collector";
    private $SBZCOLfields = array("idta_struktur_bericht_zeilen","row_idta_struktur_bericht_zeilen","sbz_collector_operator");
    private $SBZCOLatfields = array();
    private $SBZCOLhiddenfields = array();
    private $SBZCOLboolfields = array();

    public function buildSBZCollectorPullDown() {
        $this->SBZCOLedrow_idta_struktur_bericht_zeilen->DataSource = PFH::build_SQLPullDown(StrukturBerichtZeilenRecord::finder(),"ta_struktur_bericht_zeilen",array("idta_struktur_bericht_zeilen","sbz_label"),"idta_struktur_bericht = ".$this->SBedidta_struktur_bericht->Text);
        $this->SBZCOLedrow_idta_struktur_bericht_zeilen->DataBind();

        $data=array('+'=>"+","-"=>"-","/"=>"/","*"=>"*");
        $this->SBZCOLedsbz_collector_operator->DataSource = $data;
        $this->SBZCOLedsbz_collector_operator->DataBind();
    }

    public function bindListSBZCollectorValue() {
        $this->SBZCOLedidta_struktur_bericht_zeilen->Text = $this->SBZedidta_struktur_bericht_zeilen->Text;
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="idta_struktur_bericht_zeilen = :suchbedingung1";
        $criteria->Parameters[':suchbedingung1'] = $this->SBZCOLedidta_struktur_bericht_zeilen->Text;

        $this->SBZCollectorListe->VirtualItemCount = count(SBZCollectorRecord::finder()->findAll($criteria));

        $criteria->setLimit($this->SBZCollectorListe->PageSize);
        $criteria->setOffset($this->SBZCollectorListe->PageSize * $this->SBZCollectorListe->CurrentPageIndex);
        $this->SBZCollectorListe->DataKeyField = 'idta_sbz_collector';

        $this->SBZCollectorListe->DataSource=SBZCollectorRecord::finder()->findAll($criteria);
        $this->SBZCollectorListe->dataBind();
    }

    public function SBZCOLDeleteButtonClicked($sender,$param) {

        $tempus='SBZCOLed'.$this->SBZCOLprimarykey;

        if($this->SBZCOLedsbzcollector_edit_status->Text == '1') {
            $SBZCOLeditRecord = SBZCollectorRecord::finder()->findByPK($this->$tempus->Text);
            $SBZCOLeditRecord->delete();
        }
        $this->bindListSBZCollectorValue();
    }

    public function load_SBZCollector($sender,$param) {

        $item = $param->Item;
        $myitem=SBZCollectorRecord::finder()->findByPK($item->SBZCOL_idta_collector->Text);

        $tempus = 'SBZCOLed'.$this->SBZCOLprimarykey;
        $monus = $this->SBZCOLprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->SBZCOLhiddenfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->SBZCOLatfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->SBZCOLboolfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //NON DATUM
        foreach ($this->SBZCOLfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->SBZCOLedsbzcollector_edit_status->Text = 1;
        $this->SBZCOLedidta_sbz_collector->Text = $item->SBZCOL_idta_collector->Text;

    }

    public function SBZCOLSavedButtonClicked($sender,$param) {

        $tempus='SBZCOLed'.$this->SBZCOLprimarykey;

        if($this->SBZCOLedsbzcollector_edit_status->Text == '1') {
            $SBZCOLeditRecord = SBZCollectorRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $SBZCOLeditRecord = new SBZCollectorRecord;
        }

        //HIDDEN
        foreach ($this->SBZCOLhiddenfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $SBZCOLeditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->SBZCOLatfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $SBZCOLeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->SBZCOLboolfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $SBZCOLeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->SBZCOLfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $SBZCOLeditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $SBZCOLeditRecord->save();

        $this->bindListSBZCollectorValue();
    }

    public function SBZCOLNewButtonClicked($sender,$param) {

        $pivotbericht = $this->SBZedidta_struktur_bericht_zeilen->Text;

        $tempus = 'SBZCOLed'.$this->SBZCOLprimarykey;
        $monus = $this->SBZCOLprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->SBZCOLhiddenfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->SBZCOLatfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->SBZCOLboolfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->SBZCOLfields as $recordfield) {
            $edrecordfield = 'SBZCOLed'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->SBZCOLedidta_struktur_bericht_zeilen->Text = $pivotbericht;
        $this->SBZCOLedsbzcollector_edit_status->Text = '0';
    }

    public function SBZCollectorList_PageIndexChanged($sender,$param) {
        $this->SBZCollectorListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListSBZCollectorValue();
    }


//ENDE DER RISIKEN
//ENDE DER RISIKEN
//ENDE DER RISIKEN

    //the fields for the BerechtigungRecord
    private $XXRprimarykey = "idxx_berechtigung";
    private $XXRfields = array("xx_id","xx_modul","idtm_user");
    private $XXRdatfields = array();
    private $XXRtimefields = array();
    private $XXRhiddenfields = array();
    private $XXRboolfields = array("xx_read","xx_write","xx_create","xx_delete");

    private function loadBerechtigung($sender='',$param='') {
        $Criteria = new TActiveRecordCriteria();
        $Criteria->Condition = "xx_id = :idta_struktur_bericht AND xx_modul = :modul";
        $Criteria->Parameters[':idta_struktur_bericht'] = $this->SBedidta_struktur_bericht->Text;
        $Criteria->Parameters[':modul'] = "idta_struktur_bericht";
        $this->lstBerechtigung->DataSource=BerechtigungRecord::finder()->findAll($Criteria);
        $this->lstBerechtigung->dataBind();
    }

    public function editlstBerechtigung($sender,$param) {
        $item = $param->Item;
        $myitem=BerechtigungRecord::finder()->findByPK($item->lst_idxx_berechtigung->Text);

        $monus = $this->XXRprimarykey;
        $this->$monus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $this->$recordfield->setText($myitem->$recordfield);
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $this->$recordfield->setDate($myitem->$recordfield);
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $this->$recordfield->setChecked($myitem->$recordfield);
        }
        //TIME
        foreach ($this->XXRtimefields as $recordfield) {
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$recordfield->Text = $my_time_text;
        }
        //NON DATUM
        foreach ($this->XXRfields as $recordfield) {
            $this->$recordfield->Text = $myitem->$recordfield;
        }
        $this->berechtigung_edit_status->Text = 1;
        $this->loadberechtigung();
    }

    public function XXRDeleteClicked($sender,$param) {
        $Record = BerechtigungRecord::finder()->findByPK($this->{$this->XXRprimarykey}->Text);
        $Record->delete();
        $this->loadBerechtigung();
        $this->XXRNewClicked($sender,$param);
    }

    public function lstBerechtigung_PageIndexChanged($sender,$param) {
        $this->lstBerechtigung->CurrentPageIndex = $param->NewPageIndex;
        $this->loadBerechtigung();
    }

    public function XXRNewClicked($sender,$param) {
        $monus = $this->XXRprimarykey;
        $this->$monus->Text = '0';

        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $this->$recordfield->setValue('0');
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $this->$recordfield->setDate(date('Y-m-d',time()));
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $this->$recordfield->setChecked(0);
        }
        //NON DATUM
        foreach ($this->XXRtimefields as $recordfield) {
            $this->$recordfield->Text = '00:00';
        }
        //NON DATUM
        foreach ($this->XXRfields as $recordfield) {
            $this->$recordfield->Text = '0';
        }
        $this->xx_modul->Text = "idta_struktur_bericht";
        $this->xx_id->Text = $this->SBedidta_struktur_bericht->Text;
        $this->berechtigung_edit_status->Text = '0';
    }

    public function XXRSaveClicked($sender,$param) {
        if($this->berechtigung_edit_status->Text == '1') {
            $BREditRecord = BerechtigungRecord::finder()->findByPK($this->{$this->XXRprimarykey}->Text);
        }
        else {
            $BREditRecord = new BerechtigungRecord;
        }
        //HIDDEN
        foreach ($this->XXRhiddenfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Value;
        }
        //DATUM
        foreach ($this->XXRdatfields as $recordfield) {
            $BREditRecord->$recordfield=date('Y-m-d',$this->$recordfield->TimeStamp);
        }
        //BOOL
        foreach ($this->XXRboolfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Checked?1:0;
        }
        foreach ($this->XXRtimefields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Text;
        }
        foreach ($this->XXRfields as $recordfield) {
            $BREditRecord->$recordfield = $this->$recordfield->Text;
        }
        $BREditRecord->save();
        $this->loadBerechtigung();
    }

}

?>