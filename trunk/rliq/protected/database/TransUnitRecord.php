<?php
/**
 * Auto generated by prado-cli.php on 2009-04-02 04:40:05.
 */
class TransUnitRecord extends TActiveRecord
{
	const TABLE='trans_unit';

	public $msg_id;
	public $cat_id;
	public $id;
	public $source;
	public $target;
	public $comments;
	public $date_added;
	public $date_modified;
	public $author;
	public $translated;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>