<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class dimmappingview extends TPage{

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    private $ImportedValues = array();
    private $ImportedFields = array();

    public function onLoad($param){
        if(!$this->isPostBack && !$this->isCallback){
            $this->initPullDowns();
        }
    }

    public function Refresh_dim_idta_stammdaten_group($sender,$param){
        $this->dim_idta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
        $this->dim_idta_stammdaten_group->dataBind();
    }

    private function initPullDowns(){
        $this->dim_idta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
        $this->dim_idta_stammdaten_group->dataBind();

        $this->dim_idta_stammdatensicht->DataSource=StammdatensichtRecord::finder()->findAll();
        $this->dim_idta_stammdatensicht->dataBind();

        $sender=0;
        $param=0;
        $this->linkStammdaten($sender, $param);
    }

    public function linkStammdaten($sender,$param){
        $MultiDimension = 0;
        $Usersql = "SELECT idtm_stammdaten, stammdaten_name FROM tm_stammdaten WHERE idta_stammdaten_group = '". $this->dim_idta_stammdaten_group->Text ."'";
        $Userdata = PFH::convertdbObjectArray(StammdatenRecord::finder()->findAllBySql($Usersql),array("idtm_stammdaten","stammdaten_name"));
        $this->fields_idtm_stammdaten->DataSource=$Userdata;
        $this->fields_idtm_stammdaten->dataBind();

        $LABEL_AVAILABLE_VALUES="";
        $Records = StammdatenGroupView::finder()->findAll("parent_idta_stammdaten_group = ? AND idta_stammdatensicht = ?",$this->dim_idta_stammdaten_group->Text,$this->dim_idta_stammdatensicht->Text);
        $counter = 0;
        if(count($Records)>0){
            $SQLINStatement = "(";
            foreach ($Records AS $Record){
                $counter==0?$SQLINStatement.="'".$Record->idta_stammdaten_group."' ":$SQLINStatement.=",'".$Record->idta_stammdaten_group."'";
                $counter==0?$LABEL_AVAILABLE_VALUES .=" ".$Record->stammdaten_group_name:$LABEL_AVAILABLE_VALUES .=", ".$Record->stammdaten_group_name;
                $counter++;
                if($Record->stammdaten_group_multi == 1){
                    $MultiDimension = 1;
                }
            }
            $SQLINStatement .= ") ";
        }else{
            $SQLINStatement = "('-1') ";
        }
        $this->LABEL_AVAILABLE_VALUES->Text = $LABEL_AVAILABLE_VALUES;

        $TTRecords = TTStammdatenStammdatenRecord::finder()->findAll();
        $counter = 0;
        if(count($TTRecords)>=1 AND $MultiDimension == 0){
            $TTSQLINStatement = "(";
            foreach ($TTRecords AS $TTRecord){
                $counter==0?$TTSQLINStatement.="'".$TTRecord->idtm_stammdaten."' ":$TTSQLINStatement.=",'".$TTRecord->idtm_stammdaten."'";
                $counter++;
            }
            $TTSQLINStatement .= ") ";
        }else{
            $TTSQLINStatement = "('-1') ";
        }

        $Usersql = "SELECT idtm_stammdaten, stammdaten_name FROM tm_stammdaten WHERE idta_stammdaten_group IN ". $SQLINStatement."AND idtm_stammdaten NOT IN ".$TTSQLINStatement;
        $Userdata = PFH::convertdbObjectArray(StammdatenRecord::finder()->findAllBySql($Usersql),array("idtm_stammdaten","stammdaten_name"));
        if(count($Userdata)<1){
            $Userdata=array("0"=>"leer");
        }
        $this->fields_children_idtm_stammdaten->DataSource=$Userdata;
        $this->fields_children_idtm_stammdaten->dataBind();
    }

    public function addSingleStammdatenRecord($sender,$param){
        //auf welche dimension sollen die werte zugeordnet werden
        $sgIndecies = $this->fields_idtm_stammdaten->SelectedIndices;
        foreach($sgIndecies as $index)
        {
            $idtm_stammdaten_group=$this->fields_idtm_stammdaten->Items[$index]->Value;
        }
        //der Wert der zugeordnet werden soll
        $sIndecies = $this->fields_children_idtm_stammdaten->SelectedIndices;
        foreach($sIndecies as $sindex)
        {
            $NewStammdatenStammdatenRecord = new TTStammdatenStammdatenRecord();
            $NewStammdatenStammdatenRecord->idtm_stammdaten_group=$idtm_stammdaten_group;
            $NewStammdatenStammdatenRecord->idta_stammdatensicht=$this->dim_idta_stammdatensicht->Text;
            $NewStammdatenStammdatenRecord->idtm_stammdaten=$this->fields_children_idtm_stammdaten->Items[$sindex]->Value;
            $NewStammdatenStammdatenRecord->save();
        }
        $this->load_fields_mapped_idtm_stammdaten($sender, $param);
    }

    public function deleteDetailFieldsButtonClicked($sender,$param){
        //der Wert der zugeordnet werden soll
        $sIndecies = $this->fields_children_idtm_stammdaten->SelectedIndices;
        foreach($sIndecies as $sindex)
        {            
            TTStammdatenStammdatenRecord::finder()->deleteByidtm_stammdaten_group($this->fields_children_idtm_stammdaten->Items[$sindex]->Value);
            $DELStammdatenRecord = StammdatenRecord::finder()->findBy_idtm_stammdaten($this->fields_children_idtm_stammdaten->Items[$sindex]->Value);
            $DELStammdatenRecord->delete();
        }
        $this->load_fields_mapped_idtm_stammdaten($sender, $param);
    }

    public function deleteFieldsButtonClicked($sender,$param){
        //der Wert der zugeordnet werden soll
        $sIndecies = $this->fields_idtm_stammdaten->SelectedIndices;
        foreach($sIndecies as $sindex)
        {
            TTStammdatenStammdatenRecord::finder()->deleteByidtm_stammdaten_group($this->fields_idtm_stammdaten->Items[$sindex]->Value);
            $DELStammdatenRecord = StammdatenRecord::finder()->findBy_idtm_stammdaten($this->fields_idtm_stammdaten->Items[$sindex]->Value);
            $DELStammdatenRecord->delete();            
        }
        $this->load_fields_mapped_idtm_stammdaten($sender, $param);
    }

    public function removeSingleStammdatenRecord($sender,$param){
        //der Wert der entfernt werden soll
        $sIndecies = $this->fields_mapped_idtm_stammdaten->SelectedIndices;
        foreach($sIndecies as $sindex)
        {
            $NewStammdatenStammdatenRecord = TTStammdatenStammdatenRecord::finder()->findBy_idtm_stammdaten($this->fields_mapped_idtm_stammdaten->Items[$sindex]->Value);
            $NewStammdatenStammdatenRecord->delete();
        }
        $this->load_fields_mapped_idtm_stammdaten($sender, $param);
    }

    public function AddFieldsButtonClicked($sender,$param){
        //check ob dimension schon besteht
        $idta_stammdaten_group = $this->dim_idta_stammdaten_group->Text;
        $stammdaten_name = $this->add_fields_idtm_stammdaten->Text;
        if($stammdaten_name!=""){
            $NewStammdatenRecord = new StammdatenRecord();
            $NewStammdatenRecord->idta_stammdaten_group=$idta_stammdaten_group;
            $NewStammdatenRecord->stammdaten_name = $stammdaten_name;
            $NewStammdatenRecord->stammdaten_key_extern = $stammdaten_name;
            $NewStammdatenRecord->save();
        }
        $this->linkStammdaten($sender, $param);
    }

    public function load_fields_mapped_idtm_stammdaten($sender,$param){
        $this->linkStammdaten($sender, $param);
        $sgIndecies = $this->fields_idtm_stammdaten->SelectedIndices;
        $idtm_stammdaten_group = 0;
        foreach($sgIndecies as $index)
        {
            $idtm_stammdaten_group=$this->fields_idtm_stammdaten->Items[$index]->Value;
        }
        $Usersql = "SELECT tm_stammdaten.idtm_stammdaten, tm_stammdaten.stammdaten_name FROM tm_stammdaten INNER JOIN tt_stammdaten_stammdaten ON tm_stammdaten.idtm_stammdaten = tt_stammdaten_stammdaten.idtm_stammdaten WHERE tt_stammdaten_stammdaten.idtm_stammdaten_group = '".$idtm_stammdaten_group."' AND tt_stammdaten_stammdaten.idta_stammdatensicht = '".$this->dim_idta_stammdatensicht->Text."'";
        $Userdata = PFH::convertdbObjectArray(StammdatenRecord::finder()->findAllBySql($Usersql),array("idtm_stammdaten","stammdaten_name"));
        if(count($Userdata)<1){
            $Userdata=array("0"=>"leer");
        }
        $this->fields_mapped_idtm_stammdaten->DataSource=$Userdata;
        $this->fields_mapped_idtm_stammdaten->dataBind();        
    }

    public function loadCSVFile($sender,$param){
            $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name.".tmp";
            if(is_file($tempUserFile)){
                $TempFile = fopen($tempUserFile,'r'); //hier oeffne ich die datei im readonly
                $row=1;
                while (($mydata = fgetcsv($TempFile,0,';')) !== FALSE) {
                    $num = count($mydata);
                    if($row==1){
                        for($c=0; $c<$num;$c++) {
                            $FileLabel[$row][$c] = $mydata[$c];
                        }
                    }else{
                        for($c=0; $c<$num;$c++) {
                            $FileData[$row-1][$FileLabel[1][$c]] = utf8_encode($mydata[$c]);
                        }
                    }
                    $row++;
                }
                fclose($TempFile);
            }
            //$this->ImportedFields = $FileLabel;
            $this->ImportedValues = $FileData;
            $this->transferCSVFile($sender,$param);
        }

    public function transferCSVFile($sender,$param){
            //check ob dimension schon besteht
            if($sender->Id=="CSVUploadFile"){
                $idta_stammdaten_group = StammdatenGroupRecord::finder()->findBy_parent_idta_stammdaten_group($this->dim_idta_stammdaten_group->Text)->idta_stammdaten_group;
            }else{
                $idta_stammdaten_group = StammdatenGroupRecord::finder()->findBy_idta_stammdaten_group($this->dim_idta_stammdaten_group->Text)->idta_stammdaten_group;
            }
            
            //laden des Wertearrays
            foreach($this->ImportedValues As $row){
                foreach($row as $key=>$value){
                    $MyCheckRecord = StammdatenRecord::finder()->findAll('stammdaten_name = ? AND idta_stammdaten_group = ?',$value,$idta_stammdaten_group);
                    if(count($MyCheckRecord)==0){
                        $NewStammdatenRecord = new StammdatenRecord();
                        $NewStammdatenRecord->idta_stammdaten_group=$idta_stammdaten_group;
                        $NewStammdatenRecord->stammdaten_name = $value;
                        $NewStammdatenRecord->stammdaten_key_extern = $value;
                        $NewStammdatenRecord->save();
                    }
                }
            }
            $this->linkStammdaten($sender,$param);
        }

    public function fileUploaded($sender,$param){
        if($sender->getHasFile()){
            $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name.".tmp";
            if(is_file($tempUserFile)) {
                unlink($tempUserFile);
            }
            $sender->saveAs($tempUserFile,true);
            $this->loadCSVFile($sender,$param);
        }
    }

}

?>
