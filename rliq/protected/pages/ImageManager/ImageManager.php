<?php
/**
 * ImageManager and auxilary classes file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package xlab6
 */


require_once(dirname(__FILE__).'/ThumbnailManager.php');
require_once(dirname(__FILE__).'/ImageAssetManager.php');
require_once(dirname(__FILE__).'/ImageManagerDialog.php');
require_once(dirname(__FILE__).'/ImageListView.php');
require_once(dirname(__FILE__).'/ThumbnailGenerator.php');

/**
 * Image manager component for THtmlArea
 *
 * TODO: Add more doc.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package xlab6
 * @since 1.0
 */
class ImageManager extends TControl
{
	const LIST_VIEW_ID = '__IMID';
	const LIST_VIEW_PATH = '__IMPATH';
	const DIALOG_ID = '__IMDIA';

	private $_thumbnailClass='ThumbnailManager';
	private $_thumbnailManager;

	private $_assetManagerClass='ImageAssetManager';
	private $_assetManager;

	private $_dialog;
	private $_list;

	private $_clientSide;

	public function onInit($param)
	{
		parent::onInit($param);
		$this->getImageAssetManager()->setCurrentPath(
			$this->Request[self::LIST_VIEW_PATH]);
	}

	function onLoad($param)
	{
		$id = $this->Request[self::LIST_VIEW_ID];
		if($id === $this->getClientID())
			$this->Controls[] = $this->getImageList();
		else if($this->Request[self::LIST_VIEW_ID]===null)
			$this->Controls[] = $this->getDialog();
	}

	public function onCreateNewFolder($name)
	{
		$base = $this->getImageAssetManager()->getCurrentDirectory();
		$param = new IMCreateFolderEventParameter($base,$name);
		$this->raiseEvent('OnCreateNewFolder', $this, $param);
	}

	public function onFileUpload($file)
	{
		$base = $this->getImageAssetManager()->getCurrentDirectory();
		$param = new IMFileUploadEventParameter($base,$file);
		$this->raiseEvent('OnFileUpload',$this,$param);
	}

	public function onFileDelete($file)
	{
		if($file->isFile())
		{
			$thumbnail = $this->getThumbnailManager()->getThumbnailFile($file);
			$param = new IMDeleteFileEventParameter($file->getPathname(),false,$thumbnail);
		}
		else
		{
			$param = new IMDeleteFileEventParameter($file->getPathname(),true);
		}
		$this->raiseEvent('OnFileDelete', $this, $param);
	}

	public function getDialog()
	{
		if(is_null($this->_dialog))
		{
			$this->_dialog = new ImageManagerDialog();
			$this->_dialog->setManager($this);
		}
		return $this->_dialog;
	}

	public function getImageList()
	{
		if(is_null($this->_list))
		{
			$this->_list = new ImageListView();
			$this->_list->setManager($this);
		}
		return $this->_list;
	}

	public function getClientSide()
	{
		if(is_null($this->_clientSide))
			$this->_clientSide = $this->createClientSide();
		return $this->_clientSide;
	}

	protected function createClientSide()
	{
		return new ImageManagerClientSide;
	}

	public function setThumbnailManagerClass($value)
	{
		$this->_thumbnailClass=$value;
	}

	public function getThumbnailManagerClass()
	{
		return $this->_thumbnailClass;
	}

	public function getThumbnailManager()
	{
		if(is_null($this->_thumbnailManager))
		{
			$this->_thumbnailManager = Prado::createComponent(
				$this->getThumbnailManagerClass(),$this);
		}
		return $this->_thumbnailManager;
	}

	public function setImageAssetManagerClass($value)
	{
		$this->_assetManagerClass=$value;
	}

	public function getImageAssetManagerClass()
	{
		return $this->_assetManagerClass;
	}

	public function getImageAssetManager()
	{
		if(is_null($this->_assetManager))
		{
			$this->_assetManager = Prado::createComponent(
				$this->getImageAssetManagerClass(),$this);
			$this->_assetManager->OnFileAccess[] = array($this, 'filterImageFiles');
		}
		return $this->_assetManager;
	}

	protected function filterImageFiles($sender, $param)
	{
		$manager = $this->getThumbnailManager();
		$isThumbnail = $manager->isThumbnailFile($param->File);
		$isImage = $manager->isImageFile($param->File);
		$param->IsValid = $param->IsValid && $isImage && !$isThumbnail;
		$this->onFileAccessed($param);
	}

	public function onFileAccessed($param)
	{
		$this->raiseEvent('OnFileAccessed', $this,$param);
	}

	public function setAssetPath($value)
	{
		$this->setViewState('AssetPath', $value);
	}

	public function getAssetPath()
	{
		return $this->getViewState('AssetPath');
	}

	public function setAssetBaseUrl($value)
	{
		$this->setViewState('AssetBaseUrl', $value);
	}

	public function getAssetBaseUrl()
	{
		return $this->getViewState('AssetBaseUrl');
	}

	public function getEnableUpload()
	{
		return $this->getViewState('EnableUpload', false);
	}

	public function setEnableUpload($value)
	{
		$this->setViewState('EnableUpload', TPropertyValue::ensureBoolean($value),false);
	}

	public function getEnableNewFolder()
	{
		return $this->getViewState('EnableNewFolder', false);
	}

	public function setEnableDelete($value)
	{
		$this->setViewState('EnableDelete', TPropertyValue::ensureBoolean($value),false);
	}

	public function getEnableDelete()
	{
		return $this->getViewState('EnableDelete', false);
	}

	public function setEnableNewFolder($value)
	{
		$this->setViewState('EnableNewFolder', TPropertyValue::ensureBoolean($value),false);
	}

	public function getDialogID()
	{
		return $this->Request[self::DIALOG_ID];
	}

	//utility functions
	public function getImageListViewUrl()
	{
		$id = $this->getRequest()->getServiceID();
		$param = $this->getRequest()->getServiceParameter();
		$path = $this->getImageAssetManager()->getActivePath();
		$this->getRequest()->add(self::LIST_VIEW_PATH,$path);
		$this->getRequest()->add(self::LIST_VIEW_ID,$this->getClientID());
		$this->getRequest()->add(self::DIALOG_ID,$this->getDialog()->getClientID());
		$url = $this->getRequest()->constructUrl('page',$id,$param, $this->getRequest());
		return $url;
	}
}

class IMCreateFolderEventParameter extends TEventParameter
{
	private $_baseDir;
	private $_name;

	public function __construct($base, $name)
	{
		$this->_baseDir = $base;
		$this->_name = $name;
	}

	public function getBaseDir()
	{
		return $this->_baseDir;
	}

	public function getNewFolderName()
	{
		return $this->_name;
	}

	public function getDirectory()
	{
		return $this->getBaseDir().$this->getNewFolderName();
	}
}

class IMDeleteFileEventParameter extends TEventParameter
{
	private $_isDir=false;
	private $_filepath;
	private $_thumbnail;

	public function __construct($filepath,$isDir=false,$thumbnail=null)
	{
		$this->_isDir = $isDir;
		$this->_filepath = $filepath;
		$this->_thumbnail = $thumbnail;
	}

	public function getFilePath()
	{
		return $this->_filepath;
	}

	public function getIsDir()
	{
		return $this->_isDir;
	}

	public function getIsFile()
	{
		return !$this->getIsDir();
	}

	public function getThumbnail()
	{
		return $this->_thumbnail;
	}
}

class IMFileUploadEventParameter extends TEventParameter
{
	private $_file;
	private $_baseDir;

	public function __construct($base,$file)
	{
		$this->_baseDir = $base;
		$this->_file = $file;
	}

	public function getDirectory()
	{
		return $this->_baseDir;
	}

	public function getFileUpload()
	{
		return $this->_file;
	}

	/**
	 * @return string the original full path name of the file on the client machine
	 */
	public function getFileName()
	{
		return $this->_file->getFilename();
	}

	/**
	 * @return integer the actual size of the uploaded file in bytes
	 */
	public function getFileSize()
	{
		return $this->_file->getFileSize();
	}

	/**
	 * @return string the MIME-type of the uploaded file (such as "image/gif").
	 * This mime type is not checked on the server side and do not take its value for granted.
	 */
	public function getFileType()
	{
		return $this->_file->getFileType();
	}

	/**
	 * @return string the local name of the file (where it is after being uploaded).
	 * Note, PHP will delete this file automatically after finishing this round of request.
	 */
	public function getLocalName()
	{
		return $this->_file->getLocalName();
	}

	/**
	 * Returns an error code describing the status of this file uploading.
	 * @return integer the error code
	 * @see http://www.php.net/manual/en/features.file-upload.errors.php
	 */
	public function getErrorCode()
	{
		return $this->_file->getErrorCode();
	}

	/**
	 * Saves the uploaded file.
	 * @param string the file name used to save the uploaded file
	 * @param boolean whether to delete the temporary file after saving.
	 * If true, you will not be able to save the uploaded file again.
	 * @return boolean true if the file saving is successful
	 */
	public function saveAs($fileName,$deleteTempFile=true)
	{
		if($this->getErrorCode()===UPLOAD_ERR_OK)
		{
			if($deleteTempFile)
				return move_uploaded_file($this->getLocalName(),$fileName);
			else if(is_uploaded_file($this->getLocalName()))
				return file_put_contents($fileName,
					file_get_contents($this->getLocalName()))!==false;
			else
				return false;
		}
		else
			return false;
	}
}

class ImageManagerClientSide extends TClientSideOptions
{
	public function setOnUrlChange($script)
	{
		$this->setFunction('OnUrlChange', $script);
	}

	public function getOnUrlChange()
	{
		return $this->getOption('OnUrlChange');
	}

	public function setOnThumbnailClick($script)
	{
		$this->setFunction('OnThumbnailClick', $script);
	}

	public function getOnThumbnailClick()
	{
		return $this->getOption('OnThumbnailClick');
	}

	public function setOnThumbnailDblClick($script)
	{
		$this->setFunction('OnThumbnailDblClick', $script);
	}

	public function getOnThumbnailDblClick()
	{
		return $this->getOption('OnThumbnailDblClick');
	}
}

?>