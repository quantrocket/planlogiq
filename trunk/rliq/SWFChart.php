<?php
/**
 * SWFChart class file.
 *
 * @author Ciro Mattia Gonano <ciro@winged.it>
 * @link http://www.winged.it/
 * @copyright Copyright &copy; 2006 Ciro Mattia Gonano
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @license http://www.maani.us/charts/index.php?menu=License
 * @version 0.2.0
 */

/**
 * SWFChart class
 *
 * SWFChart displays a Flash chart on a Web page.
 *
 * Use {@link setData Data} property to set the chart data.
 *
 * NOTE FOR EXTERNAL CODE / LICENSING
 * The code for InsertChart() and SendChartData() functions, as well as all
 * Flash libraries needed to draw the charts are taken directly from
 * <strong>PHP/SWF Charts 4.5</strong> (http://www.maani.us/charts) under the
 * following license terms (http://www.maani.us/charts/index.php?menu=License):
 *
 * [cite]
 * PHP/SWF Charts is free to download and use. The free, unregistered version
 * contains all the features except for:
 * 		* Clicking a chart takes the user to the PHP/SWF Charts web site
 *		* No displaying charts inside another flash file
 *		* No technical support, and no product updates by e-mail
 * [/cite]
 *
 * Most functions described in the reference page
 * (http://www.maani.us/charts/index.php?menu=Reference) have been
 * implemented through setters, the params are the same (mostly arrays),
 * documentation lacks but it's the same as the reference page.
 * The only lacking feature (other than the license-requiring ones) is the,
 * composite charts, so if you want one of them you have to manually configure
 * a support page.
 *
 * Feedback is greatly appreciated.
 *
 * @author Ciro Mattia Gonano <ciro@winged.it>
 * @author PHP/SWF Charts <info@maani.us>
 * @version 0.2.0
 */
class SWFChart extends TWebControl
{	
	private function prepareChartArray()
	{
		if (count($v = $this->getAxisCategory())>0)
			$chart['axis_category'] = $v;
		if (count($v = $this->getAxisTicks())>0)
			$chart['axis_ticks'] = $v;
		if (count($v = $this->getAxisValue())>0)
			$chart['axis_value'] = $v;
		if (count($v = $this->getAxisValueText())>0)
			$chart['axis_value_text'] = $v;

		if (count($v = $this->getChartBorder())>0)
			$chart['chart_border'] = $v;
		$chart['chart_data'] = $this->getChartData();
		if (count($v = $this->getChartGridH())>0)
			$chart['chart_grid_h'] = $v;
		if (count($v = $this->getChartGridV())>0)
			$chart['chart_grid_v'] = $v;
		if (count($v = $this->getChartPref())>0)
			$chart['chart_pref'] = $v;
		if (count($v = $this->getChartRect())>0)
			$chart['chart_rect'] = $v;
		if (count($v = $this->getChartTransition())>0)
			$chart['chart_transition'] = $v;
		$chart['chart_type'] = (count($type = explode(',',$this->getChartType()))>1)?$type:$type[0];
		if (count($v = $this->getChartValue())>0)
			$chart['chart_value'] = $v;
		if (count($v = $this->getChartValueText())>0)
			$chart['chart_value_text'] = $v;

		if (count($v = $this->getChartDraw())>0)
			$chart['draw'] = $v;

		if (count($v = $this->getLegendLabel())>0)
			$chart['legend_label'] = $v;
		if (count($v = $this->getLegendRect())>0)
			$chart['legend_rect'] = $v;
		if (count($v = $this->getLegendTransition())>0)
			$chart['legend_transition'] = $v;

		if (count($v = $this->getSeriesColor())>0)
			$chart['series_color'] = $v;
		if (count($v = $this->getSeriesExplode())>0)
			$chart['series_explode'] = $v;
		if (count($v = $this->getSeriesGap())>0)
			$chart['series_gap'] = $v;
		$chart['series_switch'] = $this->getSeriesSwitch();

		/* RETURN */
		return $chart;
	}

	private function genChartXML()
	{
		$chart = $this->prepareChartArray();
//		Prado::fatalError(Prado::varDump($chart));
		$xml="<chart>\r\n";
		$Keys1= array_keys((array) $chart);
		for ($i1=0;$i1<count($Keys1);$i1++) {
			if (is_array($chart[$Keys1[$i1]])) {
				$Keys2 = array_keys($chart[$Keys1[$i1]]);
				if (is_array($chart[$Keys1[$i1]][$Keys2[0]])) {
					$xml.="\t<".$Keys1[$i1].">\r\n";
					for($i2=0;$i2<count($Keys2);$i2++){
						$Keys3=array_keys((array) $chart[$Keys1[$i1]][$Keys2[$i2]]);
						switch ($Keys1[$i1]) {
							case "chart_data":
								$xml.="\t\t<row>\r\n";
								for($i3=0;$i3<count($Keys3);$i3++){
									switch(true){
										case ($chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]===null):
											$xml.="\t\t\t<null/>\r\n";
											break;
										case ($Keys2[$i2]>0 and $Keys3[$i3]>0):
											$xml.="\t\t\t<number>".
												$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]].
												"</number>\r\n";
											break;
										default:
											$xml.="\t\t\t<string>".
												$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]].
												"</string>\r\n";
											break;
									}
								}
								$xml.="\t\t</row>\r\n";
								break;
							case "chart_value_text":
								$xml.="\t\t<row>\r\n";
								$count=0;
								for($i3=0;$i3<count($Keys3);$i3++){
									if ($chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]===null) {
										$xml.="\t\t\t<null/>\r\n";
									} else {
										$xml.="\t\t\t<string>".
										$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."</string>\r\n";
									}
								}
								$xml.="\t\t</row>\r\n";
								break;
							case "draw":
								$text="";
								$xml.="\t\t<".$chart[$Keys1[$i1]][$Keys2[$i2]]['type'];
								for ($i3=0;$i3<count($Keys3);$i3++) {
									if ($Keys3[$i3]!="type") {
										if ($Keys3[$i3]=="text") {
											$text=$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]];
										} else {
											$xml.=" ".$Keys3[$i3]."=\"".
												$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."\"";
										}
									}
								}
								if ($text!="") {
									$xml.=">".$text."</text>\r\n";
								} else {
									$xml.=" />\r\n";
								}
								break;
							default://link, etc.
								$xml.="\t\t<value";
								for ($i3=0; $i3<count($Keys3); $i3++) {
									$xml .= " ".$Keys3[$i3]."=\"".
										$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."\"";
								}
								$xml.=" />\r\n";
								break;
						}
					}
					$xml.="\t</".$Keys1[$i1].">\r\n";
				} else {
					if ($Keys1[$i1]=="chart_type" or
							$Keys1[$i1]=="series_color" or
							$Keys1[$i1]=="series_image" or
							$Keys1[$i1]=="series_explode" or
							$Keys1[$i1]=="axis_value_text") {
						$xml.="\t<".$Keys1[$i1].">\r\n";
						for ($i2=0; $i2<count($Keys2); $i2++) {
							if ($chart[$Keys1[$i1]][$Keys2[$i2]]===null) {
								$xml .= "\t\t<null/>\r\n";
							} else {
								$xml .= "\t\t<value>".
									$chart[$Keys1[$i1]][$Keys2[$i2]]."</value>\r\n";
							}
						}
						$xml.="\t</".$Keys1[$i1].">\r\n";
					} else {	//axis_category, etc.
						$xml .= "\t<".$Keys1[$i1];
						for ($i2=0; $i2<count($Keys2); $i2++) {
							$xml .= " ".$Keys2[$i2]."=\"".$chart[$Keys1[$i1]][$Keys2[$i2]]."\"";
						}
						$xml.=" />\r\n";
					}
				}
			} else {	//chart type, etc.
				$xml.="\t<".$Keys1[$i1].">".$chart[$Keys1[$i1]]."</".$Keys1[$i1].">\r\n";
			}
		}
		$xml .= "</chart>\r\n";
		$md5 = md5($xml);
		$this->Session["SWFChart_$md5"] = $xml;
		return $md5;
	}

	/**
	 * @return string tag name of the chart.
	 */
	protected function getTagName()
	{
		return 'object';
	}

	/**
	 * Adds attributes to renderer.
	 *
	 * @param THtmlWriter the renderer
	 */
	protected function addAttributesToRender($writer)
	{
		$writer->addAttribute('classid','clsid:D27CDB6E-AE6D-11cf-96B8-444553540000');
		$writer->addAttribute('codebase',
			'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0');
		$writer->addAttribute('height',$this->getHeight());
		$writer->addAttribute('width',$this->getWidth());
	}

	/**
	 * Renders the body content of the chart.
	 *
	 * @param THtmlWriter the renderer
	 */
	public function renderContents($writer)
	{
		// Hier mache ich ein Postback pruefung, damit das chart auch beim sprung das gleiche bleibt
			if (count($this->getChartData())<1) {
			parent::renderContents($writer);
		} else {
			$assetPath = $this->publishAsset('assets/SWFChart');
			$src = "{$assetPath}/charts.swf?library_path={$assetPath}";
			$src .= "&xml_source={$assetPath}/chartgenerator.php?chart_id=".$this->genChartXML();
			$writer->addAttribute('name','movie');
			$writer->addAttribute('value',$src);
			$writer->renderBeginTag('param');
			$writer->renderEndTag();
			$writer->addAttribute('name','quality');
			$writer->addAttribute('value','high');
			$writer->renderBeginTag('param');
			$writer->renderEndTag();
			if ($this->getBackColor()!='') {
				$writer->addAttribute('name','bgcolor');
				$writer->addAttribute('value',$this->getBackColor());
				$writer->renderBeginTag('param');
				$writer->renderEndTag();
			}
			if ($this->getTransparent()) {
				$writer->addAttribute('name','wmode');
				$writer->addAttribute('value','transparent');
				$writer->renderBeginTag('param');
				$writer->renderEndTag();
			}
			$writer->addAttribute('src',$src);
			$writer->addAttribute('quality','high');
			if ($this->getBackColor()!='')
				$writer->addAttribute('bgcolor',$this->getBackColor());
			$writer->addAttribute('width',$this->getWidth());
			$writer->addAttribute('height',$this->getHeight());
			$writer->addAttribute('name','charts');
			$writer->addAttribute('swLiveConnect','true');
			if ($this->getTransparent()) {
				$writer->addAttribute('wmode','transparent');
			}
			$writer->addAttribute('type','application/x-shockwave-flash');
			$writer->addAttribute('pluginspage','http://www.macromedia.com/go/getflashplayer');
			$writer->renderBeginTag('embed');
			$writer->renderEndTag();
		}
		
	}

	/**
	 * @return boolean true if background has to be transparent, false elsewhere (default false).
	 */
	public function getTransparent()
	{
		return $this->getViewState('Transparent',false);
	}

	/**
	 * @param boolean $value Set the transparent value.
	 */
	public function setTransparent($value)
	{
		$this->setViewState('Transparent',TPropertyValue::ensureBoolean($value),false);
	}

	public function getAxisCategory()
	{
		return $this->getViewState('AxisCategory',array());
	}

	public function setAxisCategory($value)
	{
		$this->setViewState('AxisCategory',TPropertyValue::ensureArray($value),array());
	}

	public function getAxisTicks()
	{
		return $this->getViewState('AxisTicks',array());
	}

	public function setAxisTicks($value)
	{
		$this->setViewState('AxisTicks',TPropertyValue::ensureArray($value),array());
	}

	public function getAxisValue()
	{
		return $this->getViewState('AxisValue',array());
	}

	public function setAxisValue($value)
	{
		$this->setViewState('AxisValue',TPropertyValue::ensureArray($value),array());
	}

	public function getAxisValueText()
	{
		return $this->getViewState('AxisValueText',array());
	}

	public function setAxisValueText($value)
	{
		$this->setViewState('AxisValueText',TPropertyValue::ensureArray($value),array());
	}

	public function getChartBorder()
	{
		return $this->getViewState('ChartBorder',array());
	}

	public function setChartBorder($value)
	{
		$this->setViewState('ChartBorder',TPropertyValue::ensureArray($value),array());
	}

	/**
	 * @return array The chart's data.
	 */
	public function getChartData()
	{
		return $this->getViewState('ChartData',array());
	}

	/**
	 * @param array $data The chart's data (default array()).
	 */
	public function setChartData($value)
	{
		$this->setViewState('ChartData',TPropertyValue::ensureArray($value),array());
	}

	public function getChartGridH()
	{
		return $this->getViewState('ChartGridH',array());
	}

	public function setChartGridH($value)
	{
		$this->setViewState('ChartGridH',TPropertyValue::ensureArray($value),array());
	}

	public function getChartGridV()
	{
		return $this->getViewState('ChartGridV',array());
	}

	public function setChartGridV($value)
	{
		$this->setViewState('ChartGridV',TPropertyValue::ensureArray($value),array());
	}

	public function getChartPref()
	{
		return $this->getViewState('ChartPref',array());
	}

	public function setChartPref($value)
	{
		$this->setViewState('ChartPref',TPropertyValue::ensureArray($value),array());
	}

	public function getChartRect()
	{
		return $this->getViewState('ChartRect',array());
	}

	public function setChartRect($value)
	{
		$this->setViewState('ChartRect',TPropertyValue::ensureArray($value),array());
	}

	public function getChartTransition()
	{
		return $this->getViewState('ChartTransition',array());
	}

	public function setChartTransition($value)
	{
		$this->setViewState('ChartTransition',TPropertyValue::ensureArray($value),array());
	}

	/**
	 * @return string The chart's type
	 */
	public function getChartType()
	{
		return $this->getViewState('ChartType','bar');
	}

	/**
	 * @param string $value The chart's type.
	 * 		Valid values are:
	 *			line
	 *			column (default)
	 * 			stacked column
	 * 			floating column
	 * 			3d column
	 * 			stacked 3d column
	 * 			parallel 3d column
	 * 			pie
	 * 			3d pie
	 * 			bar
	 * 			stacked bar
	 * 			floating bar
	 * 			area
	 * 			stacked area
	 * 			candlestick
	 * 			scatter
	 * 			polar
	 */
	public function setChartType($value)
	{
		$this->setViewState('ChartType',TPropertyValue::ensureString($value),'column');
	}

	public function getChartValue()
	{
		return $this->getViewState('ChartValue',array());
	}

	public function setChartValue($value)
	{
		$this->setViewState('ChartValue',TPropertyValue::ensureArray($value),array());
	}

	public function getChartValueText()
	{
		return $this->getViewState('ChartValueText',array());
	}

	public function setChartValueText($value)
	{
		$this->setViewState('ChartValueText',TPropertyValue::ensureArray($value),array());
	}

	public function getChartDraw()
	{
		return $this->getViewState('ChartDraw',array());
	}

	public function setChartDraw($value)
	{
		$this->setViewState('ChartDraw',TPropertyValue::ensureArray($value),array());
	}

	public function getLegendLabel()
	{
		return $this->getViewState('LegendLabel',array());
	}

	public function setLegendLabel($value)
	{
		$this->setViewState('LegendLabel',TPropertyValue::ensureArray($value),array());
	}

	public function getLegendRect()
	{
		return $this->getViewState('LegendRect',array());
	}

	public function setLegendRect($value)
	{
		$this->setViewState('LegendRect',TPropertyValue::ensureArray($value),array());
	}

	public function getLegendTransition()
	{
		return $this->getViewState('LegendTransition',array());
	}

	public function setLegendTransition($value)
	{
		$this->setViewState('LegendTransition',TPropertyValue::ensureArray($value),array());
	}

	public function getSeriesColor()
	{
		return $this->getViewState('SeriesColor',array());
	}

	public function setSeriesColor($value)
	{
		$this->setViewState('SeriesColor',TPropertyValue::ensureArray($value),array());
	}

	public function getSeriesExplode()
	{
		return $this->getViewState('SeriesExplode',array());
	}

	public function setSeriesExplode($value)
	{
		$this->setViewState('SeriesExplode',TPropertyValue::ensureArray($value),array());
	}

	public function getSeriesGap()
	{
		return $this->getViewState('SeriesGap',array());
	}

	public function setSeriesGap($value)
	{
		$this->setViewState('SeriesGap',TPropertyValue::ensureArray($value),array());
	}

	public function getSeriesSwitch()
	{
		return $this->getViewState('SeriesSwitch',false);
	}

	public function setSeriesSwitch($value)
	{
		$this->setViewState('SeriesSwitch',TPropertyValue::ensureBoolean($value),false);
	}
}

?>