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
class orgstbwindow extends TPage{
    //put your code here

    private $idtm_organisation = 0;
    public $kommunikation_type = array(1=>"Telefon","Fax","Mail");

    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

    public function onLoad($param) {

        parent::onLoad($param);

        if(!$this->IsPostBack && !$this->IsCallBack) {
            $this->idtm_organisation = $_GET['idtm_organisation'];
            $this->bindListOrgListe();
        }

    }

    public function bindListOrgListe() {
        $criteria = new TActiveRecordCriteria();
        $criteria->Condition ="idtm_organisation = :suchtext1";
        $criteria->Parameters[':suchtext1'] = $this->idtm_organisation;
        $this->OrgListe->DataSource=OrganisationRecord::finder()->findAll($criteria);
        $this->OrgListe->dataBind();
    }

    public function bindListKommunikation($sender,$param) {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem') {
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition ="idtm_organisation = :suchtext1 AND kom_information <> ''";
            $criteria->Parameters[':suchtext1'] = $this->idtm_organisation;
            $item->KomListe->DataSource=KommunikationRecord::finder()->findAll($criteria);
            $item->KomListe->dataBind();
            $sql = "SELECT ta_adresse.* FROM ta_adresse INNER JOIN tm_organisation_has_ta_adresse ON ta_adresse.idta_adresse = tm_organisation_has_ta_adresse.idta_adresse WHERE tm_organisation_has_ta_adresse.idtm_organisation = ".$this->idtm_organisation." AND adresse_street <> ''";
            $item->AdressListe->DataSource=AdresseRecord::finder()->findAllBySQL($sql);
            $item->AdressListe->dataBind();
            $criteria = new TActiveRecordCriteria();
            $criteria->Condition ="idtm_organisation = :suchtext1";
            $criteria->Parameters[':suchtext1'] = $this->idtm_organisation;
            $item->BankListe->DataSource=BankkontoRecord::finder()->findAll($criteria);
            $item->BankListe->dataBind();
        }
    }
}
?>
