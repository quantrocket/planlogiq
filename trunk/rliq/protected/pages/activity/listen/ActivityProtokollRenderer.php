<?php

Prado::using('Application.3rdParty.wikiParser.WikiParser');

class ActivityProtokollRenderer extends TDataListItemRenderer{

    public function initBrowser(){
        $this->DMSFileBrowser->loadDirectory();
    }

    public function initPrtAufgaben(){
        $this->prtAufgabenContainer->initParameters();
        $this->prtAufgabenContainer->bindListPrtAufgaben();
        $this->prtAufgabenContainer->__destruct();
    }

    public function initComments(){
        $this->KommentarContainerNOP->initParameters();
        $this->KommentarContainerNOP->bindListComments();
        $this->KommentarContainerNOP->__destruct();
    }

    function wiki2html($text){
        $myWikiParser = new WikiParser();
        $text = $myWikiParser->parse($text);
        return $text;
    }
	
}
?>