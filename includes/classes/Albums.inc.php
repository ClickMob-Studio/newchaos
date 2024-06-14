<?php

final class Albums extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'albums';
    public static $rate = -1;
    public static $item = 51;

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function current_rate()
    {
        if (Albums::$rate == -1) {
            Albums::$rate = 30;
        }

        return Albums::$rate;
    }

    public function Finish()
    {
        if ($this->end_time > time()) {
            return;
        }
        User::SAddItems(Item::getDiscId(), $this->user_id, $this->number_of_albuns);
        $user = UserFactory::getInstance()->getUser($this->user_id);
        $user->Notify('You have finished crafting ' . $this->number_of_albuns . ' medal'.($this->number_of_albuns === 1 ? '. It\'s' : 's. They\'re').' available in your inventory.');
        self::sDelete(self::$dataTable, ['id' => $this->id]);
    }

    public static function createAlbum(SUser $user, $number)
    {
        $user1 = UserFactory::getInstance()->getUser($user->id);
        if ($user1->dog_tags < $number * Albums::current_rate() || $number < 1) {
            throw new FailedResult("You don't have enough dog tags.");
        }
        $exp = rand(3, 5);
        $time = rand(30, 60);
        $user1->addExpBonus($time * $number, $exp);
        $data = ['user_id' => $user->id,
                    'end_time' => time() + (60 * 60 * 7 * 24),
                    'number_of_albuns' => $number,
                    'reputation' => $number * Albums::current_rate(), ];
        $user1->RemoveFromAttribute('dog_tags', $number * Albums::current_rate());

        self::AddRecords($data, Albums::$dataTable);
    }

    public static function Albuns_at_creation(SUser $user)
    {
        $sql = 'select * from ' . self::$dataTable . ' where user_id=' . $user->id;
        $res =DBi::$conn->query($sql);
        $albuns = [];
        while ($row = mysqli_fetch_object($res)) {
            $albuns[] = $row;
        }

        return $albuns;
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
            'user_id',
            'end_time',
            'number_of_albuns',
            'reputation',
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
