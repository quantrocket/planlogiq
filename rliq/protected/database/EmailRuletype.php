<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailRuletype extends TActiveRecord
{
	const TABLE='ta_email_ruletype';

	public $idta_email_ruletype;
        public $name;
	

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}

    }
?>
