<?php

/** Please take care, to add the client script inside the .page or .tpl file...
 * Philipp Frenzel - pf@com-x-cha.com
 * <com:TClientScriptLoader PackagePath="Application.*.tafelTree" PackageScripts="Tree" />
 */

class PFTafelTree extends TWebControl{

        private $structure;
        private $counter = 0;
        private $counterorg = 0;
        private $counteritems = 0;
        private $json;
        private $TreeRecord;
        
	public function onInit($param){
            parent::onInit($param);
        }

        public function onLoad($param){
		parent::onLoad($param);
        }

        public function onPreRender($writer)
	{
		parent::onPreRender($writer);
		$this->registerClientScripts();
	}

        protected function addAttributesToRender($writer)
	{
		$writer->addAttribute('id',$this->getClientID());
		parent::addAttributesToRender($writer);
	}

        public function getID(){
            $id = $this->getViewState('ID', '');
            return $id;
        }

        public function check_forParents($Node){
		$SQL = "SELECT idtm_struktur,parent_idtm_struktur FROM tm_struktur WHERE idtm_struktur = '".$Node->parent_idtm_struktur."'";
		$Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
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

        public function get_FirstNode(){
		$SQL = "SELECT * FROM tm_struktur WHERE parent_idtm_struktur = '0'";
		$Result = StrukturRecord::finder()->findAllBySQL($SQL);
                return $Result;		
	}

        public function buildStructure(){
            $this->TreeRecord = $this->get_FirstNode();
            //zuerst holen wir uns das startelement
            foreach($this->TreeRecord as $Datensatz){
                    $this->structure = "var struct = [";
                    $MyStartID = $Datensatz->idtm_struktur;
                    $this->build_JSON_Array($Datensatz,1);
                    $this->walkChildren($Datensatz);
            }
	}

        private function build_JSON_Array($node,$start=0,$faz=0,$RELTYPE=""){
                $itemsopen = "'items':[";
                if($start==1){
                    if($this->check_forChildren($node)){
                        $setToPage='reports.gewinnundverlust';
                        $append = ','.$itemsopen;
                    }else{
                        $setToPage='struktur.streingabemaskeram';
                        $append = '}';
                    }
                    //der Startknoten
                    $this->structure.="{'id':'".$node->idtm_struktur."', 'page':'".$setToPage."', 'txt':'".$node->struktur_name."','img':'str".$node->idta_struktur_type.".gif'".$append;
		}
		else{
                    if($this->check_forChildren($node)){
                        $setToPage='reports.gewinnundverlust';
                        $append = '';
                    }else{
                        $setToPage='struktur.streingabemaskeram';
                        $append = '}';
                    }
                    //der Endknoten
                    $this->structure.="{'id':'".$node->idtm_struktur."', 'page':'".$setToPage."', 'txt':'".$node->struktur_name."','img':'str".$node->idta_struktur_type.".gif'".$append;
		}
	}

        private function walkChildren($Node){
            $itemsopen = "'items':[";
            foreach($this->get_TreeChildren($Node) as $Result){
               $this->build_JSON_Array($Result,0);
               if($this->check_forChildren($Result)){
                   $this->structure.=',';
                   $this->structure.=$itemsopen;
                   $this->walkChildren($Result);
               }
               $this->structure.=',';
            }
            /*foreach($this->get_TreeChildren($Node) as $Result){
               $tempNode = $Result;
               if($this->check_forChildren($tempNode)){
                   $this->structure.=$itemsopen;
                   $this->walkChildren($tempNode);
               }
            }*/
        }

        public function get_TreeChildren($Node){
		$SQL = "SELECT * FROM tm_struktur WHERE parent_idtm_struktur = '".$Node->idtm_struktur."'";
		$Result = count(StrukturRecord::finder()->findAllBySQL($SQL));
                $SSQL = "SELECT * FROM tm_struktur WHERE ";
                $counter = 0;
                if($Result>=1){
                    foreach(StrukturRecord::finder()->findAllBySQL($SQL) as $Results){
                        $counter==0?$SSQL.="idtm_struktur = '".$Results->idtm_struktur."'":$SSQL.=" OR idtm_struktur = '".$Results->idtm_struktur."'";
                        $counter++;
                    }
                }else{
                    $SSQL.="idtm_struktur = '0'";
                }
                $SSQL .= ' ORDER BY idta_struktur_type';
                return StrukturRecord::finder()->findAllBySQL($SSQL);
	}

        public function buildStructureOLD($node=''){
            $itemsopen = ",'items':[";
            $itemsclose = "]}";
            if(!$node->idtm_struktur){
                $this->structure = "var struct = [";
		$subNodes = StrukturRecord::finder()->findAllBy_parent_idtm_struktur('0');

                $this->structure .= "{'id':'0', 'txt':'Struktur','img':'str1.gif'".$itemsopen;
                $this->counteritems++;
            }
            else{
                $criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "parent_idtm_struktur = :suchtext";
    		$criteria->Parameters[':suchtext'] = $node->idtm_struktur;
		$criteria->OrdersBy["parent_idtm_struktur"] = 'asc';
    		$subNodes = StrukturRecord::finder()->findAll($criteria);
                
                $this->structure .= "{'id':'".$node->idtm_struktur."', 'page':'".$setToPage."', 'txt':'".$node->struktur_name."','img':'str".$node->idta_struktur_type.".gif'";
                if($this->check_forChildren($node)){
                    $this->structure .= $itemsopen;
                    $this->counteritems++;
                }else{
                    $this->structure .= '}';
                }
            }
            foreach($subNodes as $subN){
                if($this->check_forChildren($node)){
                    if($this->check_forChildren($subN)){
                        $this->buildStructure($subN);
                        $this->counterorg<count($subNodes)?$this->counterorg++:$this->counterorg=1;
                    }
                }
            }
            
            foreach($subNodes as $subN){
                if(!$this->check_forChildren($subN)){
                    $this->buildStructure($subN);
                    $this->counter<count($subNodes)-1?$this->structure.=",":$this->structure .= ']},';
                    $this->counter<count($subNodes)-1?'':$this->counteritems--;
                    $this->counter<count($subNodes)-1?$this->counter++:$this->counter=0;
                }
            }

        }

        protected function registerClientScripts()
	{
                for($iii=0;$iii<=12;$iii++){
                    $this->structure = str_replace(",,,,", "]}]}]},", $this->structure);
                }
                for($iii=0;$iii<=12;$iii++){
                    $this->structure = str_replace(",,", "]},", $this->structure);
                }
                //hier muss noch eine pruefung hin, wieviele ebenen im Baum abgebildet werden, danach muss der folgende String aufgebaut werden...
                // dh hier muss pro ebene -1 ein weiteres }]},, durch }]}]}, ersetzt werden
                $this->structure=substr($this->structure,0,($len-1));
                $this->structure.="]}];";

                $id=$this->getClientID();
        	$this->getPage()->getClientScript()->registerScriptFile('Tree',$this->publishAsset("tafelTree/Tree.js"));
                $this->getPage()->getClientScript()->registerPradoScript("dragdrop");
                $cs=$this->getPage()->getClientScript();
                $cs->registerHeadScript('TafelTree:1',$this->structure);

                $actionScript = $this->parent->parent->MyCallback->ActiveControl->Javascript;
                $actionScriptDouble = $this->parent->parent->MyCallbackDouble->ActiveControl->Javascript;


            $js= <<< EOD

var tree = 0;

function TafelTreeInit () {
    tree = new TafelTree ('$id', struct, {
			'generate':true,
			'imgBase' : './themes/basic/imgs/',
			'openAtLoad':false,
			'cookies':true,
			'multiline':true,
			'defaultImg':'str1.gif',
			'defaultImgSelected':'globe.gif',
			'defaultImgOpen':'memobook.gif',
			'defaultImgClose':'search.gif',
			'defaultImgCloseSelected':'unlock.gif',
			'defaultImgOpenSelected':'imgfolder.gif',
			'rtlMode':false,
			'dropALT' : false,
			'dropCTRL' : false,
            'onClick' : sendAJAXRequest,
            'onCheck' : sendAJAXRequestDouble,
            'checkboxes' : true
		});
}

function sendAJAXRequest(tree)
         {
            var id = tree.getId();
            var request = $actionScript;
            var param = {'idtm_struktur' : id};
            request.setCallbackParameter(param);
            request.dispatch();
         }

function sendAJAXRequestDouble(tree)
         {
            var id = tree.getId();
            var request = $actionScriptDouble;
            var param = {'idtm_struktur' : id};
            request.setCallbackParameter(param);
            request.dispatch();
         }


EOD;
        $cs->registerHeadScript('TafelTree:2',$js);

	}

        public function renderContents($writer){
		if(!$this->getEnabled())
			return;

                $writer->write("\n<div id='".$this->getClientID()."'>\n");

		//$this->processChildren($writer);             
	}
	
}

?>