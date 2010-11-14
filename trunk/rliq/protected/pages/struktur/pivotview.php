<?php

Prado::using('Application.app_code.PFCalculator');

class pivotview extends TPage
{

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }


    private $PivotBerichtRecord;
    private $Periode = '10001';
    private $NumberOfElements = 0;
    private $NumberOfAllElements = 1;
    private $NumberLowestLevel = 0;
    private $ParentDimCounter = 0;
    private $zwischenergebnisse = array();
	
	public function onLoad($param){
		
		parent::onLoad($param);

                if($this->Request['periode']!=''){
                    $this->Periode = $this->Request['periode'];
                }
                
                if(!$this->isPostBack && !$this->isCallback){
                        $this->DWH_idta_perioden->DataSource=PFH::build_SQLPullDown(PeriodenRecord::finder(),"ta_perioden",array("per_intern","per_extern"));
                        $this->DWH_idta_perioden->dataBind();
                        $this->DWH_idta_perioden->Text = $this->Periode;
                        $this->idta_pivot_bericht->Text = $this->Request['idta_pivot_bericht'];
                        $this->PivotBerichtRecord = PivotBerichtRecord::finder()->findByPK($this->Request['idta_pivot_bericht']);
                        $this->pivot_bericht_name->Text = $this->PivotBerichtRecord->pivot_bericht_name;
                        //hier berechne ich die Anzahl der Elemente auf der untersten Ebene
                        if(count(PivotRecord::finder()->findByidta_pivot_bericht($this->PivotBerichtRecord->idta_pivot_bericht))>0){
                            $this->calc_NumberOfElements(PivotRecord::finder()->findByidta_pivot_bericht($this->PivotBerichtRecord->idta_pivot_bericht));
                            $this->calc_NumerOfAllElements();
                            $this->buildPivotReport();
                            //$this->generateGraph($this->zwischenergebnisse);
                        }                        
                }        
	}

        public function PeriodenChanged($sender,$param){
              $indices = $sender->SelectedIndices;
              foreach($indices as $index)
                {
                    $item=$sender->Items[$index];
                    $result=$item->Value;
                }
              $page = $this->Request['page'];
              $parameter['modus']=1;
              $parameter['periode']=$result;
              $parameter['idta_pivot_bericht']=$_GET['idta_pivot_bericht'];
              $anchor = ($this->getAnchor() !== null ? "#" . $this->getAnchor() : "");
              $url = $this->getApplication()->getRequest()->constructUrl('page',"page", $page, $parameter) . $anchor;
              $this->Response->redirect($url);
        }

        public function getAnchor() {
            return $this->getViewState("Anchor", null);
        }

        public function OpenPivotBerichtContainer($sender,$param){
            $this->mpnlPivotBericht->Show();
        }

        public function OpenPivotContainer($sender,$param){
            $this->mpnlPivot->Show();
        }

        private function calc_NumberOfElements($Node){
            $Result = StammdatenRecord::finder()->findAllByidta_stammdaten_group($Node->idta_stammdaten_group);
            $this->NumberOfElements = count($Result);
        }

        private function calc_Elements($Node){
            //please pass a pivotrecord object
            return count(StammdatenRecord::finder()->findAllByidta_stammdaten_group($Node->idta_stammdaten_group))+2;
        }

        private function calc_NumerOfAllElements(){
            $myResult = PivotRecord::finder()->findAllByidta_pivot_bericht($this->PivotBerichtRecord->idta_pivot_bericht);
            foreach($myResult AS $Node){
                $this->NumberOfAllElements *= $this->calc_Elements($Node);
            }
        }

        private function calc_NumberNextLevel($Node){
            if($this->check_forChildren($Node)){
                $SQL = "SELECT idta_stammdaten_group FROM tm_pivot WHERE parent_idtm_pivot = '".$Node->idtm_pivot."' LIMIT 1";
                $Result = PivotRecord::finder()->findBySQL($SQL);
                $ResultTwo = StammdatenRecord::finder()->findAllByidta_stammdaten_group($Result->idta_stammdaten_group);
                $this->NumberLowestLevel = count($ResultTwo);
                return count($ResultTwo)+2;
            }else{
                return 1; //return $this->NumberOfElements;
            }
        }

        public function buildPivotReport(){
            //setting up the db-connection
            $myDBConnection = new TDbConnection($this->Application->getModule('db1')->database->getConnectionString(),$this->Application->getModule('db1')->database->getUsername(),$this->Application->getModule('db1')->database->getPassword());
            $myDBConnection->Active = true;
            
            $PFCALC = new PFCalculator;
            $PFCALC->setDBConnection($myDBConnection);
            $PFCALC->setStartPeriod($this->Periode);
            $this->load_header($PFCALC);
            $this->load_rows();
        }

        private function load_rows(){
            $FirstColum = new TTableRow;
            $this->mastertable->Rows[]=$FirstColum;
            $cell = new TTableCell;
            $myResult = PivotRecord::finder()->findAllByidta_pivot_bericht($this->PivotBerichtRecord->idta_pivot_bericht);
            foreach($myResult AS $Node){
                if(!$this->check_forParents($Node)){
                    $cell->Text = StammdatenGroupRecord::finder()->findByidta_stammdaten_group($Node->idta_stammdaten_group)->stammdaten_group_name;
                }
            }
            //echo $this->NumberOfAllElements;
            $cell->setRowSpan($this->NumberOfAllElements);
            $cell->setCssClass('mandantory');
            $FirstColum->Cells[]=$cell;
            foreach($myResult AS $Node){
                if(!$this->check_forParents($Node)){
                   $this->walkChildren($Node);
                }
            }
        }

        private function load_header($PFCALCULATOR){
                $PFCALCULATOR->buildPivotTitle("Zeit");

                $FirstRow = new TTableRow;
                $this->mastertable->Rows[]=$FirstRow;

                $counter = 0;

                foreach($PFCALCULATOR->getTitle() AS $value){
                    $cell=new TTableHeaderCell;
                    $cell->Text=$value;
                    $counter==0?$cell->setColumnSpan($this->count_Dimension()+1):'';
                    $cell->EnableViewState = false;
                    $FirstRow->Cells[]=$cell;
                    $counter++;
		}
                $FirstRow->setCssClass('thead');
        }

        private function count_Dimension(){
            return count(PivotRecord::finder()->findAllByidta_pivot_bericht($this->PivotBerichtRecord->idta_pivot_bericht));
        }

        public function check_forChildren($Node){
		$SQL = "SELECT * FROM tm_pivot WHERE parent_idtm_pivot = '".$Node->idtm_pivot."'";
		$Result = count(PivotRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}

        public function check_forParents($Node){
		$SQL = "SELECT * FROM tm_pivot WHERE idtm_pivot = '".$Node->parent_idtm_pivot."'";
		$Result = count(PivotRecord::finder()->findAllBySQL($SQL));
		if($Result>=1){
			return true;
		}else{
			return false;
		}
	}

        private function walkChildren($Node,$Path='',$Label=''){
            $ALTERNATING = 0;
            if($this->check_forChildren($Node)){ //hier checken wir, ob noch Pivot Kinder existieren
                foreach($this->get_Children($Node) as $CurrentDimension){
                    $FirstColum = new TTableRow;
                    $this->mastertable->Rows[]=$FirstColum;
                    $cell = new TTableCell;
                    $cell->Text=$CurrentDimension->stammdaten_name;
                    $cell->setRowSpan($this->calc_NumberNextLevel($Node));
                    fmod($ALTERNATING,2)==0?$cell->setCssClass('listalternating'):$cell->setCssClass('listnonealternating');
                    $FirstColum->Cells[]=$cell;
                    $ALTERNATING++;
                    foreach($this->get_PivotChildren($Node) as $PivotNode){
                        $TmpPath=$Path.$CurrentDimension->idtm_stammdaten.'xx';
                        $this->walkChildren($PivotNode,$TmpPath,$CurrentDimension->stammdaten_name);
                    }                    
                }                
            }else{
                $this->ParentDimCounter++;
                $PFPivot = new PFCalculator();
                $PFPivot->setFeldFunktion($this->PivotBerichtRecord->idta_feldfunktion);
                $PFPivot->setStartPeriod($this->Periode);
                $PFPivot->GLOBALVARIANTE = $this->PivotBerichtRecord->idta_variante;
                $PFPivot->loadDimension($Node);
                $PFPivot->buildPivotCondition($Path);
                $PFPivot->dimension=$Label;
                $PFPivot->setOperator($this->PivotBerichtRecord->pivot_bericht_operator);
                $this->draw_cells($PFPivot);
            }
        }

        private function draw_cells($PFCALCULATOR,$name="",$details=1){

                $PFCALCULATOR->executePIVOTSQL($name,$details);
                $PFCALCULATOR->FormatWerte();
                $ROWS = $PFCALCULATOR->getValues();
                $ALTERNATING = 0;
                $templabel = '';

                foreach($ROWS as $row){

                    $ALTERNATING++;
                    $temparray = array();
                    $counter = 0;
                    //hier bauen wir die einzelnen Zeilen
                    $WorkRow=new TTableRow;
                    $this->mastertable->Rows[]=$WorkRow;

                    if($ALTERNATING<count($ROWS)){
                      foreach($row as $value){
                            $cell = new TTableCell();
                            $cell->EnableViewState = false;
                            $cell->Text = $value;
                            $counter==0?$templabel=$value:array_push($temparray,$value);
                            $counter++;
                            $WorkRow->Cells[]=$cell;                            
                            fmod($ALTERNATING,2)==0?$WorkRow->setCssClass('listalternating'):$WorkRow->setCssClass('listnonealternating');
                       }
					   $this->zwischenergebnisse[$PFCALCULATOR->dimension][$templabel]=$temparray;
                    }else{
                        foreach($row as $value){
                            $cell = new TTableCell();
                            $cell->EnableViewState = false;
                            $cell->Text = $value;
                            $counter==0?$templabel=$value:array_push($temparray,$value);
                            $counter++;
                            $cell->setCssClass('calculatedsumme');
                            $WorkRow->Cells[]=$cell;
                            //$WorkRow->setCssClass('calculatedsumme');
                       }					   
                       //$this->zwischenergebnisse[$PFCALCULATOR->dimension][$templabel]=$temparray;
                    }
                    
                }                
        }

        public function get_Parents($Node){
            if($this->check_forParents($Node)){
                //noch nichts
            }
        }

        public function get_Children($Node){
		$SQL = "SELECT * FROM tm_stammdaten WHERE idta_stammdaten_group = '".$Node->idta_stammdaten_group."'";
		$Result = count(StammdatenRecord::finder()->findAllBySQL($SQL));
                $SSQL = "SELECT * FROM tm_stammdaten WHERE ";
                $counter = 0;
                if($Result>=1){
                    foreach(StammdatenRecord::finder()->findAllBySQL($SQL) as $Results){
                        $counter==0?$SSQL.="idtm_stammdaten = '".$Results->idtm_stammdaten."'":$SSQL.=" OR idtm_stammdaten = '".$Results->idtm_stammdaten."'";
                        $counter++;
                    }
                }else{
                    $SSQL.="idtm_stammdaten = '0'";
                }
                return StammdatenRecord::finder()->findAllBySQL($SSQL);
	}

        public function get_PivotChildren($Node){
		$SQL = "SELECT * FROM tm_pivot WHERE parent_idtm_pivot = '".$Node->idtm_pivot."'";
		$Result = count(PivotRecord::finder()->findAllBySQL($SQL));
                $SSQL = "SELECT * FROM tm_pivot WHERE ";
                $counter = 0;
                if($Result>=1){
                    foreach(PivotRecord::finder()->findAllBySQL($SQL) as $Results){
                        $counter==0?$SSQL.="idtm_pivot = '".$Results->idtm_pivot."'":$SSQL.=" OR idtm_pivot = '".$Results->idtm_pivot."'";
                        $counter++;
                    }
                }else{
                    $SSQL.="idtm_pivot = '0'";
                }
                return PivotRecord::finder()->findAllBySQL($SSQL);
	}

        private function generateGraph($MyArray){
                
				//print_r($MyArray);
				$arrgarray=array();
                $xdata = array();
				$width = array();
                $height = array();
                $ytitle = array($this->PivotBerichtRecord->pivot_bericht_name);
                $title = array($this->Periode);
                $title = implode(',', $title);
				$legend = array();

                $dummy = 1;
                foreach($MyArray as $key=>$value){
                    $dummy<=$this->ParentDimCounter?$xdata[]=$key:'';
                    array_push($legend,$key);                    
                    foreach($value As $ax=>$ay){
                        if(!is_array(${'ydata'.$dummy})){
                           ${'ydata'.$dummy}=array();
                        }
                        array_push(${'ydata'.$dummy},$ay[0]);
					}
                    $dummy++;
                }		
								
				for($ii=1;$ii<=$this->ParentDimCounter;$ii++){
                    ${'ydata'.$ii} = implode(',',${'ydata'.$ii});
                    $arrgarray['ydata'.$ii]=${'ydata'.$ii};
                }

                $legend = implode(',', $legend);
				$width[] = $this->DBImage->Width;
                $height[] = $this->DBImage->Height;
                $width = implode(',', $width);
				$height = implode(',', $height);
                $xdata = implode(',', $xdata);
				$ytitledata = implode(',', $ytitle);
                
        $arrgarray['numberpivots']=$this->NumberOfElements; //*$this->ParentDimCounter
        $arrgarray['numberdimensions']=$this->ParentDimCounter;
        $arrgarray['numberchildren']=$this->NumberLowestLevel;
        $arrgarray['height']=$height;
        $arrgarray['legend']=$legend;
        $arrgarray['title']=$title;
        $arrgarray['width']=$width;
        $arrgarray['xdata']=$xdata;
        $arrgarray['ytitle']=$ytitledata;
        $arrgarray['legend']=$legend;
        //print_r($arrgarray);

		$this->DBImage->ImageUrl = $this->getRequest()->constructUrl('page','pivotbar', 1,$arrgarray , false);
       }
	
}
?>