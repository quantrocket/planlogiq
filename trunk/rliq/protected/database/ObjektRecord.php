<?php
/**
 * Auto generated by prado-cli.php on 2010-01-05 08:37:44.
 */
class ObjektRecord extends TActiveRecord
{
	const TABLE='ta_objekt';

	public $idta_objekt;
	public $idtm_organisation;
	public $obj_nutzflaeche;
	public $obj_nutzflaeche_date;
        public $obj_nutzflaeche_type; //1 = Wohnzwecke 2 = Betrieblich
	public $obj_gbanteile;
	public $obj_gbanteile_date;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>