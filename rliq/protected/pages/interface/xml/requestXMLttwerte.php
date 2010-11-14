<?php

class requestXMLttwerte extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];
        $idta_variante = $_GET['idta_variante'];
        $per_intern = $_GET['per_intern'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><tt_werte>';

        $SQL = "SELECT * FROM tt_werte WHERE w_id_variante = ".$idta_variante." AND w_monat = ".$per_intern;

        $Results = WerteRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idtt_werte='".$Result->idtt_werte."'>";
                echo "<per_intern>".$Result->w_monat."</per_intern>";
                echo "<w_wert>".$Result->w_wert."</w_wert>";
                echo "<idtm_struktur>".$Result->idtm_struktur."</idtm_struktur>";
                echo "<idta_variante>".$Result->w_id_variante."</idta_variante>";
                echo "<w_dimkey>".$Result->w_dimkey."</w_dimkey>";
                echo "<idta_feldfunktion>".$Result->idta_feldfunktion."</idta_feldfunktion>";
            echo "</row>";
        }
        
        echo '</tt_werte>';
        exit;
    }

}
?>
