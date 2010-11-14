<?php

/*
  Class JzlTreeControl.
 
  NAME:       JzlTreeControl.php
  PURPOSE:    Mount a tree based on css Tree. If CssClass Property exist not registre default css.
  			  If you have CssClass on item JzlTreeControl the default style is not seted.
  			  If you have CssClass on item JzlTreeControlItem set Css Class like 'last<yourcssclass>'
  			  If you have CssClass on last item JzlTreeControlItem set Css Class like 'last<yourcssclass>'			   
			  If you want change tlabel in itens you need set LabelType like 'file1' node example.*
			  
			  _____________________________________________________________________________________
			  * you need after put LabelType in first property if you have change de labeltype
  Example :
			 <com:JzlTreeControl ID="tree" ImagesPath='themes/default/tree' >
				<com:JzlTreeControlItem Label.Text="Folder1">
								<com:JzlTreeControlItem LabelType="THyperLink" Label.Text="file1" Label.NavigateUrl="a" />
					<com:JzlTreeControlItem Label.Text="file2" />		
					<com:JzlTreeControlItem Label.Text="file3" />		
					<com:JzlTreeControlItem Label.Text="file4" />		
				</com:JzlTreeControlItem>
				<com:JzlTreeControlItem Label.Text="Folder2"  Expand="False">
					<com:JzlTreeControlItem Label.Text="file1" />
					<com:JzlTreeControlItem Label.Text="file2" />		
					<com:JzlTreeControlItem Label.Text="file3" />		
					<com:JzlTreeControlItem Label.Text="file4" />		
				</com:JzlTreeControlItem>	
			</com:JzlTreeControl>
			
			or
			
			<com:JzlTreeControl ID="Tree" ImagesPath='themes/default/tree' RootNode="true">
				<com:JzlTreeControlItem Label.Text="Gerenciador"  Expand="False">
					<com:JzlTreeControlItem Label.Text="Folder1">
						<com:JzlTreeControlItem Label.Text="file1" />
						<com:JzlTreeControlItem Text="file2" />		
						<com:JzlTreeControlItem Label.Text="file3" />		
						<com:JzlTreeControlItem Label.Text="file4" />		
						<com:JzlTreeControlItem Label.Text="subFolder1">
							<com:JzlTreeControlItem Label.Text="file1" />
							<com:JzlTreeControlItem Label.Text="file2" />		
							<com:JzlTreeControlItem Label.Text="file3" />	
							<com:JzlTreeControlItem Label.Text="subFolder1subfonder1">
								<com:JzlTreeControlItem Label.Text="file1" />
								<com:JzlTreeControlItem Label.Text="file2" />		
								<com:JzlTreeControlItem Label.Text="file3" />		
								<com:JzlTreeControlItem Label.Text="file4" CssClass="Nada"/>		
							</com:JzlTreeControlItem>	
							<com:JzlTreeControlItem Label.Text="file4"  />		
						</com:JzlTreeControlItem>
			
					</com:JzlTreeControlItem>
					<com:JzlTreeControlItem Label.Text="Folder2" CssClass="nada" Expand="False">
						<com:JzlTreeControlItem Label.Text="file1" />
						<com:JzlTreeControlItem Label.Text="file2" />		
						<com:JzlTreeControlItem Label.Text="file3" />		
						<com:JzlTreeControlItem Label.Text="file4" />		
					</com:JzlTreeControlItem>	
					<com:JzlTreeControlItem Label.Text="Folder2" Expand="False">
						<com:JzlTreeControlItem Label.Text="file1" />
						<com:JzlTreeControlItem Label.Text="file2" />		
						<com:JzlTreeControlItem Label.Text="file3" />		
						<com:JzlTreeControlItem Label.Text="file4" />		
					</com:JzlTreeControlItem>	
				</com:JzlTreeControlItem>	


     REVISIONS:
     Ver        Date        Author          Description
     ---------  ----------  --------------  ------------------------------------
     1.0        31/01/2007  Jzlima          1. Created JzlTreeControl
     1.0        06/02/2007  Jzlima          2. Property root node added, 
	 										   problem when property expande=true put unnecessary  colapse css class.
     1.0        07/02/2007  Jzlima          3. Property root node added, 
											   subfolders problems was corrected
											   Problem when dynamic create was corrected
											   label type can be changed

     1.0        07/02/2007  Jzlima          4. problem to resgister stylesheet  for 2 trees

 */
class JzlTreeControl extends TWebControl
{

const STYLE_SHEET =
'
ul.%1$s, ul.%1$s ul {
		 list-style-type: none;
		 background: white url(\'%2$s/vline.png\') repeat-y; 
		 margin: 0; 
		 padding: 0; 
}


ul.%1$s ul{ 
	     margin-left: 10px; 
} 

ul.%1$s li {
	margin: 0;
	padding: 0 12px;
	line-height: 20px;
	background:  url(\'%2$s/node.png\') no-repeat;
	list-style-image:url(\'%2$s/page-file.png\');
	list-style-position:inside;
} 

ul.%1$s li.rootnode {
	list-style-image:url(\'%2$s/rootnode.png\');
	background: #fff no-repeat;
} 

ul.%1$s li.expand{
	list-style-image:url(\'%2$s/page-foldericon.png\');
} 

ul.%1$s li.colapse{
	list-style-image:url(\'%2$s/page-openfoldericon.png\');	
} 

ul.%1$s li.lastexpand{
	list-style-image:url(\'%2$s/page-foldericon.png\');
	background: #fff  url(\'%2$s/lastnode.png\') no-repeat; /* need background color*/
} 

ul.%1$s li.lastcolapse{
	list-style-image:url(\'%2$s/page-openfoldericon.png\');
	background: #fff url(\'%2$s/lastnode.png\') no-repeat; /* need background color*/
} 


ul.%1$s li.last { 
		background:#fff url(\'%2$s/lastnode.png\') no-repeat;/* need background color*/
}

ul.%1$s .plus { 
		background:   url(\'%2$s/plus.png\') 0  no-repeat;
		cursor: pointer;
		margin:0 -40px;
	    padding:0 40px;
}

ul.%1$s .minus { 
		background:  url(\'%2$s/minus.png\') 0  no-repeat;
		cursor: pointer;
		margin:0 -40px;
	    padding:0 40px;
}


*html ul.%1$s .minus,*html ul.%1$s .plus  /* this is recognized by IE only */
{ _margin:0 -32px; /* total width, only for IE5.x/Win */  
  _m\argin:0 -32px;/* content width for other IE */
}

'; 
	const JAVA_SCRIPT = '
function tree_Colapse(folder){
	var ulbelow=folder.parentNode.getElementsByTagName("ul");
	var action;

	if (folder.parentNode.className.indexOf(\'last\') >= 0 ){
		folder.parentNode.className=(folder.parentNode.className==\'lastcolapse\')?\'lastexpand\':\'lastcolapse\';
		action = folder.parentNode.className==\'lastcolapse\';
	}else{
		folder.parentNode.className=(folder.parentNode.className==\'colapse\')?\'expand\':\'colapse\';
		action = folder.parentNode.className==\'colapse\';
	}
	folder.className=(folder.className==\'plus\')?\'minus\':\'plus\';
	for( i=0; i<ulbelow.length; i++ ){
		ulbelow[i].style.display = (action)? \'block\' : \'none\';		
	}

}';

	private $_CssClass;
	/**
	 * @var string 
	 */
	
	private $_ImagesPath='';
	
	/**
	 * @var boolean 
	 */
	
	private $_RootNode=false;
	
	/**
	 * @return boolean  Defaults to false.
	 */
	public function getRootNode()
	{
		return $this->_RootNode;
	}
 
	/**
	 * @param boolean 
	 */
	public function setRootNode($value)
	{
		$this->_RootNode=TPropertyValue::ensureBoolean($value);
	}
	/**
	 * @return string  Defaults to ''.
	 */
	public function getImagesPath()
	{
		return $this->_ImagesPath;
	}
 
	/**
	 * @param string 
	 */
	public function setImagesPath($value)
	{
		$this->_ImagesPath=TPropertyValue::ensureString($value);
	}
	
	
	
	
	public function addParsedObject($Item) {
		if ($Item instanceof JzlTreeControlItem) {
			$this->Controls[] = $Item;
		}
		elseif (! is_string($Item))
			throw new TInvalidDataTypeException(
				'childs of JzlTreeControlItem must be JzlTreeControlItem type ('
				. get_class($MenuItem) . ' given)'
			);
	}
	
	protected function getTagName() {
		return 'div';
	}
	
	public function renderBeginTag($writer) {
		parent::renderBeginTag($writer);
		$writer->write("\n");
		$writer->addAttribute('class',$this->_CssClass);		
		$writer->renderBeginTag('ul');
		$writer->write("\n");
	}
	
	public function renderEndTag($writer) {		
		$writer->renderEndTag('ul');
		$writer->write("\n");
		parent::renderEndTag($writer);
		$writer->write("\n");
	}
	
	
	public function renderContents($writer) {					

		if ($this->Controls->Count > 0 && $this->_RootNode)
				$this->Controls[0]->setCssClass("rootnode"); 
		if ($this->Controls->Count > 1) {
			$this->Controls[$this->Controls->Count-1]->setCssClass("lastcolapse".$this->Controls[$this->Controls->Count-1]->getCssClass());
		}
		parent::renderContents($writer) ;		
	}
	
	
	public function OnInit($param) {
		parent::OnInit($param);
		
		
		
		$ClientScript = $this->Page->ClientScript;
		//CSS stylesheet		
		$this->_CssClass = $this->getID();       
	    $registerID = 'JzlTreeControl' . 
			($this->CssClass)
				? '-' . $this->CssClass
				: '';			
		if (! $ClientScript->isStyleSheetRegistered($registerID) && !$this->getCssClass())
			$ClientScript->registerStyleSheet(
				$registerID.$this->getID(),
				sprintf(self::STYLE_SHEET,$this->_CssClass,$this->_ImagesPath));		
		
		$mainRegisterID = 'JzlTreeControl.Global';			
		//Javascript		
		if (! $ClientScript->isBeginScriptRegistered($mainRegisterID))
			$ClientScript->registerBeginScript(
				$mainRegisterID,sprintf(self::JAVA_SCRIPT,$this->_CssClass));
	}

}

/*
  Class JzlTreeControlItem.
 
  NAME:       JzlTreeControlItem.php
  PURPOSE:    Mount a tree based on css Tree
  			   
  Example :
				<com:JzlTreeControlItem Text="Folder1" Expand="False">

     REVISIONS:
     Ver        Date        Author          Description
     ---------  ----------  --------------  ------------------------------------
     1.0        31/01/2007  Jzlima          1. Created JzlTreeControl
 

 */


class JzlTreeControlItem extends TWebControl implements INamingContainer {
    
	private $_Text='';
	private $_label='';	
	private $_labelType='TLabel';	
	/**
	 * @var boolean 
	 */
	private $_expand=true;
 
	/**
	 * @return boolean  Defaults to false.
	 */
	public function getExpand()
	{
		return $this->_expand;
	}
 
	/**
	 * @param boolean 
	 */
	public function setExpand($value)
	{
		$this->_expand=TPropertyValue::ensureBoolean($value);
	}

	/**
	 * @return string  Defaults to ''.
	 */
	public function getText()
	{
		return $this->_Text;
	}
 
	/**
	 * @param string 
	 */
	public function setText($value)
	{
		$this->_Text=TPropertyValue::ensureString($value);
	}
	
	/**
	 * @return string  Defaults to ''.
	 */
	public function getLabelType()
	{
		return $this->_labelType;
	}
 
	/**
	 * @param string 
	 */
	public function setLabelType($value)
	{
		$this->_labelType=TPropertyValue::ensureString($value);
	}


	protected function getTagName() {
		return 'li';
	}
	
	public function getLabel() {
		$this->ensureChildControls();
		return $this->_label;
	}

	public function createChildControls() {
		$this->_label = new $this->_labelType;	
	}

	public function renderEndTag($writer) {
		parent::renderEndTag($writer);
		$writer->write("\n");
	}
	

		/**
	 * Attach JavaScript helpers if this control has other controls.
	 * 
	 * @see addAttributesToRender()
	 */
	protected function addAttributesToRender($writer) {
		if ($this->Controls->Count > 1 && $this->_expand) 
		   $writer->addAttribute('class','colapse');
		
		parent::addAttributesToRender($writer);
	}
	/**
	 * Render contents. Add style='z-index: {@link getZIndex() ZIndex}'
	 * to ul tags.
	 * 
	 * @see renderContents()
	 */
	public function renderContents($writer) {	
	   
				
	
	    //set last css class
		if ($this->Controls->Count > 1 && $lastHasMoreContros == 0 ) {
				$lastHasMoreContros = $this->Controls[$this->Controls->Count-1]->Controls->Count;
		   
				if ($lastHasMoreContros>1) $this->Controls[$this->Controls->Count-1]->setCssClass("lastcolapse".$this->Controls[$this->Controls->Count-1]->getCssClass());
				else
				$this->Controls[$this->Controls->Count-1]->setCssClass("last".$this->Controls[$this->Controls->Count-1]->getCssClass());
			}
	
	
		if ($this->HasControls ) {
		    if ( $this->_expand){
			    $this->_label->setCssClass("minus");			
				$this->_label->setAttribute('onclick','tree_Colapse(this)');
			}
			$this->_label->render($writer);
			$writer->write("\n");
			$writer->renderBeginTag('ul');
			$writer->write("\n");
		} else $this->_label->render($writer);
		
		parent::renderContents($writer);
		if ($this->HasControls) {
			$writer->renderEndTag('ul');
			$writer->write("\n");
		}
	}
	
	/**
	 * Add MTDropDownMenuItem's. Auto-increment z-index.
	 * 
	 * @param MTDropDownMenuItem|string child control
	 */
	public function addParsedObject($Item) {
		if ($Item instanceof JzlTreeControlItem) {
			$this->Controls[] = $Item;
		}
		elseif (! is_string($Item))
			throw new TInvalidDataTypeException(
				'childs of JzlTreeControlItem must be type of ' .
				'JzlTreeControlItem ('
				. get_class($Item) . ' given)'
			);
	}
	
	
}
?>