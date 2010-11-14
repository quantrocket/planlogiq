<?php
	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);

	require("../../codebase/grid_connector.php");
	$grid = new GridConnector($res);
	$grid->enable_log("temp.log",true);
	$grid->dynamic_loading(100);
	$grid->render_sql("SELECT grid50000.item_id as ID , grid50000.item_nm FROM grid50000","item_id(ID)","grid50000.item_id(ID),item_nm");
?>