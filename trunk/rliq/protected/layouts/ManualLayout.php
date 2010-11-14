<?php
class ManualLayout extends TTemplateControl{

    public function onLoad($param)
	{
            parent::onLoad($param);
            $this->UserLevel->Text=$this->User->Roles[0];
        }
	
	/**
     * Logs out a user.
     * This method responds to the "logout" button's OnClick event.
     * @param mixed event sender
     * @param mixed event parameter
     */
    public function logoutButtonClicked($sender,$param)
    {
        $this->Application->getModule('auth')->logout();
        $url=$this->getRequest()->constructUrl('page',$this->Service->DefaultPage);
        $this->Response->redirect($url);
    }

    /**
     * Get the localized current culture name.
     * @return string localized curreny culture name.
     */
    public function getCurrentCulture()
    {
        $culture = $this->getApplication()->getGlobalization()->getCulture();
        $cultureInfo = new CultureInfo($culture);
        return $cultureInfo->getNativeName();
    }

	
}
?>