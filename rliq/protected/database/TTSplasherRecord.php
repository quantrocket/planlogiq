<?php
/**
 * Auto generated by prado-cli.php on 2010-03-07 01:57:40.
 */
class TTSplasherRecord extends TActiveRecord
{
	const TABLE='tt_splasher';

	public $idtt_splasher;
	public $idta_variante;
	public $idta_feldfunktion;
	public $spl_jahr;
	public $spl_monat;
	public $idtm_stammdaten;
	public $spl_faktor;
        public $to_idtm_stammdaten;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>