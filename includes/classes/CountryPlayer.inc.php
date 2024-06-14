<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class CountryPlayer
{
    public static function nmbr_of_players($country)
    {
        $sql = "select country, number, countryName from countryplayers where country='" . $country . "'";
        $db =DBi::$conn->query($sql);
        $row = mysqli_fetch_array($db);

        return $row['number'];
    }

    public static function CountryName($country)
    {
        $sql = "select country, number, countryName from countryplayers where country='" . $country . "'";
        $db =DBi::$conn->query($sql);
        $row = mysqli_fetch_array($db);

        return $row['countryName'];
    }

    public static function getCountries()
    {
        $sql = 'select country, number, countryName from countryplayers';
        $db =DBi::$conn->query($sql);
        $results = [];
        while ($row = mysqli_fetch_array($db)) {
            $results[] = $row;
        }

        return $results;
    }

    public static function get50($top, $country)
    {
        $sql = 'select * from ' . $top . " where country='" . $country . "' order by position LIMIT 0 , 50";
        $db =DBi::$conn->query($sql);

        while ($row = mysqli_fetch_array($db)) {
            $results[] = $row;
        }

        return $results;
    }
}
