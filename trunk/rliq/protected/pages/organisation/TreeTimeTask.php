<?php

class TreeTimeTask extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        $mShort= array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mai',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Dez');

        //error_reporting(E_ALL ^ E_NOTICE);
        $idtm_organisation = $_GET['idtm_organisation'];

        echo '<?xml version="1.0" ?><tree id="0">';

        $SQLJahr = "SELECT YEAR(auf_tdate) AS idtm_aufgaben FROM vv_aufgaben WHERE (auf_tabelle = 'tm_organisation' AND auf_id='".$idtm_organisation."') OR (auf_idtm_organisation = '".$idtm_organisation."' OR idtm_organisation = '".$idtm_organisation."') AND NOT ISNULL(auf_tdate) GROUP BY YEAR(auf_tdate)";

        $ResultJahr = AufgabenView::finder()->findAllBySQL($SQLJahr);

        foreach($ResultJahr AS $RJahr){
            echo "<item id='DAT_".$RJahr->idtm_aufgaben."' text='".$RJahr->idtm_aufgaben."'>";

            $SQLMonate = "SELECT MONTH(auf_tdate) AS idtm_aufgaben FROM vv_aufgaben WHERE ((auf_tabelle = 'tm_organisation' AND auf_id='".$idtm_organisation."') OR auf_idtm_organisation = '".$idtm_organisation."' OR idtm_organisation = '".$idtm_organisation."') AND YEAR(auf_tdate) = '".$RJahr->idtm_aufgaben."' GROUP BY MONTH(auf_tdate)";
            $ResultMonate = AufgabenView::finder()->findAllBySQL($SQLMonate);
            foreach($ResultMonate AS $RMonat){
                echo "<item id='DAT_".$RJahr->idtm_aufgaben.'_'.$RMonat->idtm_aufgaben."' text='".$mShort[$RMonat->idtm_aufgaben].' '.$RJahr->idtm_aufgaben."'>";

                    $SQLAufgaben = "SELECT idtm_aufgaben, LEFT(auf_name,30) AS auf_name FROM vv_aufgaben WHERE ((auf_tabelle = 'tm_organisation' AND auf_id='".$idtm_organisation."') OR idtm_organisation = '".$idtm_organisation."' OR auf_idtm_organisation = '".$idtm_organisation."') AND YEAR(auf_tdate) = '".$RJahr->idtm_aufgaben."' AND MONTH(auf_tdate)= '".$RMonat->idtm_aufgaben."'";
                    $ResultAufgaben = AufgabenView::finder()->findAllBySQL($SQLAufgaben);
                    foreach($ResultAufgaben AS $RAufgaben){
                        echo "<item id='".$RAufgaben->idtm_aufgaben."' text='".htmlspecialchars($RAufgaben->auf_name)."'>";
                        echo "</item>";
                    }
                
                echo "</item>";
            }

            echo "</item>";
        }
        
//        for ($i=0; $i<sizeof($data); $i++){
//            echo "<item id='arra_".$data[$i]."' text='arra ".$data[$i]."'>";
//               for ($j=0; $j<$data[$i]; $j++)
//                    echo "<item id='arra_".$i."_".$j."' text='arra ".$i." ".$j."'></item>";
//            echo "</item>";
//        }
        echo '</tree>';
        exit;
    }

}
?>
