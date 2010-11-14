<?php
class User extends TPage{

    /**
     *
     * @APP_MODULES <array> Contains all the modules, that are available in the application
     */

    private $APP_MODULES = array("mod_planung",
        "mod_risiko",
        "mod_organisation",
        "mod_zeiterfassung","mod_zeiterfassung_reports",
        "mod_ziele",
        "mod_activity",
        "mod_changemanagement",
        "mod_process",
        "mod_protokoll",
        "mod_termine",
        "mod_theme"); //a list of existing modules

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param){
        parent::onLoad($param);
        if(!$this->isPostBack && !$this->isCallback){
            $this->bind_lstUser();
        }
    }

    public function createNewUser($param,$sender){
        $this->Response->redirect($this->getRequest()->constructUrl('page','user.newuser'));
    }

    public function loadUserSettings($sender,$param){
        $item=$param->Item; //the current datarow

        $this->UserSettingsUser->Text = $item->lst_user_username->Text;
        $this->idtm_user->Text = $item->lst_idtm_user->Text;

        foreach($this->APP_MODULES AS $APP_MODULE){
            $CheckerRecord = BerechtigungRecord::finder()->find("idtm_user = ? AND xx_modul = ?",$this->idtm_user->Text,$APP_MODULE);
            if(!count($CheckerRecord)==1){
                $BerechtigungsRecord = new BerechtigungRecord();
                $BerechtigungsRecord->idtm_user = $item->lst_idtm_user->Text;
                $BerechtigungsRecord->xx_modul = $APP_MODULE;
                $BerechtigungsRecord->xx_read = 0;
                $BerechtigungsRecord->xx_write = 0;
                $BerechtigungsRecord->xx_create = 0;
                $BerechtigungsRecord->xx_delete = 0;
                $BerechtigungsRecord->save();
            }
        }
        $this->bind_lstUserSettings();
        //$this->MyTabs->ActiveView="1";
    }

    public function bind_lstUserSettings(){
        $this->lstUserSettings->DataSource = BerechtigungRecord::finder()->findAllByidtm_user($this->idtm_user->Text);
        $this->lstUserSettings->dataBind();
    }

    public function bind_lstUser() {
        $this->lstUser->DataSource=UserRecord::finder()->findAll();
        $this->lstUser->dataBind();
    }

    public function lstUser_pageIndexChanged($sender,$param){
        $this->lstUser->CurrentPageIndex = $param->NewPageIndex;
        $this->bind_lstUser();
    }

    public function lstUserSettingsEdit($sender,$param){
        $this->lstUserSettings->EditItemIndex=$param->Item->ItemIndex;
        $this->bind_lstUserSettings();
    }

    public function lstUserSettings_pageIndexChanged($sender,$param){
        $this->lstUserSettings->CurrentPageIndex = $param->NewPageIndex;
        $this->bind_lstUserSettings();
    }

    public function lstUserSettingsCancel($sender,$param)
    {
        $this->lstUserSettings->EditItemIndex=-1;
        $this->bind_lstUserSettings();
    }

    public function lstUserSettingsSave($sender,$param){
        $item=$param->Item;

        $Record = BerechtigungRecord::finder()->findByPK($this->lstUserSettings->DataKeys[$item->ItemIndex]);
        $Record->xx_id = $item->lst_xx_id->TextBox->Text;
        $Record->xx_read = $item->lst_xx_read->ATB_lst_xx_read->Checked?1:0;
        $Record->xx_write = $item->lst_xx_write->ATB_lst_xx_write->Checked?1:0;
        $Record->xx_create = $item->lst_xx_create->ATB_lst_xx_create->Checked?1:0;
        $Record->xx_delete = $item->lst_xx_delete->ATB_lst_xx_delete->Checked?1:0;
        $Record->save();

        $this->lstUserSettings->EditItemIndex=-1;
        $this->bind_lstUserSettings();
    }


}
?>