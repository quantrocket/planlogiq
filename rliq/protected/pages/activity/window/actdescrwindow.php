<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of orgextwindow
 *
 * @author pfrenz
 */
class actdescrwindow extends TPage{
    //put your code here

    private $idtm_activity = 0;
    
    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->IsPostBack && !$this->IsCallBack) {
            $this->idtm_activity = $_GET['idtm_activity'];
            $this->edact_descr->Text=ActivityRecord::finder()->findByPK($this->idtm_activity)->act_descr;
            $this->edidtm_activity->Text=$this->idtm_activity;
        }

    }

    public function SavedButtonClicked($sender,$param){
        $Datensatz = ActivityRecord::finder()->findByPK($this->edidtm_activity->Text);
        $Datensatz->act_descr = $this->edact_descr->Text;
        $Datensatz->save();
        $sender->Text="Saved";
    }

}
?>
