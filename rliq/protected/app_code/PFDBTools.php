<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class PFDBTools extends TApplication {

    public function __construct() {
        //allgemeine Klasse fuer die DB Wartung
    }

    public static function InitDBValues($idtm_struktur,$idta_variante,$idta_perioden='10001'){
            //Step One, find all relevant IDs
            $StartRecord = StrukturRecord::finder()->findByidtm_struktur($idtm_struktur);
            if(count($StartRecord)==1){
                $sql = "SELECT idta_struktur_type FROM tm_struktur WHERE struktur_lft BETWEEN ".$StartRecord->struktur_lft." AND ".$StartRecord->struktur_rgt." GROUP BY idta_struktur_type";
                //here I recieve the array of values containing the elements to be changed
                $GroupsToChange = StrukturRecord::finder()->findAllBySQL($sql);
                foreach($GroupsToChange AS $Group){
                    $checker = FeldfunktionRecord::finder()->count("idta_struktur_type = ?",$Group->idta_struktur_type);
                    if($checker>0){
                        $sqlElemente = "SELECT idtm_struktur,idta_struktur_type FROM tm_struktur WHERE (struktur_lft BETWEEN ".$StartRecord->struktur_lft." AND ".$StartRecord->struktur_rgt.") AND idta_struktur_type=".$Group->idta_struktur_type;
                        $ElementsToChange = StrukturRecord::finder()->findAllBySQL($sqlElemente);
                        foreach($ElementsToChange AS $Element){
                            echo $Element->idtm_struktur;
                            $ObjSaver = new PFBackCalculator();
                            $ObjSaver->setStartPeriod($idta_perioden);
                            $ObjSaver->setVariante($idta_variante);
                            $ObjSaver->setStartNode($Element->idtm_struktur);
                            $ObjSaver->build_DIMKEY($Element->idtm_struktur);
                            $ObjSaver->initTTWerte($Element->idtm_struktur,$Element->idta_struktur_type);
                            unset($ObjSaver);
                        }
                    empty($checker);
                    }
                }
                
                unset($ElementsToChange);
            }
        }

    public static function rebuild_NestedInformation($parent, $left) {
    // the right value of this node is the left value + 1
        $right = $left+1;

        // get all children of this node
        $TreeRecords = StrukturRecord::finder()->findAllByparent_idtm_struktur($parent);
        if(count($TreeRecords)>=1){
            foreach($TreeRecords as $TreeRecord) {
            // recursive execution of this function for each
            // child of this node
            // $right is the current right value, which is
            // incremented by the rebuild_tree function
                $right = $this->rebuild_NestedInformation($TreeRecord->idtm_struktur, $right);
            }
        }

        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value
        if($parent!=0){
            $TreeChangeRecord = StrukturRecord::finder()->findByidtm_struktur($parent);
            $TreeChangeRecord->struktur_lft = $left;
            $TreeChangeRecord->struktur_rgt = $right;
            $TreeChangeRecord->save();
            unset($TreeChangeRecord);
        }
        
        // return the right value of this node + 1
        return $right+1;
    }

    public static function cleanStrukturStruktur(){
        //sql statement to find elements without matching parent
        $sql = "SELECT idtm_struktur FROM tm_struktur WHERE idtm_struktur NOT IN (SELECT a.idtm_struktur FROM tm_struktur a INNER JOIN tm_struktur b ON a.parent_idtm_struktur = b.idtm_struktur) AND idtm_struktur > 1";
        $StrukturElements = StrukturRecord::finder()->findAllBySql($sql);
        foreach($StrukturElements AS $StrukturElement){
            WerteRecord::finder()->deleteAll('idtm_struktur = ?', $StrukturElement->idtm_struktur);
            StrukturRecord::finder()->deleteByidtm_struktur($StrukturElement->idtm_struktur);
            //debug only echo "DE\n";
        }
        unset($StrukturElements);

        //null-Werte entfernen
        $sql = "SELECT idtm_struktur FROM tm_struktur WHERE ISNULL(struktur_lft)";
        $StrukturElements = StrukturRecord::finder()->findAllBySql($sql);
        foreach($StrukturElements AS $StrukturElement){
            WerteRecord::finder()->deleteAll('idtm_struktur = ?', $StrukturElement->idtm_struktur);
            StrukturRecord::finder()->deleteByidtm_struktur($StrukturElement->idtm_struktur);
            //debug only echo "DE\n";
        }
        unset($StrukturElements);
    }

    public static function initOrganisationStruktur(){
        $OrganisationType = OrganisationTypeRecord::finder()->findAll();
        if(count($OrganisationType)>=1){
            foreach($OrganisationType As $OrgType){
                $Organisation = OrganisationRecord::finder()->findByorg_fk_internal($OrgType->org_type_name);
                if(count($Organisation)!=1){
                    $Organisation = new OrganisationRecord();
                }
                $Organisation->idta_organisation_type = 1;
                $Organisation->idta_organisation_art = 1;
                $Organisation->idtm_ressource = 1;
                $Organisation->org_name = $OrgType->org_type_name;
                $Organisation->org_fk_internal = $OrgType->org_type_name;
                $Organisation->parent_idtm_organisation = 1;
                $Organisation->save();
                /*foreach(range('A','Z') as $i){
                    $OrgChild = OrganisationRecord::finder()->findByorg_fk_internal($OrgType->org_type_name.$i);
                    if(count($OrgChild)!=1){
                        $OrgChild = new OrganisationRecord();
                    }
                    $OrgChild->idta_organisation_type = 1;
                    $OrgChild->idta_organisation_art = 1;
                    $OrgChild->idtm_ressource = 1;
                    $OrgChild->org_name = $i;
                    $OrgChild->org_fk_internal = $OrgType->org_type_name.$i;
                    $OrgChild->parent_idtm_organisation = $Organisation->idtm_organisation;
                    $OrgChild->save();
                }*/
                unset($Organisation);
            }
        }
    }

    public static function initStrukturLink(){
        //als erstes holen wir uns die Definitionen aus ta_stammdaten_link
        $StammdatenLinks = StammdatenLinkRecord::finder()->findAll();
        foreach($StammdatenLinks AS $StammdatenLink){
            //als nächstes hole ich mit die Werte der Dimensionsgruppe in der gesucht werden soll, diese gilt dann als suchkriterium...
            $StammdatenGroups = StammdatenRecord::finder()->findAllByidta_stammdaten_group($StammdatenLink->idta_stammdaten_group);
            foreach($StammdatenGroups AS $StammdatenGroup){
                //als nächstes muss ich in der Struktur suchen, welcher Bereich für die jeweiligen Kennungen gilt...
                $StrukturWechselKnoten = StrukturRecord::finder()->findByidtm_stammdaten($StammdatenGroup->idtm_stammdaten);
                if(is_object($StrukturWechselKnoten)){
                    //da lft und rgt jetzt bekannt sind, kann ich mit dem folgenden SQL die entsprechenden Elemente fuer idtm_str_from und idtm_str_to finden
                    $SQL = "SELECT idtm_struktur FROM tm_struktur WHERE (struktur_lft BETWEEN ".$StrukturWechselKnoten->struktur_lft." AND ".$StrukturWechselKnoten->struktur_rgt.") AND idtm_stammdaten='".$StammdatenLink->idtm_stammdaten_from."'";
                    $idtm_struktur_from = StrukturRecord::finder()->findBySql($SQL)->idtm_struktur;
                    $SQL = "SELECT idtm_struktur FROM tm_struktur WHERE (struktur_lft BETWEEN ".$StrukturWechselKnoten->struktur_lft." AND ".$StrukturWechselKnoten->struktur_rgt.") AND idtm_stammdaten='".$StammdatenLink->idtm_stammdaten_to."'";
                    $idtm_struktur_to = StrukturRecord::finder()->findBySql($SQL)->idtm_struktur;
                    //jetzt haben wir alle informationen um den Datensatz zu schreiben
                    $TestRecord = StrukturStrukturRecord::finder()->count("idtm_struktur_from = ? AND idtm_struktur_to = ? AND idta_feldfunktion = ?",$idtm_struktur_from,$idtm_struktur_to,$StammdatenLink->idta_feldfunktion_from);
                    if($TestRecord==1){
                        $ChangeRecord=StrukturStrukturRecord::finder()->find("idtm_struktur_from = ? AND idtm_struktur_to = ? AND idta_feldfunktion = ?",$idtm_struktur_from,$idtm_struktur_to,$StammdatenLink->idta_feldfunktion_from);
                    }else{
                        $ChangeRecord= new StrukturStrukturRecord();
                    }
                    $ChangeRecord->idtm_struktur_from = $idtm_struktur_from;
                    $ChangeRecord->idtm_struktur_to = $idtm_struktur_to;
                    $ChangeRecord->idta_feldfunktion = $StammdatenLink->idta_feldfunktion_from;
                    $ChangeRecord->save();
                    unset($TestRecord);
                    unset($ChangeRecord);
                }
            }
            unset($StammdatenGroups);
        }
    }

    public static function calculateSplasherYear(){
        //erst einmal holen wir uns alle Werte...
        $sql = "SELECT idta_variante, spl_jahr, idtm_stammdaten, idta_feldfunktion,to_idtm_stammdaten, SUM(spl_faktor) AS spl_faktor FROM `tt_splasher` GROUP BY idta_variante, spl_jahr, idtm_stammdaten, idta_feldfunktion,to_idtm_stammdaten WHERE spl_monat < 9999";
        $SplasherYearRecords = TTSplasherRecord::finder()->findAllBySQL($sql);
        foreach($SplasherYearRecords AS $SplasherYearRecord){
            //checken, ob bereits ein Datensatz existiert
            $CheckRecord = TTSplasherRecord::finder()->find('idta_variante = ? AND spl_jahr = ? AND spl_monat = ? AND idtm_stammdaten = ? AND idta_feldfunktion = ? AND to_idtm_stammdaten = ?',$SplasherYearRecord->idta_variante, $SplasherYearRecord->spl_jahr, $SplasherYearRecord->spl_jahr, $SplasherYearRecord->idtm_stammdaten, $SplasherYearRecord->idta_feldfunktion,$SplasherYearRecord->to_idtm_stammdaten);
            if(!is_object($CheckRecord)){
                $CheckRecord = new TTSplasherRecord();
            }
            $CheckRecord->idta_variante = $SplasherYearRecord->idta_variante;
            $CheckRecord->spl_jahr = $SplasherYearRecord->spl_jahr;
            $CheckRecord->spl_monat = $SplasherYearRecord->spl_jahr;
            $CheckRecord->idtm_stammdaten = $SplasherYearRecord->idtm_stammdaten;
            $CheckRecord->idta_feldfunktion = $SplasherYearRecord->idta_feldfunktion;
            $CheckRecord->to_idtm_stammdaten = $SplasherYearRecord->to_idtm_stammdaten;
            $CheckRecord->spl_faktor = $SplasherYearRecord->spl_faktor;
            $CheckRecord->save();
            unset($CheckRecord);
        }
    }

}

?>