<?php

class requestXMLactivity extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><tm_activity>';

        $SQL = "SELECT * FROM tm_activity WHERE idta_activity_type = '2'";

        $Results = ActivityRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idtm_activity='".$Result->idtm_activity."'>";
                echo "<parent_idtm_activity>".$Result->parent_idtm_activity."</parent_idtm_activity>";
                echo "<act_name>".$Result->act_name."</act_name>";
                echo "<act_pspcode>".$Result->act_pspcode."</act_pspcode>";
            echo "</row>";
        }
        
        echo '</tm_activity>';
        exit;
    }

}
?>
