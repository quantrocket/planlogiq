<?php
/**
 * Auto generated by prado-cli.php on 2009-04-23 10:25:49.
 */
class SBSCollectorRecord extends TActiveRecord
{
	const TABLE='ta_sbs_collector';

	public $idta_sbs_collector;
	public $idta_struktur_bericht_spalten;
	public $row_idta_struktur_bericht_spalten;
	public $sbs_collector_operator;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>