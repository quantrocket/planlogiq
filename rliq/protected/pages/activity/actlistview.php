<?php

Prado::using('Application.app_code.PFNetzplan');

class actlistview extends TPage {

    public $PSPListe;
    private $MaxElement = 0;

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->isPostBack && !$this->isCallback) {
            $this->idtm_activity->DataSource=PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_name","act_pspcode"),"idta_activity_type = 2","act_name ASC");
            $this->idtm_activity->dataBind();
            $this->idtm_activity->Text=ActivityRecord::finder()->findByparent_idtm_activity('0')->idtm_activity;
            $this->loadGridValues($sender, $param);
        }
    }

    public function loadGridValues($sender,$param){
        $this->PSPListe = new PFNetzplan();
        $this->PSPListe->load_PSP($this->idtm_activity->Text);
        $this->RepActListe->DataSource=$this->PSPListe->get_PSPBasisElements();
        $this->RepActListe->dataBind();

        //um die grenzen zu kennen
        $tmp = $this->PSPListe->getMaxSEZ($this->PSPListe->get_PSPBasisElements());
        $this->MaxElement = $tmp->ttact_sez;

        $this->DrawHeader($this->PSPListe->get_PSPBasisElements());
        $this->DrawPSP($this->PSPListe->get_PSPBasisElements());
        unset($PFNetzplan);
    }

    private function DrawHeader($data) {
        $FirstRow = new TActiveTableRow;
        $this->resulttable->Rows[]=$FirstRow;

        $counterrc = $this->PSPListe->getMaxSEZ($data);
        for($ii=1;$ii<=$counterrc->ttact_sez;$ii++) {
            $cell=new TTableHeaderCell;
            $cell->Text=$ii;
            $cell->EnableViewState = false;
            $FirstRow->Cells[]=$cell;
        }
        $FirstRow->setCssClass('thead');
    }

    private function DrawPSP($data) {
        foreach($data as $row) {
            $act_row = new TActiveTableRow;
            $this->resulttable->Rows[]=$act_row;

            $range1=$row->ttact_faz;
            $range2=$row->ttact_sez-$row->ttact_faz;
            $ttemp = $row->ttact_sez==0?1:$row->ttact_sez;
            $range3=$this->MaxElement-$ttemp;

            if($range1==0) {
                $cell = new TActiveTableCell();
                if(!$range2==0) {
                    $cell->setColumnSpan($range2);
                }
                $cell->setCssClass('PSPContainer');
                $cell->Text=$this->DrawPSPElement($row);
                $act_row->Cells[]=$cell;
                $dcell=new TActiveTableCell();
                if($range3>0) {
                    $dcell->setColumnSpan($range3);
                }
                $dcell->Text='';
                $dcell->setCssClass('PSPContainer');
                $act_row->Cells[]=$dcell;
            }else {
                $cell = new TActiveTableCell();
                if($range1<$this->MaxElement) {
                    $cell->setColumnSpan($range1);
                }else {
                    $cell->setColumnSpan($range1-1);
                }
                $cell->Text='';
                $cell->setCssClass('PSPContainer');
                $act_row->Cells[]=$cell;

                $zcell = new TActiveTableCell();
                if(!$range2==0) {
                    $zcell->setColumnSpan($range2);
                }
                $zcell->Text=$this->DrawPSPElement($row);
                $zcell->setCssClass('PSPContainer');
                $act_row->Cells[]=$zcell;
                if($range3>0) {
                    $dcell=new TActiveTableCell();
                    $dcell->setColumnSpan($range3);
                    $dcell->Text='';
                    $dcell->setCssClass('PSPContainer');
                    $act_row->Cells[]=$dcell;
                }
            }
        }
    }

    private function DrawPSPElement($element) {
        $html="<table class='PSPTable'><tr>";
        $html.="<td class='PSPCode'>".$element->act_pspcode."</td>";
        $html.="<td>NP</td>";
        $html.="<td class='PSPDauer'>".$element->act_dauer."</td>";
        $html.="</tr><tr>";
        $html.="<td class='PSPName' colspan='3'>".$element->act_name."</td>";
        $html.="</tr><tr>";
        $html.="<td class='PSPFAZ'>".$element->ttact_faz."</td>";
        $html.="<td class='PSPGP'>".$element->ttact_gp."</td>";
        $html.="<td class='PSPFEZ'>".$element->ttact_fez."</td>";
        $html.="</tr><tr>";
        $html.="<td class='PSPSAZ'>".$element->ttact_saz."</td>";
        $html.="<td class='PSPDauer'>".$element->ttact_fp."</td>";
        $html.="<td class='PSPFAZ'>".$element->ttact_sez."</td>";
        $html.="</tr></table>";
        return $html;
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
                $myEditRecord->act_gp=$item->edttact_gp->Text;
                $myEditRecord->act_fp=$item->edttact_fp->Text;
                $myEditRecord->act_faz=$item->edttact_faz->Text;
                $myEditRecord->act_fez=$item->edttact_fez->Text;
                $myEditRecord->act_saz=$item->edttact_saz->Text;
                $myEditRecord->act_sez=$item->edttact_sez->Text;
                $myEditRecord->save();
                $item->act_faz->Text = $myEditRecord->act_faz;
                $item->act_fez->Text = $myEditRecord->act_fez;
                $item->act_saz->Text = $myEditRecord->act_saz;
                $item->act_sez->Text = $myEditRecord->act_sez;
            }
            //  }
        }
        $this->dataBind();
    }

}
?>