<?php
/**
 * Auto generated by prado-cli.php on 2009-07-15 11:18:43.
 */
class BerichteRecord extends TActiveRecord
{
	const TABLE='ta_berichte';

	public $idta_berichte;
	public $ber_name;
	public $ber_descr;
	public $ber_cdate;
	public $idtm_user;
	public $idta_bericht_type;
	public $idtm_organisation;
	public $ber_id;
	public $ber_mail_subject;
	public $ber_mail_body;
	public $ber_local_path;
	public $ber_zyklus;
	public $ber_zyklus_gap;
	public $ber_zyklus_start;
        public $ber_zyklus_time;
	public $ber_production_time;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>