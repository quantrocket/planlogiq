<?php
	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);

   require("../../codebase/tree_connector.php");
   $tree = new TreeConnector($res);
   $tree->enable_live_update('actions_table');
   $tree->enable_log("temp.log",true);
   $tree->render_table("tasks","taskId","taskName","","parentId");
?>