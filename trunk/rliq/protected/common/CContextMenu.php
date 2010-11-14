<?php
/* 
 * CContextMenu class file
 *
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @license http://www.pradosoft.com/license
 * @version $Id: CContextMenu.php 126 2009-03-11 07:24:26Z tof $
 */

if (!prado::getPathOfAlias('CContextMenu')) prado::setPathOfAlias('CContextMenu', dirname(__FILE__));

/**
 * CContextMenu class
 *
 * This class is a Prado wrapper to 'Proto.Menu' written by 'kangax'
 *
 * Properties :
 *	o ForControl : Id of control on which attach the contextual menu.
 *	o CssSelector: a css selector to find the controls to attach the menu (e.g. CssSelector=".className").
 *  o CssUrl : an url to an alternate css file. By default, the proto.menu.0.6.css will be used
 *  o CssClass : Css class of the menu (default to "menu desktop". See proto.menu.css for info)
 *  o Items : a collection of CContextMenuItem.
 *
 * If theses two properties are present, ForControl takes precedence.
 *
 * Events :
 *  o OnMenuItemSelected event is raised when a menu item is clicked. The event parameter contains the index of the clicked element, and its command name.
 *
 * Items Properties :
 *	o Text : Text of menu item
 *  o CssClass : Css class of the item
 *	o CommandName : Name of the command to be used in OnMenuItemSelected
 *  o Enabled: true to enable the item, false to disable it
 *  o ClientSide : ClientSide events handler :
 *		o ClientSide.onClick : will launch this function if defined before raising postback. Postback will not occur if this function returns false.
 *
 *
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @license http://www.pradosoft.com/license.
 * @version $Id: CContextMenu.php 126 2009-03-11 07:24:26Z tof $
 */
class CContextMenu extends TControl implements IPostBackEventHandler {

	const DEFAULT_JS_NAME="proto.menu.0.6.js";
	const DEFAULT_CSS_NAME="proto.menu.0.6.css";

	/**
	 * Set the url for an external css file
	 * 
	 * @param string the url to the external css file
	 */
	public function setCssUrl($value)
	{
		$this->setViewState('cssUrl', TPropertyValue::ensureString($value), '');
	}

	/**
	 * Get the url of the external css file
	 *
	 * @return string the url of the external css file
	 */
	public function getCssUrl()
	{
		return $this->getViewState('cssUrl', '');
	}

	/**
	 * Set the Css class of the menu
	 *
	 * @param string css class
	 */
	public function setCssClass ($value)
	{
		$this->setViewState('cssClass', TPropertyValue::ensureString($value), '');
	}

	/**
	 * Get the css class of the menu
	 *
	 * @return string css class
	 */
	public function getCssClass ()
	{
		return $this->getViewState('cssClass','');
	}

	/**
	 * @param string id of the control to attach menu
	 */
	public function setForControl ($value)
	{
		$this->setViewState('forControl', TPropertyValue::ensureString($value), null);
	}

	/**
	 * @return string id of the control which menu is attached
	 */
	public function getForControl ()
	{
		return $this->getViewState('forControl', null);
	}

	/**
	 * @param string CssSelector of controls to attach menu
	 */
	public function setCssSelector ($value)
	{
		$this->setViewState('cssSelector',$value,null);
	}

	/**
	 * @return string CssSelector of controls to attach menu
	 */
	public function getCssSelector ()
	{
		return $this->getViewState('cssSelector', null);
	}

	/**
	 * @param CContextMenuItem
	 */
	public function addParsedObject ($object)
	{
		if ($object instanceof CContextMenuItem)
		{
			$this->getItems()->add($object);
		}
	}

	/**
	 * Get items list
	 * @return CContextMenuItemCollection
	 */
	public function getItems()
	{
		if (($items=$this->getViewState('menuItems', null))===null)
		{
			$items=new CContextMenuItemCollection();
			$this->setViewState('menuItems', $items);
		}
		return $items;
	}

	/**
	 * Registers the needed javascripts & css files
	 * @param TEventParameter $param
	 */
	public function onPreRender ($param)
	{
		parent::onPreRender ($param);
		if ($this->getItems()->count() > 0)
		{
			$cs=$this->getPage()->getClientScript();
			if (!$cs->isScriptFileRegistered('proto-menu'))
				$cs->registerScriptFile('proto-menu', $this->publishAsset('assets/'.self::DEFAULT_JS_NAME, __CLASS__));
			if (!$cs->isScriptFileRegistered('CContextMenu'))
				$cs->registerScriptFile('CContextMenu', $this->publishAsset('assets/CContextMenu.js', __CLASS__));
			if (($css=$this->getCssUrl())==='')
				$css=$this->publishAsset('assets/'.self::DEFAULT_CSS_NAME, __CLASS__);
			$cs->registerStyleSheetFile('proto-css', $css);
			$this->renderClientControlScript();
		}
	}
	/**
	 * This control doesn't render anything, except its client control script
	 *
	 * @param THtmlWriter $writer
	 */
	public function render ($writer)
	{
		
	}

	/**
	 * Renders the client-script code.
	 */
	protected function renderClientControlScript()
	{
		$cs = $this->getPage()->getClientScript();
		$cs->registerPostBackControl($this->getClientClassName(),$this->getPostBackOptions());
	}

	protected function getClientClassName ()
	{
		return "CContextMenu";
	}

	protected function getPostBackOptions ()
	{
		if (($controlId=$this->getForControl()) !== null)
		{
			if ($control=$this->findControl($controlId))
				$options['selector']='#'.$control->getClientID();
			else
				throw new TInvalidDataValueException('Invalid control id');
		}
		elseif (($selector=$this->getCssSelector())!==null)
			$options['selector']=$selector;
		if (($class=$this->getCssClass()))
			$options['className']=$class;
		else
			$options['className']="menu desktop";
		$options['items'] = $this->getItems()->getClientOptions();
		$options['EventTarget'] = $this->getUniqueID();
		$options['ID']=$this->getClientId();

		return $options;
	}

	public function raisePostBackEvent($param) {
		list($index,$elementId)=explode(',',$param);
		$this->OnMenuItemSelected(new CContextMenuEventParameter(
				$index, $this->getItems()->itemAt($index)->getCommandName(),$elementId
		));
	}

	public function OnMenuItemSelected ($param)
	{
		$this->raiseEvent('OnMenuItemSelected', $this, $param);
	}
}

/**
 * CContextMenuEventParameter class
 *
 * This class encapsulates the parameter for OnMenuItemSelected event.
 * It contains the clicked item's index inside the menu item collections, and its command name.
 */
class CContextMenuEventParameter extends TEventParameter
{
	private $_itemIndex;
	private $_itemCommand;
	private $_elementId;

	public function __construct ($index,$command, $elementId)
	{
		$this->_itemIndex=$index;
		$this->_itemCommand=$command;
		$this->_elementId=$elementId;
	}

	/**
	 * @return int the item index
	 */
	public function getIndex ()
	{
		return $this->_itemIndex;
	}

	/**
	 * @return string the item command name
	 */
	public function getCommand ()
	{
		return $this->_itemCommand;
	}

	public function getElementId ()
	{
		return $this->_elementId;
	}
}

/**
 * CContextMenuItem class
 *
 * This component represent an item in the context menu
 *
 * Properties :
 *	o Text : Text of menu item
 *  o CssClass : Css class of the item
 *	o CommandName : Name of the command to be used in OnMenuItemSelected
 *  o Enabled: true to enable the item, false to disable it
 *  o ClientSide : ClientSide events handler :
 *		o ClientSide.onClick : will launch this function if defined before raising postback. Postback will not occur if this function returns false.
 *
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @license http://www.pradosoft.com/license.
 * @version $Id: CContextMenu.php 126 2009-03-11 07:24:26Z tof $
 */
class CContextMenuItem extends TComponent
{
	private $_text;
	private $_cssClass='';
	private $_clientside;
	private $_commandName;
	private $_enabled=true;

	/**
	 * @param string Text of the menu item
	 */
	public function setText ($value)
	{
		$this->_text=TPropertyValue::ensureString($value);
	}

	/**
	 * @return string Text of the menu item
	 */
	public function getText ()
	{
		return $this->_text;
	}

	/**
	 * @param string Css class of the menu item
	 */
	public function setCssClass ($value)
	{
		$this->_cssClass=TPropertyValue::ensureString($value);
	}

	/**
	 * @return string Css class of the menu item
	 */
	public function getCssClass ()
	{
		return $this->_cssClass;
	}

	/**
	 * @return string Command name associated to the menu item
	 */
	public function getCommandName ()
	{
		return $this->_commandName;
	}

	/**
	 * @param string Command name associated to the menu item
	 */
	public function setCommandName ($value)
	{
		$this->_commandName=TPropertyValue::ensureString($value);
	}

	/**
	 * @param boolean true to enable the menu item.
	 */
	public function setEnabled ($value)
	{
		$this->_enabled=TPropertyValue::ensureBoolean($value);
	}

	/**
	 * @return boolean whether the menu item is enabled or not
	 */
	public function getEnabled ()
	{
		return $this->_enabled;
	}

	/**
	 * @return CContextMenuItemClientScript Client Side events for this item
	 */
	public function getClientSide ()
	{
		if ($this->_clientside===null)
		{
			$this->_clientside=$this->createClientSide();
		}
		return $this->_clientside;
	}

	/**
	 * @return CContextMenuItemClientScript
	 */
	protected function createClientSide ()
	{
		return new CContextMenuItemClientScript;
	}

	/**
	 * @return array Clientside options
	 */
	public function getClientOptions ()
	{
		$options['name'] = $this->getText();
		$options['className'] = $this->getCssClass();
		if (($onclick=$this->getClientSide()->getOnClick())!==null)
		{
			$options['onClick']=$onclick;
		}
		$options['disabled']=!$this->getEnabled();
		return $options;
	}

}

/**
 * CContextMenuItemSeparator class
 *
 * This is a special item which will render a 'separator' between 2 menu items.
 *
 * @author Christophe Boulain <Christophe.Boulain@ceram.fr>
 * @version $Id: CContextMenu.php 126 2009-03-11 07:24:26Z tof $
 */
class CContextMenuItemSeparator extends CContextMenuItem
{
	public function setText ($value)
	{
		throw new TConfigurationException('Menu items separators can\'t have Text property');
	}
	
	public function setCommandName ($value)
	{
		throw new TConfigurationException('Menu items separators can\'t have CommandName property');
	}
	
	public function getClientSide ()
	{
		throw new TConfigurationException('Menu items separators can\'t have ClientSide property');
	}
	
	public function getClientOptions ()
	{
		return array ('separator' => true);
	}
}

/**
 * CContextMenuItemClientScript class.
 * 
 * Client-side ContextMenuItem event {@link setOnClick OnClick} can be modified through the CContextMenuItem::getClientSide property
 * of a CContextMenuItem
 * 
 * The <tt>OnClick</tt> event is raised when user click on an item in the context menu 
 * 
 * @author Christophe Boulain <Christophe.Boulain@gmail.com>
 * @version $Id: CContextMenu.php 126 2009-03-11 07:24:26Z tof $
 */
class CContextMenuItemClientScript extends TClientSideOptions
{
	public function setOnClick ($script)
	{
		$this->setFunction('OnClick', $script);
	}

	public function getOnClick ()
	{
		return $this->getOption('OnClick');
	}
}

/**
 * CContextMenuItemCollection class
 *
 * This list holds all the items in a context menu
 */
class CContextMenuItemCollection extends TList
{
	/**
	 * Inserts an item at the specified position.
	 * This overrides the parent implementation by performing type
	 * check on the item being added.
	 * @param integer the speicified position.
	 * @param mixed new item
	 * @throws TInvalidDataTypeException if the item to be inserted is not a {@link CMultiStateButtonState}
	 */
	public function insertAt($index,$item)
	{
		if($item instanceof CContextMenuItem)
			parent::insertAt($index,$item);
		else
			throw new TInvalidDataTypeException('Invalid context menu item');
	}

	/**
	 * Generates an array with all items client options
	 * @return array
	 */
	public function getClientOptions ()
	{
		$options=array();
		foreach ($this as $item)
			$options[]=$item->getClientOptions();
		return $options;
	}

}
?>
