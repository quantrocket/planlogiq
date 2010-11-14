<?php

class CustomLayout extends TTemplateControl {

    public function onLoad($param) {
        if(!$this->page->IsPostBack && !$this->page->isCallback) {
        }
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