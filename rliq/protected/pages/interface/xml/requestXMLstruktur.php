<?php

class requestXMLstruktur extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><tm_struktur>';

        $SQL = "SELECT * FROM tm_struktur";

        $Results = StrukturRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idtm_struktur='".$Result->idtm_struktur."'>";
                echo "<parent_idtm_struktur>".$Result->parent_idtm_struktur."</parent_idtm_struktur>";
                echo "<struktur_name>".$Result->struktur_name."</struktur_name>";
                echo "<idtm_stammdaten>".$Result->idtm_stammdaten."</idtm_stammdaten>";
            echo "</row>";
        }
        
        echo '</tm_struktur>';
        exit;
    }

}
?>
