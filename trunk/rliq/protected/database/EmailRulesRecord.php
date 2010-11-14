<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class EmailRulesRecord extends TActiveRecord
{
	const TABLE='ta_email_rules';

	public $idta_email_rules;
        public $order;
        public $label;
        public $description;
        public $idtm_organisation;
        
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}

        public static $RELATIONS=array
    (
        'emailruleconditions' => array(self::HAS_MANY, 'EmailRulesConditionsRecord'),
        'organisationselement' => array(self::HAS_ONE, 'OrganisationRecord'),
    );

    }
?>
