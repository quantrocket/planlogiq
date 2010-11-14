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
include_once('protected/lib/jpgraph/jpgraph_pie.php');

class GraphService extends TService {

    private $type;
    private $legend = "Legend";
    private $title = "Title";
    private $xdata = null;
    private $ydata1 = null;
    private $ydata2 = null;
    private $ytitle = null;
    private $width = 500;
    private $height = 400;
    private $shadow;

    public function init($config) {
        $request = Prado::getApplication()->getRequest();

        if ($request->contains('graph')) {
            $this->type = TPropertyValue::ensureString($request['graph']);
        } else {
            throw new TConfigurationException('You must specify the type of the graph');
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
            //$temp = explode( ',', );
            $this->title = TPropertyValue::ensureString($request['title']);
        }
        if ($request->contains('legend')) {
            $this->legend = explode( ',', TPropertyValue::ensureString($request['legend']));
        }


        if ($request->contains('xdata')) {
            $this->xdata = explode( ',', TPropertyValue::ensureString($request['xdata']));
        } else {
            throw new TConfigurationException('You must specify the x data for the graph');
        }

        if ($request->contains('ydata1')) {
            $this->ydata1 = explode( ',', TPropertyValue::ensureString($request['ydata1']));
        } else {
            throw new TConfigurationException('You must specify the y data for the graph');
        }

        if ($request->contains('ydata2')) {
            $this->ydata2 = explode( ',', TPropertyValue::ensureString($request['ydata2']));
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
        switch( $this->type ) {
            case 1:
                $graph = $this->createStackedBarGraph($this->xdata, $this->ydata1, $this->ydata2, $this->ytitle);
                break;

            case 2:
                $graph = $this->createLineGraph($this->xdata, $this->ydata1, $this->ytitle);
                break;

            case 3:
                $graph = $this->createBarGraph($this->xdata, $this->ydata1, $this->ytitle);
                break;

            case 4:
                $graph = $this->createCookieGraph($this->xdata, $this->ytitle);
                break;
        }

        // Send image to browser
        header("Content-type: image/png");
        imagepng($graph->Stroke());
        imagedestroy($graph);
        unset($graph);
        exit;
    }

    private function createStackedBarGraph($xdata, $data1, $data2, $ytitle) {
    // Create the graph.
        $graph = new Graph($this->width, $this->height, "auto");
        $graph->SetScale("textlin");
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->SetFrame(false);

        if( $this->shadow )
            $graph->SetShadow();

        // Create the bar plots
        $b1plot = new BarPlot($data1);
        $b1plot->SetFillColor("khaki");
        $b1plot->value->Show();
        $b1plot->value->SetFormat('%d');
        $b1plot->value->SetColor("black");
        $b1plot->SetValuePos('center');
        $b1plot->SetLegend($this->legend[0]);

        $b2plot = new BarPlot($data2);
        $b2plot->SetFillColor("orange");
        $b2plot->value->Show();
        $b2plot->value->SetFormat('%d');
        $b2plot->value->SetColor("black");
        $b2plot->SetValuePos('center');
        $b2plot->SetLegend($this->legend[1]);

        // Create the stacked bar plot...
        $gbplot = new AccBarPlot(array($b1plot, $b2plot));

        // ... and add it to the graph
        $graph->Add($gbplot);

        $graph->xaxis->title->Set($this->title);
        $graph->xaxis->SetTickLabels($xdata);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

        $graph->yaxis->title->Set($ytitle);
        $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->yaxis->SetLabelMargin(5);
        $graph->yaxis->scale->SetGrace(10);
        $graph->yaxis->HideZeroLabel();
        $graph->ygrid->SetFill(true,'#f2f2f2@0.5','#cacaca@0.5');

        return $graph;
    }

    private function createLineGraph($xdata, $data1, $ytitle) {
    // Create the graph.
        $graph = new Graph($this->width, $this->height, "auto");
        $graph->SetScale("textlin");
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->SetFrame(false);

        if( $this->shadow )
            $graph->SetShadow();

        // Create the line plot
        $lineplot = new LinePlot($data1);
        $lineplot->SetColor("orange");
        $lineplot->value->Show();
        $lineplot->value->SetFormat('%d');
        $lineplot->value->SetColor("black");

        // ... and add it to the graph
        $graph->Add($lineplot);

        $graph->xaxis->title->Set($this->title);
        $graph->xaxis->SetTickLabels($xdata);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->xaxis->SetLabelAngle(90);

        $graph->yaxis->title->Set($ytitle);
        $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->yaxis->SetLabelMargin(5);
        $graph->yaxis->scale->SetGrace(10);
        $graph->yaxis->HideZeroLabel();
        $graph->ygrid->SetFill(true,'#f2f2f2@0.5','#cacaca@0.5');

        return $graph;
    }

    private function createBarGraph($xdata, $data1, $ytitle) {
    // Create the graph.
        $graph = new Graph($this->width, $this->height, "auto");
        $graph->SetScale("textlin");
        $graph->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->SetFrame(false);

        if( $this->shadow )
            $graph->SetShadow();

        // Create the bar plot
        $arrPos = array();
        $arrNeg = array();

        foreach ($data1 as &$value) {
            if ( $value >=0 ) {
                array_push( $arrPos , $value );
                array_push( $arrNeg , 0 );
            }
            else {
                array_push( $arrPos , 0 );
                array_push( $arrNeg , $value );
            }
        }

        $bplotPos = new BarPlot($arrPos);     // First group positive
        $bplotPos->SetFillColor('#ababab');     // color for positive '#ababab' Kulturplanner #8aa571

        $bplotNeg = new BarPlot($arrNeg);    // second group negative
        $bplotNeg->SetFillColor("#cc00cc");  //color for negative prologiq "#cc00cc"

        $gbplot = new AccBarPlot(array($bplotPos,$bplotNeg));
        $gbplot->value->Show();
        $gbplot->value->SetFormat('%d');
        $gbplot->value->SetColor("black");
        $gbplot->SetValuePos('center');
        //$gbplot->SetLegend(Prado::localize($this->legend));

        // ... and add it to the graph
        $graph->Add($gbplot);

        $graph->xaxis->title->Set($this->title);
        $graph->xaxis->SetTickLabels($xdata);
        $graph->xaxis->title->SetFont(FF_FONT1, FS_BOLD);

        $graph->yaxis->title->Set($ytitle);
        $graph->yaxis->title->SetFont(FF_FONT1, FS_BOLD);
        $graph->yaxis->SetLabelMargin(5);
        $graph->yaxis->scale->SetGrace(10);
        $graph->yaxis->HideZeroLabel();
        $graph->ygrid->SetFill(true,'#f2f2f2@0.6','#cacaca@0.6');

        return $graph;
    }

    private function createCookieGraph($xdata, $ytitle) {
    // Create the graph.
        $graph = new PieGraph($this->width, $this->height,"auto");
        $graph->title->SetFont(FF_FONT1,FS_NORMAL,10);
        $graph->SetFrame(false);

        if( $this->shadow )
            $graph->SetShadow();

        $p1 = new PiePlot($xdata);    // second group negative
        $p1->SetLegends($this->ydata1);
        $p1->SetCenter(0.3);
        $p1->SetTheme("earth");  //color for negative prologiq "#cc00cc"
        $p1->value->SetFont(FF_FONT1,FS_NORMAL,10);

        $graph->title->Set($ytitle);
        // ... and add it to the graph
        $graph->Add($p1);

        return $graph;
    }
}

?>