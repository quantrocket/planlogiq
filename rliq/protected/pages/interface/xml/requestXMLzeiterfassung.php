<?php

class requestXMLzeiterfassung extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $date_from = $_GET['date_from'];
        $date_to = $_GET['date_to'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><tm_zeiterfassung>';

        $SQL = "SELECT * FROM tm_zeiterfassung WHERE zeit_date >= '".$date_from."' AND zeit_date <= '".$date_to."'";

        $Results = ZeiterfassungRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idtm_zeiterfassung='0".$Result->idtm_zeiterfassung."'>";
                echo "<idtm_activity>".$Result->idtm_activity."</idtm_activity>";
                echo "<idtm_organisation>".$Result->idtm_organisation."</idtm_organisation>";
                echo "<zeit_date>".$Result->zeit_date."</zeit_date>";
                echo "<zeit_starttime>".$Result->zeit_starttime."</zeit_starttime>";
                echo "<zeit_endtime>".$Result->zeit_endtime."</zeit_endtime>";
                echo "<zeit_break>".$Result->zeit_break."</zeit_break>";
                echo "<zeit_dauer>".str_replace('.',',',$Result->zeit_dauer)."</zeit_dauer>";
                echo "<idta_kosten_status>".$Result->idta_kosten_status."</idta_kosten_status>";
                echo "<zeit_descr><![CDATA[".$Result->zeit_descr."]]></zeit_descr>";
                echo "<zeit_abgerechnet>".$Result->zeit_abgerechnet."</zeit_abgerechnet>";
                echo "<zeit_checked>".$Result->zeit_checked."</zeit_checked>";
                echo "<idtm_prozess>".$Result->idtm_prozess."</idtm_prozess>";
            echo "</row>";
        }
        
        echo '</tm_zeiterfassung>';
        exit;
    }

}
?>
