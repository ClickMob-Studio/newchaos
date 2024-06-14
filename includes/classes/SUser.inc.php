<?php

class SUserFactory extends Singleton
{
    /**
     * Store a cache of all users who have been previously loaded.
     *
     * @var array
     */
    private $users = [];

    /**
     * Retrieve an SUser.
     *
     * @return SUser
     */
    public function getUser(int $id)
    {
        if (!isset($this->users[$id])) {
            $this->users[$id] = new SUser($id);
        }

        return $this->users[$id];
    }
}

class SUser extends BaseObject
{
    public static $idField = 'id'; //id field

    public static $dataTable = 'grpgusers2'; // table implemented

    /**
     * Constructor.
     */
    public function __construct($id)
    {
        try {
            parent::__construct($id);
            if ($this->country == '') {
                $user = UserFactory::getInstance()->getUser($id);

                $ci = new CountryIp($user->ip);

                if ($user->ip == '127.0.0.1') {
                    $ci->CCode = 'PT';
                }

                $this->setAttribute('country', $ci->CCode);
                $query = DBi::$conn->query("SELECT dog_tags FROM grpgusers WHERE id = ".$user->id);
                $f = mysqli_fetch_assoc($query);
                //$this->SetAttribute('ConcertLevel' , $f['dog_tags']);
            }
        } catch (Exception $e) {
            self::AddRecords(['id' => $id], self::$dataTable);

            parent::__construct($id);
        }
    }
    public function DontWantFailMugExp()
    {
        return $this->failmugexp == 1;
    }

    public function DontWantFailAttackExp()
    {
        return $this->failatkexp == 1;
    }

    public static function GetAllMugs()
    {
        $sig50 = time() - 50 * 24 * 60 * 60;

        $objs = [];

        $res = DBi::$conn->query('SELECT mugged as total, dog_tags, grpgusers2.`id`, `level`, mugged  money, number_of_muggs nbr_mugs,`lastactive`, `gang` FROM `grpgusers2`,`grpgusers` where grpgusers2.id=grpgusers.id AND grpgusers.id not in (SELECT `id` FROM `bans`)  and lastactive>' . $sig50 . ' and admin < 1 ORDER BY total DESC LIMIT 50');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    /**
     * Function returns the class name.
     *
     * @return string
     */
    public static function GetAllBusts()
    {
        $sig50 = time() - 50 * 24 * 60 * 60;

        $objs = [];

        $res = DBi::$conn->query('SELECT busts as total, grpgusers2.`id`, `level`, busts money, number_of_muggs nbr_mugs,`lastactive`, `gang` FROM `grpgusers2`,`grpgusers` where lastactive>' . $sig50 . ' and admin < 1 and  grpgusers2.id=grpgusers.id ORDER BY total DESC LIMIT 50');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public function getReputationRank()
    {
        $objs = [];
        if (isset($this->ConcertLevel) && $this->ConcertLevel != '') {
            $res = DBi::$conn->query('SELECT COUNT(id) + 1 id from grpgusers WHERE dog_tags>' . $this->ConcertLevel);
            $obj = mysqli_fetch_object($res);

            return $obj->id;
        }

        return 1;
    }

    /**
     * Function used to get the data table name which is implemented by class.
     *
     * @return string
     */
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    /**
     * Returns the fields of table.
     *
     * @return array
     */
    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'country',

            'quizz',

            'preferences',

            'flash',

            'no_invite',

            'jails',

            'busts',

            'ConcertLevel',

            'dailyMugsAmount',

            'mugged',

            'number_of_muggs',

            'NoAutomaticConcert',

            'LeftMenu',

            'Emotions',

            'flashBB',

            'StockShow',

            'imgavatar',

            'TagType',

            'chattime',

            'secview',

            'perexp',

            'hangman',

            'multiwarn',

            'turnonmugger',

            'chatim',

            'chatimhome',

            'ntut', 'skinfb', 'forceCountry',

            'grayscale',

            'oldgym',

            'failmugexp',

            'santa_sleighs',
            'golden_eggs',
            'tutorial_v2',
            'monthly_prize_points'
        ];
    }

    /**
     * Returns the identifier field name.
     *
     * @return mixed
     */
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
