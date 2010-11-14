<?php

Prado::using('Application.3rdParty.dhtmlxConnector.codebase.tree_connector');

class TreeZeitConnector extends TPage {

    private $UserStartId = 1;

    public function onPreInit($param) {

        //$UserStartId = $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"ta_perioden"));

        $res=mysql_connect($this->Application->Parameters['Host'],$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        mysql_select_db($this->Application->Parameters['Database']);

        $tree = new TreeConnector($res);
        $tree->set_encoding("UTF-8");
        $tree->enable_log("temp.log",false);

        $mySQL = "SELECT idta_perioden,parent_idta_perioden,per_extern FROM ta_perioden";
        $mySQLOrderBy = " ORDER BY parent_idta_perioden ASC,per_intern ASC";

        $SQLComp = $mySQL.$mySQLOrderBy;
        //$tree->dynamic_loading(true);
        //$tree->render_table("tm_struktur", "idtm_struktur", "struktur_name", "parent_idtm_struktur");
        $tree->render_sql($SQLComp,"idta_perioden","per_extern","","parent_idta_perioden");
    }

    public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

}

?>