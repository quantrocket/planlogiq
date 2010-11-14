<?php
/**
 * ImageAssetManager class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2007 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id$
 * @package xlab6
 */

/**
 * ImageAssetManager class.
 *
 * TODO: Add more docs.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id$
 * @package xlab6
 * @since 1.0
 */
class ImageAssetManager extends TComponent
{
	private $_assetPath;
	private $_assetPaths;

	private $_currentPath;

	private $_currentDirectory;
	private $_baseDirectory;

	private $_manager;

	public function __construct($manager)
	{
		$this->_manager = $manager;
	}

	protected function getManager()
	{
		return $this->_manager;
	}

	public function getCurrentPath()
	{
		return $this->_currentPath;
	}

	public function setCurrentPath($value)
	{
		if(!is_null($value))
			$this->_currentPath = $value;
	}

	public function getBaseDirectory()
	{
		if(is_null($this->_baseDirectory))
		{
			$path = $this->getManager()->getAssetPath();
			if($path===null || !is_dir($path))
				$path = Prado::getPathOfNamespace($path);
			if($path === null || !is_dir($path))
				throw new TConfigurationException('invalid asset path "{0}"', $this->getManager()->getAssetPath());
			$this->_baseDirectory = realpath($path);
		}
		return $this->_baseDirectory;
	}

	public function getAssetPaths($refresh=false)
	{
		if(is_null($this->_assetPaths) || $refresh)
		{
			$path = $this->getBaseDirectory();
			$it = new RecursiveDirectoryIterator($path);
			$dirs['/'] = true;
			$this->walkDirectoryIterator($path, $dirs, $it);
			$paths = array_keys($dirs);
			usort($paths,array($this,'sortByFilename'));
			$this->_assetPaths = $paths;
		}
		return $this->_assetPaths;
	}


	protected function walkDirectoryIterator($base, &$dirs, $it)
	{
		foreach($it as $entry)
		{
			if($it->hasChildren())
				$this->walkDirectoryIterator($base, $dirs, $it->getChildren());
			if($entry->isDir())
			{
				$param = new ImageAssetManagerEventParameter($entry);
				$this->onFileAccess($param);
				if($param->getIsValid())
				{
					$dir = $this->trimDirectoryPath($base, $entry->getPathName());
					$dirs[$dir] = true;
				}
			}
		}
	}

	public function getAssetUrl($file)
	{
		$baseUrl = $this->getManager()->getAssetBaseUrl();
		$path = $this->getCurrentDirectory();
		$filepath = realpath($path.'/'.$file->getFileName());
		$fileUrl = str_replace($this->getBaseDirectory(), '', $filepath);
		$url = $file->isDir() ? $fileUrl.'/' : $baseUrl.$fileUrl;
		return str_replace('\\','/',$url);
	}

	private function trimDirectoryPath($base, $path)
	{
		$dir = str_replace($base, '', $path.'/');
		return str_replace('\\', '/', $dir);
	}


	public function getActivePath()
	{
		$dir = $this->getCurrentPath();
		if(!is_null($dir) && in_array($dir,$this->getAssetPaths()))
			return $dir;
		return '/';
	}

	public function getActiveIndex()
	{
		$i = array_search($this->getCurrentPath(), $this->getAssetPaths());
		return $i !== false ? $i : 0;
	}

	public function getCurrentDirectory()
	{
		return $this->getBaseDirectory().$this->getActivePath();
	}


	public function getFilesInActivePath()
	{
		$path = $this->getCurrentDirectory();
		$files = array();
		$dirs = array();
		$it = new RecursiveDirectoryIterator($path);
		foreach($it as $file)
		{
			$param = new ImageAssetManagerEventParameter($file);
			$this->onFileAccess($param);
			if($param->getIsValid())
			{
				if($file->isDir())
					$dirs[] = $file;
				else
					$files[] = $file;
			}
		}
		usort($dirs,array($this,'sortByFilename'));
		usort($files,array($this,'sortByFilename'));
		return array_merge($dirs,$files);
	}

	// NOTE: not UTF-8 safe
	protected function sortByFilename($a,$b)
	{
		if(is_object($a))
			return strcmp(strtolower($a->getFileName()),
				strtolower($b->getFileName()));
		else
			return strcmp(strtolower($a),strtolower($b));
	}

	public function onFileAccess($param)
	{
		$this->raiseEvent('OnFileAccess', $this, $param);
	}
}

class ImageAssetManagerEventParameter extends TComponent
{
	private $_file;
	private $_valid=true;

	public function __construct($file)
	{
		$this->_file = $file;
	}

	public function getFile()
	{
		return $this->_file;
	}

	public function setIsValid($value)
	{
		$this->_valid = $value;
	}

	public function getIsValid()
	{
		return $this->_valid;
	}
}

?>