<?php

class ActivityKostenContainer extends TTemplateControl{
    
        public function onLoad($param)
	{
		parent::onLoad($param);
                $this->bindListCostActivityValue();
	}

	public function bindListCostActivityValue(){

                $SQL = "SELECT a.idtm_activity AS idtm_activity, a.act_name AS act_name, a.act_dauer AS act_dauer,SUM( act_dauer * res_kosten *8 ) AS ttact_kosten FROM tm_activity a INNER JOIN tm_organisation b ON a.idtm_organisation = b.idtm_organisation INNER JOIN tm_ressource c ON b.idtm_ressource = c.idtm_ressource GROUP BY act_name, idtm_activity ORDER BY act_startdate";
                
                $this->ActivityKostenListe->VirtualItemCount = count(ActivityRecord::finder()->findAllBySQL($SQL));
			
                $criteria = new TActiveRecordCriteria();
                $criteria->setLimit($this->ActivityKostenListe->PageSize);
		$criteria->setOffset($this->ActivityKostenListe->PageSize * $this->ActivityKostenListe->CurrentPageIndex);
		$this->ActivityKostenListe->DataKeyField = 'idta_activity';
			
		$this->ActivityKostenListe->DataSource=ActivityRecord::finder()->findAllBySQL($SQL);
		$this->ActivityKostenListe->dataBind();

            $this->generateRisikoGraph(ActivityRecord::finder()->findAllBySQL($SQL));
        }
                
	private function generateRisikoGraph($ActiveRecord) {

		$ydata1 = array();
		$xdata = array();
		$width = array();
                $height = array();
                $ytitle = array("Cost");
                $title = array("Activity");
                $title = implode(',', $title);
		$legend = array("cost per activity");
                $legend = implode(',', $legend);


		$ii=0;

		foreach ($ActiveRecord as $DetailRecord){
			$xdata[] = $DetailRecord->idtm_activity;
			$ydata1[] = $DetailRecord->ttact_kosten;
			$ii++;
			if($ii > 100){
				break;
			}
		}

                $width[] = $this->CostActivityImage->Width;
                $height[] = $this->CostActivityImage->Height;
                $width = implode(',', $width);
		$height = implode(',', $height);
		

		$ydata1 = implode(',', $ydata1);
		$xdata = implode(',', $xdata);
		$ytitledata = implode(',', $ytitle);
		$this->CostActivityImage->ImageUrl = $this->getRequest()->constructUrl('page','graph', 3, array( 'height' => $height, 'legend' => $legend, 'title' => $title,'width' => $width,'xdata' => $xdata, 'ydata1' => $ydata1, 'ytitle' => $ytitledata), false);
	}

    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    //ENDE DER RISIKEN
    
	
}

?>