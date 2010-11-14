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
class PFPeriodPullDown extends TPage{
    //put your code here

    private $StructureTable="";
    private $StructurePath="";
    private $RecordClassFinder;
    public $StartPoint = 0;
    private $AllRecords;
    private $HasPrevious=array(); //puffer for the information if the node has an parent or not...
    private $PKField="idtm_";
    private $PPKField="parent_idtm_";
    private $BasisElements=array();
    private $FieldName = ""; //the name of the field, to be displayed in the listbox
    private $FieldCategories = array();
    private $Fields = array();
    public $myTree = array(); //inside this var, the complete tree is stored, will be the datasource for the ListBox

    private $tmp;

    private $SQLCondition = "";
    private $SQLOrder = "";

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
        if(count($this->AllRecords)>=1){
            $this->build_hierarchy();
        }else{
            $this->myTree[0]='no values';
            $this->myTree[1]='no values';
        }
    }

    public function build_hierarchy(){
        foreach($this->AllRecords as $row){
            $this->FieldCategories[$row->{$this->PPKField}][]=$row->{$this->PKField};
            $this->Fields[$row->{$this->PKField}] = $row->{$this->FieldName};
        }
        $hierarchy = $this->build_category_array($this->FieldCategories[$this->StartPoint], $this->FieldCategories, $this->Fields);
        //$this->myTree = $this->build_category_array($this->FieldCategories[$this->StartPoint], $this->FieldCategories, $this->Fields);
        $this->display_options($hierarchy);
        //print_r($this->myTree);
    }

    public function build_category_array($catIDs, $subcats, $catNames, $indent=0){

        if($this->tmp==0)
            $this->tmp=false;

        if(is_array($catIDs)){
            foreach($catIDs as $catID){
                $pair[$this->FieldName]=str_repeat("-",$indent*2).$catNames[$catID];
                $pair[$this->PKField]=$catID;
                //$pair['Indent']=$indent;
                $this->tmp[]=$pair;
                if(array_key_exists($catID,$subcats))
                    $this->build_category_array($subcats[$catID], $subcats, $catNames,$indent+1);
            }
            if($indent==0)
                return $this->tmp;
        }
    }

    private function hierarchize($cats,$parent){
        $subs = array_keys($cats, $parent);
        $tree = array();
        foreach ($subs as $sub) {
            $tree[$sub] = $this->hierarchize($cats, $sub);
        }
        return count($tree) ? $tree : $parent;
    }

    function display_options($tree) {
        if (is_array($tree)) {
            foreach ($tree as $value) {
                $PeriodRecord = PeriodenRecord::finder()->findByidta_perioden($value[$this->PKField]);
                $PeriodKey = $PeriodRecord->per_intern;
                $sufix = '';
                if($PeriodKey < 10000){
                    $sufix = PeriodenRecord::finder()->findByidta_perioden($PeriodRecord->parent_idta_perioden)->per_extern;
                }
                $displayvalue = $value[$this->FieldName].' '.$sufix;
                $this->myTree[$PeriodKey]=$displayvalue;
            }
        }
    }

}
?>
