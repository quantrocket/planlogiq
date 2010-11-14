<?php
	require_once("../config.php");
	$res=pg_connect($postrgre_connection);
	
	require("../../codebase/grid_connector.php");
	require("../../codebase/db_postgre.php");
	
	$grid = new GridConnector($res,"Postgre");
	$grid->enable_log("temp.log",true);
	$grid->dynamic_loading(100);
	$grid->render_table("grid50000","item_id","item_nm,item_cd");
?>