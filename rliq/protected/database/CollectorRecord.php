<?php
/**
 * Auto generated by prado-cli.php on 2008-10-15 08:25:05.
 */
class CollectorRecord extends TActiveRecord
{
	const TABLE='ta_collector';

	public $idta_collector;
	public $idta_feldfunktion;
	public $col_idtafeldfunktion;
	public $col_operator;
	
    //the temporary fields
    public $ff_type;

	public static $RELATIONS=array
    (
        'collectorfeldfunktion' => array(self::HAS_MANY, 'FeldfunktionRecord'),
    );

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>