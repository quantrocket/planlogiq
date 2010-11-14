<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class CommunicationMatrixXML extends TPage {

    private $idta_berichte;

    public function onPreInit($param) {
        parent::onPreInit($param);

        $docname = "tempXML";
        $ext = "xml";
        $header = "application/xml";

        $target_encoding = "UTF-8";

        if(!isset($_GET['idta_berichte'])) {
            $this->idta_berichte=0;
        }else {
            $this->idta_berichte=$_GET['idta_berichte'];
        }

        if($this->idta_berichte==0) {
            $myRecords = BerichteRecord::finder()->findAll();
            $doc=new TXmlDocument('1.0',$target_encoding);
            $doc->TagName='Reporting';
            foreach($myRecords As $SingleReport) {
                $Report = new TXmlElement('Reports');

                $QVFile=new TXmlElement('QlikViewFile');
                $QVFile->Value=mb_convert_encoding($SingleReport->ber_local_path,$target_encoding);
                $Report->Elements[]=$QVFile;

                $BerName=new TXmlElement('ReportName');
                $BerName->Value=mb_convert_encoding($SingleReport->ber_name,$target_encoding);
                $Report->Elements[]=$BerName;

                $BerID=new TXmlElement('ReportId');
                $BerID->Value=mb_convert_encoding($SingleReport->ber_id,$target_encoding);
                $Report->Elements[]=$BerID;

                $BerMailSub=new TXmlElement('MailSubject');
                $BerMailSub->Value=mb_convert_encoding($SingleReport->ber_mail_subject,$target_encoding);
                $Report->Elements[]=$BerMailSub;

                $BerMailBody=new TXmlElement('MailBody');
                $BerMailBody->Value=mb_convert_encoding(htmlspecialchars_decode($SingleReport->ber_mail_body),$target_encoding);
                $Report->Elements[]=$BerMailBody;

                $Recipients=new TXmlElement('Recipients');

                $RCPS = BerichteOrganisationRecord::finder()->findAllByidta_berichte($SingleReport->idta_berichte);
                //schleife für alle empfänger
                foreach($RCPS As $RCP) {
                    $USER = OrganisationRecord::finder()->findByPK($RCP->idtm_organisation);
                    $Mailer = new TXmlElement('Recipient');
                    $Mailer->setAttribute('ID',$RCP->bho_id);
                    $Mailer->setAttribute('TABLE',$RCP->bho_modul);
                    $Mailer->setAttribute('NTNAME',$USER->org_ntuser);                   
                    $Mailer->Value=mb_convert_encoding(htmlspecialchars_decode(KommunikationRecord::finder()->find('idtm_organisation=? AND kom_ismain=1 AND kom_type = 3',$RCP->idtm_organisation)->kom_information),$target_encoding);
                    $Recipients->Elements[]=$Mailer;
                    unset($Mailer);
                }

                $Report->Elements[]=$Recipients;
                $doc->Elements[]=$Report;
            }
        }else {
            $doc=new TXmlDocument('1.0','utf-8');

            $SingleReport = BerichteRecord::finder()->findByidta_berichte($this->idta_berichte);

            $doc->TagName='Reporting';
            $Report = new TXmlElement('Reports');

            $QVFile=new TXmlElement('QlikViewFile');
            $QVFile->Value=mb_convert_encoding($SingleReport->ber_local_path,$target_encoding);
            $Report->Elements[]=$QVFile;

            $BerName=new TXmlElement('ReportName');
            $BerName->Value=mb_convert_encoding($SingleReport->ber_name,$target_encoding);
            $Report->Elements[]=$BerName;

            $BerID=new TXmlElement('ReportId');
            $BerID->Value=mb_convert_encoding($SingleReport->ber_id,$target_encoding);
            $Report->Elements[]=$BerID;

            $BerMailSub=new TXmlElement('MailSubject');
            $BerMailSub->Value=mb_convert_encoding($SingleReport->ber_mail_subject,$target_encoding);
            $Report->Elements[]=$BerMailSub;

            $BerMailBody=new TXmlElement('MailBody');
            $BerMailBody->Value=mb_convert_encoding(htmlspecialchars_decode($SingleReport->ber_mail_body),$target_encoding);
            $Report->Elements[]=$BerMailBody;

            $Recipients=new TXmlElement('Recipients');

            $RCPS = BerichteOrganisationRecord::finder()->findAllByidta_berichte($SingleReport->idta_berichte);
            //schleife für alle empfänger
            foreach($RCPS As $RCP) {
                $USER = OrganisationRecord::finder()->findByPK($RCP->idtm_organisation);
                $Mailer = new TXmlElement('Recipient');
                $Mailer->setAttribute('ID',$RCP->bho_id);
                $Mailer->setAttribute('TABLE',$RCP->bho_modul);
                $Mailer->setAttribute('NTNAME',$USER->org_ntuser);
                $Mailer->Value=mb_convert_encoding(htmlspecialchars_decode(KommunikationRecord::finder()->find('idtm_organisation=? AND kom_ismain=1 AND kom_type = 3',$RCP->idtm_organisation)->kom_information),$target_encoding);
                $Recipients->Elements[]=$Mailer;
                unset($Mailer);
            }

            $Report->Elements[]=$Recipients;
            $doc->Elements[]=$Report;
        }



        # $query=new TXmlElement('Query');
        # $query->setAttribute('ID','xxxx');
        # $proc->Elements[]=$query;
        #
        # $attr=new TXmlElement('Attr');
        # $attr->setAttribute('Name','aaa');
        # $attr->Value='1';
        # $query->Elements[]=$attr;

        $this->getResponse()->appendHeader("Content-Type:".$header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$docName.'.'.$ext);

        $doc->saveToFile('php://output');
        exit;
    }

}

?>