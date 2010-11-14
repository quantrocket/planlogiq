<?php

require_once('../../../codebase/grid_connector.php');
require_once('../../config.php');


$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
mysql_select_db($mysql_db);

$grid = new GridConnector($res);
$grid->enable_log("sql.log");
$grid->configure("grid50000","item_id","item_nm,item_cd");

$sql = $grid->sql;

echo $id = $grid->insert(array(
	"item_nm" => "1",
	"item_cd" => Null
));

echo "<hr>";

$sql->query("SELECT * FROM grid50000 WHERE item_id=".$id);
print_r($sql->get_next());

echo "<hr>";

echo $grid->update(array(
	"item_id" => $id,
	"item_nm" => "2",
));

echo "<hr>";

$sql->query("SELECT * FROM grid50000 WHERE item_id=".$id);
print_r($sql->get_next());

echo "<hr>";

echo $grid->delete($id);

echo "<hr>";

$sql->query("SELECT * FROM grid50000 WHERE item_id=".$id);
print_r($sql->get_next() == Null);











?>