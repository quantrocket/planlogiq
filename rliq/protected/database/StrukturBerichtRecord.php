<?php
/**
 * Auto generated by prado-cli.php on 2009-04-23 10:23:06.
 */
class StrukturBerichtRecord extends TActiveRecord
{
	const TABLE='ta_struktur_bericht';

	public $idta_struktur_bericht;
	public $idtm_user;
	public $pivot_struktur_cdate;
	public $pivot_struktur_name;
        public $sb_order;
        public $sb_startbericht;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>