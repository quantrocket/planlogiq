<?php
class UserRole extends TPage
{
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function buttonClicked($sender,$param)
    {
        // $sender refers to the button component
        //$sender->Text="Hello World!";
    }
}
?>