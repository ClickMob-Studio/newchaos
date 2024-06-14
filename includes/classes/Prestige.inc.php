<?php

abstract class Prestige extends BaseObject
{
    public static $idField = 'prestige';
    public static $dataTable = 'id';

    public static function getRequirement(User $user)
    {
        if ($user->level > 249 && $user->securityLevel == 0) {
            //prestige 1
            return true;
        } else if ($user->level > 349 && $user->securityLevel == 1) {
            //prestige 2
            return true;
        } else if ($user->level > 449 && $user->securityLevel == 2) {
            //prestige 3
            return true;
        } else if ($user->level > 499 && $user->securityLevel == 3) {
            //prestige 4
            return true;
        } elseif ($user->level > 499 && $user->securityLevel == 4) {
            //pretige 5
            return true;
        } elseif ($user->level > 599 && $user->securityLevel == 5) {
            //prestige 6
            return true;
        } elseif ($user->level > 699 && $user->securityLevel == 6) {
            //prestige 7
            return true;
        } else {
            //returns false to shjow that they can not prestige
            return false;
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public static function addToPres(User $user, $type, $value){
        $securityLevel = $user->securityLevel +1;
      if(Prestige::getRequirement($user)){
          $query = BaseObject::createQueryBuilder();
          // Check if there is an existing row for the user and security level
          $result = $query->select('*')
              ->from('prestige' . $securityLevel)
              ->where('userid = :userid')
              ->setParameter('userid', $user->id)
              ->execute()
              ->fetch();
          if ($result) {
             DBi::$conn->query("UPDATE prestige$securityLevel SET ".$type." = ".$type." + ".$value." WHERE userid =".$user->id);
             return;
          }

      }
    }
    public static function WeaponCheck(User $user)
    {
        switch ($user->securityLevel) {
            case 3 :
                $item = 225; //prestige 4 item
                break;
            case 4 :
                $item = 228; //prestige 5 item
                break;
            case 5:
                $item = 234; //prestige 6 item
                break;
            case 6:
                $item = 318; //prestige 7 item
        }
        if ($user->securityLevel < 3) {
            return;
        }
        $weaponCheck = DBi::$conn->query("SELECT * FROM inventory WHERE userid = {$user->id} AND itemid = $item");
        if (mysqli_num_rows($weaponCheck) > 0) {
            return true;
        } elseif ($user->GetWeapon()->id == $item) {
            return true;
        } else {
            return false;
        }
    }

    public static function LegCheck(User $user)
    {
        switch ($user->securityLevel) {
            case 3 :
                $item = 221;
                break;
            case 4:
                $item = 231;
                break;
            case 5:
                $item = 238;
                break;
            case 6:
                $item = 315;
                break;
        }
        $legCheck = DBi::$conn->query("SELECT * FROM inventory WHERE userid = {$user->id} AND itemid = $item");
        if (mysqli_num_rows($legCheck) > 0) {
            return true;
        } elseif ($user->getArmorForType('legs')->id == $item) {
            return true;
        } else {
            return false;
        }
    }

    public static function helmetCheck(User $user)
    {
        switch ($user->securityLevel) {
            case 3:
                $item = 223;
                break;
            case 4:
                $item = 229;
                break;
            case 5 :
                $item = 235;
                break;
            case 6:
                $item = 313;
                break;
        }
        $helmetCheck = DBi::$conn->query("SELECT * FROM inventory WHERE userid = {$user->id} AND itemid = $item");
        if (mysqli_num_rows($helmetCheck) > 0) {
            return true;
        } elseif ($user->getArmorForType('head')->id == $item) {
            return true;
        } else {
            return false;
        }
    }

    public static function gloveCheck(User $user){
        switch($user->securityLevel){
            case 3: $item = 222;
            break;
            case 4: $item = 230;
            break;
            case 5: $item = 235;
            break;
            case 6: $item = 316;
            break;
        }
        $glovesCheck = DBi::$conn->query("SELECT * FROM inventory WHERE userid = {$user->id} AND itemid = $item");
        if(mysqli_num_rows($glovesCheck) > 0) {
            return true;
        } elseif($user->getArmorForType('gloves')->id == $item) {
            return true;
        }else{
            return false;
        }
    }

    public static function bodyCheck(User $user){
        switch($user->securityLevel){
            case 3: $item = 220;
            break;
            case 4: $item = 232;
            break;
            case 5: $item = 237;
            break;
            case 6: $item = 317;
            break;
        }
        $bodyCheck = DBi::$conn->query("SELECT * FROM inventory WHERE userid = {$user->id} AND itemid = $item");
        if(mysqli_num_rows($bodyCheck) > 0) {
            return true;
        } elseif ($user->getArmorForType('chest')->id == $item) {
            return true;
        }else{
            return false;
        }
    }
    public static function bootCheck(User $user){
        switch($user->securityLevel){
            case 3: $item = 224;
            break;
            case 4: $item = 233;
                break;
            case 5: $item = 237;
                break;
            case 6: $item = 314;
                break;
        }

        $boots = DBi::$conn->query("SELECT * FROM inventory WHERE userid = {$user->id} AND itemid = $item");
        if(mysqli_num_rows($boots) > 0) {
            return true;
        } elseif ($user->getArmorForType('boots')->id == $item) {
            return true;
        }else{
            return false;
        }

    }

    public static function houseCheck(User $user){
        switch($user->securityLevel){
            case 2: $house = 55;
            break;
            case 3: $house = 56;
            break;
            case 4: $house =   57;
            break;
            case 5: $house = 58;
            break;
            case 6: $house = 59;
            break;
        }
        if($user->GetCell()->id == $house){
            return true;
        }
    }
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

        ];
    }
}