<?php

class PivotBerichtContainer extends TTemplateControl{
    
	public function onInit($param)
	{
		parent::onInit($param);
                //$this->bindListPivotBerichtValue();
	}

        public function onLoad($param)
	{
		parent::onLoad($param);
                if(!$this->page->isPostBack && !$this->page->isCallback){
                    $this->buildPivotBerichtPullDown();
                    $this->bindListPivotBerichtValue();
                }
	}

        public function buildPivotBerichtPullDown(){
            $this->PBedidta_feldfunktion->DataSource=PFH::build_SQLPullDownAdvanced(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","idta_struktur_type","ff_name"));
            $this->PBedidta_feldfunktion->dataBind();

            $this->PBedidta_variante->DataSource=PFH::build_SQLPullDown(VarianteRecord::finder(),"ta_variante",array("idta_variante","var_descr"));
            $this->PBedidta_variante->dataBind();

            $data=array('SUM'=>"SUM","AVG"=>"AVG","MAX"=>"MAX","MIN"=>"MIN");
            $this->PBedpivot_bericht_operator->DataSource = $data;
            $this->PBedpivot_bericht_operator->DataBind();
        }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	
	private $PBprimarykey = "idta_pivot_bericht";
	private $PBfields = array("idta_variante","pivot_bericht_name","idtm_user","idta_feldfunktion","pivot_bericht_operator","pivot_bericht_cdate");
	private $PBdatfields = array();
	private $PBhiddenfields = array();
	private $PBboolfields = array();

        public function PBClosedButtonClicked($sender, $param) {
            $this->page->mpnlPivotBericht->Hide();
	}

	public function bindListPivotBerichtValue(){

                $this->PivotBerichtListe->VirtualItemCount = count(PivotBerichtRecord::finder()->findAll());
			
                $criteria = new TActiveRecordCriteria();
                $criteria->setLimit($this->PivotBerichtListe->PageSize);
		$criteria->setOffset($this->PivotBerichtListe->PageSize * $this->PivotBerichtListe->CurrentPageIndex);
		$this->PivotBerichtListe->DataKeyField = 'idta_pivot_bericht';
			
		$this->PivotBerichtListe->VirtualItemCount = count(PivotBerichtRecord::finder()->findAll());
		$this->PivotBerichtListe->DataSource=PivotBerichtRecord::finder()->findAll($criteria);
		
		$this->PivotBerichtListe->dataBind();
    }
        
    public function load_pivotbericht($sender,$param){
    	
    	$item = $param->Item;
    	$myitem=PivotBerichtRecord::finder()->findByPK($item->pb_idta_pivot_bericht->Text);
    	
    	$tempus = 'PBed'.$this->PBprimarykey;
		$monus = $this->PBprimarykey;
		
		$this->$tempus->Text = $myitem->$monus;
		
    	//HIDDEN
		foreach ($this->PBhiddenfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->PBdatfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->PBboolfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->PBfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->PBedpivotbericht_edit_status->Text = 1;
		$this->PBedidta_pivot_bericht->Text = $item->pb_idta_pivot_bericht->Text;

    }
    
	public function PBSavedButtonClicked($sender,$param){
		
		$tempus='PBed'.$this->PBprimarykey;
		
		if($this->PBedpivotbericht_edit_status->Text == '1'){
			$PBEditRecord = PivotBerichtRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$PBEditRecord = new PivotBerichtRecord;
		}
	
		//HIDDEN
		foreach ($this->PBhiddenfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$PBEditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->PBdatfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$PBEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->PBboolfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$PBEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->PBfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$PBEditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$PBEditRecord->save();
		
		$this->bindListPivotBerichtValue();
	}

	public function PBNewButtonClicked($sender,$param){
    		
	$tempus = 'PBed'.$this->PBprimarykey;
	$monus = $this->PBprimarykey;
	
	$this->$tempus->Text = '0';
		
    	//HIDDEN
		foreach ($this->PBhiddenfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}
		
		//DATUM
		foreach ($this->PBdatfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}
		
		//BOOL
		foreach ($this->PBboolfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->Checked(0);
		}
		
		//NON DATUM
		foreach ($this->PBfields as $recordfield){
			$edrecordfield = 'PBed'.$recordfield;
			$this->$edrecordfield->Text = '0';
		}
		
		$this->PBedpivotbericht_edit_status->Text = '0';
    }
    
    
	public function rcvList_PageIndexChanged($sender,$param)
		{
			$this->PivotBerichtListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListPivotBerichtValue();
		}
        

    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    
	
}

?>