<?php
/**
 * ThumbnailGenerator class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package xlab6
 */

/**
 * Simple thumbnail generator using GD.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package xlab6
 * @since 1.0
 */
class ThumbnailGenerator extends TComponent
{
	private $_width=96;
	private $_height=96;
	private $_proportional=true;

	public function setWidth($value)
	{
		$this->_width=TPropertyValue::ensureInteger($value);
	}

	public function getWidth()
	{
		return $this->_width;
	}

	public function setHeight($value)
	{
		$this->_height = TPropertyValue::ensureInteger($value);
	}

	public function getHeight()
	{
		return $this->_height;
	}

	public function setProportional($value)
	{
		$this->_proportional = TPropertyValue::ensureBoolean($value);
	}

	public function getProportional()
	{
		return $this->_proportional;
	}

	public function createThumbnail($source, $info, $destination)
	{
		if(!function_exists('imagecreatetruecolor'))
			return null;
		if($info[0] <= $this->getWidth() && $info[1] <= $this->getHeight())
			return $source;
		$dim = $this->getThumbnailDimension($info);
		if($dim[0] <= 0 || $dim[1] <= 0)
			return null;
		$img = $this->loadImage($source, $info);
		if($img === null)
			return null;
		$thumb = $this->resizeImage($img,$info,$dim);
		if($thumb!==null)
		{
			$saved = $this->saveImage($thumb, $info, $destination);
			imagedestroy($thumb);
			return $destination;
		}
		imagedestroy($img);
	}

	protected function resizeImage($image,$info,$dim)
	{
		$image_p = imagecreatetruecolor($dim[0], $dim[1]);
		imagecopyresampled($image_p,
			$image, 0, 0, 0, 0, $dim[0], $dim[1], $info[0], $info[1]);
		return $image_p;
	}

	protected function getThumbnailDimension($info)
	{
		$w = $info[0]; $h = $info[1];
		$mw = $this->getWidth(); $mh = $this->getHeight();
		if($w <= $mw && $h <= $mh)
			return array($w,$h);
		if(!$this->getProportional())
		{
			$width = $w > $mw ? $mw : $w;
			$height = $h > $mh ? $mh : $h;
		}
		else
		{
			$ratio = $w > $h ? $mw/$w : $mh/$h;
			$width = round($w*$ratio);
			$height = round($h*$ratio);
		}
		return array($width,$height);
	}

	protected function loadImage($source, $info)
	{
		switch($info[2])
		{
			case 1: //GIF
				return imagecreatefromgif($source);
			case 2: //JPG
				return imagecreatefromjpeg($source);
			case 3: //PNG
				return imagecreatefrompng($source);
		}
	}

	protected function saveImage($img, $info, $filename)
	{
		if(!$this->ensureDirectoryExists($filename))
			return null;
		switch($info[2])
		{
			case 1: //GIF
				return imagegif($img, $filename);
			case 2: //JPG
				return imagejpeg($img,$filename);
			case 3: //PNG
				return imagepng($img,$filename);
		}
	}

	protected function ensureDirectoryExists($filename)
	{
		$dir = dirname($filename);
		if(!is_dir($dir))
		{
			mkdir($dir, 0777);
			chmod($dir,0777);
		}
		if(!is_dir($dir) || !is_writable($dir))
			return false;
		return true;
	}
}

?>