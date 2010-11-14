<?php
/**
 * PFGoogleChart class file.
 *
 * @author Philipp Frenzel <pf@com-x-cha.com>
 * @link http://www.com-x-cha.com/
 * @copyright Copyright &copy; 2008 Philipp Frenzel
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @license http://www.maani.us/charts/index.php?menu=License
 * @version 0.2.0
 */

/**
 * PFGoogleChart class
 *
 * PFGoogleChart displays a Image chart on a Web page.
 *
 * Feedback is greatly appreciated.
 *
 */

class PFGoogleChart extends TWebControl
{	
    
	/** 
     * @var $chart_type -> List of possible Chart Types
     **/
	
	private $_chart_type = 'lc';
	private $_chart_type_map = 'world';
	private $_chart_type_country = 'DE';
	
	/** 
     * @var $chart_size -> Chartdimension WIDTH 'X' HEIGHT
     * For maps, the maximum size is 440 pixels wide by 220 pixels high.
     * 
     **/
	
	private $_chart_size = '400x200';
	
	/** 
     * @var $chart_label -> array with Labels
     **/
	
	private $_chart_label = array('Lorem Ipusum',);
	
	/** 
     * @var $chart_data -> array with data
     **/
	
	private $_chart_data = array('1','2');
	private $_chart_data_encoding = 't';
	
	private $_chart_color = "edf0d4";
	
	public function onInit($param){

		parent::onInit($param);
		
	}
	
	public function setChart_Size($size){
		$this->_chart_size = $size;
	}
	
	public function getChart_Size($size){
		return $this->_chart_size;
	}
	
	public function setChart_Label($value){
		$this->_chart_label = $value;
	}
	
	public function getChart_Label($value){
		return $this->_chart_label;
	}
	
	public function setMapRegion($region){
		$this->_chart_type_map = $region;
	}
	
	public function setMapCountry($country){
		$this->_chart_type_country = $country;
	}
	
	public function addMapCountry($country){
		$this->_chart_type_country .= $country;
	}
	
	public function setChart_Data_Encoding($type){
		
		switch ($type){
			case "Simple":
				$this->_chart_data_encoding = "s";
				break;
			case "Extended":
				$this->_chart_data_encoding = "e";
				break;
			default:
				$this->_chart_data_encoding = "t";
		}
	}
	
	public function setChart_Type($type){
		
		switch ($type){
			case "LineChart":
				$this->_chart_type = "lc";
				break;
			case "ExtendedLineChart":
				$this->_chart_type = "lxy";
				break;
			case "Sparklines":
				$this->_chart_type = "ls";
				break;
			case "BarChartHor":
				$this->_chart_type = "bhs";
				break;
			case "BarChartVer":
				$this->_chart_type = "bvs";
				break;
			case "BarChartHorGrp":
				$this->chart_type = "bhg";
				break;
			case "BarChartVerGrp":
				$this->_chart_type = "bvg";
				break;
			case "PieChart":
				$this->_chart_type = "p";
				break;
			case "Map":
				$this->_chart_type = "t";
				$this->_chart_type_map = "world";
				break;
			case "Scatter Plots":
				$this->_chart_type = "s";
				break;
			default:
				$this->_chart_type = "lc";
		}
	}
	
	private function array2string($array){
		
		if(is_array($array)){
			$result = '';
			$result = implode(',',$array);	
			return $result;
		}else{
			return $array;
		}
		
	}
	
	public function renderBeginTag($writer){
		
		parent::renderBeginTag($writer);
		
		$writer->renderBeginTag('p');
		
		$paf = "<img src=\"http://chart.apis.google.com/chart?";
		if($this->_chart_type=="t"){
			$paf .= "cht=".$this->_chart_type;
			$paf .= "&chtm=".$this->_chart_type_map;
			$paf .= "&chld=".$this->_chart_type_country;	
		}else{
			$paf .= "cht=".$this->_chart_type;
		}
		$paf .= "&chs=".$this->_chart_size;
		$paf .= "&chl=".$this->array2string($this->_chart_label);
		$paf .= "&chd=".$this->_chart_data_encoding.":".$this->array2string($this->_chart_data);
		$paf .= "&chco=".$this->_chart_color;
		$paf .= "\" alt=\"Green Source Trade\" />";
		
		$writer->write($paf);
	}
	
	public function renderEndTag($writer) {		

		$writer->renderEndTag('p');
		$writer->write("\n");

		parent::renderEndTag($writer);
		$writer->write("\n");
	}

	public function renderContents($writer){
		
		parent::renderContents($writer) ;
	}
}

?>