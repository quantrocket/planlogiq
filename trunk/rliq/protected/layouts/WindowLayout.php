<?php
class WindowLayout extends TTemplateControl {

    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->page->IsPostBack && !$this->page->isCallback) {
            
        }   
    }


}
?>