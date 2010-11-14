<?php

	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);
	
	require_once('../../codebase/treegridgroup_connector.php');
	
	$treegrid = new TreeGridGroupConnector($res,"MySQL");
	$treegrid->render_table("products2", "id", "product_name,scales,colour", "", "category");

?>