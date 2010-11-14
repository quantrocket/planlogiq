<?php
class LogoutUser extends TPage
{
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param){
        $this->Application->getModule('auth')->logout();
        $url=$this->getRequest()->constructUrl('page',$this->Service->DefaultPage);
        $this->Response->redirect($url);
    }
}
?>