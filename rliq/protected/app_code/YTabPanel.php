<?php
/**
 * YTabPanel and YTabPagesCollection class file
 *
 * @author Tomasz Wolny <tomasz.wolny@polecam.to.pl>
 * @version $Id: YTabPanel.php 0005 29.05.2007 02:43 $
 * @copyright Copyright &copy; 2007 Polecam.TO 
 * @package System.Web.UI.WebControls
 */

/**
 * YTabPanel class
 *
 * YTabPanel displays an HTML blocks (div) on a Web page.
 *
 * <code>
 *   <com:YTabPanel [CssClass = "(@string)"] 
 *                  [PageCssClass = "@string"]
 *                  [ActiveTabCssClass = "(@string)"]
 *                  [InactiveTabCssClass = "(@string)"]
 *                  [DefaltPageIndex="(@Integer)" | DefaultPageID="(@string)"]   >
 *     <com:YTabPage [ID="(@string)"] Caption="(@string)" Text="(@string)" />
 *     <com:YTabPage [ID="(@string)"] Caption="(@string)">
 *       Content (other controls)
 *     </com:YTabPage>
 *   </com:YTabPanel>
 * </code>
 *
 * LEGEND:
 * [ ] - optional parameters
 * CssClass             - default name: tab-panel
 * PageCssClass         - default name: tab-page
 * ActiveTabCssClass    - default name: tab-active
 * InactiveTabCssClass  - default name: tab-inactive 
 * DefaultPageIndex     - param: @integer - index of page for first display
 * DefaultPageID        - param: @string - this same as DefaultPageIndex, 
 *                      - but param is a ID (PRADO) of page for first display
 * IMPORTAND: DefaultPageID has highest priority(!), and DefaultPageIndex value 
 * (if it use both, and both are correct) will be ignored!    
 * IMPORTAND: Unsuitable values in DefaultPageIndex or DefaultPageID, will be ignored! 
 *   
 * @author Tomasz Wolny <tomasz.wolny@polecam.to.pl>
 * @version $Id: YTabPanel.php 0005 29.05.2007 02:43 $
 * @package System.Web.UI.WebControls
 * @since 3.0
 */
class YTabPanel extends TWebControl
{
	private $defaultID;
  /**
	 * @return string tag name for the table
	 */
	protected function getTagName()
	{
		return 'div';
	}

	/**
	 * Adds object parsed from template to the control.
	 * This method adds only {@link YTabPage} objects into the {@link getTabPages YTabPage} collection.
	 * All other objects are ignored.
	 * @param mixed object parsed from template
	 */
	public function addParsedObject($object)
	{
		if($object instanceof YTabPage)
			$this->getTabPages()->add($object);
	}
  /**
   * @return css class name for TabPanel. Default: class="tab-panel"   
   */     
  public function getCssClass()
	{
		return $this->getViewState('CssClass','tab-panel');
	}
  /**
   * @param Name of css class for TabPanel. Default: class="tab-panel"  
   */     
	public function setCssClass($value)
	{
		$this->setViewState('CssClass',$value,'tab-panel');
	}
	/**
   * @return css class name for TabPage. Default: class="tab-page"   
   */     
  public function getPageCssClass()
	{
		return $this->getViewState('PageCssClass','tab-page');
	}
  /**
   * @param Name of css class for TabPage. Default: class="tab-page" 
   */     
	public function setPageCssClass($value)
	{
		$this->setViewState('PageCssClass',$value,'tab-page');
	}
  /**
   * @return css class name for active tab. Default: class="tab-active"   
   */     
  public function getActiveTabCssClass()
	{
		return $this->getViewState('ActiveTabCssClass','tab-active');
	}
  /**
   * @param Name of css class for active tab. Default: class="tab-active" 
   */     
	public function setActiveTabCssClass($value)
	{
		$this->setViewState('ActiveTabCssClass',$value,'tab-active');
	}
	/**
   * @return css class name for inactive tab. Default: class="tab-inactive"  
   */     
  public function getInactiveTabCssClass()
	{
		return $this->getViewState('InactiveTabCssClass','tab-inactive');
	}
  /**
   * @param Name of css class for inactive tab. Default: class="tab-inactive"  
   */     
	public function setInactiveTabCssClass($value)
	{
		$this->setViewState('InactiveTabCssClass',$value,'tab-inactive');
	}
	/**
   * @return index of page for first display. Default: ''  
   */     
  public function getDefaultPageIndex()
	{
		return $this->getViewState('DefaultPageIndex','');
	}
  /**
   * @param index of page for first display. Default: ''  
   */     
	public function setDefaultPageIndex($value)
	{
    $this->setViewState('DefaultPageIndex',TPropertyValue::ensureInteger($value),0);  
	}
  /**
   * @return index of page for first display. Default: ''  
   */     
  public function getDefaultPageID()
	{
		return $this->getViewState('DefaultPageID','');
	}
  /**
   * @param index of page for first display. Default: ''  
   */     
	public function setDefaultPageID($value)
	{   
    $this->setViewState('DefaultPageID',TPropertyValue::ensureString($value));   
	}
  
  public function isDefault($pageID)
  {
    $tabpages = $this->getTabPages(); 
    if(($dpID = $this->getDefaultPageID()) == '' || ($fc = $this->findControl($dpID)) == null)
      if(($dpIndx = $this->getDefaultPageIndex()) == 0 || !isset($tabpages[$dpIndx]))
        $this->setDefaultPageID($tabpages[0]->ClientID);
      else 
        $this->setDefaultPageID($tabpages[$dpIndx]->ClientID);
    
    return $pageID == $this->getDefaultPageID() ? true : false;
  }
    
	/**
	 * Adds attributes to renderer.
	 * @param THtmlWriter the renderer
	 */	 
	protected function addAttributesToRender($writer)
	{
    $page = $this->getPage();
		$csm = $page->getClientScript();
		if(!$csm->isScriptFileRegistered('prado'))
		  $csm->registerPradoScript("prado");
		$writer->addAttribute('class',$this->getCssClass());
    $writer->addAttribute('id',$this->ClientID);
    parent::addAttributesToRender($writer);
	} 

	/**
	 * Creates a control collection object that is to be used to hold child controls
	 * @return YTabPagesCollection control collection
	 * @see getControls
	 */
	protected function createControlCollection()
	{
		return new YTabPagesCollection($this);
	}

	/**
	 * @return YTabPagesCollection list of {@link YTabPage} controls
	 */
	public function getTabPages()
	{
		return $this->getControls();
	}
       
	/**
	 * Renders the openning tag for the TabPanel control which will render TabPanel menu.
	 * @param THtmlWriter the writer used for the rendering purpose
	 */
	public function renderBeginTag($writer)
	{
	  parent::renderBeginTag($writer);		
	}
  
  /**
   * @return HTML code for tabs.
   */     
  protected function getTabHTMLCode($tabpanel, $tabpage){
    $nl = "\n";
    
    $class = $this->isDefault($tabpage->ClientID) ? 
      ' class="'.$this->getActiveTabCssClass().'"' : ' class="'.$this->getInactiveTabCssClass().'"';
		$html  = '<div id="t_'.$tabpage->ClientID.'" '.$class.'>'.$nl;
    $html .= '<a href="javascript:;//'.$tabpage->ClientID.'"';
		$html .= ' onclick="';
    $html .= $tabpanel.'.showpage(';
		$html .= "'".$tabpage->ClientID."'";
		$html .= ')">'.$tabpage->Caption;
    $html .= '</a>'.$nl;
    $html .= '</div>'.$nl;        
    return  $html;
  }
  
	/**
	 * Renders body contents of the table.
	 * @param THtmlWriter the writer used for the rendering purpose.
	 */
	public function renderContents($writer)
	{
		if($this->getHasControls()) {
      $writer->writeLine();
      $tabPages = $this->getTabPages();
      foreach($tabPages as $index => $tabpage) {
				$writer->writeLine($this->getTabHTMLCode($this->ClientID, $tabpage));
        $ClientIDList[] = $tabpage->ClientID;	
			}    
      foreach($this->getControls() as $index => $tabpage) {
        $tabpage->renderControl($writer);       // --- render of one page from tabPages collection
        $writer->writeLine();
      }
			$script = "var ".$this->ClientID."={ showpage: function(tpts) { ['".implode("','",$ClientIDList)."'].each( function(item) { if(item==tpts) { $('t_'+item).className='".$this->getActiveTabCssClass()."'; $(item).show() } else { $('t_'+item).className='".$this->getInactiveTabCssClass()."'; $(item).hide() } }) } }; ";
      $this->Page->ClientScript->registerEndScript( $this->ClientID, $script );
		}
	}
}
/**
 * YTabPageCollection class.
 *
 * YTabPageCollection is used to maintain a list of pages belong to a TabPanel.
 *
 */
class YTabPagesCollection extends TControlCollection
{
	/**
	 * Inserts an item at the specified position.
	 * This overrides the parent implementation by performing additional
	 * operations for each newly added pages in TabPanel.
	 * @param integer the speicified position.
	 * @param mixed new item
	 * @throws TInvalidDataTypeException if the item to be inserted is not a YTabPage object.
	 */
	public function insertAt($index,$item)
	{
		if($item instanceof YTabPage)
			parent::insertAt($index,$item);
		else
			throw new TInvalidDataTypeException(
      'childs of YTabPanel must be YTabPage type ('
			.get_class($TabPage).' given)');
	}
}



class YTabPage extends TWebControl
{
  protected function getTagName()
	{
		return 'div';
	}
	/**
	 * Adds attributes to renderer.
	 * @param THtmlWriter the renderer
	 */
	protected function addAttributesToRender($writer)
	{
    $writer->addAttribute('class',$this->Parent->getPageCssClass());
    $writer->addAttribute('id',$this->ClientID);
    $style = ($w = $this->getWidth()) !== '' ? 'width:'.$w.';' : '';
    $style .= ($h = $this->getHeight()) !== '' ? 'height:'.$h.';' : '';
    $style .= !$this->Parent->isDefault($this->ClientID) ? 'display:none;' : '';
    if($style !== '') $writer->addAttribute('style', $style);
    parent::addAttributesToRender($writer);
	}
	
	/**
	 * @return string
	 */   	
	public function getCaption()
	{
    return $this->getViewState('Caption','');
  }
  /**
	 * @param string 
	 */
	public function setCaption($value)
	{
		$this->setViewState('Caption',TPropertyValue::ensureString($value),'');
	}
	/**
	 * @return string
	 */   	
	public function getText()
	{
    return $this->getViewState('Text','');
  }
  /**
	 * @param string 
	 */
	public function setText($value)
	{
		$this->setViewState('Text',TPropertyValue::ensureString($value),'');
	}
	/**
	 * @return string
	 */   	
	public function getWidth()
	{
    return $this->getViewState('Width','');
  }
  /**
	 * @param string
	 */
	public function setWidth($value)
	{
		$this->setViewState('Width',TPropertyValue::ensureString($value),'');
	}
	/**
	 * @return string
	 */   	
	public function getHeight()
	{
    return $this->getViewState('Height','');
  }
  /**
	 * @param string
	 */
	public function setHeight($value)
	{
		$this->setViewState('Height',TPropertyValue::ensureString($value),'');
	}

	
  /**
	 * Renders body contents of the table cell.
	 * @param THtmlWriter the writer used for the rendering purpose.
	 */
	public function renderContents($writer)
	{
		if(($text=$this->getText())!=='')
			$writer->write($text);
		else if($this->getHasControls())
			parent::renderContents($writer);
		else
			$writer->write('&nbsp;');
	}
}

?>
