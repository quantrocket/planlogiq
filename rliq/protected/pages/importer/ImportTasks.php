<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ImportTasks extends TPage{

    private $ImportedValues = array();
    private $ImportedFields = array();

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param){
        parent::onLoad($param);
        date_default_timezone_set('Europe/Berlin');
        if(!$this->page->isPostBack && !$this->page->isCallback){
            $this->initPullDowns();
        }
    }

    private function initPullDowns(){

    }

    public function suggestOrganisation($sender,$param) {
    // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $mySQL = "SELECT idtm_organisation,org_name,org_vorname FROM tm_organisation WHERE org_name LIKE '%".$token."%'";
        $sender->DataSource=PFH::convertdbObjectSuggest(TActiveRecord::finder('OrganisationRecord')->findAllBySQL($mySQL),array('idtm_organisation','org_name','org_vorname'));
        $sender->dataBind();
    }

    public function suggestionSelectedOne($sender,$param) {
        $id=$sender->Suggestions->DataKeys[ $param->selectedIndex ];
        $this->responsible_idtm_organisation->Text=$id;
    }

    public function loadCSVFile($sender,$param){
        $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name.".tmp";
        if(is_file($tempUserFile)){
            $weiche = '';
            $TempFile = fopen($tempUserFile,'r'); //hier oeffne ich die datei im readonly
            while (($mydata = fgetcsv($TempFile,0,";")) !== FALSE) {
                $weiche = trim($mydata[4]);
                $this->importTASK($mydata);
                $this->ImportedValues[]=$mydata;
            }
            fclose($TempFile);
        }
    }

    public function importTASK($data){        
        $Aufgabe = new AufgabenRecord();
        $Aufgabe->auf_tdate = $data[0];
        $Aufgabe->auf_cdate = date($data[0]);
        $Aufgabe->auf_tag = $data[3];
        $Aufgabe->auf_name = 'AC Imp';
        $Aufgabe->auf_zeichen_eigen = utf8_encode($data[7]);
        $Aufgabe->auf_zeichen_fremd = '';
        $Aufgabe->idta_aufgaben_type = 1;
        $Aufgabe->idtm_organisation = $this->responsible_idtm_organisation->Text;
        $Aufgabe->auf_tabelle = "tm_organisation";
        if($data[2]*1 < 1000){
            $data[2] = '0'.$data[2];
        }
        $Organisation = OrganisationRecord::finder()->findByorg_fk_internal('0'.trim($data[2]));
        if(count($Organisation)==1){
            $Aufgabe->auf_id = $Organisation->idtm_organisation;
        }else{
            $Aufgabe->auf_id = $this->responsible_idtm_organisation->Text;
        }
        $Aufgabe->auf_done = $data[6];
        if($data[5]!=''){
            $Aufgabe->auf_ddate = $data[5];
        }else{
            $Aufgabe->auf_ddate = $data[0];
        }
        $html_string = "<b>".utf8_encode($data[8])."</b><br/>";
        $html_string .= utf8_encode($data[9])."<br/>";
        $html_string .= "<b>".utf8_encode($data[10])."</b><br/>";
        $html_string .= utf8_encode($data[11]);
        $Aufgabe->auf_beschreibung = $html_string;
        $Aufgabe->save();
        unset($Aufgabe,$Organisation);
    }

    public function fileUploaded($sender,$param){
        if($sender->getHasFile()){
            $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name.".tmp";
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
