<?php
/**
 * Auto generated by prado-cli.php on 2008-04-25 09:07:26.
 */
class UserRoleRecord extends TActiveRecord
{
	const TABLE='tm_user_role';

	public $idtm_user_role;
	public $user_role_name;
	public $user_role_rechte;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	public static $RELATIONS=array
    (
        'user' => array(self::BELONGS_TO, 'UserRecord','idtm_user'),
    );
	
	
}
?>