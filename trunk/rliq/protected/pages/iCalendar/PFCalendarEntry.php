<?php

Prado::using('Application.3rdParty.vcalendar.vcalendar');

class PFCalendarEntry extends TPage {

    private $docName = "PLIQCal";
    private $ext = "ical";

    private $header = "text/calendar";
    
    public function onPreInit($param){

        if(isset($_GET['idtm_termin'])){
            $TerminRecord = TerminRecord::finder()->findByPK($_GET['idtm_termin']);
        }else{
            echo "Kein Termin";
            exit;
        }

        date_default_timezone_set('Europe/Berlin');

        $v = new vcalendar();                          // initiate new CALENDAR
        $v->setConfig( 'pliq_hpartner'
                    , 'planlogiq.com' );             // config with site domain

        $e = new vevent();                             // initiate a new EVENT

        $SDateArray = explode('-',$TerminRecord->ter_startdate);
        $EDateArray = explode('-',$TerminRecord->ter_enddate);
        $STimeArray = explode(':',$TerminRecord->ter_starttime);
        $ETimeArray = explode(':',$TerminRecord->ter_endtime);

        $e->setProperty( 'categories'
                       , ActivityRecord::finder()->findByPK($TerminRecord->idtm_activity)->act_name );                   // catagorize
        $e->setProperty( 'dtstart'
                       ,  $SDateArray[0], $SDateArray[1], $SDateArray[2], $STimeArray[0], $STimeArray[1], 00 );  // 24 dec 2006 19.30
        $e->setProperty( 'dtend'
                       ,  $EDateArray[0], $EDateArray[1], $EDateArray[2], $ETimeArray[0], $ETimeArray[1], 00 );  // 24 dec 2006 19.30
        //$e->setProperty( 'duration'
        //               , 0, 0, 3 );                    // 3 hours
        $e->setProperty( 'summary'
                       , $TerminRecord->ter_betreff );    // describe the event
        $e->setProperty( 'description'
                       , $TerminRecord->ter_descr );    // describe the event
        $e->setProperty( 'location'
                       , $TerminRecord->ter_ort );                     // locate the event

        $v->addComponent( $e );                        // add component to calendar

        /* alt. production */
        // $v->returnCalendar();                       // generate and redirect output to user browser
        /* alt. dev. and test */
        $str = $v->createCalendar();                   // generate and get output in string, for testing?

        $this->getResponse()->appendHeader("Content-Type:".$this->header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$this->docName.'.'.$this->ext);

        echo $str;

        $writer->save('php://output');
        exit;
      }
      
}

?>