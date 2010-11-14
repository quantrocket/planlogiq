<?php
        
class QVHPMailer extends TPage
{
	/**
	 * Highlights a string as php code
	 * @param string $address The php code to highlight
	 * @return array lat, long
	 * @soapmethod
	 */

         private $urlxml;
         private $url;
         private $ServiceName = "QVMailerExecutionService/QVMailerWS";

    public function mailMyReports($idta_berichte = 0){
        ini_set('soap.wsdl_cache_enabled',0);
        date_default_timezone_set('Europe/Berlin');
        $this->urlxml = "http://".$this->Application->Parameters['QlikViewHost'].":8082/".$this->ServiceName."/WSDL";

        $MailReports = new mailReports();
        $MailReports->id = $idta_berichte;
        $MailReports->report = $this->buildParameter($idta_berichte);
        $MailReports->password = 'abcde';

        try{
            $QVMailerWS = new QVMailerWS($this->urlxml,array('soap_version'=>SOAP_1_1,'trace'=>1,'exceptions'=>1));
            $QVMailerWS->soap_defentcoding = 'UTF-8';
            
            $result = $QVMailerWS->mailReports($MailReports);

//            var_dump($QVMailerWS->__getLastRequest());
//            var_dump($QVMailerWS->__getLastResponse());

        }catch(Exception $e){
            echo 'Exception abgefangen: '. $e->getMessage(). "\n";
        }
    }

    public function checkStatus($idta_berichte = 0){

        ini_set('soap.wsdl_cache_enabled',0);
        date_default_timezone_set('Europe/Berlin');
        $this->urlxml = "http://".$this->Application->Parameters['QlikViewHost'].":8082/".$this->ServiceName."/WSDL";
        $result = '';
        try{            
            $QVMailerWS = new QVMailerWS($this->urlxml,array('soap_version'=>SOAP_1_1,'trace'=>1,'exceptions'=>1));
            $QVMailerWS->soap_defentcoding = 'UTF-8';

            $StatusCheck = new checkStatus();
            $StatusCheck->id = $idta_berichte;
            $StatusCheck->numberOfRecords = 100;

            $result = $QVMailerWS->checkStatus($StatusCheck);
        }catch(Exception $e){
            echo 'Exception abgefangen: '. $e->getMessage(). "\n";
        }
        return $result;
    }

    public function ReportsRegisterCronJob($idta_berichte = 0){

        $this->ReportsUnregisterCronJob($idta_berichte);

        ini_set('soap.wsdl_cache_enabled',0);
        date_default_timezone_set('Europe/Berlin');
        $this->urlxml = "http://".$this->Application->Parameters['QlikViewHost'].":8082/".$this->ServiceName."/WSDL";

        //Hier hole ich mir die Werte vom Bericht, damit ich Sie gleich verwenden kann
        $MyCronBericht = BerichteRecord::finder()->findByidta_berichte($idta_berichte);

        $MailReports = new mailReports();
        $MailReports->report = $this->buildParameter($idta_berichte);
        
        $CronJob = new CronJob();
        // Template $CronJob->DateString = '####-##-01 10:00' monatlich;
        // Template $CronJob->DateString = '####-##-## 10:00' in Kombi mit Wochentag  - woechentlich;
        // Template $CronJob->DateString = '####-##-## ##:00' stuendlich;

        //build up of datesting
        $myDateString = "####-##-";
        if($MyCronBericht->ber_zyklus == 3){
            $myDateString.= sprintf("%02d",$MyCronBericht->ber_zyklus_start);
        }else{
            $myDateString.= "##";
        }
        $myDateString.=" ".$MyCronBericht->ber_zyklus_time;

        $CronJob->DateString = $myDateString;
        
        $CronJob->WeekdayFilter = "*";
        if($MyCronBericht->ber_zyklus<3){
            //zusammenbauen des Tagesstring
            $ar_ber_zyklus_gap=array(1=>'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
            $string = strval($MyCronBericht->ber_zyklus_gap);
            $dayvalues = array();
            for($i = 0, $j = strlen($string);$i < $j;$i++){
                $dayvalues[] = $ar_ber_zyklus_gap[$string[$i]];
            }
            $CronJob->WeekdayFilter = implode(",",$dayvalues);
        }
        
        $CronJob->Id = $idta_berichte;

        try{
            $QVMailerWS = new QVMailerWS($this->urlxml,array('soap_version'=>SOAP_1_1,'trace'=>1,'exceptions'=>1));
            $QVMailerWS->soap_defentcoding = 'UTF-8';
            
            $MyCronJob = new registerCronJob();
            $MyCronJob->cronjob = $CronJob;
            $MyCronJob->report = $MailReports->report;
            $MyCronJob->password = 'abcde';

            $result = $QVMailerWS->registerCronJob($MyCronJob);

//            var_dump($QVMailerWS->__getLastRequest());

        }catch(Exception $e){
            echo 'Exception abgefangen: '. $e->getMessage(). "\n";
        }
    }

    public function ReportsUnregisterCronJob($idta_berichte = 0){
        ini_set('soap.wsdl_cache_enabled',0);
        date_default_timezone_set('Europe/Berlin');
        $this->urlxml = "http://".$this->Application->Parameters['QlikViewHost'].":8082/".$this->ServiceName."/WSDL";

        try{
            $QVMailerWS = new QVMailerWS($this->urlxml,array('soap_version'=>SOAP_1_1,'trace'=>1,'exceptions'=>1));
            $QVMailerWS->soap_defentcoding = 'UTF-8';

            $MyCronJob = new unregisterCronJob();
            $MyCronJob->id = $idta_berichte;
            $MyCronJob->password = 'abcde';

            $result = $QVMailerWS->unregisterCronJob($MyCronJob);
        }catch(Exception $e){
            echo 'Exception abgefangen: '. $e->getMessage(). "\n";
        }
    }

    public function buildParameter($idta_berichte=0){
            $target_encoding = "UTF-8";

            $SingleReport = BerichteRecord::finder()->findByidta_berichte($idta_berichte);

            $doc = new Reports();

            //$BerMailBody= mb_convert_encoding($SingleReport->ber_mail_body,$target_encoding);
            $BerMailBody= $SingleReport->ber_mail_body;
            $doc->MailBody=$BerMailBody;

            //$BerMailSub= mb_convert_encoding($SingleReport->ber_mail_subject,$target_encoding);
            $BerMailSub= $SingleReport->ber_mail_subject;
            $doc->MailSubject=$BerMailSub;

            //$QVFile=mb_convert_encoding($SingleReport->ber_local_path,$target_encoding);
            $QVFile=$SingleReport->ber_local_path;
            $doc->QlikViewFile=$QVFile;

            $RCPS = BerichteOrganisationRecord::finder()->findAllByidta_berichte($SingleReport->idta_berichte);
            //schleife für alle empfänger
            foreach($RCPS As $RCP) {
                $USER = OrganisationRecord::finder()->findByPK($RCP->idtm_organisation);
                $Mailer = new Recipient();
                $Mailer->BookmarkName='';
                $Mailer->Email=mb_convert_encoding(KommunikationRecord::finder()->find('idtm_organisation=? AND kom_ismain=1 AND kom_type = 3',$RCP->idtm_organisation)->kom_information,$target_encoding);
                $Mailer->ID=$this->ConditionCleaner($RCP->bho_id);
                $Mailer->NTNAME=$USER->org_ntuser;
                $Mailer->TABLE=$RCP->bho_modul;
                $Recipients[]=$Mailer;
                unset($Mailer,$USER);
            }
            $doc->Recipients=$Recipients;

            $BerID=mb_convert_encoding($SingleReport->ber_id,$target_encoding);
            $doc->ReportId=$BerID;

            //$BerName=mb_convert_encoding($SingleReport->ber_name,$target_encoding);
            $BerName=$SingleReport->ber_name;
            $doc->ReportName=$BerName;

            return $doc;
    }

    public function buildParameterXML($idta_berichte=0){
            $doc=new TXmlDocument('1.0','UTF-8');

            $SingleReport = BerichteRecord::finder()->findByidta_berichte($idta_berichte);

            $doc->TagName='Reports';
            $doc->setAttribute('xmlns:d4p1',"http://schemas.datacontract.org/2004/07/QVMailerExecutionService");
            $doc->setAttribute('xmlns:i',"http://www.w3.org/2001/XMLSchema-instance");
            
            $QVFile=new TXmlElement('QlikViewFile');
            $QVFile->Value=mb_convert_encoding($SingleReport->ber_local_path,$target_encoding);
            $doc->Elements[]=$QVFile;

            $BerName=new TXmlElement('ReportName');
            $BerName->Value=mb_convert_encoding($SingleReport->ber_name,$target_encoding);
            $doc->Elements[]=$BerName;

            $BerID=new TXmlElement('ReportId');
            $BerID->Value=mb_convert_encoding($SingleReport->ber_id,$target_encoding);
            $doc->Elements[]=$BerID;

            $BerMailSub=new TXmlElement('MailSubject');
            $BerMailSub->Value=mb_convert_encoding($SingleReport->ber_mail_subject,$target_encoding);
            $doc->Elements[]=$BerMailSub;

            $BerMailBody=new TXmlElement('MailBody');
            $BerMailBody->Value=mb_convert_encoding(htmlspecialchars_decode($SingleReport->ber_mail_body),$target_encoding);
            $doc->Elements[]=$BerMailBody;

            $Recipients=new TXmlElement('Recipients');

            $RCPS = BerichteOrganisationRecord::finder()->findAllByidta_berichte($SingleReport->idta_berichte);
            //schleife für alle empfänger
            foreach($RCPS As $RCP) {
                $USER = OrganisationRecord::finder()->findByPK($RCP->idtm_organisation);
                $Mailer = new TXmlElement('Recipient');
                $Mailer->setAttribute('ID',$this->ConditionCleaner($RCP->bho_id));
                $Mailer->setAttribute('TABLE',$RCP->bho_modul);
                $Mailer->setAttribute('NTNAME',$USER->org_ntuser);
                $Mailer->Value=mb_convert_encoding(htmlspecialchars_decode(KommunikationRecord::finder()->find('idtm_organisation=? AND kom_ismain=1 AND kom_type = 3',$RCP->idtm_organisation)->kom_information),$target_encoding);
                $Recipients->Elements[]=$Mailer;
                unset($Mailer);
            }

            $doc->Elements[]=$Recipients;

        return $doc;
    }

    private function ConditionCleaner($Condidtion){
        print_r(trim($Condidtion));
        $result = "";
        $mShort= array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mai',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Dez');
        if(preg_match("/\;/",$Condidtion)){
            $newvalues = array();
            $values = preg_split("/\;/",$Condidtion);
            foreach($values AS $value){
                switch ($value){
//                    case "CURRENT_MONTH_NAME":
//                        $newvalues[]= $mShort[date("n")];
//                        break;
//                    case "PREVIOUS_MONTH_NAME":
//                        $newvalues[]= $mShort[date("n",strtotime("now -1 month"))];
//                        break;
//                    case "CURRENT_MONTH":
//                        $newvalues[]= date("n");
//                        break;
//                    case "PREVIOUS_MONTH":
//                        $newvalues[]= date("n",strtotime("now -1 month"));
//                        break;
                    default:
                        $newvalues[]=$value;
                }
            }
            return implode(",",$newvalues);
        }else{
            switch (trim($Condidtion)){
//                    case "CURRENT_MONTH_NAME":
//                        $newvalues= $mShort[date("n")];
//                        break;
//                    case "PREVIOUS_MONTH_NAME":
//                        $newvalues= date("n",strtotime("now -1 month"));
//                        break;
//                    case "CURRENT_MONTH":
//                        $newvalues= date("n");
//                        break;
//                    case "PREVIOUS_MONTH":
//                        $newvalues= date("n",strtotime("now -1 month"));
//                        break;
                    default:
                        $newvalues=$Condidtion;
                }
            return $newvalues;
        }
    }

}

?>