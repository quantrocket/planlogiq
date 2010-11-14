<?php

class rfcworkspace extends TPage
{	

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    
    public function onLoad($param){
		parent::onLoad($param);
	}
}
?>