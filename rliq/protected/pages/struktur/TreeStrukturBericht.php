<?php

class TreeStrukturBericht extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        $this->getResponse()->appendHeader("Content-Type:".$this->header);

        //error_reporting(E_ALL ^ E_NOTICE);

        echo '<?xml version="1.0" ?><tree id="0">';

        $SQLJahr = "SELECT LEFT(sb_order,1) AS sb_order FROM ta_struktur_bericht GROUP BY LEFT(sb_order,1)";

        $ResultJahr = StrukturBerichtRecord::finder()->findAllBySQL($SQLJahr);

        foreach($ResultJahr AS $RJahr){
            echo "<item id='GRP_".$RJahr->sb_order."' text='".$RJahr->sb_order."_Berichte'>";

            $SQLMonate = "SELECT sb_order, idta_struktur_bericht, pivot_struktur_name FROM ta_struktur_bericht WHERE LEFT(sb_order,1) = ".$RJahr->sb_order . " ORDER BY sb_order ASC";
            $ResultMonate = StrukturBerichtRecord::finder()->findAllBySQL($SQLMonate);
            foreach($ResultMonate AS $RMonat){
                echo "<item id='".$RMonat->idta_struktur_bericht."' text='".$RMonat->sb_order.' '.$RMonat->pivot_struktur_name."'>";
                echo "</item>";
            }

            echo "</item>";
        }
        
        echo '</tree>';
        exit;
    }

}
?>
