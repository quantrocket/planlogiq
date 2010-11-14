<?php
/**
 * AccountPortlet class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 * @version $Id: AccountPortlet.php 1398 2006-09-08 19:31:03Z xue $
 */

Prado::using('Application.portlets.portlet');


/**
 * AccountPortlet class
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.pradosoft.com/
 * @copyright Copyright &copy; 2006 PradoSoft
 * @license http://www.pradosoft.com/license/
 */
class strukturportlet extends portlet{

        public function onPreRender($param){
            parent::onPreRender($param);
        }

        public function onInit($param){
            parent::onInit($param);

            $this->test();

        }

        private function test(){
            $TafelTree = $this->TreeView;
            $TafelTree->buildStructure();
        }

        public function onLoad($param){
		
		parent::onLoad($param);
       
		//if(!$this->isPostBack && !$this->isCallback){
                        $this->DWH_idta_variante->DataSource=PFH::build_SQLPullDown(VarianteRecord::finder(),"ta_variante",array("idta_variante","var_descr"));
			$this->DWH_idta_variante->dataBind();

                        $this->DWH_idta_perioden->DataSource=PFH::build_SQLPullDown(PeriodenRecord::finder(),"ta_perioden",array("per_intern","per_extern"));
			$this->DWH_idta_perioden->dataBind();

			$tree = $this->MyTree;

                        //$this->addContextMenu($tree->getID());
                      
                        $node = new StrukturRecord();
			$this->fullInTreeStrukturRecord($tree, $node);
                //}
	}
	
	public function OpenVariantenContainer($sender,$param){
            $this->mpnlTest->Show();
        }

        public function getAnchor() {
            return $this->getViewState("Anchor", null);
        }

       public function callback_MyCallback($sender,$param){
          $theObjectContainingParameters = $param->CallbackParameter;
          $Record = StrukturRecord::finder()->findBy_idtm_struktur($theObjectContainingParameters->idtm_struktur);
          if($this->check_forChildren($Record)){
                   $page='reports.gewinnundverlust';
          }else{
                   $page='struktur.streingabemaskeram';
          }
          $parameter['modus']=0;
          $parameter['idtm_struktur']=$Record->idtm_struktur;
          $parameter['idta_struktur_type']=$Record->idta_struktur_type;
          $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
          $url = $this->getApplication()->getRequest()->constructUrl('page',"page", $page, $parameter) . $anchor;
          $this->Response->redirect($url);
       }


        public function logout($sender,$param)
	{
            $this->Application->getModule('auth')->logout();
            $url=$this->getRequest()->constructUrl('page',$this->Service->DefaultPage);
            $this->Response->redirect($url);
	}

        function fullInTreeStrukturRecord($tree, $node){
		if(!$node->idtm_struktur){
			$subNodes = StrukturRecord::finder()->findAllBy_parent_idtm_struktur('0');
			$tree->setTitle("Struktur");
			$tree->setNodeType(MyTreeList::NODE_TYPE_PLAIN);
                        $this->check_forChildren($node)?$tree->setToPage('reports.gewinnundverlust'):$tree->setToPage('struktur.streingabemaskeram');
			$tree->setGetVariables(array('modus'=>0,"idtm_struktur"=>$node->idtm_struktur,"idta_struktur_type"=>$node->idta_struktur_type));
		}
		else{
			$subNodes = StrukturRecord::finder()->findAllBy_parent_idtm_struktur($node->idtm_struktur);
			$tree->setTitle($node->idta_struktur_type.'-'.$node->struktur_name);
			$tree->setNodeType(MyTreeList::NODE_TYPE_INTERNAL_LINK);
			$tree->setGetVariables(array('modus'=>0,"idtm_struktur"=>$node->idtm_struktur,"idta_struktur_type"=>$node->idta_struktur_type));
                        if($this->check_forChildren($node)){
                            $tree->setToPage('reports.gewinnundverlust');
                        }else{
                            $tree->setToPage('struktur.streingabemaskeram');
                        }
		}                   
                foreach($subNodes as $subN){
			$subTr = new MyTreeList();
			$this->fullInTreeStrukturRecord($subTr, $subN);
			$tree->addSubElement($subTr);                        
		}
                //$this->addContextMenu($objects->MyTreeList->Id);
	}

        public function check_forChildren($Node){
		$SQL = "SELECT * FROM tm_struktur WHERE parent_idtm_struktur = '".$Node->idtm_struktur."'";
		$Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}

	
        private function addContextMenu($compID){

            $myContextMenu = new CContextMenu();
            $myContextMenu->setCssClass('menu desktop');
            $myContextMenu->OnMenuItemSelected('myTreeMenuCommand');
            $myContextMenu->setForControl($compID);

            $firstItem = new CContextMenuItem();
            $firstItem->setText("Punkt 1");
            $firstItem->setCommandName("logout");
            $myContextMenu->getItems()->add($firstItem);

            $sndItem = new CContextMenuItem();
            $sndItem->setText("Punkt 2");
            $sndItem->setCommandName("Punkttwo");
            $myContextMenu->getItems()->add($sndItem);

            $this->getControls()->add($myContextMenu);
        }
}

?>