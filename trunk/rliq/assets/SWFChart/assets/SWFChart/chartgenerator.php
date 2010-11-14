<?php
session_start();
$xml = $_SESSION['SWFChart_'.$_REQUEST['chart_id']];
unset($_SESSION['SWFChart_'.$_REQUEST['chart_id']]);
echo $xml;

?>