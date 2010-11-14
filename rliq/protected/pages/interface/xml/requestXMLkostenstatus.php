<?php

class requestXMLkostenstatus extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><ta_kosten_status>';

        $SQL = "SELECT * FROM ta_kosten_status";

        $Results = KostenStatusRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idta_kosten_status='".$Result->idta_kosten_status."'>";
                echo "<kst_status_name>".$Result->kst_status_name."</kst_status_name>";
            echo "</row>";
        }
        
        echo '</ta_kosten_status>';
        exit;
    }

}
?>
