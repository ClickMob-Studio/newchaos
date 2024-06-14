<?php

class PokerStats extends BaseObject
{
    public static $idField = 'ID';
    public static $dataTable = 'poker_stats';
    public $user_id;

    public function __construct($id)
    {
        $this->user_id = $id;
    }

    public function getWinpot()
    {
        $query = "select winpot from poker_stats where user_id='" . $this->user_id . "'";

        $res = DBi::$conn->query($query);
        $arrResult = mysqli_fetch_assoc($res);
        if ($arrResult['winpot'] == '') {
            $arrResult['winpot'] = 0;
        }

        return $arrResult['winpot'];
    }

    public function getGameId()
    {
        $query = "select GUID, banned, gID, vID from poker_players where user_id='" . $this->user_id . "'";

        $res = DBi::$conn->query($query);
        $arrResult = mysqli_fetch_assoc($res);

        return $arrResult;
    }

    public function GetDataFields()
    {
        return self::GetDataTableFields();
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField, 'user_id', 'player', 'rank',
            'winpot', 'gamesplayed', 'tournamentsplayed', 'tournamentswon',
            'handsplayed', 'handswon', 'bet', 'checked',
            'called', 'allin', 'fold_pf', 'fold_f',
            'fold_t', 'fold_r',
            ];
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
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
