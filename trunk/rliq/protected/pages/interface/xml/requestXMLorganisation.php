<?php

class requestXMLorganisation extends TPage {

    private $header = "text/xml";

    public function onPreInit($param) {

        //error_reporting(E_ALL ^ E_NOTICE);
        $request_user = $_GET['user'];
        $request_password = $_GET['pass'];

        $authManager=$this->Application->getModule('auth');
        if(!$authManager->login($request_user,$request_password))
			exit;

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        
        echo '<?xml version="1.0" ?><tm_organisation>';

        $SQL = "SELECT * FROM tm_organisation";

        $Results = OrganisationRecord::finder()->findAllBySQL($SQL);

        foreach($Results AS $Result){
            echo "<row idtm_organisation='".$Result->idtm_organisation."'>";
                echo "<parent_idtm_organisation>".$Result->parent_idtm_organisation."</parent_idtm_organisation>";
                
                if($Result->org_name==''){
                    echo "<org_name>ERROR_NO_ORGNAME</org_name>";
                }else{
                    echo "<org_name>".$Result->org_name."</org_name>";
                }

                if($Result->org_matchkey==''){
                    echo "<org_matchkey>empty</org_matchkey>";
                }else{
                    echo "<org_matchkey>".$Result->org_matchkey."</org_matchkey>";
                }
            echo "</row>";
        }
        
        echo '</tm_organisation>';
        exit;
    }

}
?>
