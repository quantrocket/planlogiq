<?php

	require_once("../config.php");
	$res=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	mysql_select_db($mysql_db);
	
	require("../../codebase/treegridmultitable_connector.php");

	$treegrid = new TreeGridMultitableConnector($res);
	$treegrid->setMaxLevel(3);
	$level = $treegrid->get_level();

	switch ($level) {
		case 0:
			$treegrid->render_table("projects2", "project_id", "project_name, project_dsc", "", "");
			break;
		case 1:
			$treegrid->render_sql("SELECT teams2.team_id, teams2.team_name, project_team2.project_id FROM teams2 INNER JOIN project_team2 ON teams2.team_id=project_team2.team_id", "team_id", "team_name", "", "project_id");
			break;
		case 2:
			$treegrid->render_table("developers2", "developer_id", "developer_name,developer_email", "", "developer_team");
			break;
		case 3:
			$treegrid->render_table("phones2", "phone_id", "phone,phone_type", "", "phone_developer");
			break;
	}

?>