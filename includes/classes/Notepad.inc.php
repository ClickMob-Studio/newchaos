<?php

final class Notepad extends BaseObject
{
    public static $idField = 'userid';
    public static $dataTable = 'notepad';

    public function __construct($id)
    {
        try {
            parent::__construct($id);
        } catch (SoftException $e) {
            parent::AddRecords(['userid' => $id, 'text' => ''], self::GetDataTable());
            parent::__construct($id);
        }
    }

    public function GetFormattedText()
    {
        if (isset($this->text)) {
            return stripslashes(str_replace('\r\n', "\r\n", $this->text));
        }

        return '';
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'text',
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
