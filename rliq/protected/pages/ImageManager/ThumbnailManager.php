<?php
/**
 * ThumbnailManager class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package xlab6
 */

/**
 * Thumbnail manager class.
 *
 * TODO: Add docs.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package xlab6
 * @since 1.0
 */
class ThumbnailManager extends TComponent
{
	private $_manager;
	private $_thumbnailPath=".thumbs";
	private $_fileCache=array();
	private $_assetBase;

	private $_thumbnailGenClass='ThumbnailGenerator';
	private $_thumbnailGen;

	public function __construct($manager)
	{
		$this->_manager = $manager;
	}

	protected function getManager()
	{
		return $this->_manager;
	}

	public function getThumbnailPath()
	{
		return $this->_thumbnailPath;
	}

	public function setThumbnailPath($value)
	{
		$this->_thumbnailPath = $value;
	}

	public function setThumbnailGeneratorClass($value)
	{
		$this->_thumbnailGenClass = $value;
	}

	public function getThumbnailGeneratorClass()
	{
		return $this->_thumbnailGenClass;
	}

	protected function getThumbnailGenerator()
	{
		if(is_null($this->_thumbnailGen))
			$this->_thumbnailGen = Prado::createComponent(
				$this->getThumbnailGeneratorClass());
		return $this->_thumbnailGen;
	}

	public function isThumbnailFile($file)
	{
		return is_int(strpos($file->getFileName(), $this->getThumbnailPath()));
	}

	public function isImageFile($file)
	{
		if($file->isDir())
			return true;
		$filename = $file->getPathName();
		$info = getimagesize($filename);
		if($info !== false)
		{
			$this->_fileCache[$filename] = $info;
			return true;
		}
		return false;
	}

	public function getThumbnailFile($file)
	{
		$filename = $file->getPathName();
		$dir = dirname($filename);
		$thumbnail = $dir.'/'.$this->getThumbnailPath().'/'.basename($filename);
		if(is_file($thumbnail) && filemtime($thumbnail) >= filemtime($filename))
			return $thumbnail;
		$gen = $this->getThumbnailGenerator();
		return $gen->createThumbnail($filename,
			$this->getFileInfo($filename), $thumbnail);
	}

	protected function getFileInfo($filename)
	{
		if(isset($this->_fileCache[$filename]))
			return $this->_fileCache[$filename];
		return getimagesize($filename);
	}

	protected function getAssetUrl($filename)
	{
		if(is_null($this->_assetBase))
			$this->_assetBase = realpath($this->getManager()->getAssetPath());
		$file = str_replace($this->_assetBase,'',$filename);
		$url = $this->getManager()->getAssetBaseUrl().$file;
		return str_replace('\\','/',$url);
	}
	public function getThumbnailUrl($file)
	{
		$thumbnail = $this->getThumbnailFile($file);
		if($thumbnail !== null)
			return $this->getAssetUrl($thumbnail);
	}
}

?>