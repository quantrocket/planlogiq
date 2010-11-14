<?php

class SWFChartAdvanced extends TPage
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
		$this->makeChart1();
		$this->makeChart2();
		$this->makeChart3();
		$this->dataBind();
	}

	public function makeChart1() {
		$this->Chart1->setAxisCategory(array(
			'font'=>"arial",'bold'=>true,'size'=>10,'color'=>"000000",'alpha'=>50));
		$this->Chart1->setAxisTicks(array(
			'value_ticks'=>false,'category_ticks'=>true,'major_thickness'=>2,
			'minor_thickness'=>1,'minor_count'=>3,'major_color'=>"000000",
			'minor_color'=>"888888",'position'=>"outside"));
		$this->Chart1->setAxisValue(array(
			'font'=>"arial",'bold'=>true,'size'=>10,'color'=>"000000",'alpha'=>50,
			'steps'=>4,'prefix'=>"",'suffix'=>"",'decimals'=>0,'separator'=>"",
			'show_min'=>true));
		$this->Chart1->setChartBorder(array(
			'color'=>"000000",'top_thickness'=>1,'bottom_thickness'=>2,
			'left_thickness'=>0,'right_thickness'=>0));
		$this->Chart1->setChartData(array(
			array("", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ),
			array("projected sales", 20, 30, 35, 40, 50, 50, 40, 35, 50, 70, 80, 85 ),
			array("actual sales", 15, 25, 30, 45, 60, 65, 50, 60, 55, 65, 70, 75)));
		$this->Chart1->setChartRect(array(
			'x'=>75,'y'=>75,'width'=>300,'height'=>125,'positive_color'=>"FFFFFF",
			'positive_alpha'=>40));
		$this->Chart1->setChartType("area,column");
		$this->Chart1->setChartValue(array(
			'color'=>"000000",'alpha'=>50,'size'=>9,'position'=>"middle",
			'prefix'=>"",'suffix'=>"",'decimals'=>0,'separator'=>"",'as_percentage'=>false));
		$this->Chart1->setChartDraw(array(
			array('type'=>"line",'x1'=>150,'y1'=>260,'x2'=>450,'y2'=>260,
				'line_color'=>"ffff00",'line_alpha'=>25,'line_thickness'=>55),
			array('type'=>"text",'color'=>"4466ff",'alpha'=>75,'font'=>"arial",
				'rotation'=>0,'bold'=>true,'size'=>70,'x'=>0,'y'=>0,'width'=>380,
				'height'=>298,'text'=>"2005",'h_align'=>"right",'v_align'=>"bottom")));
		$this->Chart1->setLegendLabel(array(
			'layout'=>"horizontal",'font'=>"arial",'bold'=>true,'size'=>12,
			'color'=>"FFFFFF",'alpha'=>80));
		$this->Chart1->setLegendRect(array(
			'x'=>75,'y'=>50,'width'=>300,'height'=>20,'margin'=>5,'fill_color'=>"000000",
			'fill_alpha'=>0,'line_color'=>"000000",'line_alpha'=>0,'line_thickness'=>0));
		$this->Chart1->setSeriesColor(array("FF8844","7e6cee"));
		$this->Chart1->setSeriesGap(array('bar_gap'=>0,'set_gap'=>35));
	}

	public function makeChart2() {
		$this->Chart2->setAxisCategory(array ( 'size'=>10, 'color'=>"000000", 'alpha'=>50 ));
		$this->Chart2->setAxisTicks(array ( 'value_ticks'=>false, 'category_ticks'=>false ));
		$this->Chart2->setAxisValue(array ( 'alpha'=>0 ));
		$this->Chart2->setChartBorder(array ( 'bottom_thickness'=>0, 'left_thickness'=>0 ));
		$this->Chart2->setChartData(array ( array ( "", "JAN", "FEB", "MAR", "APR", "MAY", "JUN" ), array ( "product 1", 60,90,40,90,50,40 ), array ("product 2", 85,70,80,40,90,95 ) ));
		$this->Chart2->setChartGridH(array ( 'alpha'=>0 ));
		$this->Chart2->setChartGridV(array ( 'alpha'=>0 ));
		$this->Chart2->setChartPref(array ( 'rotation_x'=>45 ));
		$this->Chart2->setChartRect(array ( 'x'=>60, 'y'=>-40, 'width'=>350, 'height'=>250, 'positive_alpha'=>0 ));
		$this->Chart2->setChartTransition(array('type'=>"zoom", 'delay'=>.1, 'duration'=>.5, 'order'=>"series"));
		$this->Chart2->setChartType("3d column");
		$this->Chart2->setChartValue(array ( 'position'=>"cursor", 'size'=>10, 'color'=>"ffffff", 'alpha'=>90, 'background_color'=>"444444" ));
		$this->Chart2->setLegendLabel(array ( 'layout'=>"vertical", 'bullet'=>"square", 'size'=>11, 'color'=>"ffffff", 'alpha'=>85 ));
		$this->Chart2->setLegendRect(array ( 'x'=>20, 'y'=>75, 'width'=>20, 'height'=>20, 'fill_alpha'=>0 ));
		$this->Chart2->setSeriesColor(array ( "cc9944", "556688" ));
	}

	public function makeChart3() {
		$this->Chart3->setChartData(array(
			array ( "", "US","UK","India", "Japan","China" ),
			array ( "", 50,70,55,60,30 )));
		$this->Chart3->setChartPref(array ( 'rotation_x'=>60 ));
		$this->Chart3->setChartRect(array ( 'x'=>100, 'y'=>150, 'width'=>130, 'height'=>130, 'positive_alpha'=>0 ));
		$this->Chart3->setChartTransition(array ( 'type'=>"dissolve", 'delay'=>.1, 'duration'=>.3, 'order'=>"category" ));
		$this->Chart3->setChartType("3d pie");
		$this->Chart3->setChartValue(array('as_percentage'=>true,'size'=>9,'color'=>"ffffff",'alpha'=>85));
		$this->Chart3->setLegendLabel(array(
			'layout'=>"vertical",'bullet'=>"circle",'size'=>11,'color'=>"ffffff",'alpha'=>85));
		$this->Chart3->setLegendRect(array ( 'x'=>20, 'y'=>150, 'width'=>20, 'height'=>40, 'fill_alpha'=>0 ));
		$this->Chart3->setSeriesColor(array ( "cc6600", "aaaa22", "8800dd", "666666", "4488aa" ));
		$this->Chart3->setSeriesExplode(array(0,50));
	}
}

?>