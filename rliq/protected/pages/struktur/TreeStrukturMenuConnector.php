<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class TreeStrukturMenuConnector extends TPage{

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

        $StrukturElements = StrukturTypeRecord::finder()->findAll();
        foreach($StrukturElements As $Strukturtype){
            $ST=new TXmlElement('item');
            $ST->setAttribute('id','idta_struktur_type_'.$Strukturtype->idta_struktur_type);
            $ST->setAttribute('img','s'.$Strukturtype->idta_struktur_type.".gif");
            $ST->setAttribute('text',$Strukturtype->struktur_type_name);
            //hier muss die logik fuer die basiswerte aus den dimensionen hin...
            //hier hole ich mir die Dimensionsgruppen
            $fstsql = "SELECT stammdaten_group_name, (ta_stammdaten_group.idta_stammdaten_group) AS parent_idta_stammdaten_group FROM ta_stammdaten_group INNER JOIN tm_stammdaten ON tm_stammdaten.idta_stammdaten_group = ta_stammdaten_group.idta_stammdaten_group WHERE idta_struktur_type = ".$Strukturtype->idta_struktur_type." GROUP BY stammdaten_group_name, parent_idta_stammdaten_group";
            $BaseGroupElements = StammdatenGroupRecord::finder()->findAllBySQL($fstsql);
            foreach($BaseGroupElements AS $BaseGroupElement){
                $BGE=new TXmlElement('item');
                $BGE->setAttribute('id','idta_stammdaten_group_'.$BaseGroupElement->parent_idta_stammdaten_group);
                $BGE->setAttribute('img','s'.$Strukturtype->idta_struktur_type.".gif");
                $BGE->setAttribute('text',$BaseGroupElement->stammdaten_group_name);
                //zuerst hole ich alle Basiselement, die den aktuellen Strukturtypen haben
                $sql = "SELECT idtm_stammdaten,stammdaten_name, tm_stammdaten.idta_stammdaten_group FROM tm_stammdaten INNER JOIN ta_stammdaten_group ON tm_stammdaten.idta_stammdaten_group = ta_stammdaten_group.idta_stammdaten_group WHERE idta_struktur_type = ".$Strukturtype->idta_struktur_type." AND ta_stammdaten_group.idta_stammdaten_group = ".$BaseGroupElement->parent_idta_stammdaten_group." ORDER BY idta_stammdaten_group";
                $BaseElements = StammdatenRecord::finder()->findAllBySQL($sql);
                foreach($BaseElements AS $BaseElement){
                    $BE=new TXmlElement('item');
                    $BE->setAttribute('id','idtm_stammdaten_'.$BaseElement->idtm_stammdaten);
                    $BE->setAttribute('img','str'.$Strukturtype->idta_struktur_type.".gif");
                    $BE->setAttribute('text',$BaseElement->stammdaten_name);
                    $BGE->Elements[]=$BE;
                }
                $ST->Elements[]=$BGE;
            }
            $QVFile->Elements[]=$ST;
        }

        $doc->Elements[]=$QVFile;

        $CMdelete=new TXmlElement('item');
        $CMdelete->setAttribute('id',"delete_Element");
        $CMdelete->setAttribute('img',"minus.gif");
        $CMdelete->setAttribute('text',"delete element");

        $doc->Elements[]=$CMdelete;
        $docName = "temp";

        $this->getResponse()->appendHeader("Content-Type:".$header);
        $this->getResponse()->appendHeader("Content-Disposition:inline;filename=".$docName.'.'.$ext);
       
        $doc->saveToFile('php://output');
        exit;
    }

}

?>