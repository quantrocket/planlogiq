<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class TreeOrganisationMenuConnector extends TPage{

    public function onPreInit($param) {
        parent::onPreInit($param);

        $docname = "tempXML";
        $ext = "xml";
        $header = "application/xml";
        
        $doc=new TXmlDocument('1.0','ISO-8859-1');
        $doc->TagName='menu';
        $doc->setAttribute('id',"0");

        $QVFile=new TXmlElement('item');
        $QVFile->setAttribute('id',"new_Element");
        $QVFile->setAttribute('img',"plus5.gif");
        $QVFile->setAttribute('text',"new element");

        $StrukturElements = OrganisationTypeRecord::finder()->findAll();
        foreach($StrukturElements As $Strukturtype){
            $ST=new TXmlElement('item');
            $ST->setAttribute('id',$Strukturtype->idta_organisation_type);
            $ST->setAttribute('img','org'.$Strukturtype->idta_organisation_type.".gif");
            $ST->setAttribute('text',utf8_encode($Strukturtype->org_type_name));
            //hier muss die logik fuer die basiswerte aus den dimensionen hin...
            //hier hole ich mir die Dimensionsgruppen
            $QVFile->Elements[]=$ST;
        }

        $doc->Elements[]=$QVFile;

        $CMdelete=new TXmlElement('item');
        $CMdelete->setAttribute('id',"delete_Element");
        $CMdelete->setAttribute('img',"minus.gif");
        $CMdelete->setAttribute('text',"delete element");

        $doc->Elements[]=$CMdelete;

        $this->getResponse()->appendHeader("Content-Type:".$header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$docName.'.'.$ext);
       
        $doc->saveToFile('php://output');
        exit;
    }

}

?>