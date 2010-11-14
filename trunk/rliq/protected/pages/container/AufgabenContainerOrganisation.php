<?php

class AufgabenContainerOrganisation extends TTemplateControl {

    /*
     * To implement the container, use the following tags
     *  <com:Application.pages.container.AufgabenContainerOrganisation ID="AufgabenContainerOrganisation"/>
     *  <com:TActiveTextBox id="Tedauf_tabelle" Text="tm_activity" visible="false" />
     *  <com:TActiveTextBox id="Tedauf_id" Text="0" visible="false" />
     *  <com:TActiveTextBox id="Tedauf_user_id" Text="0" visible="false" />
     *
     */

    var $session = array();
    var $auf_done = array(0=>"offen","erledigt","alle");

    public function onLoad($param) {
        if(!$this->page->IsPostBack && !$this->page->IsCallback) {
            //$this->session = $this->Application->getSession();
            $this->CCAufgabenContainerPageSize->Text = 5;
            //here i check the state of the application, so the init of the pull down will work
            $this->session['loadCounter']=0;
            $this->initPullDowns();
            //$this->bindListTAValue();
        }
    }

    public function initYearPullDown(){
        $temp = '';
        if($this->page->IsPostBack || $this->page->isCallback){
            $temp = $this->CCAufgabenContainerOrganisationYear->Text;
            $tempMonth = $this->CCAufgabenContainerOrganisationMonth->Text;
        }
        $data = array();
        $sql = '';
        $sql = "SELECT Year(auf_tdate) AS auf_name FROM tm_aufgaben WHERE (auf_tabelle = '".$this->Tedauf_tabelle->Text."' AND auf_id = ".$this->Tedauf_id->Text.") OR idtm_organisation=".$this->Tedauf_id->Text." OR auf_idtm_organisation=".$this->Tedauf_id->Text." GROUP BY YEAR(auf_tdate) ORDER BY auf_name DESC";
        $Aufgaben = AufgabenRecord::finder()->findAllBySql($sql);
        if(count($Aufgaben)>=1){
            foreach($Aufgaben AS $Aufgabe){
                $data[$Aufgabe->auf_name]=$Aufgabe->auf_name; //achtung, nur ein temp field, handelt sich um das jahr
            }
        }else{
            $data[date('Y')] = date('Y');
        }
        $this->CCAufgabenContainerOrganisationYear->DataSource=$data;
        $this->CCAufgabenContainerOrganisationYear->dataBind();

        $dataMonth = array();
        $sqlMonth = '';
        $sqlMonth = "SELECT Month(auf_tdate) AS auf_name FROM tm_aufgaben WHERE (auf_tabelle = '".$this->Tedauf_tabelle->Text."' AND auf_id = ".$this->Tedauf_id->Text.") OR idtm_organisation=".$this->Tedauf_id->Text." OR auf_idtm_organisation=".$this->Tedauf_id->Text." GROUP BY Month(auf_tdate) ORDER BY auf_name DESC";
        $AufgabenMonth = AufgabenRecord::finder()->findAllBySql($sqlMonth);
        if(count($AufgabenMonth)>=1){
            foreach($AufgabenMonth AS $AufgabeMonth){
                $dataMonth[$AufgabeMonth->auf_name]=$AufgabeMonth->auf_name; //achtung, nur ein temp field, handelt sich um das jahr
            }
        }else{
            $dataMonth[date('m')] = date('m');
        }
        $dataMonth['0']="alle";
        $this->CCAufgabenContainerOrganisationMonth->DataSource=$dataMonth;
        $this->CCAufgabenContainerOrganisationMonth->dataBind();


        if((!$this->page->IsPostBack && !$this->page->isCallback) OR !in_array($temp,$data)){
            foreach($data AS $key=>$value){
                $this->CCAufgabenContainerOrganisationYear->Text = $value;
                break;
            }
        }else{
            $this->CCAufgabenContainerOrganisationYear->Text = $temp;
        }

        if((!$this->page->IsPostBack && !$this->page->isCallback) OR !in_array($tempMonth,$dataMonth)){
            foreach($dataMonth AS $key=>$value){
                $this->CCAufgabenContainerOrganisationMonth->Text = $value;
                break;
            }
        }else{
            $this->CCAufgabenContainerOrganisationMonth->Text = $tempMonth;
        }
    }

    public function initPullDowns(){
        $this->CBAufgabeDone->DataSource = $this->auf_done;
        $this->CBAufgabeDone->dataBind();
        
        $this->ttidtm_ressource->DataSource=PFH::build_SQLPullDown(RessourceRecord::finder(),"tm_ressource",array("idtm_ressource","res_name"));
        $this->ttidtm_ressource->dataBind();

        $this->Tedidta_aufgaben_type->DataSource=PFH::build_SQLPullDown(AufgabenTypeRecord::finder(),"ta_aufgaben_type",array("idta_aufgaben_type","auf_type_name"));
        $this->Tedidta_aufgaben_type->dataBind();

        $this->CCAufgabenContainerPageSize->DataSource=array(5=>"5",10=>"10",25=>"25",50=>"50");
        $this->CCAufgabenContainerPageSize->dataBind();
    }

    public function initAufOrganisation(){
//        $HRKEYTest = new PFHierarchyPullDown();
//        $HRKEYTest->setStructureTable("tm_organisation");
//        $HRKEYTest->setRecordClass(OrganisationRecord::finder());
//        $HRKEYTest->setPKField("idtm_organisation");
//        $HRKEYTest->setField("org_name");
//
//        //the parent idtm_organisation
//        $parent_idtm_organisation = OrganisationRecord::finder()->findByidtm_organisation($this->Tedauf_id->Text)->parent_idtm_organisation;
//        if(OrganisationRecord::finder()->findByidtm_organisation($parent_idtm_organisation)->parent_idtm_organisation >= 1){
//            $HRKEYTest->setStartNode(OrganisationRecord::finder()->findByidtm_organisation($parent_idtm_organisation)->idtm_organisation);
//            $HRKEYTest->letsrun();
//            $this->page->AufgabenContainerOrganisation->Tedauf_idtm_organisation->DataSource=$HRKEYTest->myTree;
//            $this->page->AufgabenContainerOrganisation->Tedauf_idtm_organisation->dataBind();
//        }else{
//            $HRKEYTest->setStartNode(OrganisationRecord::finder()->findByidtm_organisation($this->Tedauf_id->Text)->idtm_organisation);
//            $HRKEYTest->letsrun();
//            $this->page->AufgabenContainerOrganisation->Tedauf_idtm_organisation->DataSource=$HRKEYTest->myTree;
//            $this->page->AufgabenContainerOrganisation->Tedauf_idtm_organisation->dataBind();
//        }
    }

    public function initParameters(){
        $this->Tedauf_tabelle->Text = $this->page->Tedauf_tabelle->Text;
        $this->Tedauf_id->Text = $this->page->Tedauf_id->Text;
        $this->Tedauf_user_id->Text = $this->page->Tedauf_user_id->Text;
    }

	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */
	/* here comes the part for the risks */

    private $RCprimarykey = "idtm_aufgaben";
    private $RCfields = array("idtm_organisation",
        "auf_idtm_organisation",
        "auf_beschreibung",
        "auf_priority",
        "auf_name",
        "auf_tabelle",
        "auf_id",
        "auf_dauer",
        "auf_tag",
        "auf_zeichen_eigen",
        "auf_zeichen_fremd",
        "idta_aufgaben_type");
    private $RCdatfields = array("auf_tdate");
    private $RCtimefields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array("auf_done");

    public function bindListTAValue($idtm_aufgaben="0") {
        //diese kontrolle brauche ich, falls sender und param uerbergeben werden
        is_Object($idtm_aufgaben)?$idtm_aufgaben=0:'';
        $this->initAufOrganisation();
        $criteria = new TActiveRecordCriteria();
        if($idtm_aufgaben==0){
            $this->Tedidtm_aufgaben_single->Text=0;
            //einschraenken auf jahr
            $criteria->Condition ="YEAR(auf_tdate) = :suchtext4 AND auf_deleted = 0";
            $criteria->Parameters[':suchtext4'] = $this->CCAufgabenContainerOrganisationYear->Text;

            if($this->CCAufgabenContainerOrganisationMonth->Text!=0){
                $criteria->Condition .=" AND MONTH(auf_tdate) = :suchtext8";
                $criteria->Parameters[':suchtext8'] = $this->CCAufgabenContainerOrganisationMonth->Text;
            }

            if($this->Tedauf_tabelle->Text == 'tm_none'){
                //the personalisation part
                if($this->Tedauf_user_id->Text >= 1) {
                    $criteria->Condition .=" AND idtm_organisation = :suchtext3";
                    $criteria->Parameters[':suchtext3'] = $this->Tedauf_user_id->Text;
                }else{
                    $criteria->Condition .=" AND auf_tabelle = :suchtext1";
                    $criteria->Parameters[':suchtext1'] = $this->Tedauf_tabelle->Text;
                }
            }else{
                $criteria->Condition .=" AND ((auf_tabelle = :suchtext1 AND auf_id = :suchtext2) OR (auf_idtm_organisation=:suchtext5) OR (idtm_organisation=:suchtext6))";
                $criteria->Parameters[':suchtext1'] = $this->Tedauf_tabelle->Text;
                $criteria->Parameters[':suchtext2'] = $this->Tedauf_id->Text;
                $criteria->Parameters[':suchtext5'] = $this->Tedauf_id->Text;
                $criteria->Parameters[':suchtext6'] = $this->Tedauf_id->Text;
            }

            if($this->CBAufgabeDone->Text<2){
                $criteria->Condition .=" AND auf_done = :suchtext7";// AND auf_name IS NOT NULL";
                $criteria->Parameters[':suchtext7'] = $this->CBAufgabeDone->Text;
            }
            $criteria->Condition .=" AND ((auf_beschreibung = '' AND auf_tabelle='tm_protokoll_detail') IS FALSE)";// AND auf_name IS NOT NULL";

            $criteria->OrdersBy['auf_tdate']='DESC';

            $this->CCAufgabenRepeater->VirtualItemCount=AufgabenView::finder()->count($criteria);
            //->page->AufgabenContainerOrganisation
            $criteria->setLimit($this->CCAufgabenContainerPageSize->Text);
            $criteria->setOffset($this->CCAufgabenContainerPageSize->Text * $this->CCAufgabenRepeater->CurrentPageIndex);
            if($this->CCAufgabenContainerPageSize->Text<=1){
                $this->CCAufgabenRepeater->PageSize = 5;
            }else{
                $this->CCAufgabenRepeater->PageSize=1*$this->CCAufgabenContainerPageSize->Text;
            }
        }else{
            $this->Tedidtm_aufgaben_single->Text=1;
            $this->Tedidtm_aufgaben->Text=$idtm_aufgaben;
            $criteria->Condition = "idtm_aufgaben = :suchtext8";
            $criteria->Parameters[':suchtext8'] = $idtm_aufgaben;
            $this->CCAufgabenRepeater->VirtualItemCount=AufgabenView::finder()->count($criteria);
        }
        $this->CCAufgabenRepeater->DataSource=AufgabenView::finder()->findAll($criteria);
        $this->CCAufgabenRepeater->dataBind();
    }

    private function bindListRessource(){
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition = "idtm_aufgabe = :suchtext";
            $criteria->Parameters[':suchtext'] = $this->Tedidtm_aufgaben->Text;
            $criteria->OrdersBy["idtm_ressource"] = 'asc';
            $this->RessourceListe->DataSource=AufgabeRessourceView::finder()->findAll($criteria);
            $this->RessourceListe->dataBind();
    }

    /**
     * load_aufgabenvalue
     * @param <object> $sender
     * @param <array> $param if you want to display a single record, param->Item->lstcc_idtm_aufgaben->Text should be included
     */

    public function load_aufgabenvalue($sender,$param) {

        if($sender->Id=="RepeaterLoadAufgabe"){
            $myitem=AufgabenRecord::finder()->findByPK($sender->CommandParameter);
        }else{
            $item = $param->Item;
            $myitem=AufgabenRecord::finder()->findByPK($item->lstcc_idtm_aufgaben->Text);
        }
        $tempus = 'Ted'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',$myitem->$recordfield));
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->Tedaufgaben_edit_status->Text = 1;
        
        $this->bindListRessource();
        //$this->page->getCallbackClient()->click($this->page->AufgabenContainerOrganisation->TaskDetail->getClientID()."_0");
        $this->page->AufgabenContainerOrganisation->TaskTabs->ActiveViewIndex="1";
    }

    public function load_aufgabenvalue_byID($idtm_aufgabe) {

        $myitem=AufgabenRecord::finder()->findByPK($idtm_aufgabe);

        $tempus = 'Ted'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->Tedaufgaben_edit_status->Text = 1;
        
        $this->bindListRessource();
    }
    
    public function TADeleteButtonClicked($sender,$param) {
        $tempus='Ted'.$this->RCprimarykey;
        $Record = AufgabenRecord::finder()->findByPK($this->$tempus->Text);
        $Record->auf_deleted = 1;
        $Record->save();
        $this->bindListTAValue();
        $this->TANewButtonClicked($sender,$param);
        $this->page->getCallbackClient()->click($this->page->AufgabenContainerOrganisation->TaskOverview->getClientID()."_0");
        //$this->page->AufgabenContainerOrganisation->TaskTabs->ActiveViewIndex="0";
    }

    public function TASavedButtonClicked($sender,$param) {

        $tempus='Ted'.$this->RCprimarykey;

        if($this->Tedaufgaben_edit_status->Text == '1') {
            $RCEditRecord = AufgabenRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new AufgabenRecord;
        }

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        $RCEditRecord->save();

        $this->bindListTAValue();
        
        $this->panelNewTask->setDisplay("None");
        
        //$this->page->getCallbackClient()->click($this->page->AufgabenContainerOrganisation->TaskOverview->getClientID()."_0");
        //$this->page->AufgabenContainerOrganisation->TaskTabs->ActiveViewIndex="0";
    }

    public function TACancelButtonClicked($sender,$param){
        $this->panelNewTask->setDisplay("None");
    }

    public function TANewButtonClicked($sender,$param) {

        $tempus = 'Ted'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //NON DATUM
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = '00:00';
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $this->$edrecordfield->Text = '0';
        }

        $this->Tedaufgaben_edit_status->Text = '0';
        $this->Tedidta_aufgaben_type->Text = '1';

        $this->initParameters();
        $this->bindListRessource();
        $this->bindListTAValue();

        //try
        $this->Tedidtm_organisation->Text=$this->User->getUserOrgId($this->User->getUserId())>0?$this->User->getUserOrgId($this->User->getUserId()):$this->Tedauf_id->Text;
        //this is only for the aufgabencontainerorganisation
        $this->Tedauf_idtm_organisation->Text = $this->Tedauf_id->Text;
        
        $this->panelNewTask->setDisplay("Dynamic");
        //$this->page->getCallbackClient()->click($this->page->AufgabenContainerOrganisation->TaskDetail->getClientID()."_0");
        //$this->page->AufgabenContainerOrganisation->TaskTabs->ActiveViewIndex="1";
    }

    public function rcvList_PageIndexChanged($sender,$param) {
        $this->page->AufgabenContainerOrganisation->CCAufgabenRepeater->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListTAValue();
    }

    public function addRessource($sender,$param){
        //auf welche dimension sollen die werte zugeordnet werden
        $rIndecies = $this->ttidtm_ressource->SelectedIndices;
        foreach($rIndecies as $index)
        {
            $myRecord = new AufgabeRessourceRecord();
            $myRecord->idtm_aufgabe = $this->Tedidtm_aufgaben->Text;
            $myRecord->idtm_ressource = $this->ttidtm_ressource->Items[$index]->Value;
            $myRecord->auf_res_dauer = $this->ttauf_res_dauer->Text;
            $myRecord->save();
        }
        $this->bindListRessource();
    }

    public function removeRessource($sender,$param){
        AufgabeRessourceRecord::finder()->deleteByPk($param->Item->lstpart_idtm_aufgabe_ressource->Text);
        $this->bindListRessource();
    }

    public function lstCCAufgabenRepeaterEdit($sender,$param){
        $this->CCAufgabenRepeater->EditItemIndex=$param->Item->ItemIndex;
        if($this->Tedidtm_aufgaben_single->Text==0){
			$this->bindListTAValue();
		}else{
			$this->bindListTAValue($this->Tedidtm_aufgaben->Text);
		}
    }

    public function lstCCAufgabenRepeaterCancel($sender,$param){
        $this->session['loadCounter']=0;
        $this->CCAufgabenRepeater->SelectedItemIndex=-1;
        $this->CCAufgabenRepeater->EditItemIndex=-1;
        if($this->Tedidtm_aufgaben_single->Text==0){
			$this->bindListTAValue();
		}else{
			$this->bindListTAValue($this->Tedidtm_aufgaben->Text);
		}
    }

    public function lstCCAufgabenRepeaterSave($sender,$param) {
        $item=$param->Item;
        $RCEditRecord = AufgabenRecord::finder()->findByidtm_aufgaben($item->Tedidtm_aufgaben->Text);

        $temp_idta_aufgaben_type = $RCEditRecord->idta_aufgaben_type;
        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $item->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield=date('Y-m-d',$item->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $item->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $item->$edrecordfield->Text;
        }

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'Ted'.$recordfield;
            $RCEditRecord->$recordfield = $item->$edrecordfield->Text;
        }
        if($item->Tedidta_aufgaben_type->Text==''){
            $RCEditRecord->idta_aufgaben_type = $temp_idta_aufgaben_type;
        }
        $RCEditRecord->save();

        $this->CCAufgabenRepeater->EditItemIndex=-1;
        if($this->Tedidtm_aufgaben_single->Text==0){
                $this->bindListTAValue();
        }else{
                $this->bindListTAValue($this->Tedidtm_aufgaben->Text);
        }
        $this->session['loadCounter']=0;
    }

}

?>