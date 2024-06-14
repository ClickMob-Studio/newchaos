<?php

final class SupportThread extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'support_threads';

    public static function sOpen($authorId, $title, $text, $level, $lang = DEFAULT_LANGUAGE)
    {
        $tid = self::sAdd($authorId, $title, $level, $lang);
        SupportEntry::sAdd($tid, $authorId, $text, SupportEntry::EPUBLIC, 'eng');

        return $tid;
    }

    public function IsClosed()
    {
        if ($this->closed == 1) {
            return true;
        }

        return false;
    }

    public function IsAccessibleTo(User $user)
    {
        if ($user->id != $this->author
            && $this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            return false;
        }

        return true;
    }

    public function IsAccessibleToSupportUser($suser)
    {
        if ($suser === null) {
            return false;
        } elseif ($suser->GetLevel() < $this->level) {
            return false;
        }

        return true;
    }

    public function Reopen(User $user)
    {
        if ($this->closed == 0) {
            throw new SoftException(SUGGESTION_CANT_REOPEN_OPENED, SUPPORT);
        } elseif ($user->id != $this->author
            && SupportUser::HasSUserAccess($user->id, $this->level) === false) {
            throw new SoftException(SUGGESTION_CANT_REOPEN_NOT_CREATED, SUPPORT);
        }
        $this->SetAttribute('closed', 0);
        $this->SetNullAttribute('closedby');

        return true;
    }

    public function Close(User $user)
    {
        if ($this->closed == 1) {
            throw new SoftException(SUGGESTION_CANT_CLOSE_CLOSED, SUPPORT);
        } elseif ($this->IsAccessibleTo($user) === false) {
            throw new SoftException(SUGGESTION_CANT_CLOSE_NOT_CREATED, SUPPORT);
        }
        $this->SetAttribute('closed', 1);
        $this->SetAttribute('closedby', $user->id);
        $this->SetAttribute('closedate', time());
        if ($this->author != $user->id) {
            $author = UserFactory::getInstance()->getUser($this->author);
            $author->Notify(sprintf(SUPPORT_YOUR_CLOSED, $this->id), SUPPORT);
        }

        return true;
    }

    public function RaisePriority(User $user)
    {
        if ($this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            throw new SoftException(SUGGESTION_NOT_HAVE_ACCESS, SUPPORT);
        } elseif ($this->priority >= 10) {
            throw new SoftException(SUPPORT_CANNOT_RAISE, SUPPORT);
        }

        return $this->AddToAttribute('priority', 1);
    }

    public function LowerPriority(User $user)
    {
        if ($this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            throw new SoftException(SUGGESTION_NOT_HAVE_ACCESS, SUPPORT);
        } elseif ($this->priority <= 1) {
            throw new SoftException(SUPPORT_CANNOT_LOWER, SUPPORT);
        }

        return $this->RemoveFromAttribute('priority', 1);
    }

    public function Promote(User $user)
    {
        if ($this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            throw new SoftException(SUGGESTION_NOT_HAVE_ACCESS, SUPPORT);
        } elseif ($this->level >= 3) {
            throw new SoftException(SUGGESTION_CANNOT_PROMOTE, SUPPORT);
        }

        return $this->AddToAttribute('level', 1);
    }

    public function Demote(User $user)
    {
        if ($this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            throw new SoftException(SUGGESTION_NOT_HAVE_ACCESS, SUPPORT);
        } elseif ($this->level <= 1) {
            throw new SoftException(SUGGESTION_CANNOT_DEMOTE, SUPPORT);
        }

        return $this->RemoveFromAttribute('level', 1);
    }

    public static function CountAllOpenedForLevel($level, $uid = null)
    {
        if ($uid !== null) {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=0');
        } else {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `closed`=0');
        }
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public function GetMostRecentEntry(User $user)
    {
        $authorId = $user->id;
        if ($user->IsSupportUser()) {
            $authorId = 0;
        }

        return SupportEntry::GetMostRecentForThread($this->id, $authorId);
    }

    public static function GetAllOpenedForLevel($level, $uid = null, $onlyForLangs = false)
    {
        parent::$usePaging = true;
        $i = 0;
        $objs = [];
        if ($uid !== null) {
            $where = '`level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=0 ';
        } else {
            $where = '`level` <= \'' . $level . '\' AND `closed`=0';
        }
        //if ($onlyForLangs) {
        //	$where .= ' AND `lang` IN (\''.join('\',\'', $onlyForLangs).'\')';
        //}
        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'level', 'DESC, `priority` DESC, `updatedate` DESC ');

        if (count($res) == 0) {
            return null;
        }

        return $res;
    }

    public static function CountAllOpenedForAuthor($authorId)
    {
        $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `author` = \'' . $authorId . '\' AND `closed`=0');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function GetAllOpenedForAuthor($authorId)
    {
        parent::$usePaging = true;
        $i = 0;
        $objs = [];

        $where = '`author` = \'' . $authorId . '\' AND `closed`=0';
        self::$generatePagingQryString = false;
        $allres = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'level', ' DESC, `updatedate` DESC ');

        if (count($allres) == 0) {
            return null;
        }

        foreach ($allres as $obj) {
            $lastentry = SupportEntry::GetMostRecentForThread($obj->id, $authorId);
            $obj->updateauthor = $lastentry->author;
            $obj->updatedate = $lastentry->creationdate;
            $objs[$i++] = $obj;
        }

        return $objs;
    }

    public static function CountAllClosedForLevel($level, $uid = null)
    {
        if ($uid !== null) {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=1');
        } else {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `closed`=1');
        }
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function GetAllClosedForLevel($level, $uid = null, $onlyForLangs = false)
    {
        $i = 0;
        parent::$usePaging = true;
        $objs = [];
        if ($uid !== null) {
            $where = '`level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=1';
        } else {
            $where = '`level` <= \'' . $level . '\' AND `closed`=1';
        }
        //if ($onlyForLangs) {
        //	$where .= ' AND `lang` IN (\''.join('\',\'', $onlyForLangs).'\')';
        //}
        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'closedate', 'DESC');

        if (count($res) == 0) {
            return null;
        }

        return $res;
    }

    public static function CountAllClosedForAuthor($authorId)
    {
        $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `author` = \'' . $authorId . '\' AND `closed`=1');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function GetAllClosedForAuthor($authorId)
    {
        parent::$usePaging = true;
        $i = 0;
        $objs = [];

        $where = '`author` = \'' . $authorId . '\' AND `closed`=1';

        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'closedate', 'DESC');

        if (count($res) == 0) {
            return null;
        }

        return $res;
    }

    public function Delete(User $user)
    {
        if ($this->IsAccessibleTo($user) === false) {
            throw new SoftException(SUPPORT_CANNOT_DELETE_NOT_OPENED, SUPPORT);
        }
        DBi::$conn->query('DELETE FROM `' . self::$dataTable . '` WHERE `id`=\'' . $this->id . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_THREAD_CANT_DELETED, SUPPORT);
        }
        if ($this->author != $user->id) {
            $author = UserFactory::getInstance()->getUser($this->author);
            $author->Notify(sprintf(SUPPORT_DELETED_YOURS, $this->id), SUPPORT);
        }

        return true;
    }

    public function Reply(User $user, $text, $status = SupportEntry::EPUBLIC, $lang = DEFAULT_LANGUAGE)
    {
        if ($this->closed == true && $user->IsSupportUser() === false) {
            throw new SoftException(SUGGESTION_CANT_REPLY_CLOSED, SUPPORT);
        } elseif ($this->IsAccessibleTo($user) === false) {
            throw new SoftException(SUPPORT_CANT_REPLY_NOT_START, SUPPORT);
        }
        $this->SetAttribute('updatedate', time());
        $this->SetAttribute('updateauthor', $user->id);
        SupportEntry::sAdd($this->id, $user->id, $text, $status, 'eng');
        if ($user->GetSupportStatus() !== null) {
            // We add a point to this support user
            $user->GetSupportStatus()->AddToAttribute('points', 1);
        }
        if ($this->author != $user->id && $status == SupportEntry::EPUBLIC) {
            $author = UserFactory::getInstance()->getUser($this->author);
            $author->Notify(sprintf(SUPPORT_NEW_REPLY_IN, $this->id), SUPPORT);
        }

        return true;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'author',
            'title',
            'level',
            'closed',
            'closedby',
            'closedate',
            'priority',
            'creationdate',
            'updateauthor',
            'updatedate',
            'lang',
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

    private static function sAdd($authorId, $title, $level, $lang = DEFAULT_LANGUAGE)
    {
        DBi::$conn->query('INSERT INTO `' . self::$dataTable . '` (`author`, `title`, `level`, `creationdate`, `updateauthor`, `updatedate`, `lang`) VALUES (\'' . $authorId . '\',\'' . $title . '\',\'' . $level . '\', \'' . time() . '\', \'' . $authorId . '\',  \'' . time() . '\', \'eng\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_THREAD_CANT_ADDED, SUPPORT);
        }

        return DBi::$conn -> insert_id;
    }
}
