<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ImportImmo extends TPage{

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
                while (($mydata = fgetcsv($TempFile,0,"\t")) !== FALSE) {
                    $weiche = trim($mydata[4]);
                    switch($weiche){
                        case 'OBJ':
                            $this->importOBJ($mydata);                            
                            break;
                        case 'OBVS':
                            $this->importOBJART($mydata);
                            break;
                        case 'MI':
                            $this->importMI($mydata);
                            break;
                        case 'BE':
                            //$this->importBE($mydata);
                            break;
                        case 'PE':
                            $this->importPARTEI($mydata);
                            break;
                        case 'PEOB':
                            //verhaeltnis von eigentuemer zu objekt
                            //$this->importPARTEIRELATIONOWNEROBJEKT($mydata);
                            break;
                        default:
                            break;
                    }
                    $this->ImportedValues[]=$mydata;
                }
                fclose($TempFile);
            }
        }

        private function importOBJART($data){
            $Organisation = OrganisationRecord::finder()->findByorg_fk_internal($data[6]);
            if(count($Organisation)==1){
                switch ($data[8]){
                    case 'WEG':
                        $Organisation->idta_organisation_art = 3;
                        break;
                    default:
                        $Organisation->idta_organisation_art = 4;
                }                
                $Organisation->save();
            }
            unset($Organisation);
        }

        private function importBE($data){
            $sql="SELECT tm_organisation.* FROM tm_organisation ";
            $sql.="INNER JOIN tm_organisation p_tm_organisation ON tm_organisation.parent_idtm_organisation = p_tm_organisation.idtm_organisation ";
            $sql.="WHERE p_tm_organisation.org_fk_internal = '".trim($data[5])."' AND tm_organisation.org_fk_internal = '".trim($data[7])."-".trim($data[8])."'";
            $Organisation = OrganisationRecord::finder()->findBySQL($sql);
            if(count($Organisation)!=1){
                $Organisation = new OrganisationRecord();
            }
            $Organisation->org_fk_internal = trim($data[7])."-".trim($data[8]);
            $Organisation->parent_idtm_organisation = OrganisationRecord::finder()->findByorg_fk_internal($data[6])->idtm_organisation;
            $Organisation->idta_organisation_type = 6;
            $Organisation->idtm_ressource = 1;
            $Organisation->org_anrede = utf8_encode($data["10"]);
            $Organisation->org_briefanrede = utf8_encode($data["16"]);
            $Organisation->org_name = utf8_encode($data["11"]);
            $Organisation->org_name1 = utf8_encode($data["24"]);
            $Organisation->org_matchkey = utf8_encode($data["9"]);
            $Organisation->org_uid = $data["44"];
            $Organisation->save();

            unset($Organisation,$Bankkonto,$Kommunikation,$Adresse,$OrganisationAdresse,$sql);
        }

        private function importMI($data){
            $sql="SELECT tm_organisation.* FROM tm_organisation ";
            $sql.="INNER JOIN tm_organisation p_tm_organisation ON tm_organisation.parent_idtm_organisation = p_tm_organisation.idtm_organisation ";
            $sql.="WHERE p_tm_organisation.org_fk_internal = '".trim($data[6])."' AND tm_organisation.org_fk_internal = '".trim($data[7])."-".trim($data[8])."'";
            $Organisation = OrganisationRecord::finder()->findBySQL($sql);
            if(count($Organisation)!=1){
                $Organisation = new OrganisationRecord();
            }
            $Organisation->org_fk_internal = trim($data[7])."-".trim($data[8]);
            $Organisation->parent_idtm_organisation = OrganisationRecord::finder()->findByorg_fk_internal(trim($data[6]))->idtm_organisation;
            $Organisation->idta_organisation_type = 5;
            $Organisation->idtm_ressource = 1;
            $Organisation->org_anrede = utf8_encode($data["10"]);
            $Organisation->org_briefanrede = utf8_encode($data["16"]);
            $Organisation->org_name = utf8_encode($data["11"]);
            $Organisation->org_name1 = utf8_encode($data["24"]);
            $Organisation->org_matchkey = utf8_encode($data["9"]);
            $Organisation->org_uid = $data["44"];
            $Organisation->org_einzugsdatum = substr($data["36"],0,4).'-'.substr($data["36"],4,2).'-'.substr($data["36"],6,2);
            $Organisation->org_auszugsdatum = substr($data["37"],0,4).'-'.substr($data["37"],4,2).'-'.substr($data["37"],6,2);
            $compareDateBrutto = new DateTime();
            $compareDate = $compareDateBrutto->format("Ymd");
            if(substr($data["37"],0,8)>$compareDate){
                $Organisation->org_aktiv = 1;
            }else{
                $Organisation->org_aktiv = 0;
            }
            $Organisation->save();

            //Bankkonto laden
            $Bankkonto = BankkontoRecord::finder()->findByidtm_organisation($Organisation->idtm_organisation);
            if(count($Bankkonto)!=1){
                $Bankkonto = new BankkontoRecord();
            }
            $Bankkonto->idtm_organisation = $Organisation->idtm_organisation;
            $Bankkonto->bak_kontowortlaut = utf8_encode($data["38"]);
            $Bankkonto->bak_geldinstitut = utf8_encode($data["39"]);
            $Bankkonto->bak_blz = $data["40"];
            $Bankkonto->bak_konto = $data["41"];;
            $Bankkonto->bak_bic = $data["42"];;
            $Bankkonto->bak_iban = $data["43"];;
            $Bankkonto->save();

            //Kommunikation 1 Telefon
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 1 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 1;
            $Kommunikation->kom_information = $data[18].' '.$data[19];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 1 Fax2
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 2 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 2;
            $Kommunikation->kom_information = $data[20];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 1 Mail
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 3 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 3;
            $Kommunikation->kom_information = $data[21];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 2 Telefon
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 1 AND kom_ismain = 0 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 1;
            $Kommunikation->kom_information = $data[31];
            $Kommunikation->kom_ismain = 0;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 2 Fax
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 2 AND kom_ismain = 0 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 2;
            $Kommunikation->kom_information = $data[33];
            $Kommunikation->kom_ismain = 0;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 2 Mail
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 3 AND kom_ismain = 0 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 3;
            $Kommunikation->kom_information = $data[34];
            $Kommunikation->kom_ismain = 0;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //adresse 1
            $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$Organisation->idtm_organisation." AND ta_adresse.adresse_ismain = 1";
            $Adresse = AdresseRecord::finder()->findBySQL($sql);
            if(count($Adresse)!=1){
                $Adresse = new AdresseRecord();
            }
            $Adresse->adresse_street = utf8_encode($data["13"]);
            $Adresse->adresse_town = $data["14"];
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

            //adresse 2
            $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$Organisation->idtm_organisation." AND ta_adresse.adresse_ismain = 0";
            $Adresse = AdresseRecord::finder()->findBySQL($sql);
            if(count($Adresse)!=1){
                $Adresse = new AdresseRecord();
            }
            $Adresse->adresse_street = utf8_encode($data["26"]);
            $Adresse->adresse_town = $data["27"];
            $Adresse->adresse_ismain = 0;
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

        private function importPARTEI($data){
            $Organisation = OrganisationRecord::finder()->findorg_fk_internal(trim($data[7]));
            if(count($Organisation)!=1){
                $Organisation = new OrganisationRecord();
            }
            $Organisation->org_fk_internal = trim($data[7]);
            $Organisation->parent_idtm_organisation = OrganisationRecord::finder()->findByorg_fk_internal('Partei')->idtm_organisation;
            $Organisation->idta_organisation_type = 8;
            $Organisation->idtm_ressource = 1;
            $Organisation->org_anrede = utf8_encode($data["11"]);
            $Organisation->org_briefanrede = utf8_encode($data["17"]);
            $Organisation->org_name = utf8_encode($data["12"]);
            $Organisation->org_name1 = utf8_encode($data["13"]);
            $Organisation->org_matchkey = utf8_encode($data["10"]);
            $Organisation->org_uid = $data["29"];
            $Organisation->org_aktiv = 1;
            $Organisation->save();

            //Bankkonto laden
            $Bankkonto = BankkontoRecord::finder()->findByidtm_organisation($Organisation->idtm_organisation);
            if(count($Bankkonto)!=1){
                $Bankkonto = new BankkontoRecord();
            }
            $Bankkonto->idtm_organisation = $Organisation->idtm_organisation;
            //$Bankkonto->bak_kontowortlaut = utf8_encode($data["38"]);
            $Bankkonto->bak_geldinstitut = utf8_encode($data["24"]);
            $Bankkonto->bak_blz = $data["25"];
            $Bankkonto->bak_konto = $data["26"];;
            $Bankkonto->bak_bic = $data["27"];;
            $Bankkonto->bak_iban = $data["28"];;
            $Bankkonto->save();

            //Kommunikation 1 Telefon
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 1 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 1;
            $Kommunikation->kom_information = $data[18];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 1 Fax2
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 2 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 2;
            $Kommunikation->kom_information = $data[20];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 1 Mail
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 3 AND kom_ismain = 1 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 3;
            $Kommunikation->kom_information = $data[21];
            $Kommunikation->kom_ismain = 1;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //Kommunikation 2 Telefon
            $Kommunikation = KommunikationRecord::finder()->find("kom_type = 1 AND kom_ismain = 0 AND idtm_organisation = ?",$Organisation->idtm_organisation);
            if(count($Kommunikation)!=1){
                $Kommunikation = new KommunikationRecord();
            }
            $Kommunikation->kom_type = 1;
            $Kommunikation->kom_information = $data[19];
            $Kommunikation->kom_ismain = 0;
            $Kommunikation->idtm_organisation = $Organisation->idtm_organisation;
            $Kommunikation->save();

            //adresse 1
            $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$Organisation->idtm_organisation." AND ta_adresse.adresse_ismain = 1";
            $Adresse = AdresseRecord::finder()->findBySQL($sql);
            if(count($Adresse)!=1){
                $Adresse = new AdresseRecord();
            }
            $Adresse->adresse_street = utf8_encode($data["14"]);
            $Adresse->adresse_town = $data["15"];
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

        private function importOBJ($data){            
            $Organisation = OrganisationRecord::finder()->findByorg_fk_internal(trim($data[6]));
            if(count($Organisation)!=1){
                $Organisation = new OrganisationRecord();
            }
            $Organisation->org_fk_internal = trim($data["6"]);
            $Organisation->parent_idtm_organisation = OrganisationRecord::finder()->findByorg_fk_internal('Objekt')->idtm_organisation;
            $Organisation->idtm_ressource = 1;
            $Organisation->idta_organisation_type = 7;
            $Organisation->org_name = utf8_encode($data["7"]);
            $Organisation->org_matchkey = $data["4"];
            $Organisation->org_uid = $data["18"];
            $Organisation->org_finanzamt = $data["19"];
            $Organisation->org_referat = $data["20"];
            $Organisation->org_gemeinde = $data["23"];
            $Organisation->org_katastragemeinde = $data["24"];
            $Organisation->org_grundstuecksnummer = $data["25"];
            $Organisation->org_einlagezahl = $data["26"];
            $Organisation->org_baujahr = $data["27"];
            $Organisation->org_wohnungen = $data["28"];
            $Organisation->org_aktiv = 1;
            $Organisation->save();

            $Bankkonto = BankkontoRecord::finder()->findByidtm_organisation($Organisation->idtm_organisation);
            if(count($Bankkonto)!=1){
                $Bankkonto = new BankkontoRecord();
            }
            $Bankkonto->idtm_organisation = $Organisation->idtm_organisation;
            $Bankkonto->bak_kontowortlaut = $data["10"];
            $Bankkonto->bak_geldinstitut = $data["11"];
            $Bankkonto->bak_blz = $data["12"];
            $Bankkonto->bak_konto = $data["13"];;
            $Bankkonto->bak_bic = $data["14"];;
            $Bankkonto->bak_iban = $data["15"];;
            $Bankkonto->save();

            $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$Organisation->idtm_organisation." AND ta_adresse.adresse_ismain = 1";
            $Adresse = AdresseRecord::finder()->findBySQL($sql);
            if(count($Adresse)!=1){
                $Adresse = new AdresseRecord();
            }
            $Adresse->adresse_street = utf8_encode($data["8"]);
            $Adresse->adresse_town = $data["9"];
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

            unset($Organisation,$Bankkonto,$Adresse,$OrganisationAdresse,$sql);
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
