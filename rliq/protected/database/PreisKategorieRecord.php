<?php
/**
 * Auto generated by prado-cli.php on 2008-05-25 12:18:44.
 */
class PreisKategorieRecord extends TActiveRecord
{
	const TABLE='tm_preis_kategorie';

	public $idtm_preis_kategorie;
	public $preis_kategorie_name;
	public $preis_kategorie_beschreibung;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>