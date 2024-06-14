<?php

class SeasonPass extends BaseObject
{
    public static function getTypes(){
        $query = DBi::$conn->query("SELECT * FROM season_pass_types");
        return $query;
    }

    public static function Delete($id)
    {
        $id = (int)$id;
        DBi::$conn->query("DELETE FROM `season_pass` WHERE `SP_id` = '" . $id . "'");
        echo HTML::ShowSuccessMessage("Season pass deleted!");
        return;
    }
    public function getCurrentPass(){
       // self::endPass();
        $pass = DBi::$conn->query("SELECT * FROM `season_pass` WHERE `SP_status` = '1'")->fetch_assoc();
        $end = $pass['SP_end'];
        //convert end date to unix timestamp
        $end = strtotime($end);
        //get current date
        $now = time();
        //check if current date is greater than end date
        if($now > $end){
            DBi::$conn->query("UPDATE `season_pass` SET `SP_status` = '0' WHERE `SP_id` = '" . $pass['SP_id'] . "'");
        }
        return $pass;
    }
    public static function checkCurrentLevel($userid){
        //get current pass
        $pass = SeasonPass::getCurrentPass();
        //get current level
        $level = DBi::$conn->query("SELECT * FROM season_pass_users WHERE SP_passid = '" . $pass['SP_id'] . "' AND SP_userid = '" . $userid . "'");
        if(mysqli_num_rows($level) > 0){
            $level = mysqli_fetch_assoc($level);
            return $level;
        }else{
            DBi::$conn->query("INSERT INTO `season_pass_users` (`SP_passid`, `SP_userid`, `SP_level`, SP_exp) VALUES (" . $pass['SP_id'].",$userid, 0, 0)");
            $level = DBi::$conn->query("SELECT * FROM season_pass_users WHERE SP_passid = '" . $pass['SP_id'] . "' AND SP_userid = '" . $userid . "'");
            $level = mysqli_fetch_assoc($level);
            return $level;
        }
    }
    public static function SkipFive($userid){
        $data = SeasonPass::checkCurrentLevel($userid);
        if(($data['SP_level'] + 5) >= SeasonPass::getMaxLevel()) {
            echo HTML::ShowErrorMessage("You cannot skip more than 5 levels as this will take you pass the max level");
            return;
        }else{
            $newlevel = $data['SP_level'] + 5;
            $u = new User($userid);
            if(SeasonPass::getFiveLevelsCost() > $u->points){
                echo HTML::ShowErrorMessage("You do not have enough points to skip 5 levels");
                return;
            }
            $u->RemoveFromAttribute('points', SeasonPass::getFiveLevelsCost());
           DBi::$conn->query("UPDATE `season_pass_users` SET `SP_level` = " . $newlevel . ", SP_exp = 0 WHERE `SP_passid` = " . $data['SP_passid'] . " AND `SP_userid` = " . $userid );

            echo HTML::ShowSuccessMessage("You have skipped 5 levels!");
            return;
        }
    }
    public function CreatePass($name, $tiers, $start, $end, $status = 0){
        $name = DBi::$conn->real_escape_string($name);
        $tiers = (int) $tiers;
        $start = DBi::$conn->real_escape_string($start);
        $end = DBi::$conn->real_escape_string($end);
        $status = (int) $status;
        if(empty($name) || empty($tiers) || empty($start) || empty($end) || empty($status)) {
            echo HTML::ShowErrorMessage("Please fill all fields!");
            return false;
        }
        DBi::$conn->query("INSERT INTO `season_pass` (`SP_name`, `SP_tiers`, `SP_start`, `SP_end`, `SP_status`) VALUES ('".$name."', '".$tiers."', '".$start."', '".$end."', '".$status."')");
        echo HTML::ShowSuccessMessage("Season pass created!");
        return;
    }
    public static function getPrice()
    {
        $price = DBi::$conn->query("SELECT `value` FROM `server_variables` WHERE `field` = 'seasonPass_cost'")->fetch_assoc();
        return $price['value'];
    }

    public static function getMaxLevel()
    {
        $maxLevel = DBi::$conn->query("SELECT `value` FROM `server_variables` WHERE `field` = 'seasonPass_maxLevel'")->fetch_assoc();
        return $maxLevel['value'];
    }

    public static function getExpPerLevel()
    {
        $expLevel = DBi::$conn->query("SELECT `value` FROM `server_variables` WHERE `field` = 'seasonPass_expPerLevel'")->fetch_assoc();
        return $expLevel['value'];
    }

    public static function getFiveLevelsCost()
    {
        $fiveLevels = DBi::$conn->query("SELECT `value` FROM `server_variables` WHERE `field` = 'seasonPass_5levelsCost'")->fetch_assoc();
        return $fiveLevels['value'];
    }

    public static function getInfo()
    {
        $passInfo = DBi::$conn->query("SELECT `value` FROM `server_variables` WHERE `field` = 'seasonPass_info'")->fetch_assoc();
        return $passInfo['value'];
    }

    public static function getPasses()
    {
        $passes = DBi::$conn->query("SELECT * FROM `season_pass`");
        return $passes;
    }

    public static function GetPass($id)
    {
        $id = (int)$id;
        $pass = DBi::$conn->query("SELECT * FROM `season_pass` WHERE `SP_id` = '" . $id . "'");
        if (mysqli_num_rows($pass) == 0) {
            echo HTML::ShowErrorMessage("Season pass not found!");
            return false;
        }
        return $pass;
    }

    public static function DeleteLevel($id)
    {
        $id = (int)$id;
        //check if level exists
        $level = DBi::$conn->query("SELECT * FROM `season_pass_levels` WHERE `id` = '" . $id . "'");
        if (mysqli_num_rows($level) == 0) {
            echo HTML::ShowErrorMessage("Season pass level not found!");
            return false;
        }
        DBi::$conn->query("DELETE FROM `season_pass_levels` WHERE `id` = '" . $id . "'");
        echo HTML::ShowSuccessMessage("Season pass level deleted!");
        return;
    }
    public function AddLevel($levelid, $levelName, $levelType, $levelValue, $levelItemId = 0, $paid = 0){
        $levelid = (int) $levelid;
        $levelName = DBi::$conn->real_escape_string($levelName);
        $levelType = DBi::$conn->real_escape_string($levelType);
        $levelValue = (int) $levelValue;
        $levelItemId = (int) $levelItemId;
        $paid = (int) $paid;
        if(empty($levelName) || empty($levelType) || empty($levelValue)) {
            echo HTML::ShowErrorMessage("Please fill all fields!");
            return false;
        }
        if(!empty($levelItemId) && $levelType == 'item') {
            //check item exists
            $item = DBi::$conn->query("SELECT * FROM `items` WHERE `id` = '" . $levelItemId . "'");
            if (mysqli_num_rows($item) == 0) {
                echo HTML::ShowErrorMessage("Item not found!");
                return false;
            }
        }
        if($levelItemId > 0 && $levelType != 'item') {
         $levelid = 0;
        }
        DBi::$conn->query("INSERT INTO `season_pass_levels` (`SP_id`, `SP_name`, `SP_type`, `SP_value`, `SP_item_id`, SP_paid) VALUES ('".$levelid."', '".$levelName."', '".$levelType."', '".$levelValue."', '".$levelItemId."', '".$paid."')");
        echo HTML::ShowSuccessMessage("Season pass level added!");
        return true;
    }

    public static function EditLevel($id, $name, $type, $value, $item_id)
    {
        $name = DBi::$conn->real_escape_string($name);
        $type = DBi::$conn->real_escape_string($type);
        $id = (int)$id;
        $value = (int)$value;
        $item_id = (int)$item_id;
        //check if level exists
        $level = DBi::$conn->query("SELECT * FROM `season_pass_levels` WHERE `id` = '" . $id . "'");
        if (mysqli_num_rows($level) == 0) {
            echo HTML::ShowErrorMessage("Season pass level not found!");
            return false;
        }
        //check item exists
        if ($type == "item") {
            $item = DBi::$conn->query("SELECT * FROM `items` WHERE `id` = '" . $item_id . "'");
            if (mysqli_num_rows($item) == 0) {
                echo HTML::ShowErrorMessage("Item not found!");
                return false;
            }
        }
        DBi::$conn->query("UPDATE `season_pass_levels` SET `SP_name` = '" . $name . "', `SP_type` = '" . $type . "', `SP_value` = '" . $value . "', `SP_item_id` = '" . $item_id . "' WHERE `id` = '" . $id . "'");
        echo HTML::ShowSuccessMessage("Season pass level edited!");
        return;
    }


    public static function EditSettings($seasonPass_cost, $seasonPass_maxLevel, $seasonPass_expPerLevel, $seasonPass_5levelsCost, $seasonPass_info){
        $seasonPass_cost = (int)$seasonPass_cost;
        $seasonPass_maxLevel = (int)$seasonPass_maxLevel;
        $seasonPass_expPerLevel = (int)$seasonPass_expPerLevel;
        $seasonPass_5levelsCost = (int)$seasonPass_5levelsCost;
        DBi::$conn->query("UPDATE `server_variables` SET `value` = '".$seasonPass_cost."' WHERE `field` = 'seasonPass_cost'");
        DBi::$conn->query("UPDATE `server_variables` SET `value` = '".$seasonPass_maxLevel."' WHERE `field` = 'seasonPass_maxLevel'");
        DBi::$conn->query("UPDATE `server_variables` SET `value` = '".$seasonPass_expPerLevel."' WHERE `field` = 'seasonPass_expPerLevel'");
        DBi::$conn->query("UPDATE `server_variables` SET `value` = '".$seasonPass_5levelsCost."' WHERE `field` = 'seasonPass_5levelsCost'");
        DBi::$conn->query("UPDATE `server_variables` SET `value` = '".$seasonPass_info."' WHERE `field` = 'seasonPass_info'");
        echo HTML::ShowSuccessMessage("Settings saved!");
        return;
    }

    protected function GetIdentifierFieldName()
    {
        // TODO: Implement GetIdentifierFieldName() method.
    }

    protected function GetClassName()
    {
        // TODO: Implement GetClassName() method.
    }

    protected static function GetDataTable()
    {
        // TODO: Implement GetDataTable() method.
    }

    protected static function GetDataTableFields()
    {
        // TODO: Implement GetDataTableFields() method.
    }
}