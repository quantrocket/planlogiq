<?php
/**
 * BubbleService.php
 *
 * Copyright 2008 by Philipp Frenzel
 * All rights reserved.
 * 
 * Terms of use and modification:
 * 
 * You may modify the following source code as you receive it, in any medium,
 * provided that you conspicuously and appropriately retain the above
 * original copyright notice.
 * 
 */
 
include_once('protected/lib/jpgraph/jpgraph.php');
include_once('protected/lib/jpgraph/jpgraph_scatter.php');

class BubbleService extends TService{
 
	private $xdata = null;
	private $ydata = null;
	private $zdata = null;
	private $xlabel = 'Eintrittswahrscheinlichkeit';
	private $ylabel = 'Wahrscheinlichkeit das Unentdeckt';
	private $datalabel = null;
	private $title = null;
	private $width = 700;
	private $height = 300;
	private $shadow = true;
	public $format;
 
	public function init($config) {
		$request = Prado::getApplication()->getRequest();
 
		if ($request->contains('xlabel')) {
			$this->xlabel = explode( ',', TPropertyValue::ensureString($request['xlabel']));
		} else {
			throw new TConfigurationException('You must specify the x data for the graph');
		}
		if ($request->contains('ylabel')) {
			$this->ylabel = explode( ',', TPropertyValue::ensureString($request['ylabel']));
		} else {
			throw new TConfigurationException('You must specify the x data for the graph');
		}
		if ($request->contains('xdata')) {
			$this->xdata = explode( ',', TPropertyValue::ensureString($request['xdata']));
		} else {
			throw new TConfigurationException('You must specify the x data for the graph');
		}
		
		if ($request->contains('ydata')) {
			$this->ydata = explode( ',', TPropertyValue::ensureString($request['ydata']));
		} else {
			throw new TConfigurationException('You must specify the y data for the graph');
		}
		
		if ($request->contains('ydata')) {
			$this->zdata = explode( ',', TPropertyValue::ensureString($request['zdata']));
		} else {
			throw new TConfigurationException('You must specify the z data for the graph');
		}
		
		if ($request->contains('datalabel')) {
			$this->datalabel = explode( ',', TPropertyValue::ensureString($request['datalabel']));
		} else {
			throw new TConfigurationException('You must specify the x data for the graph');
		}
 
		if ($request->contains('title')) {
			$this->title = TPropertyValue::ensureString($request['title']);
		} else {
			throw new TConfigurationException('You must specify the y title for the graph.');
		}
	}
 
	public function getWidth() {
		return $this->width;
	}
	
	public function setWidth($value) {
		$this->width = TPropertyValue::ensureInteger($value);
	}
	
	public function getXLabel() {
		return $this->xlabel;
	}
	
	public function setXLabel($value) {
		$this->xlabel = TPropertyValue::ensureString($value);
	}
	
	public function getYLabel() {
		return $this->ylabel;
	}
	
	public function setYLabel($value) {
		$this->ylabel = TPropertyValue::ensureString($value);
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function setHeight($value) {
		$this->height = TPropertyValue::ensureInteger($value);
	}
 
	public function getShadow() {
		return $this->shadow;
	}
	
	public function setShadow($value) {
		$this->shadow = TPropertyValue::ensureBoolean($value);
	}
 
	public function run() {
		
		$graph = $this->createBubbleGraph($this->xdata, $this->ydata,$this->zdata,$this->datalabel,$this->title);
		// Send image to browser
		header("Content-type: image/png");
		imagepng($graph->Stroke());
		imagedestroy($graph);
	}
 
	private function createBubbleGraph($datax,$datay,$dataz,$datalabel,$title) {
		global $format;
		// Create the graph.
		
		// We need to create X,Y data vectors suitable for the
		// library from the above raw data.
		
		$n = count($datax);
		for( $i=0; $i < $n; ++$i ){
		    // Create a faster lookup array so we don't have to search
			// for the correct values in the callback function
			$format[strval($datax[$i])][strval($datay[$i])] = array($datalabel[$i],$dataz[$i]);
		}

		// Callback for markers
		// Must return array(width,border_color,fill_color,filename,imgscale)
		// If any of the returned values are '' then the
		// default value for that parameter will be used (possible empty)
		function FCallback($aYVal,$aXVal) {
		    global $format;
		    return array($format[strval($aXVal)][strval($aYVal)][0],'',
				 $format[strval($aXVal)][strval($aYVal)][1],'','');
		}
			
		$graph = new Graph($this->width, $this->height, "auto");
		
		// Set the background image
		$graph->SetBackgroundImage('protected/lib/jpgraph/gfx/riscmap.jpg',BGIMG_COPY);

		$graph->SetScale("linlin");
		$graph->img->SetMargin(60,60,60,60);
		$graph->title->Set($title);
		$graph->title->SetFont(FF_FONT1, FS_BOLD);
		
		if( $this->shadow )
			$graph->SetShadow();
		// Use a lot of grace to get large scales since the ballon have
		// size and we don't want them to collide with the X-axis
		$graph->yaxis->scale->SetGrace(50,10);
		$graph->yaxis->title->Set($this->ylabel[0]);
		$graph->xaxis->scale->SetGrace(50,10);
		$graph->xaxis->title->Set($this->xlabel[0]);
		
		// Make sure X-axis as at the bottom of the graph and not at the default Y=0
		$graph->xaxis->SetPos('min');

		// Set X-scale to start at 0
		$graph->xscale->SetAutoMin('min');

		// Create the scatter plot
		$sp1 = new ScatterPlot($datay,$datax);
		$sp1->mark->SetType(MARK_FILLEDCIRCLE);

		// Uncomment the following two lines to display the values
		$sp1->value->Show();
		$sp1->value->SetFont(FF_FONT1,FS_BOLD);

		// Callback for markers
		$sp1->mark->SetCallbackYX("FCallback");

		// Add the scatter plot to the graph
 
		$graph->Add($sp1);
		return $graph;
	}
	
}
 
 
?>