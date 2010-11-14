<?php

class requestXMLfeldfunktion extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><ta_feldfunktion>';

        $SQL = "SELECT idta_feldfunktion, ff_name, idta_struktur_type FROM ta_feldfunktion";

        $Results = FeldfunktionRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idta_feldfunktion='".$Result->idta_feldfunktion."'>";
                echo "<ff_name>".$Result->ff_name."</ff_name>";
                echo "<idta_struktur_type>".$Result->idta_struktur_type."</idta_struktur_type>";
            echo "</row>";
        }
        
        echo '</ta_feldfunktion>';
        exit;
    }

}
?>
