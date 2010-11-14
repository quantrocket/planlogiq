<?php

class stakeholdermap extends TPage
{
    public function onPreInit($param){
        $myTheme = $this->User->getUserTheme($this->User->getUserId(),'mod_theme');
        $this->setTheme($myTheme);
    }

	public function onLoad($param){
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
			$criteria = new TActiveRecordCriteria();
    		$criteria->Condition ="idta_organisation_type LIKE 4";
			$this->generateStakeMap(OrganisationRecord::finder()->findAll($criteria),"Stakeholder-Map");
                        $this->bindListStake(OrganisationRecord::finder()->findAll($criteria));
		}
		
	}

        private function bindListStake($riscs){
		$this->staketabelle->DataSource = $riscs;
		$this->staketabelle->dataBind();
	}
	
	private function generateStakeMap($riscs,$mytitle="title") {
		
		foreach($riscs AS $riscs){
			$xdata[]=$riscs->org_bedeutung;
			$ydata[]=$riscs->org_klima;
			$temp=$riscs->org_klima*$riscs->org_bedeutung;
			switch ($temp){
				case ($temp>10): $zdata[]= 'green';
				case ($temp>0): $zdata[]= 'yellow';
				default:$zdata[]= 'orange';
			}
			$datalabel[]=$riscs->org_klima*$riscs->org_bedeutung;
		}
		$datalabel = implode(',', $datalabel);
		$xdata = implode(',', $xdata);
		$ydata = implode(',', $ydata);
		$zdata = implode(',', $zdata);
		
		$this->ImgRiscMap->ImageUrl = $this->getRequest()->constructUrl('page','bubble',1,array( 'datalabel' => $datalabel,'xdata' => $xdata,'ydata' => $ydata,'zdata' => $zdata, 'title' => $mytitle,'xlabel'=>'Bedeutung','ylabel'=>'Stimmung'), false);
	}
}
?>