<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2010 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2010 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.7.3c, 2010-06-01
 */

class PHPExcel_Autoloader
{
	public static function Register() {
		return spl_autoload_register(array('PHPExcel_Autoloader', 'PHPExcelLoad'));
	}	//	function Register()


	public static function PHPExcelLoad($pObjectName){
		if ((class_exists($pObjectName)) || (strpos($pObjectName, 'PHPExcel') === False)) {
			 self::fatalError("Class file for '$pObjectName' cannot be found.");
		}

		$pObjectFilePath = PHPEXCEL_ROOT.str_replace('_',DIRECTORY_SEPARATOR,$pObjectName).'.php';

		if ((file_exists($pObjectFilePath) === false) || (is_readable($pObjectFilePath) === false)) {
			 self::fatalError("Class file for '$pObjectName' cannot be found.");
		}

		require($pObjectFilePath);
	}	//	function PHPExcelLoad()


        //code from framework - pf
        public static function fatalError($msg)
	{
		echo '<h1>Fatal Error</h1>';
		echo '<p>'.$msg.'</p>';
		if(!function_exists('debug_backtrace'))
			return;
		echo '<h2>Debug Backtrace</h2>';
		echo '<pre>';
		$index=-1;
		foreach(debug_backtrace() as $t)
		{
			$index++;
			if($index==0)  // hide the backtrace of this function
				continue;
			echo '#'.$index.' ';
			if(isset($t['file']))
				echo basename($t['file']) . ':' . $t['line'];
			else
			   echo '<PHP inner-code>';
			echo ' -- ';
			if(isset($t['class']))
				echo $t['class'] . $t['type'];
			echo $t['function'] . '(';
			if(isset($t['args']) && sizeof($t['args']) > 0)
			{
				$count=0;
				foreach($t['args'] as $item)
				{
					if(is_string($item))
					{
						$str=htmlentities(str_replace("\r\n", "", $item), ENT_QUOTES);
						if (strlen($item) > 70)
							echo "'". substr($str, 0, 70) . "...'";
						else
							echo "'" . $str . "'";
					}
					else if (is_int($item) || is_float($item))
						echo $item;
					else if (is_object($item))
						echo get_class($item);
					else if (is_array($item))
						echo 'array(' . count($item) . ')';
					else if (is_bool($item))
						echo $item ? 'true' : 'false';
					else if ($item === null)
						echo 'NULL';
					else if (is_resource($item))
						echo get_resource_type($item);
					$count++;
					if (count($t['args']) > $count)
						echo ', ';
				}
			}
			echo ")\n";
		}
		echo '</pre>';
		exit(1);
	}

}