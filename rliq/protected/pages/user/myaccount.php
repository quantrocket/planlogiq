<?php
class MyAccount extends TPage
{
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    /**
     * Initializes the inputs with existing user data.
     * This method is invoked by the framework when the page is being initialized.
     * @param mixed event parameter
     */
    public function onInit($param){
        parent::onInit($param);
        
        if(!$this->IsPostBack && !$this->isCallback)  // if the page is initially requested
        {
            $sql = "SELECT idtm_user_role, user_role_name FROM tm_user_role";
            $this->User->isInRole('Administrator')?'':$sql.= " WHERE user_role_name = 'Benutzer'";
            $data = PFH::convertdbObjectArray(UserRoleRecord::finder()->findAllBySql($sql),array("idtm_user_role","user_role_name"));
            $this->Role->DataSource=$data;
            $this->Role->dataBind();

            // Retrieves the existing user data. This is equivalent to:
            $userRecord=$this->getUserRecord();
            //$userRecord=$this->UserRecord;
 
            // Populates the input controls with the existing user data
            $this->Username->Text=$userRecord->user_username;
            $this->Email->Text=$userRecord->user_mail;
            $this->Role->SelectedValue=$userRecord->idtm_user_role;
            $this->FirstName->Text=$userRecord->user_vorname;
            $this->LastName->Text=$userRecord->user_name;

            $parteiRecord = ParteiRecord::finder()->findBy_idtm_user($userRecord->idtm_user);
            $this->idta_partei->Text=$parteiRecord->idta_partei;
            $this->bind_lstAdress();
        }
    }

    public function bind_lstAdress() {
        $criteria_p=new TActiveRecordCriteria;
        $criteria_p->Condition = 'idta_partei = :idta_partei';
        $criteria_p->Parameters[':idta_partei'] = $this->idta_partei->Text;

        $templisteadresse = ParteiAdresseRecord::finder()->findAll($criteria_p);
        $listeadresse = (array)$templisteadresse;
        $mydata=array();

        foreach($listeadresse as $walker) {
            $conditionx = new TActiveRecordCriteria;
            $conditionx->Condition = 'idta_adresse = :idta_adresse';
            $conditionx->Parameters[':idta_adresse'] = $walker->idta_adresse;
            array_push($mydata,AdresseRecord::finder()->find($conditionx));
        }

        //print_r($mydata);
        $this->lstAdress->DataSource=$mydata;
        $this->lstAdress->dataBind();
    }
 
    /**
     * Saves the user account if all inputs are valid.
     * This method responds to the OnClick event of the "save" button.
     * @param mixed event sender
     * @param mixed event parameter
     */
    public function saveButtonClicked($sender,$param)
    {
        if($this->IsValid)  // when all validations succeed
        {
            // Retrieves the existing user data. This is equivalent to:
            $userRecord=$this->UserRecord;
 
            // Fetches the input data
            $userRecord->user_username=$this->Username->Text;
            // update password when the input is not empty
            if(!empty($this->Password->Text))
                $userRecord->user_password=$this->Password->Text;
            $userRecord->user_mail=$this->Email->Text;
            // update the role if the current user is an administrator
            if($this->User->IsAdmin)
                $userRecord->idtm_user_role=(int)$this->Role->SelectedValue;
            $userRecord->user_vorname=$this->FirstName->Text;
            $userRecord->user_name=$this->LastName->Text;
 
            // saves to the database via Active Record mechanism
            $userRecord->save();
 
            // redirects the browser to the homepage
            $this->Response->redirect($this->Service->DefaultPageUrl);
        }
    }
 
    /**
     * Returns the user data to be editted.
     * @return UserRecord the user data to be editted.
     * @throws THttpException if the user data is not found.
     */
    protected function getUserRecord()
    {
        // the user to be editted is the currently logged-in user
        $username=$this->User->Name;
        // if the 'username' GET var is not empty and the current user
        // is an administrator, we use the GET var value instead.
        if($this->User->IsAdmin && $this->Request['user_username']!==null)
            $username=$this->Request['user_username'];
 
        // use Active Record to look for the specified username
        $userRecord=UserRecord::finder()->find('user_username = ?',$username);
        if(!($userRecord instanceof UserRecord))
            throw new THttpException(500,'Username is invalid.');
        return $userRecord;
    }

    public function lstAdressEdit($sender,$param){
        $this->lstAdress->EditItemIndex=$param->Item->ItemIndex;
        $this->bind_lstAdress();
    }

    public function lstAdress_pageIndexChanged($sender,$param){
        $this->lstAdress->CurrentPageIndex = $param->NewPageIndex;
        $this->bind_lstAdress();
    }

    public function lstAdressCancel($sender,$param)
    {
        $this->lstAdress->EditItemIndex=-1;
        $this->bind_lstAdress();
    }

    public function lstAdressSave($sender,$param){
        $item=$param->Item;

        $Record = AdresseRecord::finder()->findByPK($this->lstAdress->DataKeys[$item->ItemIndex]);
        $Record->adresse_street = $item->lst_adresse_street->TextBox->Text;
        $Record->adresse_zip = $item->lst_adresse_zip->TextBox->Text;
        $Record->adresse_town = $item->lst_adresse_town->TextBox->Text;
        $Record->save();

        $this->lstAdress->EditItemIndex=-1;
        $this->bind_lstAdress();
    }
}
?>