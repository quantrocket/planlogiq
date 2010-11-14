<?php
class PFH extends TApplication {


//diese funktion baut mir ein SQL Pull Down, mit Kriterien...
    public static function build_SQLPullDown($finder, $tabelle, $array_fields, $criteria='') {
    //deklarieren der variablen
        $meinSQL ='';
        $ttdata = array();

        $meinSQL = "SELECT ".$array_fields[0].", ".$array_fields[1]." FROM ".$tabelle;
        if($criteria !='') {
            $meinSQL .= ' WHERE '.$criteria;
        }
        if(count($finder->findAllBySQL($meinSQL))>=1) {
            $ttdata = PFH::convertdbObjectArray($finder->findAllBySQL($meinSQL),$array_fields);
        }else {
            $ttdata[0]='no values';
        }
        return $ttdata;
    }

    //diese funktion baut mir ein SQL Pull Down, mit combiniertem namen und Kriterien...
    public static function build_SQLPullDownAdvanced($finder, $tabelle, $array_fields, $criteria='', $order='') {
    //deklarieren der variablen
        $meinSQL ='';
        $ttdata = array();

        $meinSQL = "SELECT ".$array_fields[0].", CONCAT(".$array_fields[1].",'::',".$array_fields[2].") AS ".$array_fields[1]." FROM ".$tabelle;
        if($criteria !='') {
            $meinSQL .= ' WHERE '.$criteria;
        }
        if($order !='') {
            $meinSQL .= ' ORDER BY '.$order;
        }
        if(count($finder->findAllBySQL($meinSQL))>=1) {
            $ttdata = PFH::convertdbObjectArray($finder->findAllBySQL($meinSQL),$array_fields);
        }else {
            $ttdata[0]='no values';
        }
        //array_push($ttdata,array(0=>'no values'));
        return $ttdata;
    }

    var $array_to_clean;

    /**
     * @param array The TActiveRecord Resultobjects
     * @param array_fields id, label for the DropDownList
     */

    public static function convertdbObjectArray($array,$array_fields) {

        $result = array();

        foreach($array as $value) {
        //print_r($value);
            array_push($result,array($value->$array_fields[1], $value->$array_fields[0]));
        }

        return $result;
    }

    public static function convertdbObjectSuggest($array,$array_fields) {
        $result = array();  
        foreach($array as $value) {
        //print_r($value);
            $tmp_fields = array();
            foreach($array_fields as $field){
                $tmp_fields[$field] = $value->$field;
            }
            $result[]= $tmp_fields;
        }
        return $result;
    }

    

    public static function getDistance($koord) {
        if (!is_array($koord)) {
            return false;
        }

        $ent = 0;
        $welt = 6378.137; // Erdradius, ca. Angabe

        foreach($koord as $key => $fetch) {
            if (isset($koord[$key + 1])) {
                $erste_breite = $koord[$key][0]; // lat
                $erste_laenge = $koord[$key][1]; // lon
                $erste_breite_rad = deg2rad($erste_breite);
                $erste_laenge_rad = deg2rad($erste_laenge);

                $zweite_breite = $koord[$key + 1][0]; // lat
                $zweite_laenge = $koord[$key + 1][1]; // lon
                $zweite_breite_rad = deg2rad($zweite_breite);
                $zweite_laenge_rad = deg2rad($zweite_laenge);

                $dis = acos(
                    (sin($erste_breite_rad) * sin($zweite_breite_rad)) +
                    (cos($erste_breite_rad) * cos($zweite_breite_rad) *
                    cos($zweite_laenge_rad - $erste_laenge_rad))) * $welt;

                $ent = $ent + $dis;
            }
        }
        $entfernung = $ent * 1000;
        return round($entfernung, 0);
    }

    public static function checkCountStatement($object) {

        $validate = isset($object);
        return $validate;

    }

}
?>