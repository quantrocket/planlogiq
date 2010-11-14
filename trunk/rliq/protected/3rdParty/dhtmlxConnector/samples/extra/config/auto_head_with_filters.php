<?php
	require_once("../../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);

	require("../../../codebase/grid_connector.php");
	$grid = new GridConnector($res);
	$grid->enable_log("temp.log",true);
	
	$grid->set_config(true);
	$grid->dynamic_loading(100);
	$grid->render_table("grid50000","item_id","item_nm,item_cd");
?>