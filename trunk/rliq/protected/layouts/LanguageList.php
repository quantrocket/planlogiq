<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *
 * Philipp Frenzel
 *
 */

class LanguageList extends TTemplateControl
{

    public function __construct()
    {
        if(isset($this->Application->session['current_language'])){
             $globalization = $this->getApplication()->getGlobalization();
             $globalization->Culture = $this->Application->session['current_language'];
	}
    }

    public function setLang($sender,$param)
        {
                $this->Application->session['current_language'] = $sender->CommandParameter;
                $globalization = $this->getApplication()->getGlobalization();
                $globalization->setCulture($sender->CommandParameter);
                //$this->Response->redirect($this->Request->Url->Uri);
	}


}


?>
