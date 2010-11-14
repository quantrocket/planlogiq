<?php

/**
 * GanttService.php
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
include_once('protected/lib/jpgraph/jpgraph_gantt.php');

class GanttService extends TService {

    private $datestart = null;
    private $dateende = null;
    private $dataorder = null;
    private $constraits = null;
    private $constraitskey = null;
    private $constraitsvalue = null;
    private $datalabel = null;
    private $datastart = null;
    private $dataende = null;
    private $datamilestone = null;
    private $dataprogress = null;
    private $scale = "month";
    private $title = null;
    private $width = 800;
    private $height = 300;
    private $shadow;

    public function init($config) {
        $request = Prado::getApplication()->getRequest();

        if ($request->contains('dataorder')) {
            $this->dataorder = explode(',', TPropertyValue::ensureString($request['dataorder']));
        } else {
            throw new TConfigurationException('You must specify the x data for the graph');
        }

        if ($request->contains('constraits')) {
            $this->constraitskey = explode(',', TPropertyValue::ensureString($request['constraitskey']));
            $this->constraitsvalue = explode(',', TPropertyValue::ensureString($request['constraitsvalue']));
            $this->constraits = explode(',', TPropertyValue::ensureString($request['constraits']));
        } else {
            throw new TConfigurationException('You must specify the constraints data for the graph');
        }

        if ($request->contains('datalabel')) {
            $this->datalabel = explode(',', TPropertyValue::ensureString($request['datalabel']));
        } else {
            throw new TConfigurationException('You must specify the x data for the graph');
        }

        if ($request->contains('datestart')) {
            $this->datestart = TPropertyValue::ensureString($request['datestart']);
        } else {
            //throw new TConfigurationException('You must specify the startdate data for the graph');
        }
        if ($request->contains('dateende')) {
            $this->dateende = TPropertyValue::ensureString($request['dateende']);
        } else {
            //throw new TConfigurationException('You must specify the enddate data for the graph');
        }

        if ($request->contains('datastart')) {
            $this->datastart = explode(',', TPropertyValue::ensureString($request['datastart']));
        } else {
            throw new TConfigurationException('You must specify the x data for the graph');
        }

        if ($request->contains('dataende')) {
            $this->dataende = explode(',', TPropertyValue::ensureString($request['dataende']));
        } else {
            throw new TConfigurationException('You must specify the x data for the graph');
        }

        if ($request->contains('datamilestone')) {
            $this->datamilestone = explode(',', TPropertyValue::ensureString($request['datamilestone']));
        } else {
            throw new TConfigurationException('You must specify the x data for the graph');
        }

        if ($request->contains('dataprogress')) {
            $this->dataprogress = explode(',', TPropertyValue::ensureString($request['dataprogress']));
        } else {
            throw new TConfigurationException('You must specify the x data for the graph');
        }

        if ($request->contains('scale')) {
            $this->scale = explode(',', TPropertyValue::ensureString($request['scale']));
        } else {
            throw new TConfigurationException('You must specify the y data for the graph');
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

        $graph = $this->createGanttGraph($this->dataorder, $this->datalabel, $this->datastart, $this->dataende, $this->datamilestone, $this->dataprogress, $this->scale, $this->title, $this->constraitskey, $this->constraitsvalue, $this->constraits, $this->datestart, $this->dateende);
        // Send image to browser
        header("Content-type: image/png");
        imagepng($graph->Stroke());
        imagedestroy($graph);
    }

    private function createGanttGraph($dataorder, $datalabel, $datastart, $dataende, $datamilestone, $dataprogress, $scale, $title, $constraitkey, $constraitvalue, $constrait, $startdate, $enddate) {
        // Create the graph.
        $graph = new GanttGraph($this->width, $this->height, "auto");

        $graph->scale->actinfo->SetColTitles(array('Paket'), array(30));

//        $icon = new IconPlot( dirname(__FILE__).'/../../themes/basic/gfx/logorisklogiq.png', 0.65,0.90,1 ,40);
//        $icon->SetAnchor( 'left', 'bottom');
//        $graph->Add( $icon);


        $todaydate = new DateTime();
        $vline = new GanttVLine($todaydate->format("Y-m-d"), "Today");
        $graph->Add($vline);

        if ($startdate == NULL) {
            $myDate = new DateTime();
            $startdate = $myDate->format("Y-m-d");
        }

        if ($enddate == NULL) {
            $my2Date = new DateTime($startdate);
            $my2Date->modify("30days");
            $enddate = $my2Date->format("Y-m-d");
        }



        $graph->SetDateRange($startdate, $enddate);

        $graph->title->Set($title);
        $graph->title->SetFont(FF_FONT1, FS_BOLD);

        $graph->ShowHeaders(GANTT_HDAY | GANTT_HWEEK | GANTT_HMONTH);

        $graph->scale->week->setStyle(WEEKSTYLE_FIRSTDAY);
        $graph->scale->month->setStyle(MONTHSTYLE_SHORTNAMEYEAR2);

        // Setup a horizontal grid
        $graph->hgrid->Show();
        $graph->hgrid->SetRowFillColor('darkblue@0.93');

        if ($this->shadow)
            $graph->SetShadow();

        $mapper = array();
        $mydata = array();
        $progress = array();
        $color = array();
        $ii = 0;
        foreach ($this->dataorder AS $dorder) {
            if ($datamilestone[$ii] == 0) {
                $mapper[$dataorder[$ii]] = $ii;
                array_push($mydata, array($ii, ACTYPE_NORMAL, $datalabel[$ii], $datastart[$ii], $dataende[$ii],' '));
                array_push($progress, array($ii, $dataprogress[$ii] / 100));
                array_push($color, array($ii, 'gray'));
            } else {
                $mapper[$dataorder[$ii]] = $ii;
                array_push($mydata, array($ii, ACTYPE_MILESTONE, $datalabel[$ii], $datastart[$ii], $datalabel[$ii]));
            }
            $ii++;
        }

        $myconstrait = array();
        $ii = 0;
        foreach ($constraitkey as $dorder) {
            array_push($myconstrait, array($mapper[$constraitvalue[$ii]], $mapper[$constraitkey[$ii]], $constrait[$ii]));
            $ii++;
        }
        //print_r($myconstrait);
        //print_r($mydata);

        $graph->CreateSimple($mydata, $myconstrait, $progress, $color);

        return $graph;
    }

}
?>