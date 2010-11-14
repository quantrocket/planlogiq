<?php
	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);
	require("../../codebase/grid_connector.php");
	
	
	
	$grid = new GridConnector($res);
	$grid->enable_log("temp.log",true);
	$grid->dynamic_loading(100);
	
	/*$filter1 = new OptionsConnector($res);
	$filter1->render_table("countries","item_id","item_id(value),item_nm(label)");
	$grid->set_options("item_nm",$filter1);*/
	
	
	$grid->set_options("item_nm",array("1","two","3"));
	$grid->set_options("item_cd",array("91"=>"one", "75"=>"two"));
	
	
	
	$grid->sql->set_transaction_mode("record");
	$grid->render_table("grid50","item_id","item_nm,item_cd");
	
?>