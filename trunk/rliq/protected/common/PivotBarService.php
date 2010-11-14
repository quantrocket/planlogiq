<?php
/**
 * GraphService.php
 *
 * Copyright 2008 by Andreas Bulling
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
include_once('protected/lib/jpgraph/jpgraph_bar.php');
include_once('protected/lib/jpgraph/jpgraph_line.php');
 
class PivotBarService extends TService {
 
	private $type;
	private $legend = "Dimension";
	private $title = "Title";
	private $xdata = null;
	private $ydata = array();
	private $ytitle = null;
	private $width = 500;
	private $height = 400;
	private $shadow;
	private $numberpivots = 0;
	private $numberdimensions = 0;
	private $numberchildren = 0;
	private $colorarray = array('blue','orange','gray','khaki','darkgray','darkgreen','orange','yellow','green','orange','khaki','brown','red','brown','yellow','orange','khaki','brown','red','brown','green','orange','khaki','brown','red','brown');

 
	public function init($config) {
		$request = Prado::getApplication()->getRequest();
 
		if ($request->contains('pivotbar')) {
			$this->type = TPropertyValue::ensureString($request['pivotbar']);
		} else {
			throw new TConfigurationException('You must specify the type of the graph');
		}

                if ($request->contains('numberpivots')) {
			$this->numberpivots = TPropertyValue::ensureInteger($request['numberpivots']);
		} else {
			throw new TConfigurationException('You must calculate the number of base elements');
		}

                if ($request->contains('numberdimensions')) {
			$this->numberdimensions = TPropertyValue::ensureInteger($request['numberdimensions']);
		} else {
			throw new TConfigurationException('You must calculate the number of dim elements');
		}

                if ($request->contains('numberchildren')) {
			$this->numberchildren = TPropertyValue::ensureInteger($request['numberchildren']);
		} else {
			throw new TConfigurationException('You must calculate the number of children');
		}

                if ($request->contains('width')) {
                        $temp = explode( ',', TPropertyValue::ensureInteger($request['width']));
			$this->width = $temp[0];
		}
                if ($request->contains('height')) {
			$temp = explode( ',', TPropertyValue::ensureInteger($request['height']));
			$this->height = $temp[0];
		}

                if ($request->contains('title')) {
			$temp = explode( ',', TPropertyValue::ensureString($request['title']));
			$this->title = $temp[0];
		}
                if ($request->contains('legend')) {
			$temp = explode( ',', TPropertyValue::ensureString($request['legend']));
			$this->legend = $temp[0];
		}

 
		if ($request->contains('xdata')) {
			$this->xdata = explode( ',', TPropertyValue::ensureString($request['xdata']));
		} else {
			throw new TConfigurationException('You must specify the x data for the graph');
		}
 
		for($ii=1;$ii<=$this->numberdimensions;$ii++){
                    $variable = 'ydata'.$ii;
                    if ($request->contains($variable)) {
						$this->ydata[$ii] = explode( ',', TPropertyValue::ensureString($request[$variable]));
                    } else {
						throw new TConfigurationException('You must specify the y data for the graph');
                    }
                }
		
		if ($request->contains('ytitle')) {
			$this->ytitle = TPropertyValue::ensureString($request['ytitle']);
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
		
                $graph = $this->createBarGraph();
		
		// Send image to browser
		header("Content-type: image/png");
		imagepng($graph->Stroke());
		imagedestroy($graph);
	}

    private function createBarGraph() {
        // Create the graph.
        $graph = new Graph($this->width, $this->height, "auto");
        $graph->SetScale("textlin");
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->SetFrame(false);

        // ... and add it to the graph
        //$graph->xaxis->title->Set(Prado::localize('title'));
		//$graph->xaxis->SetTickLabels($this->xdata);
		//$graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

		// First make the labels look right
		$graph->yaxis->SetLabelFormat('%d');
		$graph->yaxis->SetLabelSide(SIDE_LEFT);
		$graph->yaxis->SetLabelMargin(5);
		$graph->yaxis->scale->SetGrace(0.1);
		$graph->yaxis->HideZeroLabel();
		$graph->ygrid->SetFill(true,'#f2f2f2@0.5','#cacaca@0.5');

		if( $this->shadow )
			$graph->SetShadow();        
	   
		// Create the bar plot                
		$tmpArray=array();
		if($this->numberdimensions>1){
            for($ii=1;$ii<=$this->numberpivots;$ii++){
                for($jj=0;$jj<$this->numberchildren;$jj++){
                    ${'tmpArray'.$ii.$jj}=array();
                    /*${'bplot'.$jj} = new BarPlot($this->ydata[$ii][$jj]);
                    ${'bplot'.$jj}->SetFillColor($this->colorarray[$jj]);
                    ${'bplot'.$jj}->value->Show();
                    ${'bplot'.$jj}->value->SetFormat('%d');
                    ${'bplot'.$jj}->value->SetColor("black");
                    ${'bplot'.$jj}->SetValuePos('top');
                    array_push(${'tmpArray'.$ii},${'bplot'.$jj});*/
                    array_push(${'tmpArray'.$ii.$jj},$this->ydata[$ii][$jj]);
                
                    ${'bplot'.$ii.$jj} = new BarPlot(${'tmpArray'.$ii.$jj});
                    ${'bplot'.$ii.$jj}->SetFillColor($this->colorarray[$jj]);
                    ${'bplot'.$ii.$jj}->value->Show();
                    ${'bplot'.$ii.$jj}->value->SetFormat('%d');
                    ${'bplot'.$ii.$jj}->value->SetColor("black");
                    ${'bplot'.$ii.$jj}->SetValuePos('top');
                    //array_push($tmpArray,${'tmpArray'.$ii});
                    array_push($tmpArray,${'bplot'.$ii.$jj});
                }                
            }
            $gbplot = new GroupBarPlot($tmpArray);
            $gbplot->setWidth(0.9);
            $gbplot->SetLegend(Prado::localize($this->legend));
            $graph->Add($gbplot);
        }else{
            $tmpArray=array();
            for($ii=0;$ii<$this->numberpivots;$ii++){
                array_push($tmpArray,$this->ydata[1][$ii]);
            }
            $bplot = new BarPlot($tmpArray);
            $bplot->value->Show();
            $bplot->value->SetFormat('%d');
            $bplot->value->SetColor("black");
            $bplot->SetValuePos('center');
            $bplot->SetWidth(0.8);
            $bplot->SetLegend(Prado::localize($this->legend));
            $graph->Add($bplot);
        }        
		
		return $graph;
	}
}
 
?>