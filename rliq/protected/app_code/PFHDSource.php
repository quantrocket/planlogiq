<?php

/**
 * Description of PFHierarchyPullDown
 *
 * @author pfrenz
 * @contact pf@com-x-cha.com
 * @copyright Frenzel GmbH
 *
 * @var StructureTable - contains the name of the SQL-Table that contains the structural information
 * @var RecordClassFinder - the Objectcontainer for the ActiveRecordClass
 * @var StructurePath - we need this parameter, to temporary save the string for the listbox
 * @var AllRecords - inside this Recordset, we make a complete copy of all available records
 *
 */
class PFHDSource extends TPage{
    //put your code here

    private $StructureTable="";
    private $RecordClassFinder;
    private $StartPoint = 1;
    private $AllRecords;
    private $PKField="idtm_";
    private $PPKField="parent_idtm_";
    private $FieldName = ""; //the name of the field, to be displayed in the listbox
    private $FieldCategories = array();
    private $Fields = array();
    public $myTree = array(); //inside this var, the complete tree is stored, will be the datasource for the ListBox
    private $SQLCondition = "";
    private $SQLOrder = "";
    private $tmp;
    

    public function setStructureTable($TableName){
        $this->StructureTable = $TableName;
    }

    public function setRecordClass($RecordObjFinder){
        $this->RecordClassFinder = $RecordObjFinder;
    }

    public function setPKField($primaryKey){
        $this->PKField = $primaryKey;
        $this->PPKField = "parent_".$primaryKey;
    }

    public function setField($FieldName){
        $this->FieldName = $FieldName;
    }

    public function setStartNode($StartNode){
        $this->StartPoint = $StartNode;
    }

    public function setSQLCondition($SQLCondition){
        $this->SQLCondition = $SQLCondition;
    }

    public function setSQLOrder($SQLOrder){
        $this->SQLOrder = $SQLOrder;
    }

    public function letsrun(){
        $mySQL = "SELECT ".$this->PKField.",".$this->PPKField.",".$this->FieldName." FROM ".$this->StructureTable;
        if($this->SQLCondition!=''){
            $mySQL.= " WHERE ".$this->SQLCondition;
        }
        if($this->SQLOrder!=''){
            $mySQL.= " ORDER BY ".$this->SQLOrder;
        }
        $this->AllRecords = $this->RecordClassFinder->findAllBySQL($mySQL);

        //hier befuelle ich das array fuer die darstellung der kinder
        foreach($this->AllRecords as $row){
            $this->FieldCategories[$row->{$this->PPKField}][]=$row->{$this->PKField};
            $this->Fields[$row->{$this->PKField}] = $row->{$this->FieldName};                       
        }
        if(count($this->AllRecords)>=1){
            $this->myTree = $this->build_category_array($this->FieldCategories[$this->StartPoint], $this->FieldCategories, $this->Fields);
        }
        $this->myTree[]=array($this->FieldName=>'no values',$this->PKField=>'0','ident'=>'0');
    }

    public function build_category_array($catIDs, $subcats, $catNames, $indent=0){
        
        if($this->tmp==0)
            $this->tmp=false;

        if(is_array($catIDs)){
            foreach($catIDs as $catID){
                $pair[$this->FieldName]= str_repeat("- ",$indent*3).$catNames[$catID];
                $pair[$this->PKField]=$catID;
                $pair['Indent']=$indent;
                $this->tmp[]=$pair;
                if(array_key_exists($catID,$subcats))
                    $this->build_category_array($subcats[$catID], $subcats, $catNames,$indent+1);
            }
            if($indent==0)
                return $this->tmp;
        }
    }

}
?>
