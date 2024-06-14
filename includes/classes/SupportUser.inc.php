<?php

final class SupportUser extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'support_users';

    public function GetLevel()
    {
        return $this->srid;
    }

    public static function Build($id)
    {
        try {
            $sUser = new SupportUser($id);

            return $sUser;
        } catch (SoftException $e) {
            return null;
        }

        return null;
    }

    public static function sAdd($uid, $srid)
    {
        DBi::$conn->query('INSERT INTO `' . self::$dataTable . '` (`id`, `srid`) VALUES (\'' . $uid . '\',\'' . $srid . '\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUPPORT_USER_CANT_ADDED);
        }

        return DBi::$conn -> insert_id;
    }

    public static function sExists($id)
    {
        try {
            $sUser = new SupportUser($id);
            throw new SuccessResult('User exists.');
        } catch (SoftException $e) {
            return false;
        } catch (SuccessResult $s) {
            return true;
        }

        return false;
    }

    public static function sDelete($uid)
    {
        DBi::$conn->query('DELETE FROM `' . self::$dataTable . '` WHERE `id` = \'' . $uid . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUPPORT_USER_CANT_DELETED);
        }

        return true;
    }

    public static function HasSUserAccess($uid, $level = 0)
    {
        try {
            $supportUser = new SupportUser($uid);
        } catch (SoftException $e) {
            return false;
        }
        if ($supportUser->srid >= $level) {
            return true;
        }

        return false;
    }

    public function HasUserAccess($uid)
    {
        try {
            $supportUser = new SupportUser($uid);
        } catch (SoftException $e) {
            return false;
        }
        if ($this->srid >= $supportUser->srid) {
            return true;
        }

        return false;
    }

    public function HasAccess($level = 0)
    {
        if ($this->srid >= $level) {
            return true;
        }

        return false;
    }

    public static function HideMod($id_user, User $self)
    {
        $user = UserFactory::getInstance()->getUser($id_user);
        if ($user->mods > 0 && $user->admin == 0 && !$self->IsSupportUser()) {
            $id = 1500;
        } else {
            $id = $id_user;
        }

        return $id;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'srid',
            'points',
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
