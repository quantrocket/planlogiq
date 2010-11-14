<?php

/**
 * PFDHTMLXWindows class file
 *
 * @author Philipp Frenzel inspired by AXListMenu
 * <com:PFDHTMLXWindows Visible="true">
 * <com:PFDHTMLXWindowsItem Object="plTasks" Text=<%[ Tasks ]%> />
    <com:PFDHTMLXWindowsItem Object="plTermine" Text=<%[ Dates ]%> />
   </com:PFDHTMLXWindows>
 */

class PFDHTMLXWindows extends TControl{

    public function getCssClass() { return $this->getViewState('CssClass',''); }
    public function setCssClass($value) { $this->setViewState('CssClass',TPropertyValue::ensureString($value),''); }

    public function onPreRender($writer) {
        //parent::onPreRender($writer);
        $this->registerClientScripts();
    }

    protected function registerClientScripts() {
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxwindows','../rliq/protected/3rdParty/dhtmlxWindows/codebase/dhtmlxwindows.css');
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxwindows_dhx_skyblue','../rliq/protected/3rdParty/dhtmlxWindows/codebase/skins/dhtmlxwindows_dhx_skyblue.css');

        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxcommon',$this->publishAsset("../3rdParty/dhtmlxWindows/codebase/dhtmlxcommon.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxwindows',$this->publishAsset("../3rdParty/dhtmlxWindows/codebase/dhtmlxwindows.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxcontainer',$this->publishAsset("../3rdParty/dhtmlxWindows/codebase/dhtmlxcontainer.js"));
    }

    public function render($writer) {
        $SammelString = "";
        if ($this->getVisible()){

            foreach($this->getControls() as $item){
                if ($item instanceof PFDHTMLXWindowsItem){
                    $SammelString .= $item->render($writer);
                }
            }
            $js= <<< EOD

<div id="winVP" style="position: relative; height: 500px; border: #cecece 1px solid; margin: 5px;"></div>

<script type="text/javascript" charset="UTF-8">

dhxWins = new dhtmlXWindows();
dhxWins.enableAutoViewport(false);
dhxWins.attachViewportTo("winVP");
dhxWins.setImagePath("../../codebase/imgs/");

$SammelString

</script>
EOD;

            $writer->writeLine($js);
        }
    }
    
}

class PFDHTMLXWindowsItem extends TControl{

    public function getText(){ return $this->getViewState('Text',''); }
    public function setText($value){ $this->setViewState('Text',TPropertyValue::ensureString($value),''); }

    public function getObject() { return $this->getViewState('Object',''); }
    public function setObject($value) { $this->setViewState('Object',TPropertyValue::ensureString($value),''); }

    public function render($writer) {
        if($this->getVisible()){
            $WindowLabel = $this->getText();
            $WindowObject = $this->getObject();
            $js= <<< EOD

dhx$WindowObject = dhxWins.createWindow("ww$WindowObject", 10, 20, 250, 250);
dhx$WindowObject.setText("$WindowLabel");
dhx$WindowObject.button("close").disable();
dhx$WindowObject.attachObject('$WindowObject',true);

EOD;
            return $js;
            //$writer->writeLine($js);
        }
    }
}

?>
