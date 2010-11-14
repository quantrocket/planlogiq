<?php

class a_Protokoll extends TPage
{
	
	public $idtm_termin;
	public $idtm_protokoll;
	private $ProtokollRecord;

        public function onPreInit($param){
            $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
            $this->setTheme($myTheme);
        }
	
	public function onLoad($param){
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
			
			$this->idtm_termin = $this->Request['idtm_termin'];
			$this->idtm_protokoll = $this->Request['idtm_protokoll'];
			
			$this->getSelected();
			
			$this->thema->Text = $this->ProtokollRecord->prt_name;
			$this->prttyp->Text = ProtokollTypeRecord::finder()->findByPK($this->ProtokollRecord->idta_protokoll_type)->prt_type_name;
			$this->moderator->Text = OrganisationRecord::finder()->findByPK($this->ProtokollRecord->idtm_organisation)->org_name;
			$this->datum->Text = $this->ProtokollRecord->prt_cdate;
			
			$this->edTerminParticipantbindList();
			$this->edParticipantbindList();
			
			$this->bindRepProtokollDetail();
		}
	}

        public function prepareForHtml($content){
            return preg_replace("/\n/", "<br/>\n", $content);
        }
	
	protected function bindRepProtokollDetail(){
		$SQL = "SELECT a.* FROM `vv_protokoll_detail_aufgabe` a INNER JOIN ta_protokoll_detail_group b ON a.idtm_protokoll_detail_group = b.idtm_protokoll_detail_group";
		$SQL .= " WHERE a.idtm_protokoll = ". $this->idtm_protokoll;
		$this->RepProtokollDetail->DataSource=ProtokollDetailAufgabeView::finder()->findAllBySQL($SQL);
		$this->RepProtokollDetail->dataBind();	
	}
	
	protected function getSelected(){
		$this->ProtokollRecord = ProtokollRecord::finder()->findByPk($this->idtm_protokoll);
	}
	
	private function edTerminParticipantbindList(){
                $criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "idtm_termin = :suchtext";
    		$criteria->Parameters[':suchtext'] = $this->idtm_termin;
                $criteria->OrdersBy["idtm_organisation"] = 'asc';
    		
    		$this->edTerminParticipant->VirtualItemCount = count(TerminOrganisationView::finder()->findAll($criteria));
    		
    		$criteria->setLimit($this->edTerminParticipant->PageSize);
		$criteria->setOffset($this->edTerminParticipant->PageSize * $this->edTerminParticipant->CurrentPageIndex);
		$this->edTerminParticipant->DataKeyField = 'idtm_organisation';
			
		$this->edTerminParticipant->DataSource=TerminOrganisationView::finder()->findAll($criteria);
		$this->edTerminParticipant->dataBind();
	}
	
	private function edParticipantbindList(){
		$criteria = new TActiveRecordCriteria();
    		$criteria->Condition = "idtm_activity = :suchtext";
    		$criteria->Parameters[':suchtext'] = $this->idtm_termin;
			$criteria->OrdersBy["idtm_organisation"] = 'asc';
    		
    		$this->ParticipantListe->VirtualItemCount = count(ActivityParticipantsView::finder()->findAll($criteria));
    		
    		$criteria->setLimit($this->ParticipantListe->PageSize);
			$criteria->setOffset($this->ParticipantListe->PageSize * $this->ParticipantListe->CurrentPageIndex);
			$this->ParticipantListe->DataKeyField = 'idtm_organisation';
			
			$this->ParticipantListe->DataSource=ActivityParticipantsView::finder()->findAll($criteria);
			$this->ParticipantListe->dataBind();
	}
}
?>