<?php

Prado::using('Application.app_code.GoogleAdressTranslator');
Prado::using('Application.common.BActiveGoogleMap');

class orgworkspace extends TPage {

    private $idta_organisation_art = array("0"=>"Team","Eigent.","Lieferant","WEG","Mietobjekt","sonstige","Kunde","Lieferant");

    //function muss eingebunden werden um detailbelege nutzen zu können
    public function recalcSumme($sender,$param){
        $this->RechnungContainer->DetailBelegContainer->recalcSumme($sender,$param);
    }

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function setPDFSteckbrief($idtm_organisation){
        $url = "javascript:win_organisation_openwin('";
        $parameter['idtm_organisation']=$idtm_organisation;
        $url.=$this->getRequest()->constructUrl('page','pdf.PDF_002_Organisation_Standard',$parameter);
        $url.="')";
        $this->PDFSteckbrief->NavigateUrl=$url;
    }

    public function onLoad($param) {

        parent::onLoad($param);

        date_default_timezone_set('Europe/Berlin');

        if(!$this->IsPostBack || !$this->IsCallBack) {

            $sql = "SELECT idta_organisation_type, org_type_name FROM ta_organisation_type";
            $data = PFH::convertdbObjectArray(OrganisationTypeRecord::finder()->findAllBySql($sql),array("idta_organisation_type","org_type_name"));
            $this->RCedidta_organisation_type->DataSource=$data;
            $this->RCedidta_organisation_type->dataBind();

            $this->RCedidta_organisation_art->DataSource=$this->idta_organisation_art;
            $this->RCedidta_organisation_art->dataBind();

            $this->RCedorg_idtm_user_role->DataSource=PFH::build_SQLPullDown(UserRoleRecord::finder(),"tm_user_role",array("idtm_user_role","user_role_name"));
            $this->RCedorg_idtm_user_role->dataBind();

            $this->RCedidta_branche->DataSource=PFH::build_SQLPullDown(BrancheRecord::finder(),"ta_branche",array("idta_branche","bra_name"));
            $this->RCedidta_branche->dataBind();

            $this->RCedidtm_ressource->DataSource=PFH::build_SQLPullDown(RessourceRecord::finder(),"tm_ressource",array("idtm_ressource","res_name"));
            $this->RCedidtm_ressource->dataBind();

            $this->RCedidtm_country->DataSource=PFH::build_SQLPullDown(CountryRecord::finder(),"tm_country",array("idtm_country","country_iso"));
            $this->RCedidtm_country->dataBind();

            $this->RCedkom_type->DataSource = array(1=>"Telefon","Fax","Mail");
            $this->RCedkom_type->dataBind();

            $this->RCedorg_status->DataSource = array(0=>"offen","interessant","nicht interessant","Kunde","EX-Kunde");
            $this->RCedorg_status->dataBind();

            if(isset($_GET['idtm_organisation'])){
                $this->view_Organisation($_GET['idtm_organisation']);
            }else{
                $this->view_Organisation(1);
            }
            
            //$this->bindListOrgListe();
        }
    }

    public function OpenSyncWindow($sender,$param){
        $id=$this->mpnlApplyToDimensionContainer->getClientID();
        $this->getPage()->getClientScript()->registerEndScript('X',"Windows.show('$id',true);");
    }

    public function PDFTest($sender,$param){
        $parameter['idtm_organisation']=$this->RCedidtm_organisation->Text;
        $url = $this->getApplication()->getRequest()->constructUrl('page','page','pdf.Letter_Standard', $parameter);
        $this->Response->redirect($url);
    }

    public function suggestUser($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $mySQL = "SELECT idtm_user,user_name FROM tm_user WHERE user_name LIKE '%".$token."%'";
        $sender->DataSource=PFH::convertdbObjectSuggest(TActiveRecord::finder('UserRecord')->findAllBySQL($mySQL),array('idtm_user','user_name'));
        $sender->dataBind();
    }

    public function suggestionSelected1($sender,$param) {
        $id=$sender->Suggestions->DataKeys[ $param->selectedIndex ];
        $this->RCedidtm_user->Text=$id;
    }

    public function bindListChildOrgListe() {
        $StartNode = $this->RCedidtm_organisation->Text;
        
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="parent_idtm_organisation = :suchtext AND org_aktiv = :suchtext2";
        $criteria->Parameters[':suchtext'] = $StartNode;
        $criteria->Parameters[':suchtext2'] = $this->CBChildrenAktiv->Checked?1:0;
        $criteria->OrdersBy['org_fk_internal']='ASC';
        $criteria->OrdersBy['org_name']='ASC';

        $this->OrgChildListe->VirtualItemCount=OrganisationRecord::finder()->count($criteria);

        $criteria->setLimit($this->OrgChildListe->PageSize);
        $criteria->setOffset($this->OrgChildListe->PageSize * $this->OrgChildListe->CurrentPageIndex);
        
        $this->OrgChildListe->DataSource=OrganisationRecord::finder()->findAll($criteria);
        $this->OrgChildListe->dataBind();
    }

    public function viewMainAdress($idtm_organisation){
        $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE ta_adresse.adresse_ismain = 1 AND tm_organisation_has_ta_adresse.idtm_organisation = ".$this->RCedidtm_organisation->Text;
        $Adresse = AdresseRecord::finder()->findBySQL($sql);
        if(is_object($Adresse)){
            $this->laborg_adresse->Text = $Adresse->adresse_street .' ,'.$Adresse->adresse_zip.' '.$Adresse->adresse_town;
        }
    }

    public function bindListAdress(){
        $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$this->RCedidtm_organisation->Text;
        $mydata = AdresseRecord::finder()->findAllBySQL($sql);
        $this->lstAdress->dataSource=$mydata;
        $this->lstAdress->dataBind();
    }

    public function bindListKom(){
        $this->lstKom->dataSource=KommunikationRecord::finder()->findAllByidtm_organisation($this->RCedidtm_organisation->Text);
        $this->lstKom->dataBind();
//        //The KomPart on Top
//        $this->lstKomTop->dataSource=KommunikationRecord::finder()->findAllByidtm_organisation($this->RCedidtm_organisation->Text);
//        $this->lstKomTop->dataBind();
    }

    public function previewOrganisation($idtm_aufgaben){
        $this->AufgabenContainerOrganisation->bindListTAValue($idtm_aufgaben);
//        if($this->RCedidtm_organisation->Text != $idtm_organisation){
//            $this->RCedidtm_organisation->Text = $idtm_organisation;
//            $myitem = OrganisationRecord::finder()->findByPK($idtm_organisation);
//            $this->laborg_name->Text = $myitem->org_name;
//            $this->labparentorg_name->Text = OrganisationRecord::finder()->findByPK($myitem->parent_idtm_organisation)->org_name;
//            $this->labparentorg_name->CommandParameter = $myitem->parent_idtm_organisation;
//            $this->bindListKom();
//            $this->viewMainAdress($idtm_organisation);
//        }
    }

    public function applyDateFilter($idtm_aufgaben){
        $values = explode('_',$idtm_aufgaben);
        $this->AufgabenContainerOrganisation->CCAufgabenContainerOrganisationYear->Text = $values[1];
        if(count($values)==3){
            $this->AufgabenContainerOrganisation->CCAufgabenContainerOrganisationMonth->Text = $values[2];
        }else{
            $this->AufgabenContainerOrganisation->CCAufgabenContainerOrganisationMonth->Text = 0;
        }
        //print_r($values);
        $this->AufgabenContainerOrganisation->bindListTAValue();
    }

    public function dtgList_PageIndexChanged($sender,$param) {
        if($sender->Id == 'OrgChildListe'){
            $this->OrgChildListe->CurrentPageIndex = $param->NewPageIndex;
            $this->bindListChildOrgListe();
        }else{
            $this->OrgListe->CurrentPageIndex = $param->NewPageIndex;
            $this->bindListOrgListe();
        }
    }

    public function searchOrg($sender,$param) {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="org_name LIKE :suchtext AND idta_organisation_type = 1";
        $criteria->Parameters[':suchtext'] = "%".$this->find_org->Text."%";
        $this->OrgListe->DataKeyField = 'idtm_organisation';

        $this->OrgListe->VirtualItemCount = count(OrganisationRecord::finder()->withorgtype()->find($criteria));
        $this->OrgListe->DataSource=OrganisationRecord::finder()->withorgtype()->findAll($criteria);
        $this->OrgListe->dataBind();        
    }


    private $RCprimarykey = "idtm_organisation";
    private $RCfields = array("org_name",
        "idta_organisation_type",
        "idta_organisation_art",
        "org_descr",
        "parent_idtm_organisation",
        "idtm_user","org_mail",
        "org_idtm_user_role",
        "org_eskalation",
        "org_klima",
        "org_bedeutung",
        "org_kommunikation",
        "idtm_ressource",
        "org_ntuser",
        "org_name1",
        "org_name2",
        "org_anrede",
        "org_briefanrede",
        "org_vorname",
        "org_matchkey",
        "org_uid",
        "org_finanzamt",
        "org_steuernummer",
        "org_referat",
        "org_gemeinde",
        "org_katastragemeinde",
        "org_grundstuecksnummer",
        "org_einlagezahl",
        "org_baujahr",
        "org_wohnungen",
        "org_fk_internal",
        "org_titel",
        "org_status",        
        "idta_branche");
    private $RCdatfields = array("org_status_date","org_birthday_date",
        "org_specialday_date");
    private $RCtimefields = array();
    private $RChiddenfields = array();
    private $RCboolfields = array("org_aktiv");
	

    public function dtgList_editCommand($sender,$param) {        
        if($sender->id == 'labparentorg_name'){
            $myitem=OrganisationRecord::finder()->findByPK($sender->CommandParameter);
        }else{
            $item = $param->Item;
            $myitem=OrganisationRecord::finder()->findByPK($item->lstc_idtm_organisation->Text);
        }

        $tempus = 'RCed'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        $this->bindListAdress();
        $this->bindListKom();

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }

        $this->RCedorganisation_edit_status->Text = 1;
        $this->suggest_idtm_user->Text = UserRecord::finder()->findByidtm_user($this->RCedidtm_user->Text)->user_username;

        $this->loadBankkonto($this->$tempus->Text);
        $this->loadObjekt($this->$tempus->Text);
        $this->viewMainAdress($this->$tempus->Text);

        $this->laborg_name->Text = $myitem->org_name;
        $this->labparentorg_name->Text = OrganisationRecord::finder()->findByPK($myitem->parent_idtm_organisation)->org_name;
        $this->labparentorg_name->CommandParameter = $myitem->parent_idtm_organisation;

        //TASKPART
        $this->Tedsend_id->Text = $myitem->$monus;
        $this->Tedauf_id->Text = $myitem->$monus;
        $this->AufgabenContainerOrganisation->initParameters();
        $this->AufgabenContainerOrganisation->initYearPullDown();
        $this->AufgabenContainerOrganisation->bindListTAValue();
        
        $this->setPDFSteckbrief($myitem->$monus);
        $this->bindListChildOrgListe();
    }

    public function view_Organisation($idtm_organisation) {
        
        $myitem=OrganisationRecord::finder()->findByPK($idtm_organisation);
        
        $tempus = 'RCed'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = $myitem->$monus;

        $this->bindListAdress();
        $this->bindListKom();
        $this->viewMainAdress($idtm_organisation);

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setText($myitem->$recordfield);
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setDate($myitem->$recordfield);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setChecked($myitem->$recordfield);
        }

        //TIME
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $my_time = explode(':',$myitem->$recordfield);
            $my_time_text = $my_time[0].':'.$my_time[1];
            $this->$edrecordfield->Text = $my_time_text;
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = $myitem->$recordfield;
        }        

        $this->RCedorganisation_edit_status->Text = 1;
        $ActualUser = UserRecord::finder()->findByidtm_user($this->RCedidtm_user->Text);
        if(is_Object($ActualUser)){
            $this->suggest_idtm_user->Text = $ActualUser->user_username;
        }else{
            $this->suggest_idtm_user->Text = 0;
        }

        $this->loadBankkonto($this->$tempus->Text);
        $this->loadObjekt($this->$tempus->Text);

        $this->laborg_name->Text = $myitem->org_name;
        $ParentOrga = OrganisationRecord::finder()->findByidtm_organisation($myitem->parent_idtm_organisation);
        if(is_Object($ParentOrga)){
            $this->labparentorg_name->Text = $ParentOrga->org_name . ', ' . $ParentOrga->org_vorname;
        }
        $this->labparentorg_name->CommandParameter = $myitem->parent_idtm_organisation;

        //Rechnungen
        //$this->Teddeb_id->Text = $myitem->$monus;
        //$this->Teddeb_tabelle->Text = 'tm_zeiterfassung';
        //$this->RechnungContainer->initParameters();
        $this->RechnungContainer->bindRechnungListe();

        //TASKPART
        $this->Tedsend_id->Text = $myitem->$monus;
        $this->Tedauf_id->Text = $myitem->$monus;
        $this->AufgabenContainerOrganisation->initParameters();
        $this->AufgabenContainerOrganisation->initYearPullDown();
        $this->AufgabenContainerOrganisation->bindListTAValue();
        
        $this->setPDFSteckbrief($myitem->$monus);
        $this->bindListChildOrgListe();
    }

    public function loadBankkonto($idtm_organisation){
        $Bankkonto = BankkontoRecord::finder()->findByidtm_organisation($idtm_organisation);
        if(count($Bankkonto)!=1){
            $Bankkonto = new BankkontoRecord();
            $Bankkonto->idtm_organisation = $idtm_organisation;
            $Bankkonto->save();
        }
        $this->RCedbak_kontowortlaut->Text = $Bankkonto->bak_kontowortlaut;
        $this->RCedbak_geldinstitut->Text = $Bankkonto->bak_geldinstitut;
        $this->RCedbak_blz->Text = $Bankkonto->bak_blz;
        $this->RCedbak_konto->Text = $Bankkonto->bak_konto;
        $this->RCedbak_bic->Text = $Bankkonto->bak_bic;
        $this->RCedbak_iban->Text = $Bankkonto->bak_iban;
    }

    public function loadObjekt($idtm_organisation){
        $Objekt = ObjektRecord::finder()->findByidtm_organisation($idtm_organisation);
        if(count($Objekt)!=1){
            $Objekt = new ObjektRecord();
            $Objekt->idtm_organisation = $idtm_organisation;
            $Objekt->save();
        }
        $this->RCedobj_nutzflaeche->Text = $Objekt->obj_nutzflaeche;
        $this->RCedobj_gbanteile->Text = $Objekt->obj_gbanteile;
        $this->RCedobj_nutzflaeche_date->setDate($Objekt->obj_nutzflaeche_date);
        $this->RCedobj_gbanteile_date->setDate($Objekt->obj_gbanteile_date);
    }

    public function RCDeleteButtonClicked($sender,$param) {
        $tempus='RCed'.$this->RCprimarykey;
        
        $Bankkonto = BankkontoRecord::finder()->findByidtm_organisation($this->$tempus->Text);
        $Bankkonto->delete();

        $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$this->$tempus->Text;
        $Adressen = AdresseRecord::finder()->findAllBySQL($sql);
        if(count($Adressen)>=1){
            foreach($Adressen as $Adresse){
                AdresseRecord::finder()->deleteByPK($Adresse->idta_adresse);
            }
        }

        $OrganisationAdressen=OrganisationAdresseRecord::finder()->find('idtm_organisation = ?',$this->$tempus->Text);
        if(count($OrganisationAdressen)>=1){
            foreach($OrganisationAdressen as $OrganisationAdresse){
                OrganisationAdresseRecord::finder()->deleteByidtm_organisation_has_ta_adresse($OrganisationAdresse->idtm_organisation_has_ta_adresse);
            }
        }

        $Kommunikation = KommunikationRecord::finder()->find("idtm_organisation = ?",$this->$tempus->Text);
        if(count($Kommunikation)>=1){
            foreach($Kommunikation as $Kommunik){
                KommunikationRecord::finder()->deleteByidta_kommunikation($Kommunik->idta_kommunikation);
            }
        }

        $Record = OrganisationRecord::finder()->findByPK($this->$tempus->Text);
        $Record->delete();

        $this->RCNewButtonClicked($sender,$param);
    }

    public function RCSavedButtonClicked($sender,$param) {

        $tempus='RCed'.$this->RCprimarykey;

        if($this->RCedorganisation_edit_status->Text == '1') {
            $RCEditRecord = OrganisationRecord::finder()->findByPK($this->$tempus->Text);
        }
        else {
            $RCEditRecord = new OrganisationRecord;
        }

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Value;
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield=date('Y-m-d',$this->$edrecordfield->TimeStamp);
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Checked?1:0;
        }

        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }

        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $RCEditRecord->$recordfield = $this->$edrecordfield->Text;
        }
        $RCEditRecord->save();

        $this->saveBankkonto($RCEditRecord->idtm_organisation);
        $this->saveObjekt($RCEditRecord->idtm_organisation);

        if($this->RCedorganisation_edit_status->Text == '0') {
            $org_name = $RCEditRecord->org_name;
            $this->getPage()->getClientScript()->registerEndScript('X',"tree.insertNewChild(tree.getSelectedItemId()||0,$RCEditRecord->idtm_organisation,'$org_name')");
        }

        $this->view_Organisation($RCEditRecord->idtm_organisation);

//        if($RCEditRecord->parent_idtm_organisation!=1){
//            $this->bindListChildOrgListe();
//        }
        //$this->bindListOrgListe();
    }

    public function saveBankkonto($idtm_organisation){
        $Bankkonto = BankkontoRecord::finder()->findByidtm_organisation($idtm_organisation);
        if(count($Bankkonto)!=1){
            $Bankkonto = new BankkontoRecord();            
        }
        $Bankkonto->idtm_organisation = $idtm_organisation;
        $Bankkonto->bak_kontowortlaut = $this->RCedbak_kontowortlaut->Text;
        $Bankkonto->bak_geldinstitut = $this->RCedbak_geldinstitut->Text;
        $Bankkonto->bak_blz = $this->RCedbak_blz->Text;
        $Bankkonto->bak_konto = $this->RCedbak_konto->Text;
        $Bankkonto->bak_bic = $this->RCedbak_bic->Text;
        $Bankkonto->bak_iban = $this->RCedbak_iban->Text;
        $Bankkonto->save();
    }

    public function saveObjekt($idtm_organisation){
        $Objekt = ObjektRecord::finder()->findByidtm_organisation($idtm_organisation);
        if(count($Objekt)!=1){
            $Objekt = new ObjektRecord();
        }
        $Objekt->idtm_organisation = $idtm_organisation;
        $Objekt->obj_nutzflaeche = $this->RCedobj_nutzflaeche->Text;
        $Objekt->obj_gbanteile = $this->RCedobj_gbanteile->Text;
        $Objekt->obj_nutzflaeche_date = date('Y-m-d',$this->RCedobj_nutzflaeche_date->TimeStamp);
        $Objekt->obj_gbanteile_date = date('Y-m-d',$this->RCedobj_gbanteile_date->TimeStamp);
        $Objekt->save();
    }

    public function add_context_Organisation($idtm_organisation,$idta_organisation_type){
       $this->RCNewButtonClicked($sender,$param);
       $this->RCedparent_idtm_organisation->Text = $idtm_organisation;
       $this->RCedidta_organisation_type->Text = $idta_organisation_type;
       $this->getPage()->getClientScript()->registerEndScript('XX',"dhxTabbar.setTabActive('a2');");
   }

    public function RCNewButtonClicked($sender,$param) {

        if($sender->ID=="newChild"){
            $parent_idtm_struktur = $this->RCedidtm_organisation->Text;
        }
        $tempus = 'RCed'.$this->RCprimarykey;
        $monus = $this->RCprimarykey;

        $this->$tempus->Text = '0';

        //HIDDEN
        foreach ($this->RChiddenfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setValue('0');
        }

        //DATUM
        foreach ($this->RCdatfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setDate(date('Y-m-d',time()));
        }

        //BOOL
        foreach ($this->RCboolfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->setChecked(0);
        }

        //NON DATUM
        foreach ($this->RCtimefields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = '00:00';
        }

        //NON DATUM
        foreach ($this->RCfields as $recordfield) {
            $edrecordfield = 'RCed'.$recordfield;
            $this->$edrecordfield->Text = '';
        }

        $this->RCedidtm_ressource->Text = 1;
        $this->RCedorg_idtm_user_role->Text = 1;

        $this->RCedidta_organisation_type->Text = 1;


        if($sender->ID=="newChild"){
            $this->RCedparent_idtm_organisation->Text = $parent_idtm_struktur;
        }
        $this->RCedorg_aktiv->setChecked(1);

        $this->bindListAdress();
        $this->bindListKom();

        $this->loadBankkonto('0');
        $this->loadObjekt('0');
        $this->RCedorganisation_edit_status->Text = '0';
    }

    public function addAdresse($sender,$param){

        if($this->RCedorganisation_edit_status->Text == '0'){
            $this->RCSavedButtonClicked($sender, $param);
        }
        $Adresse = new AdresseRecord();
        $Adresse->adresse_ismain = $this->RCedadresse_ismain->Checked?1:0;
        $Adresse->adresse_street = $this->RCedadresse_street->Text;
        $Adresse->adresse_town = $this->RCedadresse_town->Text;
        $Adresse->adresse_zip = $this->RCedadresse_zip->Text;

        //lets add the coordinates
        $myGTranslator = new GoogleAdressTranslator();
        $mapparams=$myGTranslator->getLatAndLong(implode(",",array($this->RCedadresse_street->Text,$this->RCedadresse_town->Text)));
        $myLatandLong = explode(",",$mapparams);

        //here we check, if the coordinates have been found
        if($myLatandLong[1]!=0) {
            $Adresse->adresse_lat = $myLatandLong[1];
            $Adresse->adresse_long = $myLatandLong[0];
        }else{
            $Adresse->adresse_lat = 0.00;
            $Adresse->adresse_long = 0.00;
        }
        
        $Adresse->idtm_country = 1;
        $Adresse->save();

        $OrganisationAdresse = new OrganisationAdresseRecord();
        $OrganisationAdresse->idta_adresse = $Adresse->idta_adresse;
        $OrganisationAdresse->idtm_organisation = $this->RCedidtm_organisation->Text;
        $OrganisationAdresse->save();

        $this->bindListAdress();
    }

    public function addKom($sender,$param){
        if($this->RCedorganisation_edit_status->Text == '0'){
            $this->RCSavedButtonClicked($sender, $param);
        }
        $Kommunikation = new KommunikationRecord();
        $Kommunikation->kom_type = $this->RCedkom_type->Text;
        $Kommunikation->kom_information = $this->RCedkom_information->Text;
        $Kommunikation->kom_ismain = $this->RCedkom_ismain->Checked?1:0;
        $Kommunikation->idtm_organisation = $this->RCedidtm_organisation->Text;
        $Kommunikation->save();

        $this->RCedkom_information->Text="";

        $this->bindListKom();
    }

    public function lstAdressEdit($sender,$param){
        $this->lstAdress->EditItemIndex=$param->Item->ItemIndex;
        $this->bindListAdress();
    }

    public function lstAdressDelete($sender,$param){
        AdresseRecord::finder()->deleteByPk($this->lstAdress->DataKeys[$param->Item->ItemIndex]);
        $this->bindListAdress();
    }

    public function lstAdress_pageIndexChanged($sender,$param){
        $this->lstAdress->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListAdress();
    }

    public function lstAdressCancel($sender,$param)
    {
        $this->lstAdress->EditItemIndex=-1;
        $this->bindListAdress();
    }

    public function lstAdressSave($sender,$param){
        $item=$param->Item;

        $Record = AdresseRecord::finder()->findByPK($this->lstAdress->DataKeys[$item->ItemIndex]);
        $Record->adresse_street = $item->lst_adresse_street->TextBox->Text;
        $Record->adresse_zip = $item->lst_adresse_zip->TextBox->Text;
        $Record->adresse_town = $item->lst_adresse_town->TextBox->Text;

        //lets add the coordinates
        $myGTranslator = new GoogleAdressTranslator();
        $mapparams=$myGTranslator->getLatAndLong(implode(",",array($item->lst_adresse_street->TextBox->Text,$item->lst_adresse_town->TextBox->Text)));
        $myLatandLong = explode(",",$mapparams);

        //here we check, if the coordinates have been found
        if($myLatandLong[1]!=0) {
            $Record->adresse_lat = $myLatandLong[1];
            $Record->adresse_long = $myLatandLong[0];
        }else{
            $Record->adresse_lat = $item->lst_adresse_lat->TextBox->Text;
            $Record->adresse_long = $item->lst_adresse_long->TextBox->Text;
        }

        $Record->adresse_ismain = $item->lst_adresse_ismain->ATB_lst_adresse_ismain->Checked?1:0;
        $Record->save();

        $this->lstAdress->EditItemIndex=-1;
        $this->bindListAdress();
    }

    public function lstKomEdit($sender,$param){
        $this->lstKom->SelectedItemIndex=-1;
        $this->lstKom->EditItemIndex=$param->Item->ItemIndex;
        $this->bindListKom();
    }

    public function lstKom_pageIndexChanged($sender,$param){
        $this->lstKom->CurrentPageIndex = $param->NewPageIndex;
        $this->bindListKom();
    }

    public function lstKomCancel($sender,$param)
    {
        $this->lstKom->SelectedItemIndex=-1;
        $this->lstKom->EditItemIndex=-1;
        $this->bindListKom();
    }

    public function lstKomSave($sender,$param){
        $item=$param->Item;

        $Record = KommunikationRecord::finder()->findByPK($this->lstKom->DataKeys[$item->ItemIndex]);
        $Record->kom_type = $item->ATB_lst_kom_type->Text;
        $Record->kom_information = $item->ATB_lst_kom_information->Text;
        $Record->kom_ismain = $item->ATB_lst_kom_ismain->Checked?1:0;
        $Record->save();

        $this->lstKom->EditItemIndex=-1;
        $this->bindListKom();
    }

}
?>