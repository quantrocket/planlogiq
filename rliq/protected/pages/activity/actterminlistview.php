<?php

Prado::using('Application.app_code.PFTerminplan');
Prado::using('Application.app_code.PFConstraits');

class actterminlistview extends TPage {

    public $PSPListe;

    public function onPreInit($param) {
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        parent::onLoad($param);
        
        if(!$this->isPostBack && !$this->isCallback) {
            //here comes the part for gantt 1 MUST BE DONE FIRST
            $tmpstartdate = new DateTime();
            $this->gantt_datestart->setDate($tmpstartdate->format("Y-m-d"));
            $tmpstartdate->modify("30days");
            $this->gantt_dateende->setDate($tmpstartdate->format("Y-m-d"));

            $this->idtm_activity->DataSource=PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_name","act_pspcode"),"idta_activity_type = 2","act_name ASC");
            $this->idtm_activity->dataBind();
            $this->idtm_activity->Text=ActivityRecord::finder()->findByparent_idtm_activity('0')->idtm_activity;
            
            $this->bindTerminListe();
        }
    }

    public function bindTerminListe() {
        $this->PSPListe = new PFTerminplan();
        $this->PSPListe->load_PSP($this->idtm_activity->Text);
        $this->RepActListe->DataSource=$this->PSPListe->get_PSPBasisElements();
        $this->RepActListe->dataBind();
        $this->generateUnterphasenGraph($this->PSPListe->get_PSPBasisElements(),"Terminplan PLAN");
        $this->generateUnterphasenGraphIst($this->PSPListe->get_PSPBasisElements(),"Terminplan IST");
    }

    public function selectAll() {
        foreach($this->RepActListe->Items as $item) {
            if(!$item->edanwenden->Checked) {
                $item->edanwenden->setChecked(true);
            }else {
                $item->edanwenden->setChecked(false);
            }
        }
    }

    public function TSavedButtonClicked() {
        foreach($this->RepActListe->Items as $item) {
            //if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem'){
            if($item->edanwenden->Checked) {
                $myEditRecord = ActivityRecord::finder()->findByPK($item->idtm_activity->Text);
                $myEditRecord->act_startdate=$item->edttact_startdate->Text;
                $myEditRecord->act_enddate=$item->edttact_enddate->Text;
                $myEditRecord->save();
                $item->act_startdate->Text = $myEditRecord->act_startdate;
                $item->act_enddate->Text = $myEditRecord->act_enddate;
            }
            //  }
        }
        $this->bindTerminListe();
    }

    private function generateUnterphasenGraph($activitys,$mytitle="title") {

        $datestart = date('Y-m-d',$this->gantt_datestart->TimeStamp);
        $dateende = date('Y-m-d',$this->gantt_dateende->TimeStamp);

        foreach($activitys AS $activity) {
            $xdatalabel[]=$activity->act_name;
            $xdataorder[]=$activity->idtm_activity;
            $xdatastart[]=$activity->ttact_startdate;
            $xdataende[]=$activity->ttact_enddate;
            $xdataprogress[]=$activity->act_fortschritt;
            if($activity->idta_activity_type == 1) {
                $xdatamilestone[]=1;
            }else {
                $xdatamilestone[]=0;
            }
        }

        $PFConstraits = new PFConstraits();
        $tuffyconstraits = $PFConstraits->getConstraits($activitys);
        if(count($tuffyconstraits)>0) {
            foreach($tuffyconstraits as $set) {
                $xconstraitskey[] = $set[0];
                $xconstraitsvalue[] = $set[1];
                $xconstraits[] = $set[2];
            }
            $constraitskey = implode(',',$xconstraitskey);
            $constraitsvalue = implode(',',$xconstraitsvalue);
            $constraits = implode(',',$xconstraits);
        }

        $datalabel = implode(',', $xdatalabel);
        $dataorder = implode(',', $xdataorder);
        $datastart = implode(',', $xdatastart);
        $dataende = implode(',', $xdataende);
        $datamilestone = implode(',', $xdatamilestone);
        $dataprogress = implode(',', $xdataprogress);

        $this->ImgUnterphasenGantt->ImageUrl = $this->getRequest()->constructUrl('gantt',1,array( 'constraits' => $constraits,'constraitskey' => $constraitskey,'constraitsvalue' => $constraitsvalue,'datalabel' => $datalabel,'dataorder' => $dataorder,'datastart' => $datastart,'dataende' => $dataende,'datamilestone' => $datamilestone,'dataprogress' => $dataprogress, 'scale' => "month", 'title' => $mytitle,'datestart'=>$datestart,'dateende'=>$dateende), false);
    }

    private function generateUnterphasenGraphIst($activitys,$mytitle="title") {

        foreach($activitys AS $activity) {
            $xdatalabel[]=$activity->act_name;
            $xdataorder[]=$activity->idtm_activity;
            $xdatastart[]=$activity->act_startdate;
            $xdataende[]=$activity->act_enddate;
            $xdataprogress[]=$activity->act_fortschritt;
            if($activity->idta_activity_type == 1) {
                $xdatamilestone[]=1;
            }else {
                $xdatamilestone[]=0;
            }
        }

        $PFConstraits = new PFConstraits();
        $tuffyconstraits = $PFConstraits->getConstraits($activitys);
        if(count($tuffyconstraits)>0) {
            foreach($tuffyconstraits as $set) {
                $xconstraitskey[] = $set[0];
                $xconstraitsvalue[] = $set[1];
                $xconstraits[] = $set[2];
            }
            $constraitskey = implode(',',$xconstraitskey);
            $constraitsvalue = implode(',',$xconstraitsvalue);
            $constraits = implode(',',$xconstraits);
        }

        $datestart = date('Y-m-d',$this->gantt_datestart->TimeStamp);
        $dateende = date('Y-m-d',$this->gantt_dateende->TimeStamp);

        $datalabel = implode(',', $xdatalabel);
        $dataorder = implode(',', $xdataorder);
        $datastart = implode(',', $xdatastart);
        $dataende = implode(',', $xdataende);
        $datamilestone = implode(',', $xdatamilestone);
        $dataprogress = implode(',', $xdataprogress);

        $this->ImgUnterphasenGanttIst->ImageUrl = $this->getRequest()->constructUrl('gantt',1,array( 'constraits' => $constraits,'constraitskey' => $constraitskey,'constraitsvalue' => $constraitsvalue,'datalabel' => $datalabel,'dataorder' => $dataorder,'datastart' => $datastart,'dataende' => $dataende,'datamilestone' => $datamilestone,'dataprogress' => $dataprogress, 'scale' => "month", 'title' => $mytitle,'datestart'=>$datestart,'dateende'=>$dateende), false);
    }

}
?>