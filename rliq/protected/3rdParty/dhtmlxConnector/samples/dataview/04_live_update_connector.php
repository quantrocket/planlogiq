<?php
	require_once("../config.php");
	require_once("../../codebase/dataview_connector.php");

	$conn = mysql_connect($mysql_host,$mysql_user,$mysql_pasw);
	mysql_select_db($mysql_db);

	$data = new DataViewConnector($conn);
	$data->enable_live_update('actions_table');
	$data->render_table("tasks","taskId","taskName,duration,start");
?>