<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailRulesOrgafieldtypesRecord extends TActiveRecord
{
	const TABLE='ta_email_rules_orgafieldtypes';

	public $idta_email_rules_orgafieldtypes;
        public $name;
	

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
