<?php

//taken from prado quickstart manual, modified by Philippe

class CommentRecord extends TActiveRecord
{
	const TABLE='qs_comments';

	public $idqs_comments;
	public $idtm_organisation;
	public $com_cdate;
	public $com_page;
	public $com_id;
	public $com_content;
        public $com_modul;
        public $idta_variante;
        public $idta_periode;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
}