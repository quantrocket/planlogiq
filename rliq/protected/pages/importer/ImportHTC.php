<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ImportHTC extends TPage{

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

    private function initPullDowns(){

    }

    public function loadCSVFile($sender,$param){
            $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name.".tmp";
            if(is_file($tempUserFile)){
                $weiche = '';
                $TempFile = fopen($tempUserFile,'r'); //hier oeffne ich die datei im readonly
                while (($mydata = fgetcsv($TempFile,0,";")) !== FALSE) {
                    $this->importPARTEI($mydata);
                }
                fclose($TempFile);
            }
        }

        private function importPARTEI($data){
            $Organisation = OrganisationRecord::finder()->findorg_fk_internal("HTC".trim($data[0]));
            if(count($Organisation)!=1){
                $Organisation = new OrganisationRecord();
            }
            $Organisation->org_fk_internal = trim("HTC".trim($data[0]));
            $Organisation->parent_idtm_organisation = OrganisationRecord::finder()->findByorg_fk_internal('08500')->idtm_organisation;
            $Organisation->idta_organisation_type = 8;
            $Organisation->idtm_ressource = 1;
            $Organisation->org_anrede = utf8_encode($data[3]);
            $Organisation->org_briefanrede = utf8_encode("");
            $Organisation->org_name = utf8_encode($data[1]);
            $Organisation->org_vorname = utf8_encode($data[2]);
            $Organisation->org_name1 = utf8_encode("");
            $Organisation->org_matchkey = utf8_encode($data[1]." ".$data[2]);
            $Organisation->org_uid = "";
            $Organisation->org_aktiv = $data[19]=="ja"?1:0;
            $Organisation->save();

            //Bankkonto laden
            $Bankkonto = BankkontoRecord::finder()->findByidtm_organisation($Organisation->idtm_organisation);
            if(count($Bankkonto)!=1){
                $Bankkonto = new BankkontoRecord();
            }
            $Bankkonto->idtm_organisation = $Organisation->idtm_organisation;
            //$Bankkonto->bak_kontowortlaut = utf8_encode($data["38"]);
            $Bankkonto->bak_geldinstitut = utf8_encode("");
            $Bankkonto->bak_blz = "";
            $Bankkonto->bak_konto = "";
            $Bankkonto->bak_bic = "";
            $Bankkonto->bak_iban = "";
            $Bankkonto->save();

            //Kommunikation 1 Telefon
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 1 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 1;
            $Kommunikation->kom_information = $data[9];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 1 Fax2
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 2 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 2;
            $Kommunikation->kom_information = $data[10];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 1 Mail
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 3 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 3;
            $Kommunikation->kom_information = $data[11];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //adresse 1
            $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$Organisation->idtm_organisation." AND ta_adresse.adresse_ismain = 1";
            $Adresse = AdresseRecord::finder()->findBySQL($sql);
            if(count($Adresse)!=1){
                $Adresse = new AdresseRecord();
            }
            $Adresse->adresse_street = utf8_encode($data[6]);
            $Adresse->adresse_zip = $data[7];
            $Adresse->adresse_town = $data[8];
            $Adresse->adresse_ismain = 1;
            $Adresse->idtm_country = 1;
            $Adresse->save();

            $OrganisationAdresse = OrganisationAdresseRecord::finder()->find('idtm_organisation = ? AND idta_adresse = ?',$Organisation->idtm_organisation,$Adresse->idta_adresse);
            if(count($OrganisationAdresse)!=1){
                $OrganisationAdresse = new OrganisationAdresseRecord();
            }
            $OrganisationAdresse->idta_adresse = $Adresse->idta_adresse;
            $OrganisationAdresse->idtm_organisation = $Organisation->idtm_organisation;
            $OrganisationAdresse->save();
            
            unset($Organisation,$Bankkonto,$Kommunikation,$Adresse,$OrganisationAdresse,$sql);
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
