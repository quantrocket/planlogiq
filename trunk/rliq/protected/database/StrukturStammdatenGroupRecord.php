<?php
/**
 * Auto generated by prado-cli.php on 2009-03-28 02:43:53.
 */
class StrukturStammdatenGroupRecord extends TActiveRecord
{
	const TABLE='tm_struktur_has_ta_stammdaten_group';

	public $idtm_struktur_has_ta_stammdaten_group;
	public $idtm_struktur;
	public $idta_stammdaten_group;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>