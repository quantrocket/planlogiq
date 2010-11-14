<?php
/**
 * Auto generated by prado-cli.php on 2010-01-05 08:36:51.
 */
class OrganisationRecord extends TActiveRecord
{
	const TABLE='tm_organisation';

	public $idtm_organisation;
	public $org_name;
	public $org_descr;
	public $parent_idtm_organisation;
	public $idta_organisation_type;
	public $idtm_user;
	public $org_mail;
	public $org_idtm_user_role;
	public $org_eskalation;
	public $org_klima;
	public $org_bedeutung;
	public $org_kommunikation;
	public $idtm_ressource;
	public $org_ntuser;
	public $org_name1;
	public $org_name2;
	public $org_anrede;
	public $org_briefanrede;
	public $org_vorname;
	public $org_matchkey;
	public $org_uid;
	public $org_finanzamt;
	public $org_steuernummer;
	public $org_referat;
	public $org_gemeinde;
	public $org_katastragemeinde;
	public $org_grundstuecksnummer;
	public $org_einlagezahl;
	public $org_baujahr;
	public $org_wohnungen;
        public $org_fk_internal;
        public $org_birthday_date;
        public $org_specialday_date;
        public $idta_organisation_art;
	public $org_steuerart;
        public $org_einzugsdatum;
        public $org_auszugsdatum;
        public $org_status;
        public $org_status_date;
        public $idta_branche;
        public $org_titel;
        public $org_aktiv;

        public static $RELATIONS=array
        (
            'orgtype' => array(self::BELONGS_TO, 'OrganisationTypeRecord'),
            'organisationaufgabe' => array(self::HAS_MANY, 'AufgabenRecord'),
            'organisationrcvalue' => array(self::HAS_MANY, 'RCValueRecord'),
            'organisationact' => array(self::HAS_MANY, 'ActivityRecord'),
            'emailrules' => array(self::HAS_MANY, 'EmailRulesRecord'),
        );

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>