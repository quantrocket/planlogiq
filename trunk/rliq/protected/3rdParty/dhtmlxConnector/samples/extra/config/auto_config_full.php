<?php
	require_once("../../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);

	require("../../../codebase/grid_connector.php");
	$grid = new GridConnector($res);
	$grid->set_config(new GridConfiguration(true));
	$grid->render_table("grid50");
?>