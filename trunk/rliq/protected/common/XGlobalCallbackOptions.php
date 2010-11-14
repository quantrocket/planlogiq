<?php

/**
 * XGlobalCallbackOptions related class files.
 *
 * This control allows you to set-up global client side actions for ajax events, instead of applying them
 * to each control separately.
 *
 * HOW TO USE
 * This control works like {@link TCallbackOptions}, just specify the properties.
 * 
 *
 * @author Mauro Lewinzon <maurokun@gmail.com>
 * @link http://www.enigmastudio.com.ar/
 * @copyright Copyright &copy; 2007 EnigmaStudio
 * @license http://www.pradosoft.com/license/
 * @package Enigma.ActiveControls
 */

Prado::using('System.Web.UI.ActiveControls.TCallbackOptions');
Prado::using('System.Web.UI.ActiveControls.TCallbackClientSide');

class XGlobalCallbackOptions extends TCallbackOptions
{
    const JSFILE = 'XGlobalCallbackOptions.js';

    public function onInit($param)
    {
        parent::onInit($param);
        $script = "var xgco = new Object;\n";
        foreach(array('Loading', 'Complete', 'Exception', 'Failure', 'Interactive', 'Loaded', 'Success') as $method)
        {
            $getMethod = 'getOn'.$method;
            if($func = $this->getClientSide()->$getMethod())
                $script .= 'xgco.on'.$method.' = '.trim(str_replace('javascript:', '', $func)).";\n";
        }
        $csm = $this->getPage()->getClientScript();
        if(!$csm->isHeadScriptRegistered('xglobalcallbackoptions/init'))
            $csm->registerHeadScript('xglobalcallbackoptions/init', utf8_encode($script));
        if(!$csm->isScriptFileRegistered('xglobalcallbackoptions'))
            $csm->registerScriptFile('xglobalcallbackoptions', $this->publishAsset(self::JSFILE));
    }
}

?>