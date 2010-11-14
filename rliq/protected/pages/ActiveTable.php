<?php
/* 
 * Sample for ActiveTableGrid
 * Published under PRADO License
 *
 * for questions, pls contact info@planlogiq.com
 *
 */

class ActiveTable extends TPage {

    private $session; //the variable for the session
    private $dynamicControlList; //the variable the grid is stored into

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onInit($param){        
        $this->session = $this->Application->getSession();        
        if(!$this->isPostBack && !$this->isCallback){
            $this->createTable();
            $this->session['dynamicControlList'] = $this->dynamicControlList;
        }else{
            $this->reRenderTable();
        }
        parent::onInit($param);
    }

    public function changeURL($sender,$param){
        $this->QlikViewInline->FrameUrl=("http://".$sender->Text);
    }

    public function reRenderTable(){
        foreach($this->session['dynamicControlList'] as $key=>$controlDescription){//render rows
            $controlClass = $controlDescription['class'];
            $newControl = new $controlClass;
            $newControl->setID($controlDescription['id']);
            if($controlDescription['OnCallback']!=""){
                $newControl->OnCallback = $controlDescription['OnCallback'];
            }
            if($controlDescription['CommandParameter']!=""){
                $newControl->setCommandParameter($controlDescription['CommandParameter']);
            }
            $this->MyTable->Rows[]=$newControl;
            foreach($controlDescription['children'] as $cellkey=>$cellcontrolDescription){ //render cells
                $cellcontrolClass = $cellcontrolDescription['class'];
                $newCellControl = new $cellcontrolClass;
                $newCellControl->setID($cellcontrolDescription['id']);
                if($cellcontrolDescription['OnCallback']!=""){
                    $newCellControl->OnCallback = $cellcontrolDescription['OnCallback'];
                }
                if($cellcontrolDescription['CommandParameter']!=""){
                    $newCellControl->setCommandParameter($cellcontrolDescription['CommandParameter']);
                }
                foreach($cellcontrolDescription['children'] as $cellcokey=>$cellcocontrolDescription){ //render content of cells
                    $cellcocontrolClass = $cellcocontrolDescription['class'];
                    $newCellcoControl = new $cellcocontrolClass;
                    $newCellcoControl->setID($cellcocontrolDescription['id']);
                    if($cellcocontrolDescription['OnCallback']!=""){
                        $newCellcoControl->OnCallback = $cellcocontrolDescription['OnCallback'];
                    }
                    if($cellcocontrolDescription['CommandParameter']!=""){
                        $newCellcoControl->CommandParameter=$cellcocontrolDescription['CommandParameter'];
                    }
                    $newCellControl->Controls->add($newCellcoControl);
                }
                $newControl->Cells[]=$newCellControl;
            }
        }
    }

    public function createTable(){
        for($ii=1;$ii<=10;$ii++){
            $MyRow = new TActiveTableRow;
            $ControlListCell=array(); //clean the children
            if($ii%3==0){
                $rowId="R".$ii."G";
            }else{
                $rowId="R".$ii;
            }
            $MyRow->setID($rowId);
            $this->MyTable->Rows[]=$MyRow;
            for($jj=1;$jj<=10;$jj++){
                $cell=new TActiveTableCell;
                $ControlListCellChildren=array();//clean the children
                if($jj%3==0){
                    $cellID="R".$ii."C".$jj."G";
                }else{
                    $cellID="R".$ii."C".$jj;                    
                }
                $cell->setID($cellID);
                //my imagebutton for test
                if($jj==1){
                    if($ii%3==0){
                        $imagebutton = new TActiveImageButton();
                        $imagebutton->setID($cellID."IB");
                        $imagebutton->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif");
                        $imagebutton->setText("collapse");
                        $imagebutton->onCallback="page.hideRowGroup";
                        $imagebutton->setCommandParameter($ii);
                        $cell->Controls->add($imagebutton);
                        $ControlListCellChildren[]=Array("class"=>"TActiveImageButton","id"=>$cellID."IB","OnCallback"=>"page.hideRowGroup","CommandParameter"=>$ii);
                    }else{
                        $cell->Text=$jj*$ii;
                    }
                }else{
                    if($ii==1){
                        if($jj%3==0){
                            $imagebutton = new TActiveImageButton();
                            $imagebutton->setID($cellID."IB");
                            $imagebutton->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif");
                            $imagebutton->setText("collapse");
                            $imagebutton->onCallback="page.hideColumnGroup";
                            $imagebutton->setCommandParameter($jj);
                            $cell->Controls->add($imagebutton);
                            $ControlListCellChildren[]=Array("class"=>"TActiveImageButton","id"=>$cellID."IB","OnCallback"=>"page.hideColumnGroup","CommandParameter"=>$jj);
                        }else{
                            $cell->Text=$jj*$ii;
                        }
                    }else{
                        $cell->Text=$jj*$ii;
                    }
                }
                //this must appear at the end
                $MyRow->Cells[]=$cell;
                $ControlListCell[]=Array("class"=>"TActiveTableCell","id"=>$cellID,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCellChildren);
            }
            $this->dynamicControlList[]=Array("class"=>"TActiveTableRow","id"=>$rowId,"OnCallback"=>"","CommandParameter"=>"","children"=>$ControlListCell);
        }
    }

    public function hideRowGroup($sender,$param){
        $startValue = $sender->CommandParameter + 1;
        for($ii=$startValue;$ii<=100000;$ii++){
            $collapseRow = "R".$ii;
            if($this->MyTable->FindControl($collapseRow)){
                if($this->MyTable->FindControl($collapseRow)->Visible){
                    $this->MyTable->FindControl($collapseRow)->setVisible(false);
                    $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-expand.gif"):'';
                }else{
                    $this->MyTable->FindControl($collapseRow)->setVisible(true);
                    $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif"):'';
                }
            }else{
                break;
            }
        }
    }

    public function hideColumnGroup($sender,$param){
        $startValue = $sender->CommandParameter + 1;
        for($ii=$startValue;$ii<=100000;$ii++){
            for($jj=1;$jj<=10;$jj++){
                $isGroupColumn = "R".$jj."C1G";
                if($this->MyTable->FindControl($isGroupColumn)){
                    if($this->MyTable->FindControl($isGroupColumn)->Visible){
                        $this->MyTable->FindControl($isGroupColumn)->setVisible(false);
                        $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-expand.gif"):'';
                        next;
                    }else{
                        $this->MyTable->FindControl($isGroupColumn)->setVisible(true);
                        $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif"):'';
                        next;
                    }
                }
                $collapseColumn = "R".$jj."C".$ii;
                if($this->MyTable->FindControl($collapseColumn)){ //hier muss noch eine pruefung hin, ob es eine group colum ist, dann direkt next...
                    if($this->MyTable->FindControl($collapseColumn)->Visible){
                        $this->MyTable->FindControl($collapseColumn)->setVisible(false);
                        $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-expand.gif"):'';
                    }else{
                        $this->MyTable->FindControl($collapseColumn)->setVisible(true);
                        $startValue == $ii?$sender->setImageUrl("/rliq/themes/basic/gfx/group-collapse.gif"):'';
                    }
                }else{
                    break 2;
                }
            }
        }
    }

    public function hideTableRow($sender,$param){
        $collapseRow = "R".$sender->CommandParameter;
        $this->MyTable->FindControl($collapseRow)->setVisible(false);
    }

    public function hideTableColumn($sender,$param){
        for($ii=1;$ii<=10;$ii++){
            $collapseColumn = "R".$ii."C".$sender->CommandParameter;
            $this->MyTable->FindControl($collapseColumn)->setVisible(false);
        }
    }

    public function unhideTableRows($sender,$param){
        for($ii=1;$ii<=10;$ii++){
            $collapseRow = "R".$ii;
            $this->MyTable->FindControl($collapseRow)->setVisible(true);
        }
    }

    public function prepareForHtml($content){
            return preg_replace("/\s/", "<br/>\n", $content);
        }

    public function viewClipboard($sender,$param){
        $this->PastedText->Text = $this->prepareForHtml($sender->Text);
        $sender->Text = $this->returnFirstValue($sender->Text);
    }

    public function returnFirstValue($content){
        $matches = split(" ",$content);
        return $matches[0];
    }

    public function changePastedText($sender,$param){
        $this->MyTextOutput->Text = "Raised: ".$this->TestOrganisation->Text;
    }

}

?>
