<?php
	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);

   require("../../codebase/treegrid_connector.php");
   $tree = new TreeGridConnector($res);
   $tree->enable_log("temp.log",true);
   $tree->render_sql("SELECT * from tasks WHERE complete>49","taskId","taskName,duration,complete","","parentId");
?>