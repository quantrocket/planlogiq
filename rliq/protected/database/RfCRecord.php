<?php
/**
 * Auto generated by prado-cli.php on 2009-03-09 09:24:20.
 */
class RfCRecord extends TActiveRecord
{
	const TABLE='tm_changerequest';

	public $idtm_changerequest;
	public $rfc_descr;
	public $rfc_ifnot;
	public $idtm_activity;
	public $rfc_code;
	public $rfc_date;
	public $rfc_suggestdate;
	public $suggest_idtm_organisation;
	public $rfc_cdate;
	public $rfc_gdate;
	public $genemigt_idtm_organisation;
	public $rfc_status;
	public $rfc_dauer;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>