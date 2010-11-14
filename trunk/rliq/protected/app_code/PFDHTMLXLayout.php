<?php

/** Please take care, to add the client script inside the .page or .tpl file...
 * Philipp Frenzel - pf@com-x-cha.com
 * <com:TClientScriptLoader PackagePath="Application.*.tafelTree" PackageScripts="Tree" />
 */

class PFDHTMLXLayout extends TWebControl {

    public function onPreRender($writer) {
        //parent::onPreRender($writer);
        $this->registerClientScripts();
    }

    protected function addAttributesToRender($writer) {
        $writer->addAttribute('id',$this->getClientID());
        parent::addAttributesToRender($writer);
    }

//    public function getID() {
//        $id = $this->getViewState('ID', '');
//        return $id;
//    }

    protected function registerClientScripts() {
        $id=$this->getClientID();

        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxlayout','../rliq/protected/3rdParty/dhtmlxLayout/codebase/dhtmlxlayout.css');
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxlayout_dhx_web','../rliq/protected/3rdParty/dhtmlxLayout/codebase/skins/dhtmlxlayout_dhx_web.css');
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxtabbar','../rliq/protected/3rdParty/dhtmlxTabbar/codebase/dhtmlxtabbar.css');
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxscheduler','../rliq/protected/3rdParty/dhtmlxScheduler/codebase/dhtmlxscheduler.css');
        
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxcommon',$this->publishAsset("../3rdParty/dhtmlxLayout/codebase/dhtmlxcommon.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxlayout',$this->publishAsset("../3rdParty/dhtmlxLayout/codebase/dhtmlxlayout.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxcontainer',$this->publishAsset("../3rdParty/dhtmlxLayout/codebase/dhtmlxcontainer.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtabbar',$this->publishAsset("../3rdParty/dhtmlxTabbar/codebase/dhtmlxtabbar.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxscheduler',$this->publishAsset("../3rdParty/dhtmlxScheduler/codebase/dhtmlxscheduler.js"));
        
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxmenu_dhx_web','../rliq/protected/3rdParty/dhtmlxMenu/codebase/skins/dhtmlxmenu_dhx_web.css');
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxmenu',$this->publishAsset("../3rdParty/dhtmlxMenu/codebase/dhtmlxmenu.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxmenu_ext',$this->publishAsset("../3rdParty/dhtmlxMenu/codebase/ext/dhtmlxmenu_ext.js"));
    }

    public function renderContents($writer) {
        if(!$this->getEnabled())
            return;

        $MainMenuConnector = $this->parent->getRequest()->constructUrl('page','navigation.MainMenuConnector');
        $actionScript = $this->parent->MyMenuCallback->ActiveControl->Javascript;

        $js= <<< EOD

<script type="text/javascript" charset="UTF-8">

    var dhxLayout;
    var dhxMainMenu;
    var dhxTabbar;
        
    //function doOnLoad() {
        dhxLayout = new dhtmlXLayoutObject("dhtmlxLayout", "4I");

        dhxLayout.setSkin('dhx_web');
        dhxLayout.cells("a").setHeight(48);
        dhxLayout.cells("a").attachObject("header");
        dhxLayout.cells("a").hideHeader();
        dhxMainMenu = dhxLayout.cells("a").attachMenu();
        //dhxMainMenu.setImagePath("/rliq/themes/basic/imgs/");
        dhxMainMenu.setSkin('dhx_web');
        dhxMainMenu.setIconsPath('/rliq/themes/basic/imgs/');
        dhxMainMenu.attachEvent("onClick",openMenuUrl);
        dhxMainMenu.loadXML("$MainMenuConnector");

        dhxLayout.cells("b").attachObject("sidebar");
        dhxLayout.cells("b").setWidth(250);
        dhxLayout.cells("b").setText("Navigation");
        
        dhxTabbar = dhxLayout.cells("c").attachTabbar();
        dhxTabbar.setSkin('dhx_web');
        dhxTabbar.setImagePath("/rliq/protected/3rdParty/dhtmlxTabbar/codebase/imgs/");        
        dhxLayout.cells("c").hideHeader();

        dhxLayout.cells("d").attachObject("footer");
        dhxLayout.cells("d").hideHeader();
        dhxLayout.cells("d").setHeight(10);
        initDHTMLXTabbars(dhxTabbar);
    //}

    function openMenuUrl(id){
        var myid = id;
        var request = $actionScript;
        var param = {'page' : myid};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }

</script>
EOD;
        $writer->write($js);
    }

}

?>