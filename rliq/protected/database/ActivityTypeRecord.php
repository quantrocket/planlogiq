<?php
/**
 * Auto generated by prado-cli.php on 2008-12-03 07:36:19.
 */
class ActivityTypeRecord extends TActiveRecord
{
	const TABLE='ta_activity_type';

	public $idta_activity_type;
	public $act_type_name;
	
	public static $RELATIONS=array
    (
        'typeact' => array(self::HAS_MANY, 'ActivityRecord'),
    );

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>