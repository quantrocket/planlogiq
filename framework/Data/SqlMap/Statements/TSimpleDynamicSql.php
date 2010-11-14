<?php
/**
 * TSimpleDynamicSql class file.
 *
 * @author Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2005-2010 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: TSimpleDynamicSql.php 2818 2010-04-18 04:31:22Z javalizard $
 * @package System.Data.SqlMap.Statements
 */

/**
 * TSimpleDynamicSql class.
 *
 * @author Wei Zhuo <weizho[at]gmail[dot]com>
 * @version $Id: TSimpleDynamicSql.php 2818 2010-04-18 04:31:22Z javalizard $
 * @package System.Data.SqlMap.Statements
 * @since 3.1
 */
class TSimpleDynamicSql extends TStaticSql
{
	private $_mappings=array();

	public function __construct($mappings)
	{
		parent::__construct();
		$this->_mappings = $mappings;
	}

	public function replaceDynamicParameter($sql, $parameter)
	{
		foreach($this->_mappings as $property)
		{
			$value = TPropertyAccess::get($parameter, $property);
			$sql = preg_replace('/'.TSimpleDynamicParser::DYNAMIC_TOKEN.'/', str_replace('$', '\$', $value), $sql, 1);
		}
		return $sql;
	}
}

