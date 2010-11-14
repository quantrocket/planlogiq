<?php

class TProtectUnsavedForm extends TControl {

    public function onPreRender($param) {
        parent::onPreRender($param);
        if($this->getPage()->getClientScript()->isEndScriptRegistered('protect_unsave_form') == false) {
            $this->getPage()->getClientScript()->registerEndScript('protect_unsave_form', $this->getJavaScriptCode());
        }
    }

    public function getMessage() {
        return $this->getViewState('Message', 'All changes would be lost!');
    }

    public function setMessage($value) {
        $this->setViewState('Message', $value, 'All changes would be lost!');
    }

    public function getJavaScriptCode() {

        return '

        var NON_ALERT_ON_SUBMIT_FORM_CLASS = "NON_ALERT_ON_SUBMIT_FORM"

        var form = $(document.forms[0])
        var formElements = form.getElements()

        window.onbeforeunload = function(evt) {
            var saving = form.hasClassName(NON_ALERT_ON_SUBMIT_FORM_CLASS)
            if(!saving && hasChanged()){
                if (typeof evt == "undefined") {
                    evt = window.event
                }
                evt.returnValue = "'.$this->getMessage().'"
            }
        }

        function disableLostDataProtection(){
            form.addClassName(NON_ALERT_ON_SUBMIT_FORM_CLASS)
        }

        form.onsubmit = function(evt) {
            disableLostDataProtection()
        }

        form.onreset = function(evt) {
            form.removeClassName(NON_ALERT_ON_SUBMIT_FORM_CLASS)
        }

        form.onchange = function(evt) {
            form.removeClassName(NON_ALERT_ON_SUBMIT_FORM_CLASS)
        }

        function hasChanged(){
            for(var i=0; i<formElements.length; i++){
                var formField = formElements[i]
                var formFieldType = formField.type
                if(formFieldType == "text" || formFieldType == "textarea"){
                    if(formField.value != formField.defaultValue){
                        return true
                    }
                } else if(formFieldType == "checkbox" || formFieldType == "radio"){
                    if(formField.checked != formField.defaultChecked){
                        return true
                    }
                }
            }
            return false
        }

        ';
    }
}

?>