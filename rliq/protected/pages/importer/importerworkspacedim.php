<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

Prado::using('Application.app_code.PFBackCalculator');

class importerworkspacedim extends TPage{

    private $ImportedValues = array();
    private $ImportedFields = array();

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param){
        parent::onLoad($param);
        date_default_timezone_set('Europe/Berlin');
    }   

    public function loadCSVFile($sender,$param){
        $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name."Dimport.tmp";
        if(is_file($tempUserFile)){
            $TempFile = fopen($tempUserFile,'r'); //hier oeffne ich die datei im readonly
            while (($mydata = fgetcsv($TempFile,0,",")) !== FALSE) {
                $this->importDimensions($mydata);
            }
            fclose($TempFile);
        }
    }

    public function importDimensions($data){
        if(is_array($data)){
            $tt_idta_variante = $data[0];
            $tt_per_month = $data[2];
            $tt_idta_feldfunktion = $data[4];
            $new_value = $data[6];
            //zuerst suchen wir den wechselknoten
            $WSql = "SELECT * FROM tm_struktur WHERE idtm_stammdaten = ".$data[3];
            $WechselKnoten = StrukturRecord::finder()->findBySQL($WSql);
            if(is_object($WechselKnoten)){
                $sql = "SELECT idtm_struktur FROM tm_struktur WHERE idtm_stammdaten = ".$data[5]." AND (struktur_lft BETWEEN ".$WechselKnoten->struktur_lft." AND ".$WechselKnoten->struktur_rgt.")";
                $tt_idtm_struktur = StrukturRecord::finder()->findBySQL($sql)->idtm_struktur;
                $ObjSaver = new PFBackCalculator();
                $ObjSaver->setVariante($tt_idta_variante);
                $ObjSaver->setStartPeriod($tt_per_month);
                $ObjSaver->setStartNode($tt_idtm_struktur);
                $arr_newValues[$tt_idta_feldfunktion]=$new_value;
                $ObjSaver->setNewValues($arr_newValues);
                $ObjSaver->run();
                unset($ObjSaver);
            }
            unset($WechselKnoten);
        }
    }

    public function fileUploaded($sender,$param){
        if($sender->getHasFile()){
            $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name."Dimport.tmp";
            if(is_file($tempUserFile)) {
                unlink($tempUserFile);
            }
            $sender->saveAs($tempUserFile,true);
            $this->loadCSVFile($sender,$param);
        }
        $this->UploadFileError->Text = $sender->ErrorCode;
    }

}

?>
