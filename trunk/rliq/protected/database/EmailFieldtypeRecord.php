<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailFieldtypeRecord extends TActiveRecord
{
	const TABLE='ta_email_rules_emailfieldtypes';

	public $idta_email_rules_emailfieldtypes;
        public $name;
	

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
