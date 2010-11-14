<?php
/**
 * Auto generated by prado-cli.php on 2008-09-21 09:51:48.
 */
class AufgabenRecord extends TActiveRecord {
    const TABLE='tm_aufgaben';

    public $idtm_aufgaben;
    public $auf_tabelle;
    public $auf_id;
    public $idtm_organisation;
    public $auf_cdate;
    public $auf_beschreibung;
    public $auf_tdate;
    public $auf_priority;
    public $auf_name;
    public $auf_done;
    public $auf_dauer;
    public $auf_ddate;
    public $auf_idtm_organisation;
    public $idta_aufgaben_type;
    public $auf_tag;
    public $auf_zeichen_eigen;
    public $auf_zeichen_fremd;
    public $auf_cby; //(INT)
    public $auf_deleted; //(BOOL)


    public static function finder($className=__CLASS__) {
        return parent::finder($className);
    }

    public static $RELATIONS=array
            (
            'aufgabeorganisation' => array(self::BELONGS_TO, 'OrganisationRecord'),
    );
}
?>