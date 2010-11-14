<?php

class PivotContainer extends TTemplateControl{

        public function onLoad($param)
	{
		parent::onLoad($param);
                $this->PBDedidta_pivot_bericht->Text = $this->page->idta_pivot_bericht->Text;
                if(!$this->page->isPostBack && !$this->page->isCallback){
                    $this->buildPivotPullDown();
                    $this->bindListPivotValue();
                }
	}

        public function buildPivotPullDown(){
            $this->PBDedidta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
            $this->PBDedidta_stammdaten_group->dataBind();

            $this->PBDedparent_idtm_pivot->DataSource = PFH::build_SQLPullDown(PivotRecord::finder(),"tm_pivot",array("idtm_pivot","idtm_pivot"),"idta_pivot_bericht = ".$this->page->idta_pivot_bericht->Text);
            $this->PBDedparent_idtm_pivot->DataBind();
        }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	
	private $PBprimarykey = "idtm_pivot";
	private $PBfields = array("idta_pivot_bericht","idta_stammdaten_group","parent_idtm_pivot","pivot_position","pivot_filter");
	private $PBdatfields = array();
	private $PBhiddenfields = array();
	private $PBboolfields = array();

        public function PBDClosedButtonClicked($sender, $param) {
            $this->page->mpnlPivot->Hide();
	}

	public function bindListPivotValue(){
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="idta_pivot_bericht = :suchbedingung1";
    	$criteria->Parameters[':suchbedingung1'] = $this->page->idta_pivot_bericht->Text;

        $this->PivotListe->VirtualItemCount = count(PivotStammdatenGroupView::finder()->findAll($criteria));

        $criteria->setLimit($this->PivotListe->PageSize);
        $criteria->setOffset($this->PivotListe->PageSize * $this->PivotListe->CurrentPageIndex);
		$this->PivotListe->DataKeyField = 'idtm_pivot';
			
		$this->PivotListe->DataSource=PivotStammdatenGroupView::finder()->findAll($criteria);
		$this->PivotListe->dataBind();
    }

    public function PBDDeleteButtonClicked($sender,$param){

		$tempus='PBDed'.$this->PBprimarykey;

		if($this->PBDedPivot_edit_status->Text == '1'){
			$PBDeditRecord = PivotRecord::finder()->findByPK($this->$tempus->Text);
            $PBDeditRecord->delete();
		}
        $this->bindListPivotValue();
    }
        
    public function load_Pivot($sender,$param){
    	
    	$item = $param->Item;
    	$myitem=PivotRecord::finder()->findByPK($item->pbd_idtm_pivot->Text);
    	
    	$tempus = 'PBDed'.$this->PBprimarykey;
		$monus = $this->PBprimarykey;
		
		$this->$tempus->Text = $myitem->$monus;
		
    	//HIDDEN
		foreach ($this->PBhiddenfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->PBdatfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->PBboolfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->PBfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->PBDedPivot_edit_status->Text = 1;
		$this->PBDedidtm_pivot->Text = $item->pbd_idtm_pivot->Text;

    }
    
	public function PBDSavedButtonClicked($sender,$param){
		
		$tempus='PBDed'.$this->PBprimarykey;
		
		if($this->PBDedPivot_edit_status->Text == '1'){
			$PBDeditRecord = PivotRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$PBDeditRecord = new PivotRecord;
		}
	
		//HIDDEN
		foreach ($this->PBhiddenfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$PBDeditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->PBdatfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$PBDeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->PBboolfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$PBDeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->PBfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$PBDeditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$PBDeditRecord->save();
		
		$this->bindListPivotValue();
	}

	public function PBDNewButtonClicked($sender,$param){
    	
        $pivotbericht = $this->PBDedidta_pivot_bericht->Text;

        $tempus = 'PBDed'.$this->PBprimarykey;
        $monus = $this->PBprimarykey;

        $this->$tempus->Text = '0';
		
    	//HIDDEN
		foreach ($this->PBhiddenfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}
		
		//DATUM
		foreach ($this->PBdatfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}
		
		//BOOL
		foreach ($this->PBboolfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->Checked(0);
		}
		
		//NON DATUM
		foreach ($this->PBfields as $recordfield){
			$edrecordfield = 'PBDed'.$recordfield;
			$this->$edrecordfield->Text = '0';
		}
		
        $this->PBDedpivot_position->Text = '1';
		$this->PBDedidta_pivot_bericht->Text = $pivotbericht;
		$this->PBDedPivot_edit_status->Text = '0';
    }
    
    
	public function rcvList_PageIndexChanged($sender,$param)
		{
			$this->PivotListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListPivotValue();
		}
        

    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    
	
}

?>