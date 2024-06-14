<?php

class UserBooks extends BaseObject
{
    const UNREAD = 0;
    const READING = 1;
    const STUDIED = 2;

    public static $idField = '';
    public static $dataTable = 'books_user';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->name = constant($this->name);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function Get($userId, $bookId)
    {
        $books = self::GetAll('user_id=' . $userId . ' AND book_id=' . $bookId);

        return $books[0];
    }

    /**
     * This function logs the books for the admin to be able to see who sent what to whom
     * @param $userId int
     * @param $bookId int
     * @param $toId int
     */
    public static function LogSend($userId,$bookId,$toId){
        $time = time();
        DBi::$conn->query("INSERT INTO books_sendlog (user_id,book_id,to_id,`when`) VALUES ($userId,$bookId,$toId, $time)");

    }
    public static function GetAllUnread($userId)
    {
        return self::GetAll('user_id=' . $userId . ' AND status=' . self::UNREAD);
    }

    public static function GetAllReading($userId)
    {
        return self::GetAll('user_id=' . $userId . ' AND status=' . self::READING);
    }

    public static function GetAllStudied($userId)
    {
        return self::GetAll('user_id=' . $userId . ' AND status=' . self::STUDIED);
    }

    public static function GetAllInventory($userId)
    {
        return self::GetAll('user_id=' . $userId . ' AND status IN (' . self::UNREAD . ',' . self::READING . ')');
    }

    public static function GetAllForUser($userId)
    {
        return self::GetAll('user_id=' . $userId);
    }

    public static function IsReadingAnyBook($userId)
    {
        $books = self::GetAllReading($userId);

        return count($books) > 0;
    }

    public static function IsDependent($userId, $bookId)
    {
        $book = new Book($bookId);
        if (empty($book->predecessors)) {
            return false;
        }

        $pBooksCount = MySQL::GetSingle('SELECT COUNT(*) FROM ' . self::GetDataTable() . ' WHERE book_id IN (' . $book->predecessors . ') AND user_id = ' . $userId . ' AND status = ' . self::STUDIED);

        $predecessors = explode(',', $book->predecessors);
        if (count($predecessors) != $pBooksCount) {
            return true;
        }

        return false;
    }
    public static function CancelBook($userId, $bookId)
    {
        if (!self::UserHasBook($userId, $bookId)) {
            throw new FailedResult(BOOK_USER_NOT_HAVING);
        }
        if (!self::IsReadingAnyBook($userId)) {
            throw new FailedResult('You are not reading any book.');
        }


        $book = new Book($bookId);

        $data = [
            'status' => self::UNREAD,
            'readstart' => 0,
            'readfinished' => 0,
        ];
        self::sUpdate(self::GetDataTable(), $data, ['user_id' => $userId, 'book_id' => $bookId]);
        throw new SuccessResult(sprintf(BOOK_CANCEL_READING, $book->name));
    }


    public static function StartReading($userId, $bookId)
    {
        if (!self::UserHasBook($userId, $bookId)) {
            throw new FailedResult(BOOK_USER_NOT_HAVING);
        }
        if (self::IsReadingAnyBook($userId)) {
            throw new FailedResult(BOOK_ALREADY_READING);
        }
        if (self::IsDependent($userId, $bookId)) {
            throw new FailedResult(BOOK_DEPENDENT);
        }
        $book = new Book($bookId);

        $data = [
                        'status' => self::READING,
                        'readstart' => time(),
                        'readfinished' => time() + $book->duration * DAY_SEC,
                    ];
        self::sUpdate(self::GetDataTable(), $data, ['user_id' => $userId, 'book_id' => $bookId]);
        throw new SuccessResult(sprintf(BOOK_START_READING, $book->name, $book->duration));
    }

    public static function FinishReading($userId, $bookId)
    {
        if (!self::IsReadingAnyBook($userId)) {
            throw new FailedResult(BOOK_NOT_READING);
        }
        $data = [
                        'status' => self::STUDIED,
                    ];
        $book = new Book($bookId);

        $bookLink = HTML::ShowBookPopup($book->name, $book->id);

        User::SNotify($userId, sprintf(BOOK_READING_FINISHED, addslashes($bookLink)), BOOKS);

        return self::sUpdate(self::GetDataTable(), $data, ['user_id' => $userId, 'book_id' => $bookId]);
    }

    public static function FinishAllReading()
    {
        $books = self::GetAll('status = ' . self::READING . ' AND readfinished <= ' . time());
        foreach ($books as $book) {
            self::FinishReading($book->user_id, $book->book_id);
        }
    }

    public static function UserHasBook($userId, $bookId)
    {
        $book = self::Get($userId, $bookId);

        return !empty($book);
    }

    public static function UserHasStudied($userId, $bookId)
    {
        $book = self::Get($userId, $bookId);

        return isset($book->status) && $book->status == self::STUDIED;
    }

    public static function UserHasStudiedAny($userId, array $bookIds = [])
    {
        $books = self::GetAllStudied($userId);
        foreach ($books as $book) {
            if (in_array($book->book_id, $bookIds)) {
                return true;
            }
        }

        return false;
    }

    public static function Add($userId, $bookId)
    {
        if (self::UserHasBook($userId, $bookId)) {
            throw new FailedResult(BOOK_ALREADY_EXISTS);
        }
        $data = [
                        'book_id' => $bookId,
                        'user_id' => $userId,
                        'status' => self::UNREAD,
                    ];

        return parent::AddRecords($data, self::GetDataTable());
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'book_id',
            'user_id',
            'status',
            'readstart',
            'readfinished',

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
