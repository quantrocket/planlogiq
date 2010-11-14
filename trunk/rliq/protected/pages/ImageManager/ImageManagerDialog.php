<?php
/**
 * ImageManagerDialog class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package xlab6
 */

/**
 * ImageManagerDialog class.
 *
 * TODO: Add more doc.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package xlab6
 * @since 1.0
 */
class ImageManagerDialog extends TTemplateControl
{
	private $_manager;

	public function onLoad($param)
	{
		if(!$this->Page->IsPostBack)
		{
			$assets = $this->Manager->ImageAssetManager;
			$this->assetDir->DataSource = $assets->AssetPaths;
			$this->assetDir->dataBind();
			$this->assetDir->SelectedIndex = $assets->ActiveIndex;
		}
	}

	public function setManager($manager)
	{
		$this->_manager = $manager;
	}

	public function getManager()
	{
		return $this->_manager;
	}

	protected function publishStyleAssets()
	{
		$cs = $this->getPage()->getClientScript();
		$url= $this->publishAsset('assets/dialog.css');
		if(!$cs->isStyleSheetFileRegistered($url))
			$cs->registerStyleSheetFile($url, $url);
		$js = $this->publishAsset('assets/image-manager-dialog.js');
		if(!$cs->isScriptFileRegistered($js))
			$cs->registerScriptFile($js,$js);
		$cs->registerPradoScript("prado");
	}

	protected function getClientScriptOptions()
	{
		$options['ID'] = $this->ClientID;
		$options['Name'] = $this->UniqueID;
		$client = $this->Manager->ClientSide->Options->toArray();
		$options = array_merge($client,$options);
		return $options;
	}

	protected function getClientClassName()
	{
		return 'XLAB6.ImageManager';
	}

	public function onPreRender($param)
	{
		parent::onPreRender($param);
		$this->publishStyleAssets();
		$cs = $this->getPage()->getClientScript();
		$class = $this->getClientClassName();
		$options = TJavascript::encode($this->getClientScriptOptions());
		$script = "new $class($options)";
		$cs->registerEndScript($this->getClientID().'dialog', $script);
	}
}

?>