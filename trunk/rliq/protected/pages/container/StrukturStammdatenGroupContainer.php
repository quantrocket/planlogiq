<?php

class StrukturStammdatenGroupContainer extends TTemplateControl
{

    public function onLoad($param)
    {
            parent::onLoad($param);
            if(!$this->page->isPostBack && !$this->page->isCallback){
                $this->bindListStrukturStammdatenGroup();
                $this->createStrukturStammdatenGroupPullDown();
            }
    }

    public function createStrukturStammdatenGroupPullDown(){
    //Als erstes die Organisation
            $this->RCedidta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
            $this->RCedidta_stammdaten_group->dataBind();
    }

    private $RCprimarykey = "idtm_struktur_has_ta_stammdaten_group";
    private $RCfields = array("idtm_struktur","idta_stammdaten_group");
    private $RCdatfields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array();

    public function bindListStrukturStammdatenGroup(){
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition ="idtm_struktur = :suchtext1";
            $criteria->Parameters[':suchtext1'] = $this->RCedidtm_struktur->Text;

            $this->StrukturStammdatenGroupListe->DataSource=StrukturStammdatenGroupView::finder()->findAll($criteria);
            $this->StrukturStammdatenGroupListe->dataBind();
  }

    public function bindListStammdatenValue($idta_stammdaten_group){

            $criteria = new TActiveRecordCriteria();
            $criteria->Condition = "idta_stammdaten_group = :suchtext1";
            $criteria->Parameters[':suchtext1']=$idta_stammdaten_group;

            $this->SGStammdatenListe->DataSource=$this->buildAdditionalStammdatenSource(StammdatenRecord::finder()->findAll($criteria));
            $this->SGStammdatenListe->dataBind();
}

    public function RCDeleteButtonClicked($sender,$param){
        $tempus='RCed'.$this->RCprimarykey;
        $Record = StrukturStammdatenGroupRecord::finder()->findByPK($this->$tempus->Text);
        $Record->delete();
        $this->bindListStrukturStammdatenGroup();
        $this->RCNewButtonClicked($sender,$param);
    }

    public function load_StrukturStammdatenGroup($sender,$param){

    $item = $param->Item;
    $myitem=StrukturStammdatenGroupRecord::finder()->findByPK($item->lst_idtm_struktur_has_ta_stammdaten_group->Text);

    $tempus = 'RCed'.$this->RCprimarykey;
            $monus = $this->RCprimarykey;

            $this->$tempus->Text = $myitem->$monus;

    //HIDDEN
            foreach ($this->RChiddenfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->setText($myitem->$recordfield);
            }

            //DATUM
            foreach ($this->RCdatfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->setDate($myitem->$recordfield);
            }

            //BOOL
            foreach ($this->RCboolfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->setChecked($myitem->$recordfield);
            }

            //NON DATUM
            foreach ($this->RCfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->Text = $myitem->$recordfield;
            }

            $this->RCedstruktur_stammdaten_group_edit_status->Text = 1;
            //$this->RCedidtm_struktur_has_ta_stammdaten_group->Text = $item->lst_idtm_struktur_has_ta_stammdaten_group->Text;

            $this->bindListStammdatenValue($myitem->idta_stammdaten_group);
}

    public function RCSavedButtonClicked($sender,$param){

            $tempus='RCed'.$this->RCprimarykey;

            if($this->RCedstruktur_stammdaten_group_edit_status->Text == '1'){
                    $RCEditRecord = StrukturStammdatenGroupRecord::finder()->findByPK($this->$tempus->Text);
            }
            else{
                    $RCEditRecord = new StrukturStammdatenGroupRecord;
            }

            //HIDDEN
            foreach ($this->RChiddenfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $RCEditRecord->$recordfield = $this->$edrecordfield->Value;
            }

            //DATUM
            foreach ($this->RCdatfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
            }

            //BOOL
            foreach ($this->RCboolfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
            }

            foreach ($this->RCfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
            }

            $RCEditRecord->save();

            $this->bindListStrukturStammdatenGroup();
    }

    public function RCNewButtonClicked($sender,$param){

    $tempus = 'RCed'.$this->RCprimarykey;
    $monus = $this->RCprimarykey;

    $temp = $this->RCedidtm_struktur->Text;

    //HIDDEN
            foreach ($this->RChiddenfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->setValue('0');
            }

            //DATUM
            foreach ($this->RCdatfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->setDate(date('Y-m-d',time()));
            }

            //BOOL
            foreach ($this->RCboolfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->Checked(0);
            }

            //NON DATUM
            foreach ($this->RCfields as $recordfield){
                    $edrecordfield = 'RCed'.$recordfield;
                    $this->$edrecordfield->Text = '0';
            }

            $this->RCedstruktur_stammdaten_group_edit_status->Text = '0';
            $this->RCedidtm_struktur->Text = $temp;

            $this->bindListStammdatenValue(0);

}


    public function StrukturStammdatenGroupList_PageIndexChanged($sender,$param)
            {
                    $this->StrukturStammdatenGroupListe->CurrentPageIndex = $param->NewPageIndex;
                    $this->bindListStrukturStammdatenGroup();
            }

    public function buildAdditionalStammdatenSource($StammdatenRecord){

        foreach($StammdatenRecord As $Node){
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition = "parent_idtm_struktur = :suchtext1 AND idtm_stammdaten = :suchtext2";
            $criteria->Parameters[':suchtext1']=$this->RCedidtm_struktur->Text;
            $criteria->Parameters[':suchtext2']=$Node->idtm_stammdaten;

            if(count(StrukturRecord::finder()->findAll($criteria))>0){
                $Node->ttstammdaten_created = 'OK';
            }

        }
        return $StammdatenRecord;
    }

    public function create_Stammdaten($sender,$param){
        $item = $param->Item;
        $StammdatenRecord = StammdatenRecord::finder()->findByidtm_stammdaten($item->lst_idtm_stammdaten->Text);
        $StammdatenGroupRecord = StammdatenGroupRecord::finder()->findByidta_stammdaten_group($StammdatenRecord->idta_stammdaten_group);
        if(!$item->lst_ttstammdaten_created->Text=="OK"){
            $StrukturRecord = new StrukturRecord();
            $StrukturRecord->idta_struktur_type = $StammdatenGroupRecord->idta_struktur_type;
            $StrukturRecord->idtm_stammdaten = $StammdatenRecord->idtm_stammdaten;
            $StrukturRecord->struktur_name = $StammdatenRecord->stammdaten_name;
            $StrukturRecord->parent_idtm_struktur = $this->RCedidtm_struktur->Text;
            $StrukturRecord->struktur_lft = 2;
            $StrukturRecord->struktur_rgt = 2;
            $StrukturRecord->save();
        }
        $this->bindListStammdatenValue($StammdatenRecord->idta_stammdaten_group);
    }

    public function create_StammdatenAll($sender,$param){
        $StammdatenGroupRecord = StammdatenGroupRecord::finder()->findByidta_stammdaten_group($this->RCedidta_stammdaten_group->Text);
        $AllStammdatenRecord = StammdatenRecord::finder()->findAllByidta_stammdaten_group($this->RCedidta_stammdaten_group->Text);
        foreach($AllStammdatenRecord AS $SRecord){
            $StammdatenRecord = StammdatenRecord::finder()->findByidtm_stammdaten($SRecord->idtm_stammdaten);
            if(count(StrukturRecord::finder()->find("idtm_stammdaten = ? AND parent_idtm_struktur = ?",$StammdatenRecord->idtm_stammdaten,$this->RCedidtm_struktur->Text))<1){
                $StrukturRecord = new StrukturRecord();
                $StrukturRecord->idta_struktur_type = $StammdatenGroupRecord->idta_struktur_type;
                $StrukturRecord->idtm_stammdaten = $StammdatenRecord->idtm_stammdaten;
                $StrukturRecord->struktur_name = $StammdatenRecord->stammdaten_name;
                $StrukturRecord->parent_idtm_struktur = $this->RCedidtm_struktur->Text;
                $StrukturRecord->struktur_lft = 2;
                $StrukturRecord->struktur_rgt = 2;
                $StrukturRecord->save();
            }
        }
        $this->bindListStammdatenValue($this->RCedidta_stammdaten_group->Text);
    }

    public function StammdatenList_PageIndexChanged($sender,$param)
            {
			$this->SGStammdatenListe->CurrentPageIndex = $param->NewPageIndex;
			$this->bindListStammdatenValue($this->RCedidta_stammdaten_group->Text);
		}

	
}

?>