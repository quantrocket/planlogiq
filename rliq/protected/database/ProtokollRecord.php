<?php
/**
 * Auto generated by prado-cli.php on 2008-12-10 10:46:10.
 */
class ProtokollRecord extends TActiveRecord
{
	const TABLE='tm_protokoll';

	public $idtm_protokoll;
	public $prt_name;
	public $prt_cdate;
	public $prt_location;
	public $prt_dauer;
	public $idtm_organisation;
	public $idtm_termin;
	public $idta_protokoll_type;
	
	public static $RELATIONS=array
    (
        'kindprotokolltype' => array(self::BELONGS_TO, 'ProtokollTypeRecord'),
        'kindprotokolldetailgroup' => array(self::HAS_MANY, 'ProtokollDetailGroupRecord'),
    );

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>