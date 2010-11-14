<?php
class TextReportLayout extends TTemplateControl{

	public function onLoad($param)
	{
		parent::onLoad($param);
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

	
}
?>