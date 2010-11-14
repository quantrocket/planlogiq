<?php

class prtworkspace extends TPage {

    private $auf_done = array(0=>"offen","erledigt","alle");
    public $idta_organisation_art = array("-1"=>"alle","0"=>"Team","Eigent.","Lieferant","WEG","Mietobjekt","sonstige","Kunde","Lieferant");
    
    private $subcats = array();//list of all subcats
    private $parentcats = array();//list of all parentcats
    private $catNames=array();
    private $UserStartId = 1;

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }
    
    public function onLoad($param) {

        parent::onLoad($param);

        $this->setUserStartId($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_activity"));

        if(!$this->isPostBack && !$this->isCallback) {            
            $NEWRECORD = $this->NewRecord;
            $NEWRECORD->setText("neues Protokoll anlegen");
            $NEWRECORD->setToPage("protokoll.prtview");
            $NEWRECORD->setGetVariables('modus=0');

            //brauchen wir fuer die aggregation der Protokolle
            $this->FFidtm_activity->Text=$this->UserStartId;

            $this->bindListOrgListe();
            $this->initPullDown();
        }
    }

    public function load_prttopics($sender,$param){
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
            $sql = "SELECT idtm_protokoll_detail_group, prtdet_topic,idta_protokoll_ergebnistype,auf_done,auf_tdate, prtdet_descr, idtm_protokoll_detail, idtm_organisation,org_name,idta_organisation_art FROM vv_protokoll_detail_aufgabe WHERE idtm_protokoll = ".$item->Data->idtm_protokoll;
            if($this->CBAufgabeDone->Text == 0 AND $this->CBAufgabeDone->Text != ''){
                $sql .= " AND (auf_done = ".$this->CBAufgabeDone->Text." AND idta_protokoll_ergebnistype<3)";
            }
            if($this->CBAufgabeDone->Text == 1 AND $this->CBAufgabeDone->Text != ''){
                $sql .= " AND ((auf_done = ".$this->CBAufgabeDone->Text." AND idta_protokoll_ergebnistype<3) OR idta_protokoll_ergebnistype>2)";
            }
            if($this->CBidta_organisation_art->Text >= 0 AND $this->CBidta_organisation_art->Text != ''){
                $sql .= " AND (idta_organisation_art = ".$this->CBidta_organisation_art->Text.")";
            }
            $item->CCProtokollTops->DataSource=ProtokollDetailAufgabeView::finder()->findAllBySQL($sql);
            $item->CCProtokollTops->dataBind();
        }
    }

    public function initPullDown(){
        $HRKEYTest = new PFHierarchyPullDown();
        $HRKEYTest->setStructureTable("tm_activity");
        $HRKEYTest->setRecordClass(ActivityRecord::finder());
        $HRKEYTest->setPKField("idtm_activity");
        $HRKEYTest->setField("act_name");
        $HRKEYTest->setSQLCondition("idta_activity_type = 2");
        $HRKEYTest->setStartNode($this->user->getStartNode($this->user->getUserId($this->user->Name),"tm_activity"));
        $HRKEYTest->letsrun();
        $this->FFidtm_activity->DataSource=$HRKEYTest->myTree;
        //PFH::build_SQLPullDownAdvanced(ActivityRecord::finder(),"tm_activity",array("idtm_activity","act_name","act_pspcode"),"idta_activity_type = 2","idtm_activity ASC, act_name ASC");
        $this->FFidtm_activity->dataBind();

        $this->CBAufgabeDone->DataSource = $this->auf_done;
        $this->CBAufgabeDone->dataBind();

        $this->CBidta_organisation_art->DataSource = $this->idta_organisation_art;
        $this->CBidta_organisation_art->dataBind();
    }

    public function CreatePDFLink($idtm_protokoll){
        $parameter['idtm_protokoll']=$idtm_protokoll;
        $url = $this->getApplication()->getRequest()->constructUrl('page','pdf.PDF_001_KP_Protokoll_Standard', $parameter);
        return $url;
    }

    public function bindListOrgListe() {
        $mySQL = "SELECT idtm_activity,parent_idtm_activity,act_name,idta_activity_type FROM tm_activity";
        $mySQLOrderBy = " ORDER BY parent_idtm_activity,act_step";
        $this->load_all_cats($mySQL.$mySQLOrderBy);

        $SKNode=$this->FFidtm_activity->Text>=1?$this->FFidtm_activity->Text:$this->UserStartId;

        if($this->subCategory_list($this->subcats, $SKNode)!=''){
            $sql = "SELECT tm_protokoll.* AS idtm_protokoll FROM tm_protokoll LEFT JOIN tm_termin ON tm_termin.idtm_termin = tm_protokoll.idtm_termin";
            $sql .= " WHERE tm_termin.idtm_activity IN (". $this->subCategory_list($this->subcats, $SKNode).")";
            $sql .= " ORDER BY prt_cdate DESC";
            $this->OrgListe->DataSource=ProtokollRecord::finder()->findAllBySQL($sql);
            $this->OrgListe->dataBind();
        }
    }


    public function dtgList_PageIndexChanged($sender,$param) {
        $this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListOrgListe();
    }

    public function dtgList_deleteCommand($sender,$param) {
        $item=$param->Item;
        $finder = ProtokollRecord::finder();
        $finder->deleteAll('idtm_activity = ?',$item->lst_org_idtm_activity->Text);
        $this->bindListOrgListe();
    }

    public function searchOrg($sender,$param) {

        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="prt_name LIKE :suchtext";
        $criteria->Parameters[':suchtext'] = "%".$this->find_org->Text."%";
        $criteria->setLimit($this->OrgListe->PageSize);
        $criteria->setOffset($this->OrgListe->PageSize * $this->OrgListe->CurrentPageIndex);
        $this->OrgListe->DataKeyField = 'idtm_protokoll';

        $this->OrgListe->VirtualItemCount = count(ProtokollRecord::finder()->withacttype()->find($criteria));
        $this->OrgListe->DataSource=ProtokollRecord::finder()->withacttype()->findAll($criteria);
        $this->OrgListe->dataBind();

    }

    public function dtgList_editCommand($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"protokoll.prtview",array('modus'=>'1','idtm_protokoll'=>$sender->CommandParameter));
        $this->Response->redirect($url);
    }

    public function dtgList_viewChildren($sender,$param) {
        $url=$this->getRequest()->constructUrl('page',"reports.protokoll.a_Protokoll",array('idtm_protokoll'=>$sender->CommandParameter,'idtm_termin'=>ProtokollRecord::finder()->findByPk($sender->CommandParameter)->idtm_termin));
        $this->Response->redirect($url);
    }

    public function open_Excel($sender,$param) {
        $url=$this->getRequest()->constructUrl('page','reports.workbook.WBK_Protokoll',array('idtm_protokoll'=>$sender->CommandParameter,'idtm_termin'=>$param->Item->lst_prt_idtm_termin->Text));
        $this->Response->redirect($url);
    }

    /*
     * @function: hier kommen alle Funktionen, die ich brauche um die Liste der verfuegbaren werte zu inkludieren
     */

     public function setUserStartId($idtm_struktur) {
        $this->UserStartId = $idtm_struktur;
    }

    private function load_all_cats($TTSQL) {
        $rows = ActivityRecord::finder()->findAllbySQL($TTSQL);
        foreach($rows as $row) {
            $this->subcats[$row->parent_idtm_activity][]=$row->idtm_activity;
            $this->parentcats[$row->idtm_activity]=$row->parent_idtm_activity;
        }
    }

    private function subCategory_list($subcats,$catID) {
        $lst = $catID; //id des ersten Startelements...
        if(array_key_exists($catID,$subcats)) {
            foreach($subcats[$catID] as $subCatID) {
                $lst .= ", " . $this->subCategory_list($subcats, $subCatID);
            }
        }
        return $lst;
    }

    private function parentCategory_list($parentcats,$catID) {
        $lst = $catID; //id des ersten Startelements...
        while($parentcats[$catID] != NULL) {
            $catID = $parentcats[$catID];
            $lst .= ", " . $catID;
        }
        return $lst;
    }
    
}
?>