<?php
/**
 * Auto generated by prado-cli.php on 2008-10-15 08:25:05.
 */
class CollectorView extends TActiveRecord
{
	const TABLE='vv_collector_feldfunktion';

	public $idta_collector;
	public $idta_feldfunktion;
	public $col_idtafeldfunktion;
	public $col_operator;
    public $ff_name;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>