<?php
class NewUser extends TPage {


    public function onPreInit($param){
            $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
            $this->setTheme($myTheme);
        }
        
    public function onLoad($param) {

        parent::onLoad($param);
            $sql = "SELECT idtm_user_role, user_role_name FROM tm_user_role WHERE user_role_name = 'Benutzer'";
            $data = PFH::convertdbObjectArray(UserRoleRecord::finder()->findAllBySql($sql),array("idtm_user_role","user_role_name"));
            $this->Role->DataSource=$data;
            $this->Role->dataBind();
        }

    /**
     * Checks whether the username exists in the database.
     * This method responds to the OnServerValidate event of username's custom validator.
     * @param mixed event sender
     * @param mixed event parameter
     */
    public function checkUsername($sender,$param) {
    // valid if the username is not found in the database
        $param->IsValid=UserRecord::finder()->findByuser_name($this->Username->Text)===null;
    }

    public function resetButtonClicked($sender,$param){
        $this->Username->Text = "";
        $this->Password->Text = "";
        $this->Email->Text = "";
        $this->FirstName->Text = "";
        $this->LastName->Text = "";
    }

    /**
     * Creates a new user account if all inputs are valid.
     * This method responds to the OnClick event of the "create" button.
     * @param mixed event sender
     * @param mixed event parameter
     */
    public function createButtonClicked($sender,$param) {
        if($this->IsValid)  // when all validations succeed
        {
        // populates a UserRecord object with user inputs
            $userRecord=new UserRecord;
            $userRecord->user_username=$this->Username->Text;
            $userRecord->user_password=$this->Password->Text;
            $userRecord->user_mail=$this->Email->Text;
            $userRecord->idtm_user_role=(int)$this->Role->SelectedValue;
            $userRecord->user_vorname=$this->FirstName->Text;
            $userRecord->user_name=$this->LastName->Text;
            // saves to the database via Active Record mechanism
            $userRecord->save();

            $parteiRecord = new ParteiRecord();
            $parteiRecord->idtm_user = $userRecord->idtm_user;
            $parteiRecord->partei_name = $this->FirstName->Text . " " . $this->LastName->Text;
            //save the partei
            $parteiRecord->save();

            $adressRecord = new AdresseRecord();
            $adressRecord->adresse_street = $this->adresse_street->Text;
            $adressRecord->adresse_zip = $this->adresse_zip->Text;
            $adressRecord->adresse_town = $this->adresse_town->Text;
            $adressRecord->idtm_country = 1;

            //lets add the coordinates
            $myGTranslator = new GoogleAdressTranslator();
            $mapparams=$myGTranslator->getLatAndLong(implode(",",array($this->adresse_street->Text,$this->adresse_town->Text)));
            $myLatandLong = explode(",",$mapparams);

            //here we check, if the coordinates have been found
            if($myLatandLong[1]!=0) {

                $adressRecord->adresse_lat = $myLatandLong[1];
                $adressRecord->adresse_long = $myLatandLong[0];

            }
            else {
                $adressRecord->adresse_lat = "48.189950";
                $adressRecord->adresse_long = "16.377319";
            }

            $adressRecord->save();

            $parteiadresseRecord = new ParteiAdresseRecord;
            $parteiadresseRecord->idta_partei = $parteiRecord->idta_partei;
            $parteiadresseRecord->idta_adresse = $adressRecord->idta_adresse;
            //save adress to partei
            $parteiadresseRecord->save();

            // redirects the browser to the homepage
            $this->Response->redirect($this->Service->DefaultPageUrl);
        }
    }
}
?>