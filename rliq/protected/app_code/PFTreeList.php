<?php
class PFTreeList extends TWebControl {
	
	const NODE_TYPE_PLAIN = 0;
	const NODE_TYPE_INTERNAL_LINK = 1;
	const NODE_TYPE_LINK = 2;
	const NODE_TYPE_INTERNAL_ACTIVE_LINK = 3;
	const SUB_ID_TAG = "__ctlSon";
	const SUB_ID_UL = "__UL";
	const SUB_ID_LI = "__LI";
	
	private $subTree=array();
	
	public function onLoad($param){
		parent::onLoad($param);
		$this->Page->ClientScript->registerEndScript('ToggleSub', $this->getClientJavaScript());
	}
	
	public function getDeploy(){
		return $this->getViewState("Deploy", true);
	}
	public function setDeploy($value){
		return $this->setViewState("Deploy", TPropertyValue::ensureBoolean($value), true);
	}
	
	public function getCanDeploy(){
		return $this->getViewState("CanDeploy", true);
	}
	public function setCanDeploy($value){
		return $this->setViewState("CanDeploy", TPropertyValue::ensureBoolean($value), true);
	}
	
	public function getNodeType(){
        return $this->getViewState('NodeType',PFTreeList::NODE_TYPE_PLAIN);
	}
	public function setNodeType($value){
        $this->setViewState('NodeType',TPropertyValue::ensureInteger($value),PFTreeList::NODE_TYPE_PLAIN);
	}
	public function getTitle(){
        return $this->getViewState('Title','');
	}
	public function setTitle($value){
        $this->setViewState('Title',TPropertyValue::ensureString($value),'');
	}
	public function getTitleClass(){
        return $this->getViewState('TitleClass','');
	}
	public function setTitleClass($value){
        $this->setViewState('TitleClass',TPropertyValue::ensureString($value),'');
	}
	public function getCssClass(){
        if($this->getViewState('CssClass','')){
        	return $this->getViewState('CssClass','');
        }
        return ($this->getParent())?$this->getParent()->getCssClass() : $this->getViewState('CssClass','');
	}
	public function setCssClass($value){
        $this->setViewState('CssClass',TPropertyValue::ensureString($value),'');
	}
	public function getID(){
		$id = $this->getViewState('ID', '');
		if($id != '')
			return $id; 
        $id = ($this->getParent())?$this->getParent()->getID().PFTreeList::SUB_ID_TAG:"";
		$id .= $this->getOrder();
		$this->getViewState('ID',TPropertyValue::ensureString($id));
        return $id;
	}
	public function setID($value){
        $this->setViewState('ID',TPropertyValue::ensureString($value),'');
	}
	
	public function getParent(){
        return $this->getViewState('Parent','');
	}
	public function setParent($value){
        $this->setViewState('Parent',$value,'');
	}
	
	public function getOrder(){
        return $this->getViewState('Order',0);
	}
	public function setOrder($value){
        $this->setViewState('Order',$value,0);
	}
	
	
    public function setToPage($value) {
        $this->setViewState("ToPage", $value, '');
    }
    public function getToPage() {
        return $this->getViewState("ToPage", '');
    }
    public function setGetVariables($value) {
        $this->setViewState("GetVariables", $value, '');
    }
    public function getGetVariables() {
        return $this->getViewState("GetVariables", '');
    }
    public function setAnchor($value) {
        $this->setViewState("Anchor", $value, null);
    }
    public function getAnchor() {
        return $this->getViewState("Anchor", null);
    }
    
	private function getClientJavaScript(){

		$script = '
				function toggleSub(id){
					sub = document.getElementById(id+"'.PFTreeList::SUB_ID_UL.'");
					li = document.getElementById(id+"'.PFTreeList::SUB_ID_LI.'");
					
					if (sub == null) return;

					if(sub.style.display == "none")	{
						sub.style.display = "block"; li.className = "node";
					}
					else{
						sub.style.display = "none"; li.className = "nodeDeployed"
					}
				}';

		return	$script;

	}   
	public function renderContents($writer){
		if(!$this->getEnabled())
			return;
		$writer->write("\n<div id='".$this->getID()."'>\n");
		
		$cssclass = ($this->getCssClass())?" class='".$this->getCssClass()."'":"";
		
		$writer->write("<ul $cssclass>\n");
		$this->processChildren($writer);
		$writer->write("</ul>\n");
		
		$writer->write("</div>\n");
	}
	
	private function processChildren($writer){
		if($this->getNodeType() == PFTreeList::NODE_TYPE_INTERNAL_LINK){
			$title = new InternalHyperLink();
			$title->setToPage($this->getToPage());
			$title->setGetVariables($this->getGetVariables());
			$title->setAnchor($this->getAnchor());
			$title->Attributes->OnClick="toggleSub('".$this->getID()."')";
		}
		elseif($this->getNodeType() == PFTreeList::NODE_TYPE_INTERNAL_ACTIVE_LINK){
			$title = new InternalHyperLink();
			$title->setToPage($this->getToPage());
			$title->setGetVariables($this->getGetVariables());
			$title->setAnchor($this->getAnchor());
			$title->Attributes->OnClick="toggleSub('".$this->getID()."')";
		}
		elseif($this->getNodeType() == PFTreeList::NODE_TYPE_LINK){
			$title = new THyperLink();
			$title->setNavigateUrl($this->getToPage());
			$title->Attributes->OnClick="toggleSub('".$this->getID()."')";
		}
		else{
			$title = new TLabel();
		}
		$title->setCssClass($this->getTitleClass());
		$title->setText($this->getTitle());
		
	
		$i=0;
		foreach($this->subTree as $c){
			if(!$c instanceof TWebControl)
				continue;
			$i++;
			break;
		}
		if($i == 0){
			$class = "leaf";
			$fct = "";
		}
		else{
			$class = ($this->getDeploy())?"node":"nodeDeployed";
			$fct = ($this->getCanDeploy())?"onClick='toggleSub(\"".$this->getID()."\")'":"";
		}
		$writer->write("<li class='$class' $fct id='".$this->getID().PFTreeList::SUB_ID_LI."'>");
		$title->render($writer);
		$writer->write("</li>");
		
		
		$cssclass = ($this->getCssClass())?" class='".$this->getCssClass()."'":"";
		
		$style = ($this->getDeploy())?"block":"none";
		if($i > 0)
			$writer->write("<li style='list-style: none outside; padding-left:0px; margin-left:0px; min-height: 0px;' id='".$this->getID().PFTreeList::SUB_ID_UL."'><ul $cssclass style='display: $style'>\n");
		$order = 0;
		foreach($this->subTree as $c){
			if(!$c instanceof TWebControl)
				continue;
			if($c instanceof PFTreeList){
				$c->Parent = $this;
				$c->setOrder($order++);
				$c->setCssClass($this->getCssClass());
				$c->render($writer);
			}
			else{
				$cssclass = ($this->getCssClass())?" class='".$this->getCssClass()."'":"";
				$writer->write("<ul $cssclass><li class='leaf'>");

				$c->getPage()->addParsedObject($c);
				$c->render($writer);
				$writer->write("</li></ul>\n");
			}
		}
		if($i > 0)
			$writer->write("</ul></li>\n");
		
	}
	
	public function getSubTree(){
		return $this->subTree;
	}
	
	public function addSubElement($elt){
		$elt->setPage($this->getPage());
		array_push($this->subTree, $elt);
	}
	
	public function getElements(){
		$elts = array();
		foreach($this->Controls as $c){
			if(!$c instanceof TWebControl)
				continue;
			if($c instanceof PFTreeList){
				$elts = array_merge($elts, $c->getElements());
			}
			else{
				$elts[] = $c;
			}
		}
		return $elts;
	}
}
?>