<?php
/**
 * ImageListView class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package xlab6
 */

/**
 * ImageListView class.
 *
 * TODO: Add more docs.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package xlab6
 * @since 1.0
 */
class ImageListView extends TTemplateControl
{
	private $_manager;
	private $_folderImage;
	private $_defaultThumbnail;

	private $_files=array();

	private $_refreshDir=false;

	public function onLoad($param)
	{
		if(!$this->Page->IsPostBack)
			$this->showFiles();
	}

	protected function showFiles()
	{
		$assets = $this->Manager->ImageAssetManager;
		$this->list->DataSource = $assets->FilesInActivePath;
		$this->list->dataBind();
	}

	protected function createNewFolder($sender, $param)
	{
		if($this->Page->IsValid && $this->Manager->EnableNewFolder)
		{
			$this->getManager()->onCreateNewFolder($this->NewFolder->Text);
			$this->showFiles();
			$this->_refreshDir = true;
		}
	}

	protected function deleteSelectedFile($sender, $param)
	{
		if($this->Manager->EnableDelete)
		{
			foreach($this->Manager->ImageAssetManager->FilesInActivePath as $file)
			{
				$filename = $this->FileToDelete->Value;
				if($file->getFileName() === $filename)
				{
					$this->getManager()->onFileDelete($file);
					$this->showFiles();
					$this->_refreshDir = true;
					return;
				}
			}
		}
	}

	protected function uploadCompleted($sender,$param)
	{
		$this->showFiles();
	}

	protected function uploadFiles($sender, $param)
	{
		if($sender->getHasFile() && $this->Manager->EnableUpload)
			$this->getManager()->onFileUpload($sender);
	}

	public function setManager($manager)
	{
		$this->_manager = $manager;
	}

	public function getManager()
	{
		return $this->_manager;
	}

	protected function list_OnItemCreated($sender, $param)
	{
		$item = $param->getItem();
		$file = $item->DataItem;
		if($file !== null)
		{
			$options = $this->getFileOptions($file);
			$item->thumbnail->ImageUrl = $options['Thumbnail'];
			$item->thumbnail->Attributes['alt'] = 'IMG://'.$options['Filename'];
			$item->thumbnail->Attributes['title'] = $options['Caption'];

			$id = $this->Manager->DialogID;
			$click = "return IM_Click(this,'$id');";
			$dbClick = "return IM_DblClick(this, '$id');";

			$item->link->Attributes['title'] = $options['Caption'];
			$item->link->Attributes['ondblclick'] = $dbClick;
			$item->link->Attributes['onclick'] = $click;
			$item->link->Attributes['type'] = TJavascript::encode($options);
		}
	}

	protected function getFileOptions($file)
	{
		$assets = $this->Manager->ImageAssetManager;
		$options['Filename'] = $assets->getAssetUrl($file);
		$options['Thumbnail'] = $this->getThumbnailUrl($file);
		$options['LastModified'] = $this->getLastModified($file);
		$options['Name'] = $file->getFilename();
		$options = array_merge($options, $file->isDir() ?
			$this->getFolderOptions($file) : $this->getImageFileOptions($file));
		return $options;
	}

	protected function getFolderOptions($file)
	{
		$options['Folder'] = true;
		$options['Caption'] = $file->getFileName();
		return $options;
	}

	protected function getImageFileOptions($file)
	{
		$options['Size'] = $file->getSize();
		$this->_files[] = $options['Size'];
		$options['DisplaySize'] = $this->formatSize($file->getSize());
		$info = getimagesize($file);
		$options['Width'] = $info[0];
		$options['Height'] = $info[1];
		$options['Caption'] = $file->getFileName().' - '.
								$options['Width'].'x'.$options['Height'].' - '.
								$options['DisplaySize'];
		return $options;
	}

	protected function getLastModified($file)
	{
		return date('Y-m-d h:i:s', $file->getMTime());
	}

	protected function getThumbnailUrl($file)
	{
		if($file->isDir())
			return $this->publishFolderImage();
		else
		{
			$thumb = $this->Manager->ThumbnailManager->getThumbnailUrl($file);
			return $thumb !== null ? $thumb : $this->publishDefaultThumbnail();
		}
	}

	protected function formatSize($size)
	{
		$kb = $size/1024.0;
		if($kb < 1)
			return $size.' bytes';
		$Mb = $size/1048576.0;
		if($Mb < 1)
			return sprintf('%01.2f kb', $kb);
		return sprintf('%01.2f Mb', $Mb);
	}

	protected function publishStyleAssets()
	{
		$cs = $this->getPage()->getClientScript();
		$url = $this->publishAsset('assets/imagelist.css');
		if(!$cs->isStyleSheetFileRegistered($url))
			$cs->registerStyleSheetFile($url, $url);
		$js = $this->publishAsset('assets/image-manager-list.js');
		if(!$cs->isScriptFileRegistered($js))
			$cs->registerScriptFile($js,$js);
		$cs->registerPradoScript("prado");
	}

	protected function publishFolderImage()
	{
		if(is_null($this->_folderImage))
		{
			$cs = $this->getPage()->getClientScript();
			$image = 'assets/folder_images.gif';
			$this->_folderImage = $this->publishAsset($image);
		}
		return $this->_folderImage;
	}

	protected function publishDefaultThumbnail()
	{
		if(is_null($this->_defaultThumbnail))
		{
			$cs = $this->getPage()->getClientScript();
			$image = 'assets/default_thumbnail.gif';
			$this->_defaultThumbnail = $this->publishAsset($image);
		}
		return $this->_defaultThumbnail;
	}

	protected function getClientScriptOptions()
	{
		$options['DialogID'] = $this->Manager->DialogID;
		$options['ID'] = $this->ClientID;
		$options['FormID'] = $this->Page->Form->ClientID;
		$options['Files'] = count($this->_files);
		$options['TotalSize'] = $this->formatSize(array_sum($this->_files));
		if($this->_refreshDir)
			$options['Dirs'] = $this->Manager->ImageAssetManager->getAssetPaths(true);
		return $options;
	}

	protected function getClientClassName()
	{
		return 'XLAB6.ImageManagerListView';
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
	}}

?>