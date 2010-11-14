<?php

/**
 * Class TTooltip
 * 
 * This class class generates tooltip for any control.
 * 
 * Usage:
 * 
 * <com:TImage ImageUrl="http://path.to.image" ID="image1" />
 * <com:TTooltip forControl="image1" text="tooltip!" />
 * 
 * Or more complex tooltip:
 * 
 * 	<com:TImage ImageUrl="http://path.to.image" ID="image1" />
 * 	<com:TTooltip forControl="image1">
 * 		<prop:Text>
 *			<h1>Tooltip!</h1>
 *			<img src="http://path.to.image2" />
 *	</prop:Text>
 * 	</com:TTooltip>
 * 
 * Tooltip parameters:
 * 
 * Text - tooltip content
 * forControl - tooltip's parent control id
 * CssClass - obvious
 * 
 * You can put html code inside TooltipText property.
 * 
 * @author ikioloak <ikioloak@gmail.com>
 * @version 1.0
 */

Prado::using('System.Web.UI.TTemplateControl');

class TTooltip extends TTemplateControl
{
	const DIR = 'Tooltip/';
	static $instance = false;

	public function onInit($param)
	{
		parent::onInit($param);

		if (self::$instance === false) {
			$jsfile = $this->publishAsset(self::DIR . 'tooltip-v0.1.js');
			$cssfile = $this->publishAsset(self::DIR . 'tooltip.css');

			$csm = $this->getPage()->getClientScript();
			$csm->registerPradoScript("prado");
			if(!$csm->isStyleSheetFileRegistered('tooltip')) {
				$csm->registerStyleSheetFile('tooltip', $cssfile,'screen');
			}

			if (!$csm->isScriptFileRegistered('tooltip')) {
				$csm->registerScriptFile('tooltip', $jsfile);
			}
			
			self::$instance = true;
		}

	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		
		if ($this->getCssClass() === '') {
			$this->setCssClass('defaultTooltip');
		}
	}

	protected function getForControlClientId()
	{
		$fc = $this->getForControl();
		if ($this->getParent() instanceof TContent) {
			return $this->getPage()->$fc->getClientId();
		} else {
			return $this->getParent()->$fc->getClientId();
		}

	}

	public function getText()
	{
		return $this->getViewState('Text', '');
	}

	public function setText($text)
	{
		return $this->setViewState('Text', $text, '');
	}

	public function getForControl()
	{
		return $this->getViewState('ForControl', '');
	}

	public function setForControl($ForControl)
	{
		return $this->setViewState('ForControl', $ForControl, '');
	}

	public function getCssClass()
	{
		return $this->getViewState('CssClass', '');
	}

	public function setCssClass($CssClass)
	{
		return $this->setViewState('CssClass', $CssClass, '');
	}
}

?>