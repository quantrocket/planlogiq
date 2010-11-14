<?php

Prado::using('Application.app_code.htmlsql');

class GoogleAdressTranslator
{
	/**
	 * Highlights a string as php code
	 * @param string $address The php code to highlight
	 * @return array lat, long
	 * @soapmethod
	 */
public static function getLatAndLong($address)
    {
        $address = urlencode($address);
        $apiKey = Prado::getApplication()->Parameters['GMapApiKey'];
        $apiUrl = "http://maps.google.com/maps/geo?&output=xml&key=".$apiKey."&q=".$address;

        $wsql = new htmlsql();
        
    	// connect to a URL
    	if (!$wsql->connect('url', $apiUrl)){
        	print 'Error while connecting: ' . $wsql->error;
    	}
    	
    	if (!$wsql->query('SELECT * FROM coordinates')){
        	print "Query error: " . $wsql->error;
    	}
    	
    	$temp = $wsql->fetch_array();
    	
        return $temp[0]['text'];
    }
}

?>