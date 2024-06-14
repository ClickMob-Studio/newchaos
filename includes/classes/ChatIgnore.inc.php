<?php

final class ChatIgnore extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'chatignore';
    public $list = [];

    public function __construct($id)
    {
        try {
            parent::__construct($id);
            $this->list = explode(',', $this->ignore);
        } catch (Exception $e) {
            self::AddRecords(['id' => $id], self::$dataTable);
            $this->list = [];
            $this->id = DBi::$conn -> insert_id;
        }
    }

    public static function Get($id)
    {
        return new ChatIgnore($id);
    }

    public function Add($user_id)
    {
        $user = UserFactory::getInstance()->getUser($user_id);
        if ($user->IsAdmin()) {
            throw new FailedResult("You can't ignore Moderator or admins");
        }
        $this->list[] = $user_id;
        $this->SetAttribute('ignore', implode(',', $this->list));
    }

    public function Remove($user_id)
    {
        for ($i = 0; $i < count($this->list); ++$i) {
            if ($this->list[$i] == $user_id) {
                unset($this->list[$i]);
            }
        }
        $this->SetAttribute('ignore', implode(',', $this->list));
    }

    public static function GetAll($where = '')
    {
        $objs = parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable(), $where);

        foreach ($objs as $key => $obj) {
            $objs[$key] = $obj;
        }

        return $objs;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
                        'ignore',
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
