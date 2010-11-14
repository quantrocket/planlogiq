<?php

/** Please take care, to add the client script inside the .page or .tpl file...
 * Philipp Frenzel - pf@com-x-cha.com
 * <com:TClientScriptLoader PackagePath="Application.*.tafelTree" PackageScripts="Tree" />
 */

class PFDHTMLXTreeActivity extends TWebControl {

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

        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxtree','../rliq/protected/3rdParty/dhtmlxTree/codebase/dhtmlxtree_pro.css');
        //$this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxmenu_standard','../rliq/protected/3rdParty/dhtmlxMenu/codebase/skins/dhtmlxmenu_standard.css');
        $this->getPage()->getClientScript()->registerStyleSheetFile('dhtmlxmenu_clear_silver','../rliq/protected/3rdParty/dhtmlxMenu/codebase/skins/dhtmlxmenu_dhx_skyblue.css');
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxcommon',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/dhtmlxcommon.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/dhtmlxtree_pro.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_srnd',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_srnd.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_xw',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_xw.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxtree_kn',$this->publishAsset("../3rdParty/dhtmlxTree/codebase/ext/dhtmlxtree_kn.js"));
        //$this->getPage()->getClientScript()->registerScriptFile('dhtmlxcommonmenu',$this->publishAsset("../3rdParty/dhtmlxMenu/codebase/dhtmlxcommon.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxmenu',$this->publishAsset("../3rdParty/dhtmlxMenu/codebase/dhtmlxmenu.js"));
        $this->getPage()->getClientScript()->registerScriptFile('dhtmlxmenu_ext',$this->publishAsset("../3rdParty/dhtmlxMenu/codebase/ext/dhtmlxmenu_ext.js"));
    //$this->getPage()->getClientScript()->registerPradoScript("dragdrop");
    //$cs=$this->getPage()->getClientScript();
    //$cs->registerHeadScript('TafelTree:1',$this->structure);
    }

    public function renderContents($writer) {
        if(!$this->getEnabled())
            return;

        $actionScript = $this->parent->MyCallback->ActiveControl->Javascript;
        $actionScriptDrop = $this->parent->MyCallbackDrop->ActiveControl->Javascript;
        $actionScriptDouble = $this->parent->MyCallbackDouble->ActiveControl->Javascript;
        $actionMenuClick = $this->parent->MyCallbackMenuClick->ActiveControl->Javascript;


        if($this->User->getIsAdmin()){
            $allowed = "true";
            $contextmenu = "menu";
        }else{
            $allowed = "true";
            $contextmenu = "menu";
        }

        if(isset($_GET['idtm_activity'])){
            $findItem = $_GET['idtm_activity'];
        }else{
            $findItem = 0;
            //$findItem = $this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_activity");
        }

        $StrukturTreeConnector = $this->parent->getRequest()->constructUrl('page','activity.TreeActivityConnector');
        $StrukturTreeMenuConnector = $this->parent->getRequest()->constructUrl('page','activity.TreeActivityMenuConnector');

        $js= <<< EOD
<script type="text/javascript" charset="UTF-8">    

    function initDHTMLXTree(){

        menu = new dhtmlXMenuObject("ContextMenu");
        //menu.setImagePath('/rliq/themes/basic/imgs/');
        menu.setIconsPath('/rliq/themes/basic/imgs/');
        menu.renderAsContextMenu();
        menu.setOpenMode("web");
        menu.attachEvent("onClick",onButtonClick);
        menu.loadXML('$StrukturTreeMenuConnector');

        dhtmlxError.catchError("LoadXML",function(){});

        //tree=new dhtmlXTreeObject("TreeViewDHTMLX","100%","100%",0);
        tree = dhxLayout.cells("b").attachTree($findItem);
        tree.setImagePath('/rliq/themes/basic/imgs/');
        tree.setItemStyle('position:static');
        tree.enableKeyboardNavigation(true);
        tree.enableKeySearch(true);
        tree.enableDragAndDrop($allowed);
        tree.enableCheckBoxes(false);
        tree.setOnClickHandler(onNodeSelect);
        tree.setOnCheckHandler(onNodeCheck);
        tree.setDragHandler(onNodeDrop);
        tree.enableContextMenu($contextmenu);
        tree.setXMLAutoLoading('$StrukturTreeConnector&id=$findItem')
        tree.loadXML('$StrukturTreeConnector&id=$findItem',function(){
            tree.loadOpenStates()
        });
    }

   
   function onButtonClick(idta_activity_type){
        var id = tree.contextID;
        var myid = idta_activity_type;
        var request = $actionMenuClick;
        var param = {'idta_activity_type' : myid,'idtm_activity':id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }
    
    function onNodeSelect(nodeId){
        tree.saveOpenStates();
        var id = nodeId;
        var request = $actionScript;
        var param = {'idtm_activity' : id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }

    function onNodeDrop(idFrom,idTo){
        var idFrom = idFrom;
        var idTo = idTo;
        var request = $actionScriptDrop;
        var param = {'idtm_activity' : idFrom , 'parent_idtm_activity' : idTo};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
    }

    function onNodeCheck(nodeId)
     {
        tree.saveOpenStates();
        var id = nodeId;
        var request = $actionScriptDouble;
        var param = {'idtm_activity' : id};
        request.setCallbackParameter(param);
        request.dispatch();
        return true;
     }

</script>
EOD;

        $writer->write("\n<div id='TreeViewDHTMLX' style='width:100%;overflow:hidden;'></div>\n");
        $writer->write($js);
    }

}

?>