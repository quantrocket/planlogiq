<?php
/**
 * Auto generated by prado-cli.php on 2009-04-06 04:12:50.
 */
class ActivityOrganisationView extends TActiveRecord
{
	const TABLE='vv_activity_organisation';

	public $idtm_activity_has_tm_organisation;
	public $idtm_activity;
	public $idtm_organisation;
        public $org_name;
        public $org_stundensatz;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>