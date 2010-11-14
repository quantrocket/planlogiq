<?php
	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);

	require("../../codebase/grid_connector.php");
	
	
	$grid = new GridConnector($res);
	$grid->enable_log("temp.log",true);
	function grid_header(){
		if (!isset($_GET["posStart"]))
			echo '<head>
	        <column width="50" type="ed" align="right" color="white" sort="na">Sales</column>
	        <column width="150" type="ed" align="left" color="#d5f1ff" sort="na">Book Title</column>
	        <column width="100" type="ed" align="left" color="#d5f1ff" sort="na">Author</column>
		    </head>';
	}
	$grid->event->attach("beforeOutput","grid_header");
	$grid->dynamic_loading(100);
	$grid->render_table("grid50000","item_id","item_nm,item_cd");
?>