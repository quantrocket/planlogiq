<?php

/**
 * Description of PFNestedClass
 *
 * @author pfrenz
 * @contact pf@com-x-cha.com
 * @copyright Frenzel GmbH
 *
 * @var StructureTable - contains the name of the SQL-Table that contains the structural information
 * @var RecordClassFinder - the Objectcontainer for the ActiveRecordClass
 *
 */
class PFNestedClass extends TApplication {

//put your code here
    private $DBConnection;

    private $StructureTable="";
    private $RecordClassFinder;
    private $RecordClass;
    private $PKField="idtm_";
    private $PPKField="parent_idtm_";
    private $lft_field = ""; //the synonym of the left field
    private $rgt_field = ""; //the synonym of the left field

    //die variablen zum Verarbeiten der Datensaetze
    private $parent_id = "";
    private $rgt_parent_before_update = 0; //Der Wert, der im Datensatz vorhanden war, bevor ein neuer dazu gekommen ist...

    public function setStructureTable($TableName) {
        $this->StructureTable = $TableName;
    }

    public function setRecordClass($RecordObjFinder) {
        $this->RecordClassFinder = $RecordObjFinder;
    }

    public function setPKField($primaryKey) {
        $this->PKField = $primaryKey;
        $this->PPKField = "parent_".$primaryKey;
    }

    public function setField($FieldName) {
        $this->FieldName = $FieldName;
    }

    public function setlft_field($FieldName) {
        $this->lft_field = $FieldName;
    }

    public function setrgt_field($FieldName) {
        $this->rgt_field = $FieldName;
    }

    public function __construct($RecordClass,$current_id=0) {
        $this->RecordClass = $RecordClass;
        $this->parent_id = $current_id;
        //verbinden zur Datenbank
        $this->DBConnection = new TDbConnection($this->Application->getModule('db1')->database->getConnectionString(),$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
        $this->DBConnection->Active = true;
    }


/* *** internal routines *** */

    function _insertNew ($thandle, $node,$othercols)
        /* creates a new root record and returns the node 'l'=1, 'r'=2. */ {
        $MyRecord = new $this->RecordClass;
        if(count($othercols)>=1) {
            foreach($othercols AS $key=>$value) {
                $MyRecord->{$key} = $value;
            }
        }
        $MyRecord->{$this->lft_field} = $node['l'];
        $MyRecord->{$this->rgt_field} = $node['r'];
        $MyRecord->{$this->PPKField} = $this->parent_id;
        //am Ende speichern wir das noch
        $MyRecord->save();
    }

    function _shiftRLValues ($thandle, $first, $delta)
        /* adds '$delta' to all L and R values that are >= '$first'. '$delta' can also be negative. */ { //print("SHIFT: add $delta to gr-eq than $first <br/>");
        $mySQL = '';
        $mySQL = "UPDATE ".$thandle['table']." SET ".$thandle['lvalname']."=".$thandle['lvalname']."+$delta WHERE ".$thandle['lvalname'].">=$first";
        $command = $this->DBConnection->createCommand($mySQL);
        $dataReader=$command->query();

        $mySQL = '';
        $mySQL = "UPDATE ".$thandle['table']." SET ".$thandle['rvalname']."=".$thandle['rvalname']."+$delta WHERE ".$thandle['rvalname'].">=$first";
        $command = $this->DBConnection->createCommand($mySQL);
        $dataReader=$command->query();
    }

    function _shiftRLRange ($thandle, $first, $last, $delta)
        /* adds '$delta' to all L and R values that are >= '$first' and <= '$last'. '$delta' can also be negative.
           returns the shifted first/last values as node array.
         */ {
        $mySQL = '';
        $mySQL = "UPDATE ".$thandle['table']." SET ".$thandle['lvalname']."=".$thandle['lvalname']."+$delta WHERE ".$thandle['lvalname'].">=$first AND ".$thandle['lvalname']."<=$last";
        $command = $this->DBConnection->createCommand($mySQL);
        $dataReader=$command->query();

        $mySQL = '';
        $mySQL = "UPDATE ".$thandle['table']." SET ".$thandle['rvalname']."=".$thandle['rvalname']."+$delta WHERE ".$thandle['rvalname'].">=$first AND ".$thandle['rvalname']."<=$last";
        $command = $this->DBConnection->createCommand($mySQL);
        $dataReader=$command->query();

        return array('l'=>$first+$delta, 'r'=>$last+$delta);
    }

/* ******************************************************************* */
/* Tree Constructors */
/* ******************************************************************* */

    function nstNewRoot ($thandle, $othercols)
        /* creates a new root record and returns the node 'l'=1, 'r'=2. */ {
        $newnode['l'] = 1;
        $newnode['r'] = 2;
        _insertNew ($thandle, $newnode, $othercols);
        return $newnode;
    }

    function nstNewFirstChild ($thandle, $node, $othercols)
        /* creates a new first child of 'node'. */ {
        $newnode['l'] = $node['l']+1;
        $newnode['r'] = $node['l']+2;
        _shiftRLValues($thandle, $newnode['l'], 2);
        _insertNew ($thandle, $newnode, $othercols);
        return $newnode;
    }

    function nstNewLastChild ($thandle, $node, $othercols)
        /* creates a new last child of 'node'. */ {
        $newnode['l'] = $node['r'];
        $newnode['r'] = $node['r']+1;
        _shiftRLValues($thandle, $newnode['l'], 2);
        _insertNew ($thandle, $newnode, $othercols);
        return $newnode;
    }

    function nstNewPrevSibling ($thandle, $node, $othercols) {
        $newnode['l'] = $node['l'];
        $newnode['r'] = $node['l']+1;
        _shiftRLValues($thandle, $newnode['l'], 2);
        _insertNew ($thandle, $newnode, $othercols);
        return $newnode;
    }

    function nstNewNextSibling ($thandle, $node, $othercols) {
        $newnode['l'] = $node['r']+1;
        $newnode['r'] = $node['r']+2;
        _shiftRLValues($thandle, $newnode['l'], 2);
        _insertNew ($thandle, $newnode, $othercols);
        return $newnode;
    }

/* ******************************************************************* */
/* Tree Reorganization */
/* ******************************************************************* */

/* all nstMove... functions return the new position of the moved subtree. */
    function nstMoveToNextSibling ($thandle, $src, $dst)
        /* moves the node '$src' and all its children (subtree) that it is the next sibling of '$dst'. */ {
        return _moveSubtree ($thandle, $src, $dst['r']+1);
    }

    function nstMoveToPrevSibling ($thandle, $src, $dst)
        /* moves the node '$src' and all its children (subtree) that it is the prev sibling of '$dst'. */ {
        return _moveSubtree ($thandle, $src, $dst['l']);
    }

    function nstMoveToFirstChild ($thandle, $src, $dst)
        /* moves the node '$src' and all its children (subtree) that it is the first child of '$dst'. */ {
        return _moveSubtree ($thandle, $src, $dst['l']+1);
    }

    function nstMoveToLastChild ($thandle, $src, $dst)
        /* moves the node '$src' and all its children (subtree) that it is the last child of '$dst'. */ {
        return _moveSubtree ($thandle, $src, $dst['r']);
    }

    function _moveSubtree ($thandle, $src, $to)
        /* '$src' is the node/subtree, '$to' is its destination l-value */ {
        $treesize = $src['r']-$src['l']+1;
        _shiftRLValues($thandle, $to, $treesize);
        if($src['l'] >= $to) { // src was shifted too?
            $src['l'] += $treesize;
            $src['r'] += $treesize;
        }
  /* now there's enough room next to target to move the subtree*/
        $newpos =
            _shiftRLRange($thandle, $src['l'], $src['r'], $to-$src['l']);
  /* correct values after source */
        _shiftRLValues($thandle, $src['r']+1, -$treesize);
        if($src['l'] <= $to) { // dst was shifted too?
            $newpos['l'] -= $treesize;
            $newpos['r'] -= $treesize;
        }
        return $newpos;
    }

/* ******************************************************************* */
/* Tree Functions */
/*
 * the following functions return a boolean value
 */
/* ******************************************************************* */

    function nstValidNode ($thandle, $node)
        /* only checks, if L-value < R-value (does no db-query)*/ { return ($node['l'] < $node['r']);
    }
    function nstHasAncestor ($thandle, $node) { return nstValidNode($thandle, nstAncestor($thandle, $node));
    }
    function nstHasPrevSibling ($thandle, $node) { return nstValidNode($thandle, nstPrevSibling($thandle, $node));
    }
    function nstHasNextSibling ($thandle, $node) { return nstValidNode($thandle, nstNextSibling($thandle, $node));
    }
    function nstHasChildren ($thandle, $node) { return (($node['r']-$node['l'])>1);
    }
    function nstIsRoot ($thandle, $node) { return ($node['l']==1);
    }
    function nstIsLeaf ($thandle, $node) { return (($node['r']-$node['l'])==1);
    }
    function nstIsChild ($node1, $node2)
        /* returns true, if 'node1' is a direct child or in the subtree of 'node2' */ { return (($node1['l']>$node2['l']) and ($node1['r']<$node2['r']));
    }
    function nstIsChildOrEqual ($node1, $node2) { return (($node1['l']>=$node2['l']) and ($node1['r']<=$node2['r']));
    }
    function nstEqual ($node1, $node2) { return (($node1['l']==$node2['l']) and ($node1['r']==$node2['r']));
    }

/* ******************************************************************* */
/* Tree Destructors */
/* ******************************************************************* */

    function nstDeleteTree ($thandle)
    /* deletes the entire tree structure including all records. */ {
        $this->RecordClassFinder->deleteAll();
    }

    function nstDelete ($thandle, $node)
        /* deletes the node '$node' and all its children (subtree). */ {
        $leftanchor = $node['l'];
        $this->RecordClassFinder->deleteAll($thandle['lvalname'].">=".$node['l']." AND ".$thandle['rvalname']."<=".$node['r']);
        _shiftRLValues($thandle, $node['r']+1, $node['l'] - $node['r'] -1);
        return nstGetNodeWhere ($thandle,$thandle['lvalname']."<".$leftanchor." ORDER BY ".$thandle['lvalname']." DESC");
    }

}


?>
