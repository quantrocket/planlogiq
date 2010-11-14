<?php

//require("../3rdParty/dhtmlxConnector/codebase/tree_connector.php");

Prado::using('Application.3rdParty.dhtmlxConnector.codebase.tree_connector');

class TreeStrukturConnector extends TPage {

    private $DBConnection;
    private $subcats = array();//list of all subcats
    private $parentcats = array();//list of all parentcats
    private $catNames=array();
    private $UserStartId = 1;

    public function onPreInit($param) {

        //parent::onPreInit($param);

        if(isset($_GET['idta_stammdatensicht'])){
            $openItem = $_GET['idta_stammdatensicht'];
        }else{
            $openItem = 1;
        }

        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_struktur",$openItem));

        $res=mysql_connect($this->Application->Parameters['Host'],$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        mysql_select_db($this->Application->Parameters['Database']);

        //$tree = new TreeConnector($this->DBConnection);
        $tree = new TreeConnector($res);
        $tree->set_encoding("UTF-8");
        $tree->enable_log("temp.log",false);

        $mySQL = "SELECT idtm_struktur,parent_idtm_struktur,struktur_name,idta_struktur_type,(struktur_rgt - struktur_lft) AS struktur_rgt FROM vv_struktur";
        $mySQLOrderBy = " ORDER BY idta_struktur_type, struktur_name";
        $mySQLcond1 = "";
        
        //the start ID
        $numberofstructureelements = StrukturRecord::finder()->count();
        if($numberofstructureelements>1){
            $MaxRecord = StrukturRecord::finder()->findBySql('SELECT MAX(struktur_rgt) AS struktur_rgt FROM tm_struktur');
            if(is_Object($MaxRecord)){
                $maxrgtvalue = $MaxRecord->struktur_rgt;
            }else{
                $maxrgtvalue = 0;
            }
            if($numberofstructureelements*2<=$maxrgtvalue){
                $StrStartRecord = StrukturRecord::finder()->findByidtm_struktur($this->UserStartId);
                $mySQLcond1 = "(struktur_lft BETWEEN ".$StrStartRecord->struktur_lft." AND ".$StrStartRecord->struktur_rgt.")";
                $mySQLcond1 .= " OR idtm_struktur IN ( " . $this->parentCategory_list_Nested($this->UserStartId).")";
            }else{
                $this->load_all_cats($mySQL.$mySQLOrderBy);
                $mySQLcond1 = "idtm_struktur IN (" . $this->subCategory_list($this->subcats, $this->UserStartId) . ",". $this->parentCategory_list($this->parentcats, $this->UserStartId) .")";
            }
            $tmp_Strukturfilter = $this->user->getStartNode($this->user->getUserId($this->user->Name),"ta_stammdaten_group");
            if($tmp_Strukturfilter>0 AND $tmp_Strukturfilter != StammdatenGroupRecord::finder()->findByparent_idta_stammdaten_group(0)->idta_stammdaten_group){
                $tsgroup = "";
                $loopcounter = 0;
                while($this->checkMyParent($tmp_Strukturfilter)>0){
                    if($loopcounter==0){
                        $tsgroup .= $tmp_Strukturfilter;
                    }else{
                        $tsgroup .= ",".$tmp_Strukturfilter;
                    }
                    $loopcounter++;
                    $tmp_Strukturfilter = $this->checkMyParent($tmp_Strukturfilter);
                }
                $mySQLcond1 .= " AND idta_stammdaten_group IN (" .$tsgroup. ", ". StammdatenGroupRecord::finder()->findByparent_idta_stammdaten_group(0)->idta_stammdaten_group.")";
            }
        }

        

        function custom_format($item) {
            $item->set_image("s".$item->get_value("idta_struktur_type").".gif");
            if ($item->get_value("struktur_rgt")>1)
                    $item->set_kids(true);
            else
                    $item->set_kids(false);
        }
        
        $tree->event->attach("beforeRender",'custom_format');

        $SQLComp = $mySQL;
        if($mySQLcond1!=''){
            $SQLComp.=" WHERE (".$mySQLcond1.") AND idta_stammdatensicht = ".$openItem;//.$mySQLOrderBy
        }else{
            $SQLComp.=" WHERE idta_stammdatensicht = ".$openItem;
        }
        $tree->dynamic_loading(true);
        //$tree->render_table("tm_struktur", "idtm_struktur", "struktur_name", "parent_idtm_struktur");
        $tree->render_sql($SQLComp,"idtm_struktur","struktur_name,idta_struktur_type","","parent_idtm_struktur");
    }

    public function checkMyParent($idta_stammdaten_group){
        if(StammdatenGroupRecord::finder()->findByidta_stammdaten_group($idta_stammdaten_group)->parent_idta_stammdaten_group>0){
            return StammdatenGroupRecord::finder()->findByidta_stammdaten_group(StammdatenGroupRecord::finder()->findByidta_stammdaten_group($idta_stammdaten_group)->parent_idta_stammdaten_group)->idta_stammdaten_group;
        }else{
            return 0;
        }
    }

    public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

    private function load_all_cats($TTSQL) {
        $rows = StrukturRecord::finder()->findAllbySQL($TTSQL);
        foreach($rows as $row) {
            $this->subcats[$row->parent_idtm_struktur][]=$row->idtm_struktur;
            $this->parentcats[$row->idtm_struktur]=$row->parent_idtm_struktur;
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
        if(array_key_exists($parentcats,$catID)){
            while($parentcats[$catID] != NULL) {
                $catID = $parentcats[$catID];
                $lst .= ", " . $catID;
            }
        }
        return $lst;
    }

    private function parentCategory_list_Nested($catID) {
        $lst = $catID; //id des ersten Startelements...
        while(StrukturRecord::finder()->findByidtm_struktur(StrukturRecord::finder()->findByPK($catID)->parent_idtm_struktur)->idtm_struktur != NULL) {
            $catID = StrukturRecord::finder()->findByidtm_struktur(StrukturRecord::finder()->findByPK($catID)->parent_idtm_struktur)->idtm_struktur;
            $lst .= ", " . $catID;
        }
        return $lst;
    }

}

?>