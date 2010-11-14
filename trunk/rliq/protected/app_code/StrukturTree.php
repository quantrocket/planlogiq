<?php
class StrukturTree extends TApplication{
	
    function fullInTreeStrukturRecord($tree, $node){
		if(!$node->idtm_struktur){
			$subNodes = StrukturRecord::finder()->findAllBy_parent_idtm_struktur('0');
			$tree->setTitle("Struktur");
			$tree->setNodeType(MyTreeList::NODE_TYPE_PLAIN);
                        StrukturTree::check_forChildren($node)?$tree->setToPage('reports.gewinnundverlust'):$tree->setToPage('struktur.streingabemaske');
			$tree->setGetVariables(array('modus'=>0,"idtm_struktur"=>$node->idtm_struktur,"idta_struktur_type"=>$node->idta_struktur_type));
		}
		else{
			$subNodes = StrukturRecord::finder()->findAllBy_parent_idtm_struktur($node->idtm_struktur);
			$tree->setTitle($node->idta_struktur_type.'-'.$node->struktur_name);
			$tree->setNodeType(MyTreeList::NODE_TYPE_INTERNAL_LINK);
			StrukturTree::check_forChildren($node)?$tree->setToPage('reports.gewinnundverlust'):$tree->setToPage('struktur.streingabemaske');
			$tree->setGetVariables(array('modus'=>0,"idtm_struktur"=>$node->idtm_struktur,"idta_struktur_type"=>$node->idta_struktur_type));
		}
		foreach($subNodes as $subN){
			$subTr = new MyTreeList();
			StrukturTree::fullInTreeStrukturRecord($subTr, $subN);
			$tree->addSubElement($subTr);
		}
	}

        public function check_forChildren($Node){
		$SQL = "SELECT * FROM tm_struktur WHERE parent_idtm_struktur = '".$Node->idtm_struktur."'";
		$Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}
}
?>