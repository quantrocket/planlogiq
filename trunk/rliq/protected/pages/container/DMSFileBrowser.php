<?php

class DMSFileBrowser extends TTemplateControl {

    /*
     *  To implement the container, use the following tags
     *  <com:Application.pages.container.DMSFileBrowser ID="DMSFileBrowser"/>
     *
     */

    //private $WorkPath = Prado::getFrameworkPath()."/../rliq/assets/dms/";
    private $DBTable = "tmaufgaben";
    private $DBTableID = "1";

    public function DMSremoveFile($sender,$param){
        $compFile = Prado::getFrameworkPath()."/../rliq/assets/dms/".$param->CallbackParameter;
        if(is_file($compFile)){
            unlink($compFile);
        }
        $this->loadDirectory();
        $this->UploadDialog->setDisplay("None");
    }

    public function showUploadDialog($sender,$param){
        $this->UploadDialog->setDisplay("Dynamic");
    }

    public function hideUploadDialog($sender,$param){
        $this->UploadDialog->setDisplay("None");
    }
    
    public function loadDirectory(){
        $this->initParameters();
        $files = array();
        $dirname = Prado::getFrameworkPath()."/../rliq/assets/dms/";
        if(is_dir($dirname)){
            $dir = opendir($dirname);
            while(($file = readdir($dir)) !== false){
                $compFile = Prado::getFrameworkPath()."/../rliq/assets/dms/".$file;
                if(is_file($compFile)){
                    preg_match('/^([0-9]{8})_([0-9]{4})_([a-zA-Z]*)_([0-9]*)_(.*)/',$file,$matches);
                    preg_match('/^(.*)\.([a-zA-Z]*)/',$matches[5],$matchos);
                    if($matches[3]==$this->DMSAufTabelle->Text AND $matches[4]==$this->DMSAufId->Text){
                        $files[] = array('filetype' => $matchos[2],'filename' => $matches[5],'filesize' => filesize($compFile),'filedate'=>$matches[1],'filetime'=>$matches[2],'internalfile'=>$file, 'completefile'=>$compFile);
                    }
                }
            }
        }else{
            throw new TException("Directory doesn't exist: ".$dirname);
        }
        $this->DMSFileBrowserRepeater->DataSource = $files;
        $this->DMSFileBrowserRepeater->dataBind();
        unset($files);
    }

    public function fileUploaded($sender,$param){
        if($sender->getHasFile()){
            $filename = $sender->FileName;
            $tempUserFileName = date('Ymd_Hi_');
            $compFile = $tempUserFileName.$this->DMSAufTabelle->Text."_".$this->DMSAufId->Text."_".$filename;
            $tempUserFile=Prado::getFrameworkPath()."/../rliq/assets/dms/".$compFile;
            if(is_file($tempUserFile)) {
                unlink($tempUserFile);
            }
            $sender->saveAs($tempUserFile,true);
        }
        $this->UploadFileError->Text = $sender->ErrorCode;
        $this->loadDirectory();
        $this->UploadDialog->setDisplay("None");
    }    

    public function initParameters(){
        $this->DMSAufTabelle->Text = $this->parent->DMSFileTabelle->Text;
        $this->DMSAufId->Text = $this->parent->DMSFileId->Text;
    }

}

?>