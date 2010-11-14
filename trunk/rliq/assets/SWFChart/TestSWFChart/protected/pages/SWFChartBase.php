<?php

class SWFChartBase extends TPage
{
	public function onInit($param)
	{
		parent::onInit($param);
		$session = $this->getSession();
		if (!$session->getIsStarted()) {
			$session->open();
		}
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		$this->Chart1->setChartData(array(
			array(""				, "JAN"	, "FEB"	, "MAR"	, "APR"	, "MAY"	, "JUN"),
			array("Loans by month"	, 60	, 90	, 40	, 90	, 50	, 40)));
		$this->Chart2->setChartData(array(
			array(""		, "S"	, "M"	, "T"	, "W"	, "T"	, "F"	, "S"),
			array("lib 1"	, 22	, 15	, 11	, 15	, 20	, 22	, 21),
			array("lib 2"	, 15	, 20	, 15	, 17	, 25	, 12	, 11),
			array("lib 3"	, 30	, 32	, 35	, 20	, 30	, 30	, 36)));
		$this->Chart3->setChartData(array(
			array(""			, "JAN"	, "FEB"	, "MAR"	, "APR"	, "MAY"	, "JUN"),
			array("library 1"	, 60	, 90	, 40	, 90	, 50	, 40),
			array("library 2"	, 85	, 70	, 80	, 40	, 90	, 95)));
		$this->Chart4->setChartData(array(
			array(""			, "2003"	, "2004"	, "2005"	, "2006"),
			array("library 1"	, 20		, 40		, 15		, 50),
			array("library 2"	, 32		, 11		, 25		, 10)));
		$this->Chart5->setChartData(array(
			array ("","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31" ),
			array("Milan",10,12,11,15,20,22,21,25,31,32,28,29,40,41,45,50,65,45,50,51,65,60,62,65,45,55,59,52,53,40,45 ),
			array("Rome",30,32,35,40,42,35,36,31,35,36,40,42,40,38,40,40,38,36,30,29,28,25,28,29,30,40,32,33,34,30,35 )));
		$this->Chart6->setChartData(array(
			array("", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y", "x", "y" ),
			array ("library 1", 11, 9, 12, 8, 13, 9, 14, 8, 15.5, 8, 19, 7.5, 17, 8, 18, 8, 20, 7, 11.5, 8.5, 12.5, 9, 13.5, 9.5, 14.5, 9, 15, 8.5, 15.5, 9, 15.5, 9, 16, 8.5, 21, 6.5),
			array ("library 2", 11, 8, 12, 7.25, 13, 7, 14, 7, 15, 7, 16.5, 5, 17, 5, 18, 4, 20, 3, 11.5, 7, 12.5, 7.5, 13.5, 6.5, 14.5, 6.5, 15, 5.5, 15.5, 5.25, 15.5, 6.5, 16, 6.15, 16, 4.5)));
		$this->Chart7->setChartData(array(
			array(""				, "novels"		, "s. f."	, "tech reports"		, "comics"	, "manuscripts"),
			array("internal loans"	, 60			, 90		, 40				, 90		, 50),
			array("external loans"	, 85			, 70		, 80				, 40		, 90)));
		$this->dataBind();
	}
}

?>