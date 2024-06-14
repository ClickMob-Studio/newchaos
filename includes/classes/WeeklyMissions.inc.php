<?php

class WeeklyMissions extends BaseObject
{

    public static $idField = 'id';


    public static $dataTable = 'weekly_missions';

    public function __construct($id)
    {
        parent::__construct($id);

        $this->name = constant($this->name);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function Get()
    {
        $sql = DBi::$conn->query('SELECT * FROM weekly_missions ORDER BY missions DESC LIMIT 5');

        return $sql;
    }
    public static function GetMugs()
    {
        $sql = DBi::$conn->query('SELECT * FROM weekly_mugs ORDER BY mugs DESC LIMIT 5');

        return $sql;
    }
    public static function GetAttacks()
    {
        $sql = DBi::$conn->query('SELECT * FROM weekly_attacks ORDER BY attacks DESC LIMIT 5');

        return $sql;
    }
    public static function AddMission($userid, $exp){
        $sql = DBi::$conn->query("SELECT * FROM weekly_missions WHERE userid = ". $userid);
        if(!mysqli_num_rows($sql)){
            DBi::$conn->query("INSERT INTO weekly_missions (userid,missions) VALUES(".$userid.", ".$exp.")");
        }else{
            DBi::$conn->query("UPDATE weekly_missions SET missions = missions+".$exp." WHERE userid=".$userid);
        }
    }
    public static function AddMugs($userid){
        $sql = DBi::$conn->query("SELECT * FROM weekly_mugs WHERE userid = ". $userid);
        if(!mysqli_num_rows($sql)){
            DBi::$conn->query("INSERT INTO weekly_mugs (userid,mugs) VALUES(".$userid.", 1)");
        }else{
            DBi::$conn->query("UPDATE weekly_mugs SET mugs = mugs+1 WHERE userid=".$userid);
        }
    }
    public static function AddAttacks($userid){
        $sql = DBi::$conn->query("SELECT * FROM weekly_attacks WHERE userid = ". $userid);
        if(!mysqli_num_rows($sql)){
            DBi::$conn->query("INSERT INTO weekly_attacks (userid,attacks) VALUES(".$userid.", 1)");
        }else{
            DBi::$conn->query("UPDATE weekly_attacks SET attacks = attacks+1 WHERE userid=".$userid);
        }
    }
    public function CreditMugs(){
        DBi::$conn->query("TRUNCATE weekly_table");
        $sql = DBi::$conn->query('SELECT * FROM weekly_mugs ORDER BY mugs DESC LIMIT 3');
        $i = 0;
        $prize1 = 5;
        $var1 = 'credits';
        $prize2 = 300;
        $var2 = 'points';
        $prize3= 300000;
        $var3 = 'money';
        while ($row = mysqli_fetch_array($sql)){
            $i++;
            $pri =  ${'prize'.$i};
            $vars = ${'var'.$i};
            DBi::$conn->query('UPDATE grpgusers SET '.$vars.' = '.$vars.' + '.$pri.' WHERE id ='.$row['userid']);
            User::SNotify($row['userid'], 'You placed No.'.$i.' in the weekly mug event and won '.$pri.' '.$vars );
            DBi::$conn->query("INSERT into weekly_table (userid, `value`, section, place) VALUES (".$row['userid'].", ".$row['mugs'].", 'mugs', ".$i.")");
            DBi::$conn->query("TRUNCATE weekly_mugs");
        }
    }
    public function CreditMissions(){
        $sql = DBi::$conn->query('SELECT * FROM weekly_missions ORDER BY missions DESC LIMIT 3');
        $i = 0;
        $prize1 = 5;
        $var1 = 'credits';
        $prize2 = 300;
        $var2 = 'points';
        $prize3= 300000;
        $var3 = 'money';
        while ($row = mysqli_fetch_array($sql)){
            $i++;
            $pri =  ${'prize'.$i};
            $vars = ${'var'.$i};
            DBi::$conn->query('UPDATE grpgusers SET '.$vars.' = '.$vars.' + '.$pri.' WHERE id ='.$row['userid']);
            User::SNotify($row['userid'], 'You placed No.'.$i.' in the weekly missions event and won '.$pri.' '.$vars );
            DBi::$conn->query("INSERT into weekly_table (userid, `value`, section, place) VALUES (".$row['userid'].", ".$row['missions'].", 'missions', ".$i.")");
            DBi::$conn->query("TRUNCATE weekly_missions");
        }
    }
    public function CreditAttacks(){
        $sql = DBi::$conn->query('SELECT * FROM weekly_attacks ORDER BY attacks DESC LIMIT 3');
        $i = 0;
        $prize1 = 5;
        $var1 = 'credits';
        $prize2 = 300;
        $var2 = 'points';
        $prize3= 300000;
        $var3 = 'money';
        while ($row = mysqli_fetch_array($sql)){
            $i++;
            $pri =  ${'prize'.$i};
            $vars = ${'var'.$i};
            DBi::$conn->query('UPDATE grpgusers SET '.$vars.' = '.$vars.' + '.$pri.' WHERE id ='.$row['userid']);
            User::SNotify($row['userid'], 'You placed No.'.$i.' in the weekly attacks event and won '.$pri.' '.$vars );
            DBi::$conn->query("INSERT into weekly_table (userid, `value`, section, place) VALUES (".$row['userid'].", ".$row['attacks'].", 'attacks', ".$i.")");
            DBi::$conn->query("TRUNCATE weekly_attacks");
            
        }
    }



    protected static function GetDataTable()
    {
        return self::$dataTable;
    }
    protected static function EndWeek(){
        //run for prizes before truncate
        DBi::$conn->query('TRUNCATE '. $datatable);
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',

            'userid',

            'missions',

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

?>

