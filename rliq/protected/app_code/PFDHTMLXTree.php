<?php

/** Please take care, to add the client script inside the .page or .tpl file...
 * Philipp Frenzel - pf@com-x-cha.com
 * <com:TClientScriptLoader PackagePath="Application.*.tafelTree" PackageScripts="Tree" />
 */

class PFDHTMLXTree extends TWebControl {

    public function onPreRender($writer) {
        //parent::onPreRender($writer);
        $this->registerClientScripts();
    }

    protected function addAttributesToRender($writer) {
        $writer->addAttribute('id',$this->getClientID());
        parent::addAttributesToRender($writer);
    }

    protected function registerClientScripts() {
        $id=$this->getClientID();
        //$this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxmenu_clear_silver','../rliq/protected/3rdParty/dhtmlxMenu/codebase/skins/dhtmlxmenu_dhx_skyblue.css');
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxtree','../rliq/protected/3rdParty/dhtmlxTree/codebase/dhtmlxtree_pro.css');
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/dhtmlxtree_pro.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_srnd',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_srnd.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_xw',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_xw.js"));
        $this->getPage()->getClientScript()->registerScriptFile('connector',$this->publishAsset("../3rdParty/dhtmlxConnector/codebase/connector.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxdataprocessor',$this->publishAsset("../3rdParty/dhtmlxDataProcessor/codebase/dhtmlxdataprocessor.js"));
    }

    public function renderContents($writer) {
        if(!$this->getEnabled())
            return;

        $actionScript = $this->parent->MyCallback->ActiveControl->Javascript;
        $actionScriptTime = $this->parent->MyCallbackTime->ActiveControl->Javascript;
        $actionScriptReport = $this->parent->MyCallbackReport->ActiveControl->Javascript;
        $actionScriptDrop = $this->parent->MyCallbackDrop->ActiveControl->Javascript;
        $actionScriptDouble = $this->parent->MyCallbackDouble->ActiveControl->Javascript;

        if($this->User->getIsAdmin()){
            $allowed = "true";
            $contextmenu = "menu";
        }else{
            $allowed = "false";
            $contextmenu = "false";
        }

        if(isset($_GET['idta_stammdatensicht'])){
            $findItem = $_GET['idta_stammdatensicht'];
        }else{
            $findItem = 1;
        }

        //$custom_id = StrukturRecord::finder()->find('parent_idtm_struktur=0 AND idta_stammdatensicht = ?',$findItem)->idtm_struktur;
        $custom_id = 0;


        $StrukturTreeConnector = $this->parent->getRequest()->constructUrl('page','struktur.TreeStrukturConnector');
        $ZeitTreeConnector = $this->parent->getRequest()->constructUrl('page','struktur.TreeZeitConnector');
        $StrBerichtConnector = $this->parent->getRequest()->constructUrl('page','struktur.TreeStrukturBericht');
        $StrukturTreeMenuConnector = $this->parent->getRequest()->constructUrl('page','struktur.TreeStrukturMenuConnector');

        $js= <<< EOD
<script type="text/javascript" charset="UTF-8">

    var tree;
    var zeittree;
    var bertree;

    function onButtonClick(menuitemId,type){
        var id = tree.contextID;
        tree.setItemColor(id,menuitemId.split(">")[1]);
    }

    function initDHTMLXTree(){

        menu = new dhtmlXMenuObject("ContextMenu");
        //menu.setImagePath('/rliq/themes/basic/imgs/');
        menu.setIconsPath('/rliq/themes/basic/imgs/');
        menu.renderAsContextMenu();
        menu.setOpenMode("web");
        menu.attachEvent("onClick",onButtonClick);
        menu.loadXML("$StrukturTreeMenuConnector");

        dhtmlxError.catchError("LoadXML",function(){});

        //mytree=new dhtmlXTreeObject("TreeViewDHTMLX","100%","100%",$custom_id);
        
        dhxinTab.addTab("tab1","Struktur","60px");
        dhxinTab.addTab("tab2","Periode","60px");
        dhxinTab.addTab("tab3","Berichte","60px");

        tree = dhxinTab.cells("tab1").attachTree($custom_id);
        tree.setImagePath('/rliq/themes/basic/imgs/');
        tree.setItemStyle('position:static');
        //tree.enableSmartXMLParsing(true);
        //tree.enableDistributedParsing(true);
        tree.enableDragAndDrop($allowed);
        tree.enableCheckBoxes($allowed);
        tree.attachEvent("onClick",onNodeSelect);
        tree.setOnCheckHandler(onNodeCheck);
        tree.setDragHandler(onNodeDrop);
        tree.enableContextMenu($contextmenu);
        tree.setXMLAutoLoading("$StrukturTreeConnector&idta_stammdatensicht=$findItem&id=$custom_id");
        tree.loadXML("$StrukturTreeConnector&idta_stammdatensicht=$findItem&id=$custom_id",function(){
            tree.loadOpenStates();}
            )

        zeittree = dhxinTab.cells("tab2").attachTree();
        zeittree.setImagePath('/rliq/themes/basic/imgs/csh_vista/');
        zeittree.enableCheckBoxes(true);
        zeittree.attachEvent("onClick",onTimeNodeSelect);
        zeittree.loadXML("$ZeitTreeConnector",function(){
            zeittree.loadOpenStates();}
            )

        bertree = dhxinTab.cells("tab3").attachTree();
        bertree.setImagePath('/rliq/themes/basic/imgs/csh_books/');
        bertree.attachEvent("onClick",onReportNodeSelect);
        bertree.loadXML("$StrBerichtConnector",function(){
            zeittree.loadOpenStates();}
            )

        dhxinTab.setTabActive("tab1");
    }

    function onReportNodeSelect(nodeId){
        tree.saveOpenStates();
        var id = nodeId;
        var request = $actionScriptReport;
        var param = {'idta_struktur_bericht' : id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }

    function onTimeNodeSelect(nodeId){
        tree.saveOpenStates();
        var id = nodeId;
        var request = $actionScriptTime;
        var param = {'idta_perioden' : id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }
    
    function onNodeSelect(nodeId){
        tree.saveOpenStates();
        var id = nodeId;
        var request = $actionScript;
        var param = {'idtm_struktur' : id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }

    function onNodeDrop(idFrom,idTo){
        var idFrom = idFrom;
        var idTo = idTo;
        var request = $actionScriptDrop;
        var param = {'idtm_struktur' : idFrom , 'parent_idtm_struktur' : idTo};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }

    function onNodeCheck(nodeId)
     {
        tree.saveOpenStates();
        var id = nodeId;
        var request = $actionScriptDouble;
        var param = {'idtm_struktur' : id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
     }

</script>
EOD;

       $writer->write("\n<div id='TreeViewDHTMLX' style='width:100%;overflow:hidden;'></div>\n");
       $writer->write($js);
    //$this->processChildren($writer);
    }

}

?>