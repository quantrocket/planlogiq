<?php

class risikomap extends TPage
{
	
	public function onLoad($param){
		
		parent::onLoad($param);
		
		if(!$this->isPostBack && !$this->isCallback){
			$SQL = "SELECT rcv_schaden, rcv_prio, rcv_ewk, idtm_rcvalue, MAX( rcv_cdate ) AS rcv_cdate FROM `tt_rcvalue` GROUP BY idtm_rcvalue ORDER BY rcv_prio ASC";
			$this->generateRiscMap(RCTTValueRecord::finder()->findAllBySQL($SQL),"Riskmap");
			$this->bindListRisc(RCTTValueRecord::finder()->findAllBySQL($SQL));
		}
		
	}
	
	private function bindListRisc($riscs){
		$this->risctabelle->DataSource = $riscs;
		$this->risctabelle->dataBind();
	}
	
	private function generateRiscMap($riscs,$mytitle="title") {
		
		foreach($riscs AS $riscs){
			$xdata[]=$riscs->rcv_ewk;
			$ydata[]=$riscs->rcv_schaden;
			switch ($riscs->rcv_prio){
				case ($riscs->rcv_prio<=30): $zdata[]= 'green';
				case ($riscs->rcv_prio<=60): $zdata[]= 'yellow';
				default:$zdata[]= 'red';
			}
			$datalabel[]=$riscs->rcv_prio*2;
		}
		$datalabel = implode(',', $datalabel);
		$xdata = implode(',', $xdata);
		$ydata = implode(',', $ydata);
		$zdata = implode(',', $zdata);
		
		$this->ImgRiscMap->ImageUrl = $this->getRequest()->constructUrl('page','bubble',1,array( 'datalabel' => $datalabel,'xdata' => $xdata,'ydata' => $ydata,'zdata' => $zdata, 'title' => $mytitle,'xlabel'=>'Eintrittswahrscheinlichkeit','ylabel'=>'Schadensh�he'), false);
	}
}
?>