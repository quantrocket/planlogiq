<?php
	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);
	
	require("../../codebase/grid_connector.php");
	$grid = new GridConnector($res);
	$grid->enable_log("temp.log",true);
	$grid->dynamic_loading(100);
	function change_filter($set){
		$ind = $set->index("item_ch");
		if ($ind!==false){
			$set->rules[$ind]["value"]=($set->rules[$ind]["value"]=="checked")?1:0;
			print_r($set->rules[$ind]["value"]); die();
		}
	}

	$grid->event->attach("beforeFilter","change_filter");
	//$grid->set_options("item_nm",array("1","two","3"));
	$grid->set_options("item_ch",array("checked" => true ,"unchecked" => true));
	$grid->render_table("countries","item_id","item_nm,item_cd,item_ch");
?>