<?php
class MainLayoutTree extends TTemplateControl {

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            if(!$this->User->IsGuest) {
                $this->UserLevel->Text=$this->User->Roles[0];
            }
        }
    }

    public function mainMenuAction($sender,$param){
        $theObjectContainingParameters = $param->CallbackParameter;
        $url=$this->getRequest()->constructUrl('page',$theObjectContainingParameters->page);
        $this->Response->redirect($url);
    }

    /**
     * Logs out a user.
     * This method responds to the "logout" button's OnClick event.
     * @param mixed event sender
     * @param mixed event parameter
     */
    public function logoutButtonClicked($sender,$param) {
        $this->Application->getModule('auth')->logout();
        $url=$this->getRequest()->constructUrl('page',$this->Service->DefaultPage);
        $this->Response->redirect($url);
    }

    /**
     * Get the localized current culture name.
     * @return string localized curreny culture name.
     */
    public function getCurrentCulture() {
        $ccl = $this->getApplication()->getGlobalization()->getCulture();
        return $ccl;
    }

}
?>