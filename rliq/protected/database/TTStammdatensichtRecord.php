<?php
/**
 * Auto generated by prado-cli.php on 2010-10-27 04:33:18.
 */
class TTStammdatensichtRecord extends TActiveRecord
{
	const TABLE='tt_stammdatensicht';

	public $idtt_stammdatensicht;
	public $idta_stammdaten_group;
	public $parent_idta_stammdaten_group;
	public $idta_stammdatensicht;
        public $sts_stammdaten_group_use;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>