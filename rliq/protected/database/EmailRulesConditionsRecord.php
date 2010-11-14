<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailRulesConditionsRecord extends TActiveRecord
{
	const TABLE='ta_email_rules_conditions';

	public $idta_email_rules_conditions;
        public $manual_value;
       
        
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}

        public static $RELATIONS=array
    (
        'emailfieldtype' => array(self::HAS_ONE, 'EmailFieldtypeRecord'),
        'orgafieldtype' => array(self::HAS_ONE, 'EmailRulesOrgafieldtypesRecord'),
        'ruletype' => array(self::HAS_ONE, 'EmailRuletypeRecord'),
    );

    }
?>
