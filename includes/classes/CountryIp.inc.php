<?php

class CountryIp
{
    public $CName;
    public $CCode;

    public function __construct($ip)
    {
        $country_query = 'SELECT COUNTRY_CODE2,COUNTRY_NAME FROM ip_range ' .
            "WHERE IP_FROM<=inet_aton('" . $ip . "') " .
            "AND IP_TO>=inet_aton('" . $ip . "') ";
        $res = DBi::$conn->query($country_query);
        
        $row = mysqli_fetch_array($res);
        $this->CName = $row['COUNTRY_NAME'];
        $this->CCode = $row['COUNTRY_CODE2'];
    }

    public function getImage()
    {
        return 'images/flags_big/' . strtolower($this->CCode) . '.png';
    }
}
