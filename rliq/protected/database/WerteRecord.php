<?php
/**
 * Auto generated by prado-cli.php on 2008-10-04 01:35:54.
 */
class WerteRecord extends TActiveRecord
{
	const TABLE='tt_werte';

	public $idtt_werte;
	public $w_jahr;
	public $w_monat;
	public $w_wert;
	public $w_endwert;
	public $idta_feldfunktion;
	public $idtm_struktur;
	public $w_id_variante;
        public $w_dimkey;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>