<?php
/**
 * Auto generated by prado-cli.php on 2008-12-23 09:50:35.
 */
class ActivityParticipantsView extends TActiveRecord
{
	const TABLE='vv_activity_participants';

	public $idtm_activity;
	public $idtm_organisation;
	public $idtm_activity_participant;
	public $org_name;
	public $user_role_name;
	public $act_part_anwesend;
	public $act_part_notiz;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>