<?php

//require("../3rdParty/dhtmlxConnector/codebase/tree_connector.php");

Prado::using('Application.3rdParty.dhtmlxConnector.codebase.scheduler_connector');

class TerminConnector extends TPage {

    private $DBConnection;
    private $subcats = array();//list of all subcats
    private $parentcats = array();//list of all parentcats
    private $catNames=array();
    private $UserStartId = 1;

    public function onPreInit($param) {
        
//        if(isset($_GET['idtm_organisation'])){
//            $openItem = $_GET['idtm_organisation'];
//        }else{
//            $openItem = 1;
//        }
//
//        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_organisation"));

        $res=mysql_connect($this->Application->Parameters['Host'],$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        mysql_select_db($this->Application->Parameters['Database']);

        $scheduler = new schedulerConnector($res);
	$scheduler->enable_log("log.txt",true);
	$scheduler->render_table("vv_termin_connector","idtm_termin","ter_starttimestamp, ter_endtimestamp, ter_betreff, ter_descr,idtm_activity");
    }

    public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }


}

?>