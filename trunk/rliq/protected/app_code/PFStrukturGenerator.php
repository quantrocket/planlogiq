<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class PFStrukturGenerator extends TPage {

    private $allowedIds; // eine liste der erlaubten kinder

    public function addChildElmenents($StammdatenRecord, $parent_idtm_struktur=0,$planungssicht=1) {
    //hier holen wir erst einmal die Werte aus der tt_stammdaten_stammdaten, die zur aktuellen idtm_stammdaten gehoeren...
        $ChildElements = TTStammdatenStammdatenRecord::finder()->findAll('idtm_stammdaten_group = ? AND idta_stammdatensicht = ?',$StammdatenRecord->idtm_stammdaten,$planungssicht);
        if(count($ChildElements)>=1) {
            foreach($ChildElements AS $ChildElement) {
                $ChildRecord = StammdatenRecord::finder()->find('idtm_stammdaten=?',$ChildElement->idtm_stammdaten);
                if(count($ChildRecord)==1) {
                //hier muss ich noch auslesen, welchen Strukturtyp die neuen Elemente haben...
                    $idta_struktur_type = StammdatenGroupRecord::finder()->findByidta_stammdaten_group($ChildRecord->idta_stammdaten_group)->idta_struktur_type;
                    $new_parent_idtm_struktur = $this->ifNodeExists($ChildRecord->idtm_stammdaten, $ChildRecord->stammdaten_name, $parent_idtm_struktur, $idta_struktur_type,0,$planungssicht);
                    $this->addChildElmenents($ChildRecord,$new_parent_idtm_struktur);
                }
            }
        }
        else {
        //hier muss der check hin, ob es eine eine naechste Ebene ohne Original gibt
            $rsql = "SELECT stammdaten_group_name, idta_stammdaten_group, idta_struktur_type FROM vv_stammdaten_group WHERE parent_idta_stammdaten_group = '".$StammdatenRecord->idta_stammdaten_group."' AND idta_stammdatensicht = ". $planungssicht ."  AND sts_stammdaten_group_use = 1 ORDER BY idta_stammdaten_group";
            $BElements = StammdatenGroupView::finder()->findAllBySQL($rsql);
            if(count($BElements)>=1) {
                foreach($BElements AS $RElement) {
                    $LOne = StammdatenRecord::finder()->findAll('idta_stammdaten_group = ? AND stammdaten_aktiv = 0',$RElement->idta_stammdaten_group);
                    foreach($LOne AS $LElement) {
                    //check, ob der knoten schon existiert, wenn ja, wird er automatisch umgeordnet
                        $new_parent_idtm_struktur = $this->ifNodeExists($LElement->idtm_stammdaten, $LElement->stammdaten_name, $parent_idtm_struktur, $RElement->idta_struktur_type,0,$planungssicht);
                        //jetzt schauen wir, ob bei den Kindern Informationen aus der Zuordnung existieren...
                        $this->addChildElmenents($LElement,$new_parent_idtm_struktur,$planungssicht);
                    }
                }
            }
        }
    }

    public function ifNodeExists($idtm_stammdaten,$stammdaten_name, $parent_idtm_struktur=0,$idta_struktur_type=1,$SingleEbene=1,$planungssicht=1) {
        $return_idtm_struktur = "";
        if($SingleEbene==1) {
            $myRecord = StrukturRecord::finder()->find('idtm_stammdaten = ? AND idta_stammdatensicht = ?',$idtm_stammdaten,$planungssicht);
            $parent_idtm_struktur==NULL?$parent_idtm_struktur=0:'';
            $idta_struktur_type==NULL?$idta_struktur_type=1:'';
            if(count($myRecord)>=1) {
                if($myRecord->parent_idtm_struktur != $parent_idtm_struktur OR $myRecord->struktur_name != $stammdaten_name) {
                    $myRecord->parent_idtm_struktur = $parent_idtm_struktur;
                    $myRecord->struktur_name = $stammdaten_name;
                    $myRecord->save();
                }
                $return_idtm_struktur = $myRecord->idtm_struktur;
            }else {
                $newStruktur = new StrukturRecord();
                $newStruktur->idta_struktur_type = $idta_struktur_type;
                $newStruktur->idtm_stammdaten = $idtm_stammdaten;
                $newStruktur->parent_idtm_struktur = $parent_idtm_struktur;
                $newStruktur->struktur_name = $stammdaten_name;
                $newStruktur->struktur_lft = 0;
                $newStruktur->struktur_rgt = 0;
                $newStruktur->idta_stammdatensicht = $planungssicht;
                $newStruktur->save();
                $return_idtm_struktur = $newStruktur->idtm_struktur;
            }
        }else {
            $parent_idtm_struktur==NULL?$parent_idtm_struktur=0:'';
            $this->allowedIds = array();
            $this->getChildren($parent_idtm_struktur);
            $idtm_struktur_in_sql="";
            $counter=0;
            foreach($this->allowedIds As $key=>$value) {
                if($value!='') {
                    $counter==0?$idtm_struktur_in_sql .= "'".$value."' ":$idtm_struktur_in_sql .= ",'".$value."' ";
                    $counter++;
                }
            }
            $idta_struktur_type==NULL?$idta_struktur_type=1:'';
            $parent_idtm_stammdaten = StrukturRecord::finder()->findByidtm_struktur($parent_idtm_struktur)->idtm_stammdaten;
            $parent_idtm_stammdaten==''?$parent_idtm_stammdaten=0:'';
            $sql = "SELECT a.idtm_struktur AS idtm_struktur FROM tm_struktur a INNER JOIN tm_struktur b ON a.parent_idtm_struktur = b.idtm_struktur WHERE a.idtm_stammdaten=".$idtm_stammdaten." AND b.idtm_stammdaten=".$parent_idtm_stammdaten." AND b.idtm_struktur IN (".$idtm_struktur_in_sql.") AND b.idta_stammdatensicht = ".$planungssicht." GROUP BY idtm_struktur LIMIT 1";
            $MyResultRecord = StrukturRecord::finder()->findBySQL($sql);
            if(count($MyResultRecord)>=1) {
                $myRecord=StrukturRecord::finder()->findByidtm_struktur($MyResultRecord->idtm_struktur);
                if($myRecord->parent_idtm_struktur != $parent_idtm_struktur OR $myRecord->struktur_name != $stammdaten_name) {
                    $myRecord->parent_idtm_struktur = $parent_idtm_struktur;
                    $myRecord->struktur_name = $stammdaten_name;
                    $myRecord->save();
                }
                $return_idtm_struktur = $myRecord->idtm_struktur;
            }else {
                $newStruktur = new StrukturRecord();
                $newStruktur->idta_struktur_type = $idta_struktur_type;
                $newStruktur->idtm_stammdaten = $idtm_stammdaten;
                $newStruktur->parent_idtm_struktur = $parent_idtm_struktur;
                $newStruktur->struktur_name = $stammdaten_name;
                $newStruktur->struktur_lft = 0;
                $newStruktur->struktur_rgt = 0;
                $newStruktur->idta_stammdatensicht = $planungssicht;
                $newStruktur->save();
                $return_idtm_struktur = $newStruktur->idtm_struktur;
            }
        }
        unset($newStruktur);
        return $return_idtm_struktur;
    }

    public function getChildren($idtm_struktur) {
        $this->allowedIds[] = $idtm_struktur;
        $Result = StrukturRecord::finder()->findAllByparent_idtm_struktur($idtm_struktur);
        if(count($Result) >= 1) {
            foreach($Result AS $Record) {
                $this->allowedIds[] = $Record->idtm_struktur;
            }
        }
    }

    /*
     *
     * @param $OnlyNested = defines if only the nested information will be added or not...
     *
     */

    public function PFStrukturGenerator($OnlyNested=0,$planungssicht=1) {
    //als erstes suchen wir die ebenen der obersten ebene
    //das parent elment ist NULL
        if($OnlyNested==0){
            $rootsql = "SELECT * FROM vv_stammdaten_group WHERE parent_idta_stammdaten_group = 0 AND stammdaten_group_original = 0 AND idta_stammdatensicht = ". $planungssicht ." AND sts_stammdaten_group_use = 1";
            $BaseGroupElements = StammdatenGroupView::finder()->findAllBySQL($rootsql);
            foreach($BaseGroupElements AS $RootElement) {
                $LevelOne = StammdatenRecord::finder()->findAllByidta_stammdaten_group($RootElement->idta_stammdaten_group);
                foreach($LevelOne AS $LOElement) {
                //check, ob der knoten schon existiert, wenn ja, wird er automatisch umgeordnet
                    $new_parent_idtm_struktur = $this->ifNodeExists($LOElement->idtm_stammdaten, $LOElement->stammdaten_name, 0, $RootElement->idta_struktur_type,1,$planungssicht);
                    //jetzt schauen wir, ob bei den Kindern Informationen aus der Zuordnung existieren...
                    $this->addChildElmenents($LOElement,$new_parent_idtm_struktur,$planungssicht);
                }
            }
        }
        $this->rebuild_NestedInformation(0,1);
    }

    function rebuild_NestedInformation($parent, $left) {
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
        }
        
        // return the right value of this node + 1
        return $right+1;
    }

}

?>