<?php
/**
 * Auto generated by prado-cli.php on 2009-03-29 10:43:25.
 */
class StrukturStammdatenGroupView extends TActiveRecord
{
	const TABLE='vv_struktur_stammdaten_group';

	public $idtm_struktur_has_ta_stammdaten_group;
	public $idtm_struktur;
	public $idta_stammdaten_group;
	public $stammdaten_group_name;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>