<?php
/**
 * Auto generated by prado-cli.php on 2009-04-05 06:25:49.
 */
class AufgabeRessourceView extends TActiveRecord
{
	const TABLE='vv_aufgabe_ressource';

	public $idtm_aufgabe_ressource;
	public $idtm_aufgabe;
	public $idtm_ressource;
	public $auf_res_dauer;
	public $res_name;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>