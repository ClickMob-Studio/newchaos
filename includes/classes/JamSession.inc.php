<?php

final class JamSession extends CachedObject {
    static $idField = 'id';
    static $dataTable = 'jamsession';
    static $MaxNumber=1;
    static $running=1;
    static $finished=2;


    public function __construct($id)
    {
        parent::__construct($id);

    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return array(
            self::$idField,
            'jam_name',
            'creator',
            'starttime',
            'elements',
            'finishtime',
            'labelexperience',
            'reputation',
            'stat');
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
    public static function getJam(User $user)
    {

    }

    public static function join(User $user)
    {

    }
    public static function finish()
    {
        $sql="select id from ".
            self::$datatable." where finishtime<".time()." and stat=".self::running;
        $res=DBi::$conn->query($sql);
        while($row= mysqli_fetch_object($res))
        {
            $session=new JamSession($row->id);
            foreach(explode(",",$jam->elements) as $elem)
            {
                Event::Add($elem, 'The jam session is over and everybody had a great time! Your regiment earned
                            [10*jampoints] Exp and you earned [jampoints] concert reputation points.');
            }
            self::sUpdate(self::$dataTable, Array(
                "stat"=>self::$finished,
                "labelexperience"=>
                    "reputation"

            ),Array("id"=>$row->id));

        }

    }
    public static function Create(User $user, $name)
    {
        if($user->gang==0)
            throw new SoftException ("You are not at a label");
        if(self::getJam($user))
            self::AddRecords(Array(
                'jam_name'=>$name,
                'creator'=>$user->id,
                'starttime'=>time(),
                'elements'=>implode(",",Array($user->id)),
                'finishtime'=>time()+30,
                'stat'=> 1
    ), self::$dataTable);
        }

}

?>
