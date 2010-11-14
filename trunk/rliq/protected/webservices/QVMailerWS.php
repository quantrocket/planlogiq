<?php
class Reports {
  public $MailBody; // string
  public $MailSubject; // string
  public $QlikViewFile; // string
  public $Recipients; // ArrayOfRecipient
  public $ReportId; // string
  public $ReportName; // string
}

class Recipient {
  public $BookmarkName; // string
  public $Email; // string
  public $ID; // string
  public $NTNAME; // string
  public $TABLE; // string
}

class CronJob {
  public $DateString; // string
  public $Id; // string
  public $WeekdayFilter; // string
}

class StatusList {
  public $Id; // string
  public $StatusListe; // ArrayOfStatusObject
}

class StatusObject {
  public $Category; // string
  public $Date; // dateTime
  public $Message; // string
  public $Section; // string
}

class mailReports {
  public $id; // string
  public $report; // Reports
  public $password; // string
}

class mailReportsResponse {
  public $mailReportsResult; // string
}

class registerCronJob {
  public $cronjob; // CronJob
  public $report; // Reports
  public $password; // string
}

class registerCronJobResponse {
}

class unregisterCronJob {
  public $id; // string
  public $password; // string
}

class unregisterCronJobResponse {
}

class checkStatus {
  public $id; // string
  public $numberOfRecords; // int
}

class checkStatusResponse {
  public $checkStatusResult; // StatusList
}

class checkLastExecution {
  public $id; // string
}

class checkLastExecutionResponse {
  public $checkLastExecutionResult; // dateTime
}

class isCronjobRegistered {
  public $id; // string
}

class isCronjobRegisteredResponse {
  public $isCronjobRegisteredResult; // boolean
}

class char {
}

class duration {
}

class guid {
}


/**
 * QVMailerWS class
 * 
 *  
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class QVMailerWS extends SoapClient {

  private static $classmap = array(
                                    'Reports' => 'Reports',
                                    'Recipient' => 'Recipient',
                                    'CronJob' => 'CronJob',
                                    'StatusList' => 'StatusList',
                                    'StatusObject' => 'StatusObject',
                                    'mailReports' => 'mailReports',
                                    'mailReportsResponse' => 'mailReportsResponse',
                                    'registerCronJob' => 'registerCronJob',
                                    'registerCronJobResponse' => 'registerCronJobResponse',
                                    'unregisterCronJob' => 'unregisterCronJob',
                                    'unregisterCronJobResponse' => 'unregisterCronJobResponse',
                                    'checkStatus' => 'checkStatus',
                                    'checkStatusResponse' => 'checkStatusResponse',
                                    'checkLastExecution' => 'checkLastExecution',
                                    'checkLastExecutionResponse' => 'checkLastExecutionResponse',
                                    'isCronjobRegistered' => 'isCronjobRegistered',
                                    'isCronjobRegisteredResponse' => 'isCronjobRegisteredResponse',
                                    'char' => 'char',
                                    'duration' => 'duration',
                                    'guid' => 'guid',
                                   );

  public function QVMailerWS($wsdl = "http://127.0.0.1:8082/QVMailerExecutionService/QVMailerWS/WSDL", $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    parent::__construct($wsdl, $options);
  }

  /**
   *  
   *
   * @param mailReports $parameters
   * @return mailReportsResponse
   */
  public function mailReports(mailReports $parameters) {
    return $this->__soapCall('mailReports', array($parameters),       array(
            'uri' => 'http://tempuri.org/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param registerCronJob $parameters
   * @return registerCronJobResponse
   */
  public function registerCronJob(registerCronJob $parameters) {
    return $this->__soapCall('registerCronJob', array($parameters),       array(
            'uri' => 'http://tempuri.org/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param unregisterCronJob $parameters
   * @return unregisterCronJobResponse
   */
  public function unregisterCronJob(unregisterCronJob $parameters) {
    return $this->__soapCall('unregisterCronJob', array($parameters),       array(
            'uri' => 'http://tempuri.org/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param checkStatus $parameters
   * @return checkStatusResponse
   */
  public function checkStatus(checkStatus $parameters) {
    return $this->__soapCall('checkStatus', array($parameters),       array(
            'uri' => 'http://tempuri.org/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param checkLastExecution $parameters
   * @return checkLastExecutionResponse
   */
  public function checkLastExecution(checkLastExecution $parameters) {
    return $this->__soapCall('checkLastExecution', array($parameters),       array(
            'uri' => 'http://tempuri.org/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param isCronjobRegistered $parameters
   * @return isCronjobRegisteredResponse
   */
  public function isCronjobRegistered(isCronjobRegistered $parameters) {
    return $this->__soapCall('isCronjobRegistered', array($parameters),       array(
            'uri' => 'http://tempuri.org/',
            'soapaction' => ''
           )
      );
  }

}

?>
