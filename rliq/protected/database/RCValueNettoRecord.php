<?php
/**
 * Auto generated by prado-cli.php on 2009-01-05 10:53:15.
 */
class RCValueNettoRecord extends TActiveRecord
{
	const TABLE='tt_rcvalue_netto';

	public $idtt_rcvalue;
	public $rcv_ewk;
	public $rcv_schaden;
	public $rcv_prio;
	public $rcv_cby;
	public $rcv_cdate;
	public $idtm_rcvalue;
	public $rcv_descr;
	public $rcv_kosten;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>