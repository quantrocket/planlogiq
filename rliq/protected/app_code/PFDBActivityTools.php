<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class PFDBActivityTools extends TApplication {

    public function __construct() {
        //allgemeine Klasse fuer die DB Wartung
    }

    public static function rebuild_NestedInformation($parent, $left) {
    // the right value of this node is the left value + 1
        $right = $left+1;

        // get all children of this node
        $TreeRecords = ActivityRecord::finder()->findAllByparent_idtm_activity($parent);
        if(count($TreeRecords)>=1){
            foreach($TreeRecords as $TreeRecord) {
            // recursive execution of this function for each
            // child of this node
            // $right is the current right value, which is
            // incremented by the rebuild_tree function
                $right = PFDBActivityTools::rebuild_NestedInformation($TreeRecord->idtm_activity, $right);
            }
        }

        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value
        if($parent!=0){
            $TreeChangeRecord = ActivityRecord::finder()->findByidtm_activity($parent);
            $TreeChangeRecord->act_lft = $left;
            $TreeChangeRecord->act_rgt = $right;
            $TreeChangeRecord->save();
            unset($TreeChangeRecord);
        }
        
        // return the right value of this node + 1
        return $right+1;
    }

    public static function cleanStrukturStruktur(){
        //sql statement to find elements without matching parent
        $sql = "SELECT idtm_activity FROM tm_activity WHERE idtm_activity NOT IN (SELECT a.idtm_activity FROM tm_activity a INNER JOIN tm_activity b ON a.parent_idtm_activity = b.idtm_activity) AND idtm_activity > 1";
        $StrukturElements = ActivityRecord::finder()->findAllBySql($sql);
        foreach($StrukturElements AS $StrukturElement){
            WerteRecord::finder()->deleteAll('idtm_activity = ?', $StrukturElement->idtm_activity);
            ActivityRecord::finder()->deleteByidtm_activity($StrukturElement->idtm_activity);
            //debug only echo "DE\n";
        }
        unset($StrukturElements);

        //null-Werte entfernen
        $sql = "SELECT idtm_activity FROM tm_activity WHERE ISNULL(act_lft)";
        $StrukturElements = ActivityRecord::finder()->findAllBySql($sql);
        foreach($StrukturElements AS $StrukturElement){
            WerteRecord::finder()->deleteAll('idtm_activity = ?', $StrukturElement->idtm_activity);
            ActivityRecord::finder()->deleteByidtm_activity($StrukturElement->idtm_activity);
            //debug only echo "DE\n";
        }
        unset($StrukturElements);
    }

}

?>