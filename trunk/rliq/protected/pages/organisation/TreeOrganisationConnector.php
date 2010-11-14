<?php

//require("../3rdParty/dhtmlxConnector/codebase/tree_connector.php");

Prado::using('Application.3rdParty.dhtmlxConnector.codebase.tree_connector');

class TreeOrganisationConnector extends TPage {

    private $DBConnection;
    private $subcats = array();//list of all subcats
    private $parentcats = array();//list of all parentcats
    private $catNames=array();
    private $UserStartId = 1;

    public function onPreInit($param) {

        //parent::onPreInit($param);

        if(isset($_GET['idtm_organisation'])){
            $openItem = $_GET['idtm_organisation'];
        }else{
            $openItem = 0;
        }

        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_organisation",$openItem));

        $res=mysql_connect($this->Application->Parameters['Host'],$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        mysql_select_db($this->Application->Parameters['Database']);

        //$tree = new TreeConnector($this->DBConnection);
        $tree = new TreeConnector($res);
        $tree->set_encoding("UTF-8");
        $tree->enable_log("temp.log",true);

        $mySQL = "SELECT idtm_organisation,parent_idtm_organisation,CONCAT(org_name,' ',IF(ISNULL(org_vorname),'',org_vorname)) AS org_name,idta_organisation_type FROM tm_organisation";
        $mySQLOrderBy = " ORDER BY parent_idtm_organisation";
        
        $this->load_all_cats($mySQL.$mySQLOrderBy);

        //the start ID
        $mySQLcond1 = "idtm_organisation IN (" . $this->subCategory_list($this->subcats, $this->UserStartId) . ",". $this->parentCategory_list($this->parentcats, $this->UserStartId) .")";

        function custom_format($data) {            
            $data->set_image("org".$data->get_value("idta_organisation_type").".gif");
            //$data->setUserData("open",$data->get_value("idtm_organisation")==$openItem?"1":"0");
        }
        $tree->event->attach("beforeRender",custom_format);

        $SQLComp = $mySQL." WHERE ".$mySQLcond1. " AND org_aktiv = 1";//.$mySQLOrderBy
        $tree->dynamic_loading(true);
        //$tree->render_table("tm_struktur", "idtm_struktur", "struktur_name", "parent_idtm_struktur");

        $tree->render_sql($SQLComp,"idtm_organisation","org_name,idta_organisation_type","","parent_idtm_organisation");
    }

    public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

    private function load_all_cats($TTSQL) {
        $rows = OrganisationRecord::finder()->findAllbySQL($TTSQL);
        foreach($rows as $row) {
            $this->subcats[$row->parent_idtm_organisation][]=$row->idtm_organisation;
            $this->parentcats[$row->idtm_organisation]=$row->parent_idtm_organisation;
        }
    }

    private function subCategory_list($subcats,$catID) {
        $lst = $catID; //id des ersten Startelements...
        if(array_key_exists($catID,$subcats)) {
            foreach($subcats[$catID] as $subCatID) {
                $lst .= ", " . $this->subCategory_list($subcats, $subCatID);
            }
        }
        return $lst;
    }

    private function parentCategory_list($parentcats,$catID) {
        $lst = $catID; //id des ersten Startelements...
        while($parentcats[$catID] != NULL) {
            $catID = $parentcats[$catID];
            $lst .= ", " . $catID;
        }
        return $lst;
    }

}

?>