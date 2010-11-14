<?php

class FeldfunktionContainer extends TTemplateControl{
    
	public function onInit($param)
	{
		parent::onInit($param);
                //$this->bindListPivotValue();
	}

        public function onLoad($param)
	{
		parent::onLoad($param);
                
                if(!$this->page->isPostBack && !$this->page->isCallback){
                    $this->initParameters();
                    $this->buildFeldfunktionPullDown();
                    $this->bindListFeldfunktionValue();
                    $this->buildCollectorPullDown();
                }
	}

        public function initParameters(){
            $this->FFedidta_struktur_type->Text = $this->page->idta_struktur_type->Text;
        }

        public function buildFeldfunktionPullDown(){            
            $this->FFedpre_idta_feldfunktion->DataSource = PFH::build_SQLPullDown(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name"),"idta_struktur_type = ".$this->page->idta_struktur_type->Text);
            $this->FFedpre_idta_feldfunktion->DataBind();

            $data=array('0'=>"SUM","1"=>"AVERAGE","2"=>"SHEET COLLECTOR","3"=>"STRUCTUR COLLECTOR","4"=>"OPENING BALANCE","5"=>"CONTINUANCE","6"=>"PAYABLES","7"=>"SPLASHER DOWN");
            $this->FFedff_type->DataSource = $data;
            $this->FFedff_type->DataBind();

            $data=array('0'=>"NEUTRAL","1"=>"CASH-IN","2"=>"CASH-OUT","3"=>"TAX-IN","4"=>"TAX-OUT");
            $this->FFedff_cashbalance->DataSource = $data;
            $this->FFedff_cashbalance->DataBind();

            $data=array('+'=>"+","-"=>"-","/"=>"/","*"=>"*");
            $this->FFedff_operator->DataSource = $data;
            $this->FFedff_operator->DataBind();
        }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	
	private $FFprimarykey = "idta_feldfunktion";
	private $FFfields = array("ff_name","pre_idta_feldfunktion","ff_operator","ff_descr","idta_struktur_type","ff_faktor","ff_type","ff_default","ff_order","ff_cashbalance","ff_gewichtung");
	private $FFatfields = array();
	private $FFhiddenfields = array();
	private $FFboolfields = array("ff_fix","ff_readonly","ff_calcopening");

    public function FFClosedButtonClicked($sender, $param) {
            $this->page->mpnlFeldfunktion->Hide();
	}

    public function bindListFeldfunktionValue(){
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition="idta_struktur_type = :suchbedingung1";
    	$criteria->Parameters[':suchbedingung1'] = $this->page->idta_struktur_type->Text;

        $this->FeldfunktionListe->DataSource=FeldfunktionRecord::finder()->findAll($criteria);
	$this->FeldfunktionListe->dataBind();
    }

    public function FFDeleteButtonClicked($sender,$param){

            $tempus='FFed'.$this->FFprimarykey;

            if($this->FFedfeldfunktion_edit_status->Text == '1'){
                $WerteRecord = WerteRecord::finder()->findAllByidta_feldfunktion($this->$tempus->Text);
                foreach($WerteRecord As $Werte){
                    $TmpRec = WerteRecord::finder()->FindByPK($Werte->idtt_werte);
                    $TmpRec->delete();
                }
		$FFeditRecord = FeldfunktionRecord::finder()->findByPK($this->$tempus->Text);
                $FFeditRecord->delete();
            }
        $this->bindListFeldfunktionValue();
    }
        
    public function load_Feldfunktion($sender,$param){
    	
    	$item = $param->Item;
    	$myitem=FeldfunktionRecord::finder()->findByPK($item->FF_idta_feldfunktion->Text);
    	
    	$tempus = 'FFed'.$this->FFprimarykey;
		$monus = $this->FFprimarykey;
		
		$this->$tempus->Text = $myitem->$monus;
		
    	//HIDDEN
		foreach ($this->FFhiddenfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}
		
		//DATUM
		foreach ($this->FFatfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}
		
		//BOOL
		foreach ($this->FFboolfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}
		
		//NON DATUM
		foreach ($this->FFfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}
		
		$this->FFedfeldfunktion_edit_status->Text = 1;
		$this->FFedidta_feldfunktion->Text = $item->FF_idta_feldfunktion->Text;
        //$this->FFedff_type->Text==2 OR $this->FFedff_type->Text==5?$this->COLLECTOR->setVisible(true):$this->COLLECTOR->setVisible(false);
        $this->bindListCollectorValue();
    }
    
	public function FFSavedButtonClicked($sender,$param){
		
		$tempus='FFed'.$this->FFprimarykey;
		
		if($this->FFedfeldfunktion_edit_status->Text == '1'){
			$FFeditRecord = FeldfunktionRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$FFeditRecord = new FeldfunktionRecord;
		}
	
		//HIDDEN
		foreach ($this->FFhiddenfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$FFeditRecord->$recordfield = $this->$edrecordfield->Value;
		}
		
		//DATUM
		foreach ($this->FFatfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$FFeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}
		
		//BOOL
		foreach ($this->FFboolfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$FFeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}
		
		foreach ($this->FFfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$FFeditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$FFeditRecord->save();
		
		$this->bindListFeldfunktionValue();
	}

	public function FFNewButtonClicked($sender,$param){
    	
        $pivotbericht = $this->FFedidta_struktur_type->Text;

        $tempus = 'FFed'.$this->FFprimarykey;
        $monus = $this->FFprimarykey;

        $this->$tempus->Text = '0';
		
    	//HIDDEN
		foreach ($this->FFhiddenfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}
		
		//DATUM
		foreach ($this->FFatfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}
		
		//BOOL
		foreach ($this->FFboolfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->setChecked(0);
		}
		
		//NON DATUM
		foreach ($this->FFfields as $recordfield){
			$edrecordfield = 'FFed'.$recordfield;
			$this->$edrecordfield->Text = '0';
		}
		
        $this->FFedidta_struktur_type->Text = $pivotbericht;
		$this->FFedfeldfunktion_edit_status->Text = '0';
    }
    
    
	public function FeldfunktionList_PageIndexChanged($sender,$param)
		{
			$this->FeldfunktionListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListFeldfunktionValue();
		}
        

    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN

    /* here comes the part for the collector */
	/* here comes the part for the collector */
	/* here comes the part for the collector */
	/* here comes the part for the collector */

	private $COLprimarykey = "idta_collector";
	private $COLfields = array("idta_feldfunktion","col_idtafeldfunktion","col_operator");
	private $COLatfields = array();
	private $COLhiddenfields = array();
	private $COLboolfields = array();

    public function buildCollectorPullDown(){
            $this->COLedcol_idtafeldfunktion->DataSource = PFH::build_SQLPullDown(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name"),"idta_struktur_type = ".$this->page->idta_struktur_type->Text);
            $this->COLedcol_idtafeldfunktion->DataBind();

            $data=array('+'=>"+","-"=>"-","/"=>"/","*"=>"*");
            $this->COLedcol_operator->DataSource = $data;
            $this->COLedcol_operator->DataBind();
        }

    public function bindListCollectorValue(){
            $this->COLedidta_feldfunktion->Text = $this->FFedidta_feldfunktion->Text;
            $this->buildCollectorPullDown();
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition="idta_feldfunktion = :suchbedingung1";
            $criteria->Parameters[':suchbedingung1'] = $this->COLedidta_feldfunktion->Text;

            $this->CollectorListe->DataSource=CollectorView::finder()->findAll($criteria);
            $this->CollectorListe->dataBind();
    }

    public function COLDeleteButtonClicked($sender,$param){

		$tempus='COLed'.$this->COLprimarykey;

		if($this->COLedcollector_edit_status->Text == '1'){
			$COLeditRecord = CollectorRecord::finder()->findByPK($this->$tempus->Text);
            $COLeditRecord->delete();
		}
        $this->bindListCollectorValue();
    }

    public function load_Collector($sender,$param){

    	$item = $param->Item;
    	$myitem=CollectorRecord::finder()->findByPK($item->COL_idta_collector->Text);

    	$tempus = 'COLed'.$this->COLprimarykey;
		$monus = $this->COLprimarykey;

		$this->$tempus->Text = $myitem->$monus;

    	//HIDDEN
		foreach ($this->COLhiddenfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->setText($myitem->$recordfield);
		}

		//DATUM
		foreach ($this->COLatfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->setDate($myitem->$recordfield);
		}

		//BOOL
		foreach ($this->COLboolfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->setChecked($myitem->$recordfield);
		}

		//NON DATUM
		foreach ($this->COLfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->Text = $myitem->$recordfield;
		}

		$this->COLedcollector_edit_status->Text = 1;
		$this->COLedidta_collector->Text = $item->COL_idta_collector->Text;
        
    }

	public function COLSavedButtonClicked($sender,$param){

		$tempus='COLed'.$this->COLprimarykey;

		if($this->COLedcollector_edit_status->Text == '1'){
			$COLeditRecord = CollectorRecord::finder()->findByPK($this->$tempus->Text);
		}
		else{
			$COLeditRecord = new CollectorRecord;
		}

		//HIDDEN
		foreach ($this->COLhiddenfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$COLeditRecord->$recordfield = $this->$edrecordfield->Value;
		}

		//DATUM
		foreach ($this->COLatfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$COLeditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
		}

		//BOOL
		foreach ($this->COLboolfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$COLeditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
		}

		foreach ($this->COLfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$COLeditRecord->$recordfield = $this->$edrecordfield->Text;
		}

		$COLeditRecord->save();

		$this->bindListCollectorValue();
	}

	public function COLNewButtonClicked($sender,$param){

        $pivotbericht = $this->COLedidta_feldfunktion->Text;

        $tempus = 'COLed'.$this->COLprimarykey;
        $monus = $this->COLprimarykey;

        $this->$tempus->Text = '0';

    	//HIDDEN
		foreach ($this->COLhiddenfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->setValue('0');
		}

		//DATUM
		foreach ($this->COLatfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->setDate(date('Y-m-d',time()));
		}

		//BOOL
		foreach ($this->COLboolfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->setChecked(0);
		}

		//NON DATUM
		foreach ($this->COLfields as $recordfield){
			$edrecordfield = 'COLed'.$recordfield;
			$this->$edrecordfield->Text = '0';
		}

        $this->COLedidta_feldfunktion->Text = $pivotbericht;
		$this->COLedcollector_edit_status->Text = '0';
    }


	public function CollectorList_PageIndexChanged($sender,$param)
		{
			$this->CollectorListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListCollectorValue();
		}


    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    
	
}

?>