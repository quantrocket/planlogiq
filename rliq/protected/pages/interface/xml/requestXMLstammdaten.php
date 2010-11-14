<?php

class requestXMLstammdaten extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><tm_stammdaten>';

        $SQL = "SELECT * FROM tm_stammdaten";

        $Results = StammdatenRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idtm_stammdaten='".$Result->idtm_stammdaten."'>";
                echo "<stammdaten_name>".$Result->stammdaten_name."</stammdaten_name>";
                echo "<idta_stammdaten_group>".$Result->idta_stammdaten_group."</idta_stammdaten_group>";
            echo "</row>";
        }
        
        echo '</tm_stammdaten>';
        exit;
    }

}
?>
