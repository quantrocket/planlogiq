<?php
/**
 * Auto generated by prado-cli.php on 2008-04-27 11:07:02.
 */
class ParteiRecord extends TActiveRecord
{
	const TABLE='ta_partei';

	public $idta_partei;
	public $partei_name;
	public $partei_name2;
	public $partei_name3;
	public $partei_vorname;
	public $idtm_user;
	
	public static $RELATIONS=array
    (
        'parteiadressen' => array(self::HAS_MANY, 'ParteiAdresseRecord','ta_partei_idta_partei'),
    );

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>