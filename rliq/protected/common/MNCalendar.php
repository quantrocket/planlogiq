<?php

class MNCalendar extends TTemplateControl
{
    //********************* Start of variable declarations *********************
    //*********** Class variables that apply to all calendar formats ***********

    //public $content;
    public function getContent(){ return $this->getViewState('calContent',''); }
    public function setContent($value){ $this->setViewState('calContent',$value,''); }

    public $hiddenURL;
    public $prevMonth;
    public $prevYear;
    public $nextMonth;
    public $nextYear;
    public $prevLink;
    public $nextLink;
    public $prevWeek;
    public $startWeek;
    public $nextWeek;
    public $endWeek;
    public $actualDay;
    public $actualMonth;
    public $actualTextMonth;
    public $actualYear;
    public $actualWeek;

    //public $culture;
    public function getCulture(){ return $this->getViewState('calCulture',''); }
    public function setCulture($value){ $this->setViewState('calCulture',$value,''); }
    public $cultureArrays;

    /*
    Declare the main array to hold the calendar events.
    $events[]['date'] is the date and time of the event in unix time format.
    $events[]['event'] holds the event information.
    */
    public $events = array();
    /*
    Declare a variable indicating the day of the week that the calendar starts on.
    0 = Sunday
    1 = Monday
    */
    public $startingDOW;
    /*
    Decalare a variable to indicate the calendar format.
    smallMonth
    largeMonth
    fullYear
    weekly
    */
    //public $calFormat;
    function getFormat(){ return $this->getViewState('calFormat',''); }
    function setFormat($value){ $this->setViewState('calFormat',$value,''); }

    //Declare a boolean variable to determine weather to display events.
    public $displayEvents;
    /*
    Declare a boolean variable to determine if previous and next links are used
    when displaying a calendar.  This will show arrows for the previous and next
    months on month calendars, and arrows for the previous and next year for the
    full year calendar.
    */
    public $displayPrevNextLinks;
    /*
    Declare variables to hold the images for the previous and next arrows that
    are used for the previous and next links.  If these are not defined, the arrows
    are formatted as << for previous and >> for next.
    */
    public $largeFormatPrevArrow;
    public $largeFormatNextArrow;
    public $fullYearPrevArrow;
    public $fullYearNextArrow;

    //Declare a variable to tell how the month is displayed.  Values are long and short.
    //public $monthFormat;
    function getMonthFormat(){ return $this->getViewState('calMonthFormat',''); }
    function setMonthFormat($value){ $this->setViewState('calMonthFormat',$value,''); }

    //Declare a boolean variable to determine weather the current day is highlighted.
    public $showToday;

    /*
    Decalare a variable to indicate how the calendar is outputted from the class.
    echo - echoes the output to the screen.
    return - returns the HTML formatted calendar to the calling variable.
    */
    public $outputFormat;

    //****** Class variables that apply only to the small calendar format ******

    /*
    This defines the border width of the table cells for the small month format.
    */
    public $smallMonthBorder;
    //Declare color format variables.
    public $colorSmallFormatDayOfWeek;
    public $colorSmallFormatDateText;
    public $colorSmallFormatDateHighlight;
    public $colorSmallFormatHeaderText;
    public $colorSmallFormatWeekendHighlight;

    //****** Class variables that apply only to the large calendar format ******

    /*
    Declare a boolean variable to determine if previous and next month calendars
    are displayed. This only applies to the large month calendar format.
    */
    public $displayPrevNext;
    //Declare a variable to hold the background image for lsrge formst cslendsrs.
    public $backgroundLargeFormatImage;
    /*
    Declare a variable that tells how a background image is repeated.
    repeat - Tiles the image both horizontally and vertically.
    repeat-x - Tiles the image in the horizontal direction only.
    repeat-y - Tiles the image in the vertical direction only.
    no-repeat - No repeating takes place; only one copy of the image is displayed.
    */
    public $backgroundImageRepeat;
    /*
    Define the large format calendars element variables.  These can be used if you
    want to define your own CSS stylesheets for the calendar.
    */
    public $largeFormatID;
    public $largeFormatClass;
    /*
    Declare a boolean variable to determine weather the week numbers are displayed
    for the large month calendar.
    */
    public $showWeek;
    /*
    Decalare a variable to indicate the large calendar day of the week format.
    short - eg. Sun, Mon,Tue...
    long - eg. Sunday, Monday, Tuesday...
    */
    public $DOWformat;
    /*
    Declare a variable to determine the alignment of the large format calendar
    on the page. The options are left, center and right
    */
    public $largeFormatAlign;
    //Declare a variable for the height of the cell for large format calendars.
    public $largeCellHeight;

    //Declare color format variables.
    public $colorLargeFormatDayOfWeek;
    public $colorLargeFormatDateText;
    public $colorLargeFormatDateHighlight;
    public $colorLargeFormatHeaderText;
    public $colorLargeFormatEventText;
    public $colorLargeFormatWeekendHighlight;

    //**** Class variables that apply only to the full year calendar format ****

    /*
    Decalre a boolean variable to determine weather the year is shown for small
    month calendars.  This is typically used when displaying the full year calendars.
    */
    public $displayYear;

    //**************************************************************************
    //****** Class variables that apply only to the weekly calendar format *****
    //Declare a variable for the height of the weekly format calendars.
    public $weekCalendarHeight;
    //Declare a variable for the height of the cell for weekly format calendars.
    public $weekCellHeight;
    /*
    Declare a boolean variable that tells weather or not to highlight the work
    hours for the week view.
    */
    public $showWorkHours;
    /*
    Declare a variable that defines the start time of a work day.  This is only
    relevant when showWorkHours is set to true.
    */
    public $workStartHour;
    public $workStartMinute;
    //AM = 0 : PM - 1
    public $workStartAmPm;
    /*
    Declare a variable that defines the end time of a work day.  This is only
    relevant when showWorkHours is set to true.
    */
    public $workEndHour;
    public $workEndMinute;
    //AM = 0 : PM - 1
    public $workEndAmPm;
    //Declare color format variables.
    public $colorWeekFormatHeaderText;
    public $backcolorWeekFormatHeaderText;
    public $colorWeekFormatDayOfWeek;
    public $colorWeekFormatEventText;
    //**************************************************************************

    //********************** End of variable declarations **********************

    /*
    This function for the class has the same name as the class itself, therefore
    it is automatically invoked whenever a new instance of the class is created.
    This will clear the events array and set defaults for the calendar formatting
    variables.
    */
    public function __construct()
    {
        date_default_timezone_set('Europe/Berlin');
        //************************* Set the main defaults  *************************
        $this->setCultureArrays();
        //Clear the events array.
        $this->events = array();
        //Start the week on Sunday
        $this->startingDOW = 0;
        //Set the calendar to display the current date
        $this->actualDay   = date('j');
        $this->actualMonth = date("n");
        $this->actualYear  = date("Y");
        $this->actualWeek  = strtotime(date("Y").'W'.date('W').date('w'));
        $this->setPrevNextWeekOf( $this->actualYear, date('W'), false );
        //Tell the calendar to highlight the current day when viewing the current month.
        $this->showToday = true;
        //Tell the calendar to Show or Not the HLinks to change Month/Year to Next or Previous
        $this->displayPrevNextLinks = true;
        //Set the display events variable to not show events.
        $this->displayEvents = false;        
        //Define how the calendar is outputted from the class.
        $this->outputFormat = "return"; //echo";
        //******************** Set defaults for small calendars ********************
        //Set the small month border to 0.
        $this->smallMonthBorder = "0px solid #BBBBBB";
        //Set the color formats
        $this->colorSmallFormatDayOfWeek = "blue";
        $this->colorSmallFormatDateText = "black";
        $this->colorSmallFormatDateHighlight = "red";
        $this->colorSmallFormatHeaderText = "purple";
        //******************** Set defaults for large calendars ********************
        //Tell the calendar to not show the week numbers.
        $this->showWeek = false;
        //Tell the calendar to use the long day of week format
        $this->DOWformat = "short";//"long";//
        //Set the height of large format calendar cells.
        $this->largeCellHeight = "80px";
        //Set the attribute for aligning the large format calendar.
        $this->largeFormatAlign = "center";
        //Set the display previous next to not show.
        $this->displayPrevNext = true; //false;
        //Set the color formats
        $this->colorLargeFormatDayOfWeek = "#3333FF";
        $this->colorLargeFormatDateText = "#663399";
        $this->colorLargeFormatDateHighlight = "red";
        $this->colorLargeFormatHeaderText = "orange";
        $this->colorLargeFormatEventText = "#FF6699";
        $this->colorLargeFormatWeekendHighlight = "#00CCFF";
        //******************** Set defaults for weekly calendars *******************
        //Set the default height of the weekly calendar
        $this->weekCalendarHeight = "300px";//"520px";
        //Set the default cell height of the weekly calendar
        $this->weekCellHeight = "40px";
    }

    public function OnInit($param)
    {
        // Add the body of CALENDAR
        $this->ensureChildControls();
        $this->getCalendar();
        $this->painelcorpocalendario->addParsedObject($this->getContent());
    }

    private function setCultureArrays()
    {
        $cultureArrays = array();
        foreach( array('pt_BR','en_US','de_DE') as $culture ) {
            switch( $culture )
            {
                case 'pt_BR' :
                    $mLong = array(1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro');
                    $mShort= array(1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez');
                    $dowLong = array( 0=>"Domingo", 1=>"Domingo", 2=>"Segunda", 3=>"Terça", 4=>"Quarta", 5=>"Quinta", 6=>"Sexta", 7=>"Sabado", 8=>"Domingo" );
                    $dowShort= array( 0=>"Dom", 1=>"Dom", 2=>"Seg", 3=>"Ter", 4=>"Qua", 5=>"Qui", 6=>"Sex", 7=>"Sab", 8=>"Dom" );
                    $dow1Letter= array( 0=>"D", 1=>"D", 2=>"S", 3=>"T", 4=>"Q", 5=>"Q", 6=>"S", 7=>"S", 8=>"D" );
                    $weekName='Semana';
                    break;
                case 'en_US' :
                    $mLong = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
                    $mShort= array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
                    $dowLong = array( 0=>"Sunday", 1=>"Sunday", 2=>"Monday", 3=>"Tuesday", 4=>"Wednesday", 5=>"Thursday", 6=>"Friday", 7=>"Saturday", 8=>"Sunday" );
                    $dowShort= array( 0=>"Sun", 1=>"Sun", 2=>"Mon", 3=>"Tue", 4=>"Wed", 5=>"Thu", 6=>"Fri", 7=>"Sat", 8=>"Sun" );
                    $dow1Letter= array( 0=>"S", 1=>"S", 2=>"M", 3=>"T", 4=>"W", 5=>"T", 6=>"F", 7=>"S", 8=>"S" );
                    $weekName='Week';
                    break;
                case 'de_DE' :
                    $mLong = array(1=>'Januar',2=>'Februar',3=>'März',4=>'April',5=>'Mai',6=>'Juni',7=>'Juli',8=>'August',9=>'September',10=>'Oktober',11=>'November',12=>'Dezember');
                    $mShort= array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mai',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Dez');
                    $dowLong = array( 0=>"Sonntag", 1=>"Sonntag", 2=>"Montag", 3=>"Dienstag", 4=>"Mittwoch", 5=>"Donnerstag", 6=>"Freitag", 7=>"Samstag", 8=>"Sonntag" );
                    $dowShort= array( 0=>"So", 1=>"So", 2=>"Mo", 3=>"Di", 4=>"Mi", 5=>"Do", 6=>"Fr", 7=>"Sa", 8=>"So" );
                    $dow1Letter= array( 0=>"S", 1=>"S", 2=>"M", 3=>"D", 4=>"M", 5=>"D", 6=>"F", 7=>"S", 8=>"S" );
                    $weekName='Woche';
                    break;
            }
            $cultureArrays = array_merge( $cultureArrays, array( $culture=>array('mLong'=>$mLong,'mShort'=>$mShort,'dowLong'=>$dowLong,'dowShort'=>$dowShort,'dow1Letter'=>$dow1Letter,'weekName'=>$weekName) ) );
        }
        $this->cultureArrays = $cultureArrays;
        unset($cultureArrays);
    }

    public function nextORprevClicked( $sender, $param )
    {
        if( $this->Format=='smallMonth' || $this->Format=='largeMonth' )
        {
            if( $sender->getID() == 'prevHLink' ) {
                $arr = explode('|', $this->actvhiddenURL->Value);
                if( count($arr) ) {
                    unset($arr[0]);
                    foreach( $arr as $kk=>$vv ) {
                        $arrP = explode('=', $vv);
                        if( count($arrP) ) {
                            if( $arrP[0]=='prevMon' ) $m = $arrP[1];
                            if( $arrP[0]=='prevYr' ) $y = $arrP[1];
                        }
                    }
                }
            } elseif( $sender->getID() == 'nextHLink' ) {
                $arr = explode('|', $this->actvhiddenURL->Value);
                if( count($arr) ) {
                    unset($arr[0]);
                    foreach( $arr as $kk=>$vv ) {
                        $arrP = explode('=', $vv);
                        if( count($arrP) ) {
                            if( $arrP[0]=='nextMon' ) $m = $arrP[1];
                            if( $arrP[0]=='nextYr' ) $y = $arrP[1];
                        }
                    }
                }
            }
            $this->actualMonth = $m;
            $this->actualYear  = $y;
            $this->displayPrevNextLinks = true;

        } elseif( $this->Format=='weekly' ) {
            $pW = $nW = '';
            if( $sender->getID() == 'prevHLink' ) {
                $arr = explode('|', $this->actvhiddenURL->Value);
                if( count($arr) ) {
                    unset($arr[0]);
                    foreach( $arr as $kk=>$vv ) {
                        $arrP = explode('=', $vv);
                        if( count($arrP) ) {
                            if( $arrP[0]=='prevWeek' ) { $pW = $arrP[1]; break; }
                        }
                    }
                }
                $this->actualWeek = $pW;
                $this->setPrevNextWeekOf( date('Y',$pW), date('W',$pW), true );
            } elseif( $sender->getID() == 'nextHLink' ) {
                $arr = explode('|', $this->actvhiddenURL->Value);
                if( count($arr) ) {
                    unset($arr[0]);
                    foreach( $arr as $kk=>$vv ) {
                        $arrP = explode('=', $vv);
                        if( count($arrP) ) {
                            if( $arrP[0]=='nextWeek' ) { $nW = $arrP[1]; break; }
                        }
                    }
                }
                $this->actualWeek = $nW;
                $this->setPrevNextWeekOf( date('Y',$nW), date('W',$nW), true );
            }
        }
        $this->getCalendar();
        // Need to render content again.
        $this->painelcorpocalendario->getControls()->clear();
        $this->painelcorpocalendario->addParsedObject( $this->getContent() );
        $this->painelcomponentecalendario->render( $param->getNewWriter() );
        $this->actvhiddenURL->Value = $this->hiddenURL;
    }

    private function getWeekRangeDates($year,$week,$onlyStartEnd=false,$fmt='d')
    {   // FMT can be: d=>as date | w=>as week | t=>as timeStamp
        // set the 7 dates of the week
        for($i=$this->startingDOW; $i<7; $i++) {
            switch( $fmt ) {
                case 'd' :
                    $dates[$i] = date('d-m-Y',strtotime($year.'W'.$week.$i));
                    break;
                case 't' :
                    $dates[$i] = strtotime($year.'W'.$week.$i);
                    break;
                case 'w' :
                    $dates[$i] = $year.'W'.$week.$i;
                    break;
            }
            if( $onlyStartEnd && ($i>=$this->startingDOW+1 && $i<=5) ) {
                unset($dates[$i]);
            }
        }
        return $dates;
    }

    private function setPrevNextWeekOf( $year, $week, $ajustaNewMonthYear=false )
    {
        $dates = $this->getWeekRangeDates($year,$week,true,'t');
        $this->startWeek= $dates[0];
        $this->endWeek  = $dates[6];
        $this->prevWeek = $dates[0]-(6*24*60*60);
        $this->nextWeek = $dates[6]+(6*24*60*60);
        $this->actualDay= date('d',$this->startWeek);
        $newYear = date( 'Y', $dates[0] );
        $newMonth= date( 'n', $dates[0] );
        if( $ajustaNewMonthYear && ( intval($this->actualYear) != intval($newYear) || intval($this->actualMonth) != intval($newMonth) ) ) {
            if( intval($this->actualYear) != intval($newYear) ) $this->actualYear = $newYear;
            if( intval($this->actualMonth) != intval($newMonth) ) $this->actualMonth = $newMonth;
        } elseif( empty($this->actualYear) || empty($this->actualMonth) ) {
            $this->actualYear = date('Y');
            $this->actualMonth = date('n');
        }
    }

    public function changeFormat( $sender, $param )
    {
        if( $sender->getID() == 'formatHLink' ) 
        {
            $val = $this->getViewState('calFormat');
            $arr = array(0=>'smallMonth',1=>'largeMonth',2=>'fullYear',3=>'weekly');
            $key = array_search( $val, $arr );
            $key = ( $key===false ? 0 : ($key+1 > 3 ? 0 : $key+1) );
            $this->setViewState('calFormat',$arr[$key]);
            $this->getCalendar();
            // Need to render content again.
            $this->painelcorpocalendario->getControls()->clear();
            $this->painelcorpocalendario->addParsedObject( $this->getContent() );
            $this->painelcomponentecalendario->render( $param->getNewWriter() );
        }
    }

    /*
    This function for the class outputs the calendar based on the parameters given.
    */
    function getCalendar() {
        //Check which format to display
        switch ($this->getFormat()) {
            case "smallMonth":
                $this->DOWformat = "short";
                $displayCal = $this->showSmallMonth($this->actualMonth, $this->actualYear, $this->displayPrevNextLinks, true, true);
                break;
            case "largeMonth":
                $this->DOWformat = "long";
                $displayCal = $this->showLargeMonth($this->actualMonth, $this->actualYear, $this->displayPrevNextLinks, true, true);
                break;
            case "fullYear":
                $this->DOWformat = "short";
                $displayCal = $this->showFullYear($this->actualYear, false);
                break;
            case "weekly":
                $this->DOWformat = "long";
                $this->colorWeekFormatHeaderText = '#FFA500';
                $this->backcolorWeekFormatHeaderText = '#E0FFFF';//#87CEEB';
                $displayCal = $this->showWeekView();//$this->actualWeek, $this->prevWeek, $this->nextWeek);
                break;
            default:
                $error = "Invalid definition of the calFormat variable in display function.";
                $this->displayError($error);
        }
        //Output the HTML based on the outputFormat variable
        switch ($this->outputFormat) {
            case "echo":
                echo ($displayCal);
                break;
            case "return":
                $this->setContent( $displayCal );
                break;
            default:
                $error = "Invalid definition of the outputFormat variable in the display function.";
                $this->displayError($error);

        }
    } //End function display()

    /*
    This function for the class adds a new event to the events array.  The arguments
    passed are date and event.
    */
    public function addEvent($date, $event)
    {
        //Get the next event ID for the events variable.
        $eventID = sizeof($this->events);
        //Add the event to the array.
        $this->events[$eventID]['date'] = $date;
        $this->events[$eventID]['event'] = $event;
    } //End function addEvent()

    /*
    This function for the class return a <div> tag containing the events for the
    day defined by $date.  This function is used for large format calendars.
    */
    public function getEvents($date, $cal, $highlightDate = false)
    {
        //Set a boolean variable to determine weather events were displayed or not.
        $displayed = false;
        //Clear an events variable based on the calendar format.
        switch ($cal) {
            case "smallMonth":
                if ($this->displayEvents) {
                    //display the event with hover titles
                    $events = "<a href='#' title='";
                } else {
                    //display the event without hover titles
                    $events = "";
                }
                break;
            case "largeMonth":
                //Check if this is the weekend
                $week = "";
                if (((date("w", $date) == "0") || (date("w", $date) == "1")) && $this->showWeek) {
                    $week = " - <span style=\"font-size: 12px;\">Week ".date("W", $date)."</span>";
                }
                if ($highlightDate) {
                    $dateColor = $this->colorLargeFormatDateHighlight;
                } else {
                    $dateColor = $this->colorLargeFormatDateText;
                }
                //display the event with full text
                $events = "<div style=\"font-size: 12px; color: ".$this->colorLargeFormatEventText."; width: 100%; height: ".$this->largeCellHeight."; overflow: auto;\">\n";
                $events .= "<span style=\"font-size: 18px; font-weight: bold; color: ".$dateColor.";\">&nbsp;".date("j", $date).$week."</span><br>\n";
                break;
            case "weekly":
                //display the event with full text
                $events = "<div style=\"font-size: 12px; color: ".$this->colorWeekFormatEventText."; width: 100%; height: ".$this->weekCellHeight."; overflow: auto;\">\n";
                break;
            default:
                $error = "Invalid calendar format passed to the getEvents function.";
                $this->displayError($error);
        }
        //Check if any events are defined.
        if (isset($this->events) && $this->displayEvents) {
            //Cycle through the events that are defined.
            for ($i = 0; $i < sizeof($this->events); $i++) {
                //Define a boolean variable that will tell us to show the event or not.
                $showEvent = false;
                //If we are searching for events in a weekly calendar we must also search the time.
                if ($cal == "weekly") {
                    //First determine if this is Am or Pm
                    if (date("A", $this->events[$i]['date']) == "AM") {
                        $ampm = 0;
                    } else if (date("g", $this->events[$i]['date']) == 12) {
                        $ampm = 0;
                    } else {
                        $ampm = 12;
                    }
                    //Define the event date down to the minute.
                    $eventDate = mktime((date("g", $this->events[$i]['date']) + $ampm), date("i", $this->events[$i]['date']), 0, date("m", $this->events[$i]['date']), date("d", $this->events[$i]['date']), date("Y", $this->events[$i]['date']));
                    //echo(date("y/m/d h:i:s A", $this->events[$i]['date'])." -- ".$ampm." - ".date("y/m/d h:i:s A", $eventDate)."<br>");
                    //Define 15 minutes in seconds
                    $quarterHour = 900;
                    //Check if the event is within a quarter hour of the date
                    if (($eventDate >= $date) && ($eventDate < ($date + $quarterHour))) {
                        $showEvent = true;
                    }
                } else {
                    /*
                    Since the calendar format was not weekly we only need to check
                    weather the event fell on the date specified.
                    */
                    $eventDate = mktime(0, 0, 0, date("m", $this->events[$i]['date']), date("d", $this->events[$i]['date']), date("Y", $this->events[$i]['date']));
                    if ($date == $eventDate) {
                        $showEvent = true;
                    }
                }
                if ($showEvent) {
                    //An event was found so determine the calendar format we need to display.
                    switch ($cal) {
                        case "smallMonth":
                            //Check if this is the first event displayed.
                            if ($displayed) {
                                //Display the event with hover titles on a new line.
                                $events .= "\n".date("h:i A", $this->events[$i]['date'])." - ".$this->events[$i]['event'];
                            } else {
                                //Display the event with hover titles.
                                $events .= date("h:i A", $this->events[$i]['date'])." - ".$this->events[$i]['event'];
                                $displayed = true;
                            }
                            break;
                        case "largeMonth":
                        case "weekly":
                            //Display the event with full text.
                            $events .= "<span style=\"font-weight: bold;\">".date("h:i A", $this->events[$i]['date'])."</span> - ".$this->events[$i]['event']."<br><br>\n";
                            break;
                        case "weekly":
                    }
                }
            }
        }
        switch ($cal) {
            case "smallMonth":
                if ($this->displayEvents) {
                    if ($displayed) {
                        //Continue to show the display the event with hover titles.
                        $events .= "' style=\"text-decoration: none; font-weight: bold;\"> ".date("d",$date)."</a>";
                    } else {
                        //No events were added to the title do just display the date.
                        $events = "".date("d",$date); //&nbsp;
                    }
                } else {
                    //display the event without hover titles
                    $events = "".date("d", $date);
                }
                break;
            case "largeMonth":
            case "weekly":
                //display the event with full text
                $events .= "</div>";
                break;
        }
        return $events;
    }

    /*
    This function for the class will return the month name.
    */
    function getMonth($m, $y) 
    {
        //Get the name of the month based on the monthFormat variable.
        switch (strtolower($this->MonthFormat)) {
            case "long":
                $month = $this->cultureArrays[$this->culture]['mLong'][$m];
                break;
            case "short":
                $month = $this->cultureArrays[$this->culture]['mShort'][$m];
                break;
            default:
                $error = "Invalid definition of the monthFormat variable in the getMonth function.";
                $this->displayError($error);
        }
        return $month;
    } //End function getMonth()

    /*
    This function for the class will return the day of the week.
    */
    function getDOW($dow) {
        $dow = $dow + $this->startingDOW;
        //Get the name of the month based on the DOWformat variable.
        switch (strtolower($this->DOWformat)) {
            case "long":
                $weeekday = $this->cultureArrays[$this->culture]['dowLong'][$dow];
                break;
            case "short":
                $weeekday = $this->cultureArrays[$this->culture]['dowShort'][$dow];
            default:
                $error = "Invalid definition of the DOWformat variable in the getDOW function.";
                $this->displayError($error);
        }
        return $weeekday;
    } //End function getDOW()

    /*
    This function for the class will return (1 letter) name of the day of the week.
    */
    function get1LetterDOW($dow) {
        $dow = $dow + $this->startingDOW;
        $weeekday = $this->cultureArrays[$this->culture]['dow1Letter'][$dow];
        return $weeekday;
    } //End function get1LetterDOW()

    /*
    This function for the class will display a month in small format. The inputs
    to the function are as follows:
    $m - Month to display.
    $y - Year to display.
    $np - A boolean value indicating weather to display the links for the previous and next months.
    */
    function showSmallMonth($m, $y, $np = false, $showyear = true, $recalcProperties = false)
    {
        //Calculate the number of days in the month
        $days = date('t',mktime(0,0,0,$m, 1, $y));
        //Calculate the day of the week that the month starts on
        $startDay = date('w',mktime(0,0,0,$m, 1, $y)) - $this->startingDOW;
        //set the column offset for the starting day of the week.
        $offset = "";
        if ($startDay > 0) {
            $offset .= "        <td width=\"14%\" colspan=\"".$startDay."\">&nbsp;</td>\n";
        } else if ($startDay == -1) {
            $offset .= "        <td width=\"14%\" colspan=\"6\">&nbsp;</td>\n";
            $startDay = 6;
        }

        if( $recalcProperties ):
            //Get the textual representation of the month
            $this->actualTextMonth = $this->getMonth($m, $y) . ' - ';
            //Calculate the previous month and year for the header link.
            if (($m - 1) == 0) {
                $this->prevMonth = 12;
                $this->prevYear = $y - 1;
            } else {
                $this->prevMonth = $m - 1;
                $this->prevYear = $y;
            }
            //Calculate the next month and year for the header link.
            if (($m + 1) == 13) {
                $this->nextMonth = 1;
                $this->nextYear = $y + 1;
            } else {
                $this->nextMonth = $m + 1;
                $this->nextYear = $y;
            }

            $this->hiddenURL = "|prevMon={$this->prevMonth}|prevYr={$this->prevYear}|fmt=smallMonth|nextMon={$this->nextMonth}|nextYr={$this->nextYear}";

            //Get the currrent date to display if the month showing is the current month.
            if (mktime(0, 0, 0, date("m"), 1, date("Y")) == mktime(0, 0, 0, $m, 1, $y)) {
                $this->actualDay = date("j");
            } else {
                $this->actualDay = 0;
            }
            if ($showyear) {
                $this->actualYear = $y;
            } else {
                $this->actualYear = "";
            }
        endif;

        // create the header of calendar
        $output = "<div style='vertical-align: top;'>";
        $output.= "<table /*class='calendar'*/ border='{$this->smallMonthBorder}' cellspacing='1' cellpadding='0' align='center' style='font-size: 10px; text-align: center;'>";
        if( $this->getFormat()!='smallMonth' ):
            $output .= "    <tr>\n";
            $output .= "        <th colspan=\"7\" style=\"text-align: center;\">\n";
            $output .= "            <span style=\"font-weight: bold; color: ".$this->colorSmallFormatHeaderText.";\">".$this->getMonth($m, $y)."</span>\n";
            $output .= "        </th>\n";
            $output .= "    </tr>\n";
        endif;
        $output.= "     <tr style='color:{$this->colorSmallFormatDayOfWeek}'>";

        //now create the weekday headers
        for ($i = 1; $i < 8; $i++) {
            $output .= "        <td style=\"width: 14%; text-align: center;\">".$this->get1LetterDOW($i)."</td>\n";
        }
        $output .= "    </tr>\n";
        $output .= "    <tr>\n";

        //Now generate the calendar
        for($i=1; $i<=$days; $i++){
            if ($i == $this->actualDay && $this->showToday) {
                $output .= $offset."        <td style=\"width: 14%; text-align: center; color: ".$this->colorSmallFormatDateHighlight."; font-weight: bold;\">&nbsp;".$this->getEvents(mktime(0, 0, 0, $m, $i, $y), "smallMonth")."&nbsp;</td>\n";
            } else {
                $output .= $offset."        <td style=\"width: 14%; text-align: center; color: ".$this->colorSmallFormatDateText.";\">&nbsp;".$this->getEvents(mktime(0, 0, 0, $m, $i, $y), "smallMonth")."&nbsp;</td>\n";
            }
            $offset = "";
            $startDay ++;
            if ($startDay == 7) {
                $output .= "    </tr>\n";
                $output .= "    <tr>\n";
                $startDay = 0;
            }
        }
        if ($startDay > 0) {
            $output .= "        <td colspan=\"".(7 - $startDay)."\" style=\"width: 14%;\">&nbsp;</td>\n";
        }
        $output .= "    </tr>\n";
        $output .= "</table>\n";
        $output .= "</div>\n";
        //Now output the calendar
        return $output;
    } //End function showSmallMonth()

    /*
    This function for the class will display a month in large format. The inputs
    to the function are as follows:
    $m - Month to display.
    $y - Year to display.
    $np - A boolean value indicating weather to display the links for the previous and next months.
    */
    function showLargeMonth($m, $y, $np=true, $showyear = true, $recalcProperties = false) {
        //Calculate the number of days in the month
        $days = date('t',mktime(0,0,0,$m, 1, $y));
        //Calculate the day of the week that the month starts on
        $startDay = date('w',mktime(0,0,0,$m, 1, $y)) - $this->startingDOW;
        //set the column offset for the starting day of the week.
        $offset = "";
        if ($startDay > 0) {
            $offset .= "        <td width=\"14%\" colspan=\"".$startDay."\">&nbsp;</td>\n";
        } else if ($startDay == -1) {
            $offset .= "        <td width=\"14%\" colspan=\"6\">&nbsp;</td>\n";
            $startDay = 6;
        }
        if ($this->displayPrevNext) {
            $headerHeight = "120px";
        } else {
            $headerHeight = "50px";
        }
        if( $recalcProperties ):
            //Get the textual representation of the month
            $this->actualTextMonth = $this->getMonth($m, $y) . ' - ';
            //Calculate the previous month and year for the header link.
            if (($m - 1) == 0) {
                $this->prevMonth = 12;
                $this->prevYear = $y - 1;
            } else {
                $this->prevMonth = $m - 1;
                $this->prevYear = $y;
            }
            //Calculate the next month and year for the header link.
            if (($m + 1) == 13) {
                $this->nextMonth = 1;
                $this->nextYear = $y + 1;
            } else {
                $this->nextMonth = $m + 1;
                $this->nextYear = $y;
            }
            if ($showyear) {
                $this->actualYear = $y;
            } else {
                $this->actualYear = "";
            }
            //Set default arrows to use if no images are defined.
            $prevArrow = "&laquo;";
            $nextArrow = "&raquo;";
            //If images were set for the previous month and next month links, set the images.
            if (isset($this->largeFormatPrevArrow)) {
                $prevArrow = "<img src=\"".$this->largeFormatPrevArrow."\" border=\"0\" align=\"top\">";
            }
            if (isset($this->largeFormatNextArrow)) {
                $nextArrow = "<img src=\"".$this->largeFormatNextArrow."\" border=\"0\" align=\"top\">";
            }

            $prevLink = "";
            $nextLink = "";

            $this->hiddenURL = "|prevMon={$this->prevMonth}|prevYr={$this->prevYear}|fmt=largeMonth|nextMon={$this->nextMonth}|nextYr={$this->nextYear}";

            //Get the currrent date to display if the month showing is the current month.
            if (mktime(0, 0, 0, date("m"), 1, date("Y")) == mktime(0, 0, 0, $m, 1, $y)) {
                $this->actualDay = date("j");
            } else {
                $this->actualDay = 0;
            }
        endif;
        //Define the table elements for the calendar.
        $largeCalendarID = "";
        $largeCalendarClass = "";
        if (isset($this->largeFormatID)) {
            $largeCalendarID = " id=\"".$this->largeFormatID."\"";
        }
        if (isset($this->largeFormatClass)) {
            $largeCalendarClass = " class=\"".$this->largeFormatClass."\"";
        }
        //Set some default attributes for the size of the calendar
        $background = "";
        $backgroundRepeat = "";
        $width = "100%";
        $height = "";
        $heightCalCell = " height: ".$this->largeCellHeight.";";
        /*
        Check if there is a background image set for the calendar.  If so, reset
        the calendar width and height to the width and height of the image.  Also,
        clearing the cell height will allow the browser to automatically size the
        height of the cells since the total table height is pre-defined.
        */
        if (isset($this->backgroundLargeFormatImage)) {
            if (isset($this->backgroundImageRepeat)) {
                $backgroundRepeat = " background-repeat: ".$this->backgroundImageRepeat.";";
            }
            $background = " background-image: url('".$this->backgroundLargeFormatImage."');".$backgroundRepeat;
            $size = getimagesize($this->backgroundLargeFormatImage);
            $width = $size[0]."px";
            $height = " height: ".$size[1]."px;";
            $heightCalCell = "";
        }
        
        $this->showToday = false; // No show hightlighted today on small-months

        //Set default attributes

        //Create the header
        $output = "<div style=\"vertical-align: top;\">";
        $output .= "<table".$largeCalendarClass.$largeCalendarID." border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=\"".$this->largeFormatAlign."\" style=\"width: ".$width.";".$height.$background."\">\n";
        $output .= "    <tr>\n";
        $output .= "        <td colspan=\"7\" style=\"text-align: center; height: ".$headerHeight.";\">\n";
        $output .= "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"width: 100%;\">\n";
        $output .= "    <tr>\n";
        $output .= "        <td style=\"width: 15%; text-align: left; vertical-align: top;\">\n";
        if ($this->displayPrevNext) {
            $output .= $this->showSmallMonth($this->prevMonth, $this->prevYear, false, false, false);
        }
        $output .= "        </td>\n";
        $output .= "        <td style=\"width: 70%; text-align: center; vertical-align: middle;\">\n";
        $output .= "            <span style=\"font-size: 30px; font-weight: bold; color: ".$this->colorLargeFormatHeaderText.";\">".$prevLink.$this->actualTextMonth."&nbsp;".$y.$nextLink."</span>\n";
        $output .= "        </td>\n";
        $output .= "        <td style=\"width: 15%; text-align: right; vertical-align: top;\">\n";
        if ($this->displayPrevNext) {
            $output .= $this->showSmallMonth($this->nextMonth, $this->nextYear, false, false, false);
        }
        $output .= "        </td>\n";
        $output .= "    </tr>\n";
        $output .= "</table>";
        $output .= "        </td>\n";
        $output .= "    </tr>\n";
        $output .= "    <tr style=\"color: ".$this->colorLargeFormatDayOfWeek."; font-weight: bold;\">\n";
        //now create the weekday headers
        for ($i = 1; $i < 8; $i++) {
            $output .= "        <td style=\"width: 14%; text-align: center;\">".$this->getDOW($i)."</td>\n";
        }
        $output .= "    </tr>\n";
        $output .= "    <tr>\n";
        //Now generate the calendar
        for($i=1; $i<=$days; $i++){
            $date = mktime(0, 0, 0, $m, $i, $y);
            if ((date("w",$date) == "0") || (date("w",$date) == "6") && isset($this->colorLargeFormatWeekendHighlight)) {
                $bgcolor = " background-color: ".$this->colorLargeFormatWeekendHighlight."; filter: alpha(opacity=70); -moz-opacity: 70%;";
            } else {
                $bgcolor = "";
            }
            if ($i == $days && $this->showToday) {
                $output .= $offset."        <td style=\"width: 14%;".$heightCalCell." vertical-align: top; text-alTControl::addParsedObject($this->header)ign: left;".$bgcolor." color: ".$this->colorLargeFormatDateHighlight."; font-weight: bold;\">".$this->getEvents($date, "largeMonth", true)."</td>\n";
            } else {
                $output .= $offset."        <td style=\"width: 14%;".$heightCalCell." vertical-align: top; text-align: left;".$bgcolor."\">".$this->getEvents($date, "largeMonth")."</td>\n";
            }
            $offset = "";
            $startDay ++;
            if ($startDay == 7) {
                $output .= "    </tr>\n";
                $output .= "    <tr>\n";
                $startDay = 0;
            }
        }
        if ($startDay > 0) {
            $output .= "        <td colspan=\"".(7 - $startDay)."\" style=\"width: 14%;".$heightCalCell."\">&nbsp;</td>\n";
        }
        $output .= "    </tr>\n";
        $output .= "</table>\n";
        $output .= "</div>";
        //Now output the calendar
        return $output;
    } //End function showLargeMonth()

    /*
    This function for the class will display a month in large format. The inputs
    to the function are as follows:
    $m - Month to display.
    $y - Year to display.
    $np - A boolean value indicating weather to display the links for the previous and next months.
    */
    function showFullYear($y, $np = false) {
        //Get the previous and next years for the year selection links.
        $prevYear = $y - 1;
        $nextYear = $y + 1;
        //Set default arrows to use if no images are defined.
        $prevArrow = "<<";
        $nextArrow = ">>";
        //If images were set for the previous month and next month links, set the images.
        if (isset($this->fullYearPrevArrow)) {
            $prevArrow = "<img src=\"".$this->fullYearPrevArrow."\" border=\"0\" align=\"top\">";
        }
        if (isset($this->fullYearNextArrow)) {
            $nextArrow = "<img src=\"".$this->fullYearNextArrow."\" border=\"0\" align=\"top\">";
        }
        
//        if ($this->passGetRequests) {
//            $get = $this->addGetRequests();
//        } else {
//            $get = "";
//        }

        //If chosen, prepare the links for the previous month and next month
        if ($np) {
            $prevLink = "<a href='".$_SERVER['PHP_SELF']."?yr=".$prevYear."&fmt=fullYear".$get."' style=\"text-decoration: none;\">".$prevArrow."</a> &nbsp;";
            $nextLink = " &nbsp;<a href='".$_SERVER['PHP_SELF']."?yr="."&fmt=fullYear".$get."' style=\"text-decoration: none;\">".$nextArrow."</a>";
        } else {
            $prevLink = "";
            $nextLink = "";
        }
        //Create the table that will contain the months and add the year header.
        $output = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\" align=\"center\">\n";
        $output .= "    <tr>\n";
        $output .= "        <td colspan=\"3\" style=\"text-align: center;\">\n";
        $output .= "            <span style=\"font-size: 50px; font-weight: bold;\">".$prevLink.$y.$nextLink."</span>\n";
        $output .= "        </td>\n";
        $output .= "    </tr>\n";
        $output .= "    <tr>\n";
        //Create a variable to count the columns.
        $col = 1;
        //Now show the months for that year.
        for ($i = 1; $i <= 12; $i++) {
            $output .= "        <td style=\"text-align: center; vertical-align: top;\">\n";
            $output .= $this->showSmallMonth($i, $y, false, false);
            $output .= "        </td>\n";
            $col ++;
            if ($col == 4) {
                $output .= "    </tr>\n";
                $output .= "    <tr>\n";
                $col = 1;
            }
        }
        $output .= "    </tr>\n";
        $output .= "</table>\n";
        return $output;
    } //End function showFullYear()

    /*
    This function is used to show a weekly view of the calendar.
    */
    public function showWeekView()//$week, $pW='', $nW='')
    {
        //Determine what week of the year the date falls on.
        $week = $this->actualWeek;
        //Determine what day of the week the date falls on.
        $dayOfWeek = date("w",$week);
        //Define one day in seconds (60 seconds * 60 minutes * 24 hours).
        $oneDay = 60 * 60 * 24;
        //Determine the first day of the week that the day falls on.
        $firstDayOfWeek = $this->startWeek; //$date - ($dayOfWeek * $oneDay);
        $weekCalendarClass = "";
        $weekCalendarID = "";
        $prevLink = "";
        $nextLink = "";
        $width = "100%";

        $highlightWorkHours = false;
        $toggle = 0;

        //Get the textual representation of the month
        $this->actualTextMonth = $this->getMonth($this->actualMonth, $this->actualYear) . ' - ';

        if(empty($pW)) $pW = $this->prevWeek;
        if(empty($nW)) $nW = $this->nextWeek;

        $date = date( "d/m/Y", mktime(0, 0, 0, $this->actualMonth, $this->actualDay, $this->actualYear) );

        $this->hiddenURL = "|prevWeek={$pW}|fmt=weekly|nextWeek={$nW}";

        if (isset($this->workStartHour) && isset($this->workStartMinute) && isset($this->workStartAmPm)) {
            /*
            Determine the quarter hour of the starting work time for highlighting the
            hours in a work day.
            */
            $unixWorkStartTime = mktime(($this->workStartHour + ($this->workStartAmPm * 12)), $this->workStartMinute, 0, date("m", $date), date("j", $date), date("Y", $date));
            /*
            Determine the quarter hour of the ending work time for highlighting the
            hours in a work day.
            */
            $unixWorkEndTime = mktime(($this->workEndHour + ($this->workEndAmPm * 12)), $this->workEndMinute, 0, date("m", $date), date("j", $date), date("Y", $date));
            $highlightWorkHours = true;
        } else {
            $unixWorkStartTime = mktime(0, 0, 0, date("m", $date), date("j", $date), date("Y", $date));
            $unixWorkEndTime = mktime(0, 0, 0, date("m", $date), date("j", $date), date("Y", $date));
        }

        //Create the header
        $output = "<div style=\"vertical-align: top;\">";        
        $output .= "<table".$weekCalendarClass.$weekCalendarID." border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=\"".$this->largeFormatAlign."\" style=\"width: ".$width.";\">\n";

        $output .= "    <tr>\n";
        $output .= "        <th colspan=9 style=\"width: 100%; text-align: center; vertical-align: middle; background-color: {$this->backcolorWeekFormatHeaderText};\">\n";
        $output .= "            <span style=\"font-size: 30px; font-weight: bold; color: ".$this->colorWeekFormatHeaderText.";\">".$this->cultureArrays[$this->culture]['weekName']." ".date('W',$week).' | '.$this->actualYear."</span>\n";
        $output .= "        </th>\n";
        $output .= "    </tr>\n";

        //$output .= "    <tr style=\"color: ".$this->colorWeekFormatDayOfWeek."; font-weight: bold;\">\n";
        //$output .= "        <td>\n";
        //$output .= "            <table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"width: 100%;\">\n";
        $output .= "                <tr>\n";
        $output .= "                    <td style=\"width: 12.5%; text-align: center;\">Hour</td>\n";
        //now create the weekday headers
        for ($i = 1; $i < 8; $i++) {
            //if($i!=7) $output .= "      <td style=\"width: 12.5%; text-align: center;\">".$this->getDOW($i)."</td>\n";
            //else
            $output .= "                <td style=\"width: 12.7%; text-align: center;\">".$this->getDOW($i)."</td>\n";
        }
        $output .= "                    <td style=\"width: 1%;\">&nbsp;&darr;&nbsp;</td>\n";
        $output .= "                </tr>\n";
        //$output .= "            </table>\n";
        //$output .= "        <td>\n";
        //$output .= "    </tr>\n";

        $output .= "    <tr>\n";
        $output .= "        <td colspan=\"9\">\n";
        $output .= "            <div style=\"width: 100%; height: ".$this->weekCalendarHeight."; overflow: auto;\">\n";
        $output .= "                <table border=\"1\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\" style=\"width: 100%;\">\n";
        for($ampm = 0; $ampm < 2; $ampm++) {
            for ($hour = 2; $hour <= 13; $hour++) {
                for ($minute = 0; $minute < 4; $minute++) {
                    //Format the time display.
                    $unixTime = mktime((($hour + ($ampm * 12)) - 1), ($minute * 15), 0, date("m", $date), date("j", $date), date("Y", $date));
                    $time = date("g:i A", $unixTime);

                    if ($minute == 0) {
                        $highlightZeroHour = "font-weight: bold;";
                    } else {
                        $highlightZeroHour = "";
                    }

                    if ($minute == 0) {
                        $toggle++;
                        if ($toggle > 1) { $toggle = 0; }
                    }

                    if ($toggle == 0) {
                        if ((($unixTime >= $unixWorkStartTime)) && ($unixTime < $unixWorkEndTime) && $highlightWorkHours) {
                            $highlightHour = " background-color: #DDDDFF;";
                        } else {
                            $highlightHour = " background-color: #DDFFDD;";
                        }
                    } else {
                        if ((($unixTime >= $unixWorkStartTime)) && ($unixTime < $unixWorkEndTime) && $highlightWorkHours) {
                            $highlightHour = " background-color: #BBBBFF;";
                        } else {
                            $highlightHour = " background-color: #BBFFBB;";
                        }
                    }

                    $output .= "                    <tr style=\"".$highlightHour." height: ".$this->weekCellHeight.";\">\n";
                    $output .= "                        <td style=\"width: 12.5%; text-align: center;".$highlightZeroHour." vertical-align: middle;\">\n";
                    $output .= "                        <a name=\"".$time."\">".$time."</a>\n";
                    $output .= "                        </td>\n";
                    for ($dow = 0; $dow < 7; $dow++) {
                        $output .= "                        <td style=\"width: 12.5%; text-align: left; vertical-align: top;\">\n";
                        $dateCheck = mktime(($hour + ($ampm * 12) - 1), ($minute * 15), 0, date("m", ($firstDayOfWeek + ($oneDay * $dow))), date("d", ($firstDayOfWeek + ($oneDay * $dow))) + $this->startingDOW, date("Y", ($firstDayOfWeek + ($oneDay * $dow))));
                        $output .= "                        ".$this->getEvents($dateCheck, "weekly")."\n";
                        $output .= "                        </td>\n";
                    }
                    $output .= "                    </tr>\n";
                }
            }
        }
        $output .= "                    </tr>\n";
        $output .= "                </table>\n";
        $output .= "            </div>\n";
        $output .= "        </td>\n";
        $output .= "    </tr>\n";
        $output .= "    </table>\n";

        return $output;
    } //End function showWeekVIew()

    /*
    This function of the class is used to display errors generated by the class.
    */
    function displayError($error) {
        $output = "<table border=\"1\" cellspacing=\"0\" cellpadding=\"5\" align=\"left\">\n";
        $output .= "    <tr>\n";
        $output .= "        <td style=\"text-align: center;\">\n";
        $output .= "        The clendar class has generated the following error:<br>\n";
        $output .= "        <span style=\"color: red;\">".$error."</span>\n";
        $output .= "        </td>\n";
        $output .= "    <tr>\n";
        $output .= "</table>\n";
        die($output);
    } //End function displayError()

} //End class calendar

