<?php

/**
 * discription: This class is used to manage ads placed by users.
 *
 * @author: Harish<harish282@gmail.com>
 * @name: UserAds
 * @package: includes
 * @subpackage: classes
 * @final: Final
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
class UserAds extends BaseObject
{
    /** Define constatns for status **/
    const AD_MONEY = 10000; //Money for an ad
    const AD_TIME = 900;    //Ad time in seconds i.e 15 min
    const AD_LENGTH = 75;    //Characters in an ad.

    const REPORTED = 10;    //number of times an ad can be reported by users to remove
    const MAX_ADS = 21;    //Maximun active ad at a time
    const MAX_USER_ADS = 3;    //Maximun active ad at a time for an user
    const AUTHORIZED = -1;    //Maximun active ad at a time for an user

    public static $idField = 'id'; //id field
    public static $dataTable = 'user_ads'; // table implemented

    /**
     * Constructor.
     */
    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }

    public static function GetWhere($where)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function sCountWhere($where)
    {
        $query = 'SELECT COUNT(`' . self::$idField . '`) as `totalCount` FROM `' . self::GetDataTable() . '` WHERE ' . $where;

        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->totalCount;
    }

    public static function GetAllForMod()
    {
        return self::GetWhere('reported < ' . self::REPORTED . ' AND finished > ' . time());
    }

    public static function GetAllReported()
    {
        return self::GetWhere('reported >= ' . self::REPORTED);
    }

    public static function GetAllForUser($user_id)
    {
        return self::GetWhere('user_id = ' . $user_id . ' AND finished > ' . time());
    }

    public static function GetNext()
    {
        $query = 'SELECT `' . implode('`,`', self::GetDataTableFields()) . '` FROM ' . self::GetDataTable() . ' WHERE reported < ' . self::REPORTED . ' AND finished > ' . time() . ' ORDER BY `updated` LIMIT 1';

        $res = DBi::$conn->query($query);

        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        $obj = mysqli_fetch_object($res);

        return $obj;
    }

    public static function touch($id)
    {
        $updates = [
            'updated' => time(),
        ];

        self::sUpdate(self::GetDataTable(), $updates, ['id' => $id]);

        return true;
    }

    public static function GetRandom()
    {
        $offset_result = DBi::$conn->query('SELECT FLOOR(RAND() * COUNT(*)) AS `offset` FROM `news` ');
        $offset_row = mysqli_fetch_object($offset_result);
        $offset = $offset_row->offset;
        $query = 'SELECT `' . implode('`,`', self::GetDataTableFields()) . '` FROM ' . self::GetDataTable() . ' WHERE reported < ' . self::REPORTED . ' AND finished > ' . time() . ' LIMIT $offset, 1';

        $res = DBi::$conn->query($query);

        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        $obj = mysqli_fetch_object($res);

        return $obj;
    }

    public static function Add(User $user, $content, $longAd = false, $freeAd = false)
    {
        if (self::AD_LENGTH < strlen($content) && !$longAd) {
            throw new FailedResult(sprintf(USERADS_AD_LENGTH, self::AD_LENGTH));
        }
        $totalAds = self::sCountWhere('finished > ' . time() . ' AND reported < ' . self::REPORTED);
        if (!$freeAd) {
            if (self::MAX_ADS <= $totalAds) {
                throw new FailedResult(USERADS_ALL_SLOTS_BOOKED);
            }
            $totalAdsByUser = self::sCountWhere('user_id = ' . $user->id . ' AND finished > ' . time());
            if (self::MAX_USER_ADS <= $totalAdsByUser) {
                throw new FailedResult(sprintf(USERADS_POST_MAX_ADS, self::MAX_USER_ADS));
            }
        }
        if (!$freeAd) {
            if (self::AD_MONEY > $user->bank) { //Check buyer has enough bank money to post
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if (!$user->RemoveFromAttribute('bank', self::AD_MONEY)) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
        }

        $time = time();
        if ($longAd) {
            $finishedTime = $time + 3500;
        } else {
            $finishedTime = $time + self::AD_TIME;
        }

        $data = [
            'user_id' => $user->id,
            'content' => Utility::SmartEscape($content),
            'created' => $time,
            'updated' => $time,
            'finished' => $finishedTime,
        ];
        self::AddRecords($data, self::GetDataTable());

        return true;
    }

    public static function Delete($id)
    {
        self::sDelete(self::GetDataTable(), ['id' => $id]);
    }

    public static function Authorize($id)
    {
        DBi::$conn->query('UPDATE ' . self::GetDataTable() . ' SET reported = \'' . self::AUTHORIZED . '\' WHERE id = \'' . $id . '\'');

        return true;
    }

    public static function Report(User $user, $id)
    {
        if (is_array($_SESSION['SES_AD_REPORTED'])) {
            $reportedAds = $_SESSION['SES_AD_REPORTED'];
        } else {
            $reportedAds = [];
        }

        if (isset($reportedAds[$id])) {
            throw new SoftException(USERADS_ALREADY_REPORTED);
        }
        $reported = MySQL::GetSingle('SELECT reported FROM ' . self::GetDataTable() . ' WHERE id = \'' . $id . '\'');

        if ($reported == self::AUTHORIZED) {
            throw new SoftException(USERADS_AUTHORIZED_REPORT);
        }
        $ponderation = 1;
        if ($user->IsModerator()
            || $user->IsSuperModerator()
            || $user->IsAdmin()) {
            $ponderation = 10;
        }
        DBi::$conn->query('UPDATE ' . self::GetDataTable() . ' SET reported = reported + ' . $ponderation . ' WHERE id = \'' . $id . '\'');

        $reportedAds[$id] = $id;

        $_SESSION['SES_AD_REPORTED'] = $reportedAds;

        return true;
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
            'user_id',
            'content',
            'created',
            'updated',
            'finished',
            'reported',
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

    /**
     * Function returns the class name.
     *
     * @return string
     */
    protected function GetClassName()
    {
        return __CLASS__;
    }
}
