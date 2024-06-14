<?php

    class IPinfo
    {
        public static function countryCityFromIP($ipAddr)
        {
            ip2long($ipAddr) == -1 || ip2long($ipAddr) === false ? trigger_error('Invalid IP', E_USER_ERROR) : '';
            $ipDetail = []; //initialize a blank array

            $xml = file_get_contents('http://api.hostip.info/?ip=' . $ipAddr);
            preg_match("@<Hostip>(\s)*<gml:name>(.*?)</gml:name>@si", $xml, $match);
            $ipDetail['city'] = $match[2];
            preg_match('@<countryName>(.*?)</countryName>@si', $xml, $matches);
            $ipDetail['country'] = $matches[1];
            preg_match('@<countryAbbrev>(.*?)</countryAbbrev>@si', $xml, $matches);
            $ipDetail['countryAbbrev'] = $matches[1];
            preg_match('@<countryAbbrev>(.*?)</countryAbbrev>@si', $xml, $cc_match);
            $ipDetail['country_code'] = $cc_match[1]; //assing the country code to array
            if ($ipDetail['country_code'] == 'UK' || $ipDetail['country_code'] == 'uk') {
                $ipDetail['country_code'] = 'GB';
            }

            return $ipDetail;
        }
    }
