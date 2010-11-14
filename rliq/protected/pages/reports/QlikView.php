<?php

class QlikView extends TPage{
    
        private $Periode = '10001';
        private $Variante = '1';
        private $idta_struktur_bericht = 1;
        private $zwischenergebnisse = array();
        private $SinglePeriode = 0;
        
        public function onLoad($param){
		
		parent::onLoad($param);

                $QVTestSecure = new QVTicketSecure();
                $RolandTicket = $QVTestSecure->getTicketHTMLSQL($this->User->Name);

                if(!$this->page->isPostBack && !$this->page->isCallback){
                    //print_r($RolandTicket);
                    $this->QlikViewInline->FrameUrl=("http://192.168.0.105/qlikview/AjaxZfc/dms_ajax/?userid=".$RolandTicket);
                }//ende der postback schleife
	}
	
}
?>