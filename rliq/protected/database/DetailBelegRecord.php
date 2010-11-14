<?php
/**
 * Auto generated by prado-cli.php on 2010-06-22 11:21:56.
 */
class DetailBelegRecord extends TActiveRecord
{
	const TABLE='tm_detail_beleg';

	public $idtm_detail_beleg;
	public $deb_tabelle;
	public $deb_id;
	public $deb_cdate;
	public $deb_order;
	public $deb_nummer;
	public $deb_descr;
	public $deb_menge;
	public $deb_preis;
	public $deb_inout;
	public $deb_tax;
	public $deb_date;
	public $deb_deleted;
	public $deb_cby;
	public $deb_konto;

        //neu
        //20101005:: ALTER TABLE `tm_detail_beleg` ADD COLUMN `deb_detail` TINYINT(1) NULL DEFAULT 0 COMMENT 'Gruppiert oder nicht gruppiert'  AFTER `deb_konto` , ADD COLUMN `deb_discount` FLOAT NULL DEFAULT 0.00 COMMENT 'Nachlass oder Rabatt'  AFTER `deb_detail` , ADD COLUMN `deb_summe` FLOAT NULL DEFAULT 0.00 COMMENT 'Zeilen Betrag'  AFTER `deb_detail` ;
        public $deb_detail; //handelt es sich um eine gruppierte Zeile oder eine Detailzeile
        public $deb_summe;
        public $deb_discount;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>