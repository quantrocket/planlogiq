<?php

class QVTicketSecure extends TPage
{
	/**
	 * Highlights a string as php code
	 * @param string $address The php code to highlight
	 * @return array lat, long
	 * @soapmethod
	 */

         //xml::
         private $urlxml;
         private $url;
         private $wsuser = "webserviceuser";
         private $wspassword = "PA55word";

    public function getTicket($username){
        $this->urlxml = "http://".$this->Application->Parameters['QlikViewHost']."/ticketWebserviceSecure/Service.asmx?wsdl";
        $this->url = "http://".$this->Application->Parameters['QlikViewHost']."/ticketWebservicesecure/service.asmx/GetTicket?";

        $client = new SoapClient($this->urlxml);
        return $client->getTicket($username);

    }
    

    public function getTicketHTMLSQL($username)
        {
            $this->urlxml = "http://".$this->Application->Parameters['QlikViewHost']."/ticketWebserviceSecure/Service.asmx?wsdl";
            $this->url = "http://".$this->Application->Parameters['QlikViewHost']."/ticketWebservicesecure/service.asmx/GetTicket?";

            $address = urldecode($this->url);
            $apiKey = $username;
            $apiUrl = $address."UserID=".$apiKey;

            $wsql = new htmlsql();

            // connect to a URL
            if (!$wsql->connect('url', $apiUrl)){
                    print 'Error while connecting: ' . $wsql->error;
            }

            if (!$wsql->query('SELECT * FROM string')){
                    print "Query error: " . $wsql->error;
            }

            $temp = $wsql->fetch_array();

            return $temp[0]['text'];
        }
}

?>