<?php
/**
 * Auto generated by prado-cli.php on 2008-09-15 10:04:49.
 */
class StrukturRecord extends TActiveRecord
{
	const TABLE='tm_struktur';

	public $idtm_struktur;
	public $struktur_name;
	public $parent_idtm_struktur;
	public $idta_struktur_type;
        public $idtm_stammdaten;
        public $struktur_lft;
	public $struktur_rgt;
        public $idta_stammdatensicht;

	public static $RELATIONS=array
    (
        'strtype' => array(self::BELONGS_TO, 'StrukturTypeRecord'),
    );
	
    public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>