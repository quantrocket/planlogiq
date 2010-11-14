<?php
	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);

	require("../../codebase/grid_connector.php");
	$grid = new GridConnector($res);
	$grid->enable_log("temp.log",true);
	
	$grid->enable_live_update('actions_table');
	$grid->render_table("grid50","item_id","item_nm,item_cd");
?>