<?php
	require_once("../../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);
	

	require("../../../codebase/grid_connector.php");
	require("../../../codebase/convert.php");
	
	$convert = new ConvertService("http://192.168.1.16/dhtmlxdev/ExportTools/grid2pdf/generate.php");
	
	$grid = new GridConnector($res);
	$grid->set_config(new GridConfiguration());
	$grid->render_table("grid50");
?>