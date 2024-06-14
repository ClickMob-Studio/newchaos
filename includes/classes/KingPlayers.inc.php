<?php
define('NUM_BOSS', 16);

final class KingPlayers extends ARObject
{
    public static $idField = 'id';
    public static $dataTable = 'kingplayer';

    public function __construct($id)
    {
        try {
            parent::__construct($id);
        } catch (Exception $e) {
            self::AddRecords(['id' => $id], self::$dataTable);
            parent::__construct($id);
        }
    }

    public function getBossStats($i)
    {
        $id = 'king_' . $i;
        $guards = 'king_' . $i . '_guards';
        $when = 'king_' . $i . '_time';
        $arr['id'] = $this->$id;
        $arr['guards'] = $this->$guards;
        $arr['when'] = $this->$when;

        return $arr;
    }

    public function BossKilled($i)
    {
        $king = new KingList($i);
        $this->SetAttribute('king_' . $i, $i);
        $this->SetAttribute('king_' . $i . '_guards', $king->num_guards);
        $this->SetAttribute('king_' . $i . '_time', time());
    }

    public function GooseKilled($i)
    {
        $king = new KingList($i);
        $ls = 'king_' . $i . '_guards';
        if ($king->num_guards == $this->$ls) {
            return;
        }

        $this->AddToAttribute('king_' . $i . '_guards', 1);
    }

    public function getNextBoss()
    {
        $i = 1;
        for ($i = 1; $i <= NUM_BOSS; ++$i) {
            $s = 'king_' . $i;
            if ($this->$s == 0) {
                break;
            }
        }
        if ($i < NUM_BOSS) {
            return new KingList($i++);
        }
    }

    public static function GetAllKingPlayers()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'MinLevel', 'ASC');
    }

    public static function GetAllByIdKingPlayers()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        $arr = [];
        for ($i = 1; $i <= NUM_BOSS; ++$i) {
            $arr[] = 'king_' . $i;
            $arr[] = 'king_' . $i . '_guards';
            $arr[] = 'king_' . $i . '_time';
        }
        $arr[] = self::$idField;

        return $arr;
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


