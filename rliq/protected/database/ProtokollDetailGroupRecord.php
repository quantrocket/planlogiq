<?php
/**
 * Auto generated by prado-cli.php on 2009-01-18 12:51:11.
 */
class ProtokollDetailGroupRecord extends TActiveRecord
{
	const TABLE='ta_protokoll_detail_group';

	public $idta_protokoll_detail_group;
	public $idtm_protokoll;
        public $idtm_protokoll_detail_group;
	
	public static $RELATIONS=array
    (
        'papaprotokoll' => array(self::BELONGS_TO, 'ProtokollRecord'),
    	'kindprotokolldetail' => array(self::HAS_MANY, 'ProtokollDetailRecord'),
    );

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>