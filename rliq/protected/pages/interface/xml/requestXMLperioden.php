<?php

class requestXMLperioden extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><ta_perioden>';

        $SQL = "SELECT * FROM ta_perioden";

        $Results = PeriodenRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idta_perioden='".$Result->idta_perioden."'>";
                echo "<parent_idta_perioden>".$Result->parent_idta_perioden."</parent_idta_perioden>";
                echo "<per_intern>".$Result->per_intern."</per_intern>";
                echo "<per_extern>".$Result->per_extern."</per_extern>";
            echo "</row>";
        }
        
        echo '</ta_perioden>';
        exit;
    }

}
?>
