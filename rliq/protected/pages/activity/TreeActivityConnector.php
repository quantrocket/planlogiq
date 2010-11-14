<?php

//require("../3rdParty/dhtmlxConnector/codebase/tree_connector.php");

Prado::using('Application.3rdParty.dhtmlxConnector.codebase.tree_connector');

class TreeActivityConnector extends TPage {

    private $DBConnection;
    private $subcats = array();//list of all subcats
    private $parentcats = array();//list of all parentcats
    private $catNames=array();
    private $UserStartId = 1;

    public function onPreInit($param) {

        parent::onPreInit($param);

        if(isset($_GET['idtm_activity'])){
            $openItem = $_GET['idtm_activity'];
        }else{
            $openItem = 1;
        }

        if(isset($_GET['idta_activity_type'])){
            if($_GET['idta_activity_type']!=''){
                $mySQLcond1 = "idta_activity_type = ".$_GET['idta_activity_type']." AND ";
            }else{
                $mySQLcond1 = '';
            }
        }else{
            $mySQLcond1 = '';
        }

        if(isset($_GET['idtm_activity_start'])){
           $this->setUserStartId($_GET['idtm_activity_start']);
        }else{
           $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_activity"));
        }
//        $this->DBConnection = new TDbConnection($this->Application->getModule('db1')->database->getConnectionString(),$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
//        $this->DBConnection->Active = true;

        $res=mysql_connect($this->Application->Parameters['Host'],$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        mysql_select_db($this->Application->Parameters['Database']);

        //$tree = new TreeConnector($this->DBConnection);
        $tree = new TreeConnector($res);
        $tree->set_encoding("UTF-8");
        $tree->enable_log("temp.log",false);

        $mySQL = "SELECT idtm_activity,parent_idtm_activity,act_name,idta_activity_type FROM tm_activity";
        $mySQLOrderBy = " ORDER BY parent_idtm_activity ASC,act_name ASC,act_step";
        
        $this->load_all_cats($mySQL.$mySQLOrderBy);

        //the start ID
        $mySQLcond1 .= "idtm_activity IN (" . $this->subCategory_list($this->subcats, $this->UserStartId) . ",". $this->parentCategory_list($this->parentcats, $this->UserStartId) .")";

        function custom_format($data) {            
            $data->set_image("s".$data->get_value("idta_activity_type").".gif");
            //$data->setUserData("open",$data->get_value("idtm_organisation")==$openItem?"1":"0");
        }
        $tree->event->attach("beforeRender",custom_format);

        $SQLComp = $mySQL." WHERE ".$mySQLcond1.$mySQLOrderBy;
        $tree->dynamic_loading(true);
        //$tree->render_table("tm_struktur", "idtm_struktur", "struktur_name", "parent_idtm_struktur");
        $tree->render_sql($SQLComp,"idtm_activity","act_name,idta_activity_type","","parent_idtm_activity");
    }

    public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

    private function load_all_cats($TTSQL) {
        $rows = ActivityRecord::finder()->findAllbySQL($TTSQL);
        foreach($rows as $row) {
            $this->subcats[$row->parent_idtm_activity][]=$row->idtm_activity;
            $this->parentcats[$row->idtm_activity]=$row->parent_idtm_activity;
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