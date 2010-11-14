<?php

class importerworkspace extends TPage {

    private $_assetPath;
    private $_assetPaths;
    private $_currentPath;
    private $_currentDirectory;
    private $_baseDirectory;

    private $session;
    private $ImportedValues = array();
    private $ImportedFields = array();

    private $MyMapping = array();

    private $InternalFields=array('ti_id1'=>'ID 1','ti_id2'=>'ID 2','ti_id3'=>'ID 3','ti_id4'=>'ID 4','ti_id5'=>'ID 5','ti_id6'=>'ID 6','ti_id7'=>'ID 7','ti_id8'=>'ID 8','ti_id9'=>'ID 9','ti_id10'=>'ID 10',
    'ti_label1'=>'Label 1','ti_label2'=>'Label 2','ti_label3'=>'Label 3','ti_label4'=>'Label 4','ti_label5'=>'Label 5','ti_label6'=>'Label 6','ti_label7'=>'Label 7','ti_label8'=>'Label 8','ti_label9'=>'Label 9','ti_label10'=>'Label 10',
    'ti_value1'=>'Value 1','ti_value2'=>'Value 2','ti_value3'=>'Value 3','ti_value4'=>'Value 4','per_intern'=>'Periode','nomap'=>'no mapping');

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {
        parent::onLoad($param);
        if(!$this->isPostBack && !$this->isCallback) {
        //$this->bindList_FilePreview();
            $this->loadAvailableMappings();
            $this->buildAutoMappingPullDown();
            $this->buildStammdatenMapping();
            $this->bindListImportMapping();

            $HRKEYTest = new PFHierarchyPullDown();
            $HRKEYTest->setStructureTable("tm_struktur");
            $HRKEYTest->setRecordClass(StrukturRecord::finder());
            $HRKEYTest->setPKField("idtm_struktur");
            $HRKEYTest->setField("struktur_name");
            //$HRKEYTest->StartPoint=$this->idtm_struktur->Text;
            $HRKEYTest->letsrun();

            $this->idtm_struktur->dataSource = $HRKEYTest->myTree;
            $this->idtm_struktur->dataBind();
        }
    }

    public function NextStep($sender,$param){
            $this->ImporterWizzard->ActiveStepIndex=$this->ImporterWizzard->ActiveStepIndex+1;
    }

    public function PreviousStep($sender,$param){
            $this->ImporterWizzard->ActiveStepIndex=$this->ImporterWizzard->ActiveStepIndex-1;
    }

    public function loadAvailableMappings() {
        $sql = "SELECT ima_name FROM ta_automapping GROUP BY ima_name";
        $result = AutoMappingRecord::finder()->findAllBySQL($sql);
        foreach($result as $record) {
            $tbd[$record->ima_name]=$record->ima_name;
        }
        $this->availableMappingName->DataSource=$tbd;
        $this->availableMappingName->dataBind();
    }

    public function chooseMappingName($sender,$param) {
        $this->MappingName->Text = $this->availableMappingName->Text;
    }

    public function buildStammdatenMapping() {
        $this->idta_stammdaten_group->DataSource=PFH::build_SQLPullDown(StammdatenGroupRecord::finder(),"ta_stammdaten_group",array("idta_stammdaten_group","stammdaten_group_name"));
        $this->idta_stammdaten_group->dataBind();
        $this->bindListStammdatenValue();
    }

    public function bindListStammdatenValue() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "idta_stammdaten_group = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->idta_stammdaten_group->Text;
        $this->StammdatenListe->DataSource=StammdatenRecord::finder()->findAll($criteria);
        $this->StammdatenListe->dataBind();
    }

    public function StammdatenList_PageIndexChanged($sender,$param) {
        $this->StammdatenListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListStammdatenValue();
    }

    public function buildAutoMappingPullDown() {
        $listSource = array();
        for($i=1;$i<=$this->NumberID->Text;$i++) {
            $listSource[$i] = "Level ".$i;
        }
        $this->SelectAMLevel->dataSource=$listSource;
        $this->SelectAMLevel->dataBind();

        $this->Selectidta_feldfunktion->DataSource=PFH::build_SQLPullDownAdvanced(FeldfunktionRecord::finder(),"ta_feldfunktion",array("idta_feldfunktion","ff_name","idta_struktur_type"));
        $this->Selectidta_feldfunktion->dataBind();
    }

    public function buildARepeaterPullDown() {
        foreach($this->MappingPreview->Items as $Gitem) {
            $Gitem->TargetField->DataSource=$this->InternalFields;
            $Gitem->TargetField->dataBind();
        }
    }

    public function loadCSVFile() {
        $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name.".tmp";
        if(is_file($tempUserFile)) {
            $TempFile = fopen($tempUserFile,'r'); //hier oeffne ich die datei im readonly
            $row=1;
            while (($mydata = fgetcsv($TempFile,0,$this->FileSeperator->Text)) !== FALSE) {
                $num = count($mydata);
                if($row==1) {
                    for($c=0; $c<$num;$c++) {
                        $FileLabel[$row][$c] = $mydata[$c];
                    }
                }else {
                    for($c=0; $c<$num;$c++) {
                        $FileData[$row-1][$FileLabel[1][$c]] = utf8_encode($mydata[$c]);
                    }
                }
                $row++;
            }
            fclose($TempFile);
        }
        $this->ImportedFields = $FileLabel;
        $this->ImportedValues = $FileData;
    }

    public function fileUploaded($sender,$param) {
        if($sender->HasFile) {
            $this->UploadFileName->Text=$sender->FileName;
            $this->UploadFileLocalName->Text=$sender->LocalName;
            $this->UploadFileSize->Text=$sender->FileSize;
            $this->UploadFileType->Text=$sender->FileType;

            $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/".$this->user->Name.".tmp";
            if(is_file($tempUserFile)) {
                unlink($tempUserFile);
            }
            $sender->saveAs($tempUserFile,true);

            $this->loadCSVFile();

            $this->bindList_FilePreview();
            $this->bindList_MappingPreview();
        }
    }

    public function bindList_MappingPreview() {
        $resultfields = array();
        foreach($this->ImportedFields as $Fields) {
            foreach($Fields as $Field) {
                array_push($resultfields,array('OriginalField'=>$Field));
            }
        }
        $this->MappingPreview->dataSource=$resultfields;
        $this->MappingPreview->dataBind();
        $this->buildARepeaterPullDown();
    }

    public function bindList_FilePreview() {
        $this->loadCSVFile();

        $this->FilePreview->dataSource=$this->ImportedValues;
        $this->FilePreview->dataBind();

        $this->bindList_MappingPreview();
    }

    public function FilePreview_changed($sender,$param) {
        $this->FilePreview->CurrentPageIndex = $param->NewPageIndex;
        $this->bindList_FilePreview();
    }

    public function applyMapping($sender,$param) {
        foreach($this->MappingPreview->Items as $Gitem) {
            if($Gitem->TargetField->Text!="nomap") {
                $this->MyMapping[$Gitem->OriginalField->Text]=$Gitem->TargetField->Text;
            }
        }
        $this->transferCSVFile();
        $sender->Text = "Zuordnung gesichert";
        $this->generateAutoMappingInformation();
        $this->bindListAutoMapping();
    }

    public function generateAutoMappingInformation() {
        $availableIDs = $this->NumberID->Text;
        for($i=1;$i<=$availableIDs;$i++) {
            $SQLTempImport = "SELECT ti_id{$i}, ti_label{$i} FROM tm_tempimport GROUP BY ti_id{$i}, ti_label{$i}";
            $Results = TempImportRecord::finder()->findAllBySQL($SQLTempImport);
            foreach($Results As $Result) {
                $ti_id = $Result->{"ti_id".$i};
                $ti_label = $Result->{"ti_label".$i};
                //print_r($ti_id);
                $ima_name = $this->MappingName->Text;
                if(count(AutoMappingRecord::finder()->find("ima_name = ? AND ti_id = ?",$ima_name,$ti_id))==1) {
                    $AMRecord = AutoMappingRecord::finder()->find("ima_name = ? AND ti_id = ?",$ima_name,$ti_id);
                }else {
                    $AMRecord = new AutoMappingRecord();
                }
                $AMRecord->ti_id = $ti_id;
                if($ti_label!="") {
                    $AMRecord->ti_label = $ti_label;
                }else {
                    $AMRecord->ti_label = $ti_id;
                }
                $AMRecord->ama_id=$i;
                $AMRecord->ima_name = $ima_name;
                $AMRecord->save();
            }
        }
    }

    public function transferCSVFile() {
    //loeschen der bestehenden mappings
        TempImportRecord::finder()->deleteAll('user_name = ?',$this->user->Name);
        //laden des Wertearrays
        $this->loadCSVFile();
        foreach($this->ImportedValues As $row) {
            $NewTIRow = new TempImportRecord;
            $NewTIRow->user_name = $this->user->Name;
            $NewTIRow->ti_name = $this->MappingName->Text;
            foreach($this->MyMapping AS $key=>$value) {
                $NewTIRow->{$value} = $row[$key];
            }
            $NewTIRow->save();
        }
        $this->CopyTempImport();
    }

    public function bindListAutoMapping() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "ima_name = :suchtext AND ama_id = :suchtext2";
        $criteria->Parameters[':suchtext'] = $this->MappingName->Text;
        $criteria->Parameters[':suchtext2'] = $this->SelectAMLevel->Text;
        $this->lstAutoMapping->dataSource=AutoMappingRecord::finder()->findAll($criteria);
        $this->lstAutoMapping->dataBind();
    }

    public function bindListImportMapping() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition = "ima_name = :suchtext";
        $criteria->Parameters[':suchtext'] = $this->MappingName->Text;
        $this->lstImportMapping->dataSource=ImportMappingRecord::finder()->findAll($criteria);
        $this->lstImportMapping->dataBind();
    }

    public function AutoMappingListeChanged($sender,$param) {
        $this->lstAutoMapping->CurrentPageIndex=$param->NewPageIndex;
        $this->bindListAutoMapping();
    }

    public function ImportMappingListeChanged($sender,$param) {
        $this->lstImportMapping->CurrentPageIndex=$param->NewPageIndex;
        $this->bindListImportMapping();
    }

    public function lstAutoMappingEdit($sender,$param) {
        $this->lstAutoMapping->EditItemIndex=$param->Item->ItemIndex;
        $this->bindListAutoMapping();
    }

    public function lstAutoMappingCancel($sender,$param) {
        $this->lstAutoMapping->EditItemIndex=-1;
        $this->bindListAutoMapping();
    }

    public function applyAutoMapFilter($sender,$param) {
        $this->bindListAutoMapping();
    }

    public function applyDimension($sender,$param) {
        $this->select_idtm_stammdaten->Text = $sender->CommandParameter;
        $RecordToChange = AutoMappingRecord::finder()->findByPK($this->applyto_idtm_stammdaten->Text);
        $RecordToChange->idtm_stammdaten = $sender->CommandParameter;
        $RecordToChange->save();
        $this->bindListAutoMapping();
    }

    public function createDimension($sender,$param) {
        $NewDimension = new StammdatenRecord();
        $NewDimension->idta_stammdaten_group = $this->idta_stammdaten_group->Text;
        $NewDimension->stammdaten_name = AutoMappingRecord::finder()->findByPK($sender->CommandParameter)->ti_label;
        $NewDimension->save();
        $this->bindListStammdatenValue();
    }

    public function FieldapplyDimension($sender,$param) {
        $this->applyto_idtm_stammdaten->Text = $sender->CommandParameter;
    }

    public function SaveCurrentAutoMapping($sender,$param) {
        $RecordToChange = AutoMappingRecord::finder()->findByPK($sender->parent->lst_idta_automapping->Text);
        $RecordToChange->idta_feldfunktion = $sender->parent->lst_idta_feldfunktion->Text;
        $RecordToChange->ama_faktor = $sender->parent->lst_ama_faktor->Text;
        $RecordToChange->ama_lauf = $sender->parent->lst_ama_lauf->Text;
        $RecordToChange->ama_source = "M";
        $RecordToChange->save();
        $this->bindListAutoMapping();
    }

    public function getFeldfunktion($sender,$param) {
        $RecordToChange = AutoMappingRecord::finder()->findByPK($sender->CommandParameter);
        $RecordToChange->idta_feldfunktion = $this->Selectidta_feldfunktion->Text;
        $RecordToChange->save();
        $this->bindListAutoMapping();
    }

    private function CopyTempImport() {
    //loeschen der bestehenden mappings -> darf ich nicht loeschen...
    //ImportMappingRecord::finder()->deleteAll('ima_name = ?',$this->MappingName->Text);
        $AllTempImportedRecords = TempImportRecord::finder()->findAll('ti_name = ?',$this->MappingName->Text);
        foreach($AllTempImportedRecords AS $EXRecord) {
            $sqlin = "";
            for($i=1;$i<$this->NumberID->Text;$i++) {
                $sqlin .= "ima_id".$i."='".$EXRecord->{"ti_id".$i}."' AND ";
            }
            $sqlin .= "ima_id".$i."='".$EXRecord->{"ti_id".$i}."' AND ima_name='".$this->MappingName->Text."'";
            $sql = "SELECT * FROM tm_importmapping WHERE ".$sqlin;
            $RecordToCheck = ImportMappingRecord::finder()->findBySQL($sql);
            if(count($RecordToCheck)==1) {
                $RecordToSave = $RecordToCheck;
            }else {
                $RecordToSave = new ImportMappingRecord();
            }
            $RecordToSave->ima_name = $EXRecord->ti_name;
            for($ii=1;$ii<=10;$ii++) {
                $RecordToSave->{"ima_id".$ii} = $EXRecord->{"ti_id".$ii};
            }
            $RecordToSave->Save();
        }
        $this->bindListImportMapping();
    }

    /**
     * @name applyAutoMap -> the mapping loader
     * @param StartNode -> the startposition for the automapping
     */

    private $StartNode=1;
    private $TreeArray=array();

    public function applyStartPoint($sender,$param) {
        $this->StartNode = $sender->Text;
    }

    public function applyAutoMap() {
    //hier kommt dann der Part, wo ich das Automapping laufen lasse...
        $StructureRecords = StrukturRecord::finder()->findAll();
        foreach($StructureRecords As $Row) {
            $this->TreeArray[$Row->parent_idtm_struktur][]=$Row->idtm_struktur;
        }
        $cond0 = " idtm_struktur IN (".
            $this->subcategory_list($this->TreeArray,$this->StartNode) .") AND";
        $ImporterRecords = ImportMappingRecord::finder()->findAll('ima_name = ?',$this->MappingName->Text);
        foreach($ImporterRecords as $ImportRecord) {
            for($j=1;$j<=$this->NumberID->Text;$j++) {
            //to ensure no older conditions are stored inside
                $clearCon = "cond".$j;
                ${$clearCon} = "";
                $tempCon = "cond".($j-1);
                $sql1 = "SELECT * FROM tm_struktur WHERE".${$tempCon};
                $AMRecord = AutoMappingRecord::finder()->find('ti_id = ? AND ima_name = ?',$ImportRecord->{"ima_id".$j},$this->MappingName->Text);
                $tmpSQL = $sql1." idtm_stammdaten=".$AMRecord->idtm_stammdaten;
                if(count(StrukturRecord::finder()->findBySQL($tmpSQL))==1) {
                    $NewStartNode = StrukturRecord::finder()->findBySQL($tmpSQL)->idtm_struktur;
                    ${$clearCon} = " idtm_struktur IN (".
                        $this->subcategory_list($this->TreeArray,$NewStartNode) .") AND";
                    $ima_source = "A";
                    $idta_feldfunktion = $AMRecord->idta_feldfunktion;
                }else {
                    $NewStartNode = 0;
                    $ima_source = "";
                    $idta_feldfunktion = "";
                    break 1;
                }
            }
            $ImportRecord->idtm_struktur = $NewStartNode;
            $ImportRecord->idta_feldfunktion = $idta_feldfunktion;
            $ImportRecord->ima_source = $ima_source;
            $ImportRecord->save();
            $NewStartNode = 0;
        }
    //$this->bindListImportMapping();
    }

    private function subcategory_list($subcats,$catID) {
        $lst = $catID;
        if(array_key_exists($catID, $subcats)) {
            foreach($subcats[$catID] as $subCatID) {
                $lst .= ",".$this->subcategory_list($subcats, $subCatID);
            }
        }
        return $lst;
    }

}
?>