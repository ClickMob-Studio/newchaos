<?php

class regTags Extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'reg_tags';

    public static function GetRegTags($gangid){
        $res = DBi::$conn->query("SELECT * FROM reg_tags WHERE gang = '$gangid'");
        return mysqli_fetch_object($res);
    }

    //get last 20 logs
    public static function GetLogs($gangid){
        return $res = DBi::$conn->query("SELECT * FROM reg_tags_logs WHERE gang = '$gangid'  ORDER BY id ASC");

    }

    public static function GetGangUsers($gangid){
        $res = DBi::$conn->query("SELECT * FROM grpgusers WHERE gang = '$gangid'");
        return $res;
    }

    //add log
    public static function AddLog($gangid, $tag, $user){
        $res = DBi::$conn->query("INSERT INTO reg_tags_logs (gang, amount, user, time) VALUES ('$gangid', '$tag', '$user', '".time()."')");
    }

    //donate to gang
    public static function donate($gangid, $userid, $amount){
        $sql = DBi::$conn->query("SELECT * FROM reg_tags WHERE gang = '$gangid'");
        if(mysqli_num_rows($sql) < 1){
            $sql = DBi::$conn->query("INSERT INTO reg_tags (gang, tags) VALUES ('$gangid', '$amount')");
        }else{
            $sql = DBi::$conn->query("UPDATE reg_tags SET tags = tags + '$amount' WHERE gang = '$gangid'");
        }
        //remove tags from grpgusers
        $user = new User($userid);
        $user->setAttribute('dog_tags', $user->dog_tags - $amount);
        return true;
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
            'tags',
            'gang'
        ];
    }
}