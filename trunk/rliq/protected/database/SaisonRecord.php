<?php
/**
 * Auto generated by prado-cli.php on 2009-07-30 09:20:08.
 */
class SaisonRecord extends TActiveRecord
{
	const TABLE='ta_saisonalisierung';

	public $idta_saisonalisierung;
	public $sai_name;
	public $idtm_struktur;
	public $idta_feldfunktion;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>