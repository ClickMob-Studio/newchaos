<?php

final class country extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'countries';


    public function __construct($id)
    {
        parent::__construct($id);
    }
    public static function getProv($id){
        $sql = 'select * from country_owners where country_id='.$id;
        $res = DBi::$conn->query($sql);
        $provs = [];
        while($row = mysqli_fetch_object($res)){
            $provs[] = $row;
        }
        return $provs;
    }
    //check if a valid country
    public static function isValidCountry($id)
    {
        $sql = 'select * from ' . self::$dataTable . ' where id=' . $id;
        $res = DBi::$conn->query($sql);
        if (mysqli_num_rows($res) == 0) {
            return false;
        }

        return true;
    }

    public function isValidProv($id, $user){
        $sql = 'select * from country_owners where id='.$id.' and owner_id='.$user;
        $res = DBi::$conn->query($sql);
        if(mysqli_num_rows($res) == 0){
            return false;
        }
        return true;
    }
    public function removeFood(){
        $sql = 'update country_owners set food = 0';
        DBi::$conn->query($sql);
    }
    public function feedPeople($id){
        //reterive provence
        $sql = 'select * from country_owners where id='.$id;
        $res = DBi::$conn->query($sql);
        $prov = mysqli_fetch_object($res);
        //check if food is 1
        if($prov->food == 1){
           return HTML::ShowErrorMessage("You have already fed your people today.");
        }
        //difference between heath and maxhealth
        $diff = $prov->maxhealth - $prov->health;
        if($diff > 70){
        $feed = mt_rand(1, $diff);
        }else{
            $feed = mt_rand(1, 70);
        }
        //feed as a percentage of maxhealth

        //update health if health is greater than maxhealth set to maxhealth
        if($prov->health + $feed > $prov->maxhealth){
            $sql = 'update country_owners set health = max_health, food = 1 where id='.$id;
            DBi::$conn->query($sql);
        }else{
            $sql = 'update country_owners set health = health + '.$feed.', food = 1 where id='.$id;
           DBi::$conn->query($sql);
        }
        return HTML::ShowSuccessMessage("You have fed your people, they are starting to feel happier.");

    }

    public function happinessState()
    {
        //fetch all provences
        $sql = 'select * from country_owners';
        $res = DBi::$conn->query($sql);
        while ($row = mysqli_fetch_object($res)) {
            //check if happiness is less than 0
            if ($row->health < 1) {
                //1 in 6 chance to revolt
                $randomchacne = mt_rand(1, 6);
                //select random grpguser using sql
                if ($randomchacne == 1) {
                    $sql = 'select id from grpgusers WHERE lastactive > ' . time() . '-432000 order by rand() limit 1';
                    $res = DBi::$conn->query($sql);
                    $new = mysqli_fetch_object($res);
                    $newowner = new User($new->id);
                    $oldowner = new User($row->owner_id);
                    $newowner->Notify($oldowner->formattedname . ' has been overthrown by ' . $newowner->formattedname . ' in ' . $row->country_prov . '!');
                    $oldowner->Notify('You did not keep your people happy in ' . $row->country_prov . ' and have been overthrown by ' . $newowner->formattedname . '!');
                    $sql = 'update country_owners set owner_id=' . $newowner->id . ' SET health = max_health where id=' . $row->id;
                    $res = DBi::$conn->query($sql);
                    return 'done';
                }
            }else{
                //remove rand 5 , 80 from health
                $random = mt_rand(5, 80);
                $health = $row->health - $random;
                if($health < 0){
                    $health = 0;
                }
                $sql = 'update country_owners set health='.$health.' where id='.$row->id;
                $res = DBi::$conn->query($sql);
                //send notify to owner
                $owner = new User($row->owner_id);
                $owner->Notify('Your people are unhappy in '.$row->country_prov.'! You have lost '.$random.'% happiness!');
            }
        }
    }

    public static function getAllCountries()
    {
        $sql = 'select * from ' . self::$dataTable;
        $res = DBi::$conn->query($sql);
        $countries = [];
        while ($row = mysqli_fetch_object($res)) {
            $countries[] = $row;
        }

        return $countries;
    }
    //fetch all owners of prov in country
    public static function getProvOwners($country)
    {
        $sql = 'select * from country_owners where country_id=' . $country;
        $res = DBi::$conn->query($sql);
        $owners = [];
        while ($row = mysqli_fetch_object($res)) {
            $owners[] = $row->owner_id;
        }

        return $owners;
    }
    public function formatHealth($id){
        $sql = 'select * from country_owners where id='.$id;
        $res = DBi::$conn->query($sql);
        $row = mysqli_fetch_object($res);
        $health = $row->health;
        $max_health = $row->max_health;
        $health = $health/$max_health;
        $health = $health*100;
        $health = round($health);
        return $health;
    }

    public static function Get($id)
    {
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'country_name',
            'country_description',
            'country_flag',
            'country_image',
            'health',
            'max_health',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
