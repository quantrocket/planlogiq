<?php

/*
 * This template -> must be used as draft
 * by planlogiq :: pf@com-x-cha.com
 */
Prado::using('Application.app_code.PFBackCalculator');

class HDBPInputMask extends TPage {

    private $TTWerte;
    private $MaskInputFields;

    public function onLoad($param){
        $sender="";
        if(!$this->isPostback && !$this->isCallback){
            $this->initValues($sender, $param);
        }
    }

    public function validateUser($sender, $param) {
        $authManager = $this->Application->getModule('auth');
        if (!$authManager->login(strtolower($this->Username->Text), $this->Password->Text))
            $param->IsValid = false;
    }

    public function loginButtonClicked($sender, $param) {
        if ($this->Page->IsValid) {
            //here you must enter the adress of the custom mask, so that the user is send to the correct page after login
            $this->Response->redirect($this->getRequest()->constructUrl('page', "struktur.custommask.HDBPInputMask"));
        }
    }

    public function initValues($sender,$param){
        $this->MaskInputFields = CustomMaskFieldRecord::finder()->findAll("cuf_maskenname = ?",$this->cuf_maskenname->Text);
        if(count($this->MaskInputFields)>=1){
            foreach($this->MaskInputFields AS $MaskField){
                //als erstes lese ich die Periode aus
                $Periode = PeriodenRecord::finder()->findByidta_perioden($MaskField->idta_perioden);
                $w_monat = $Periode->per_intern;
                $w_jahr = $this->getYearByMonth($w_monat);
                //auslesen des roots, damit ich den linken und den rechten wert habe...
                $parent_idtm_struktur = StrukturRecord::finder()->findByidtm_struktur($this->idtm_struktur->Text);
                //jetzt suche ich die entsprechende dimension innerhalb der selektion
                $idtm_struktur = StrukturRecord::finder()->find('(struktur_lft BETWEEN ? AND ?) AND idtm_stammdaten = ?',$parent_idtm_struktur->struktur_lft,$parent_idtm_struktur->struktur_rgt,$MaskField->idtm_stammdaten)->idtm_struktur;
                //print_r("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$idtm_struktur."' AND idta_feldfunktion = '".$MaskField->idta_feldfunktion."' AND w_jahr = '".$w_jahr."' AND w_monat = '".$w_monat."' AND w_id_variante = '".$MaskField->idta_variante."' LIMIT 1");
                $myttvalue = WerteRecord::finder()->findBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$idtm_struktur."' AND idta_feldfunktion = '".$MaskField->idta_feldfunktion."' AND w_jahr = '".$w_jahr."' AND w_monat = '".$w_monat."' AND w_id_variante = '".$MaskField->idta_variante."' LIMIT 1");
                if(is_object($myttvalue))
                    $this->{$MaskField->cuf_maskenid}->Text=number_format($this->cleanInputValue($myttvalue->w_wert, $MaskField,0),$MaskField->cuf_numberformat,'.','');
            }
        }
    }

    public function cleanInputValue($value,$fieldobjekt,$read=1){
        $temporary = floatval(str_replace(",", ".", $value));
        if($fieldobjekt->cuf_numberformat==1 AND $read==1){
            $temporary = floatval((100-$temporary)/100);
        }
        if($fieldobjekt->cuf_numberformat==1 AND $read==0){
            $temporary = floatval(100-($temporary*100));
        }
        return $temporary;
    }
    
    public function updateDBValue($sender,$param){
        $MaskField = CustomMaskFieldRecord::finder()->find("cuf_maskenname = ? AND cuf_maskenid = ?",$this->cuf_maskenname->Text,$sender->parent->Id);
        //als erstes lese ich die Periode aus
        $Periode = PeriodenRecord::finder()->findByidta_perioden($MaskField->idta_perioden);
        $w_monat = $Periode->per_intern;
        $w_jahr = $this->getYearByMonth($w_monat);

        //auslesen des roots, damit ich den linken und den rechten wert habe...
        $parent_idtm_struktur = StrukturRecord::finder()->findByidtm_struktur($this->idtm_struktur->Text);
        //jetzt suche ich die entsprechende dimension innerhalb der selektion
        $idtm_struktur = StrukturRecord::finder()->find('(struktur_lft BETWEEN ? AND ?) AND idtm_stammdaten = ?',$parent_idtm_struktur->struktur_lft,$parent_idtm_struktur->struktur_rgt,$MaskField->idtm_stammdaten)->idtm_struktur;

        //hier startet jetzt der Part, wo ich nur eine Periode habe -> entweder SubJahr oder Jahr...
                $PFBackCalculator = new PFBackCalculator();
                $PFBackCalculator->setVariante($MaskField->idta_variante);
                /* Folgende Parameter sind zur Berechnung der Werte notwendig...
                 * @param idta_periode -> die interne Periodenbezeichnung -> 10001 für 1. Jahr oder 1 für 1 Monat (Bsp)
                 * @param idtm_struktur -> die Struktur ID, auf der die Werte nachher gespreichert werden sollen
                 * @param w_dimkey -> der Schlüssel, der angehängt werden soll...
                 * @param assoc_array(feldbezug=>wert) -> array mit den Werten, die als "neu" betrachtet werden sollen...
                 */
                $PFBackCalculator->setStartPeriod($w_monat);
                $PFBackCalculator->setStartNode($idtm_struktur);
                //vorbereiten des Wertearrays, damit die bestehenden Werte in der Datenbank, mit den neuen Uerberschrieben werden koennen
                //jetzt laden wir die einzelnen Werte
                $NEWWerteRecord = WerteRecord::finder()->findBySql("SELECT w_wert FROM tt_werte WHERE idtm_struktur = '".$idtm_struktur."' AND idta_feldfunktion = '".$MaskField->idta_feldfunktion."' AND w_jahr = '".$w_jahr."' AND w_monat = '".$w_monat."' AND w_id_variante = '".$MaskField->idta_variante."' LIMIT 1");
                $w_wert[$MaskField->idta_feldfunktion] = $this->cleanInputValue($sender->Text, $MaskField,1);
                $PFBackCalculator->setNewValues($w_wert);
                $PFBackCalculator->run();
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
