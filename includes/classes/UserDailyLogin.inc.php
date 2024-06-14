<?php

class UserDailyLogin extends BaseObject
{
    public static $dataTable = 'user_daily_login';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    /*
     * Create a UserDailyLogin
     *
     * @param array $data
     *
     * return array
     */
    public static function create(array $data)
    {
        $response = [];
        $response['success'] = true;

        if (!isset($data['user_id'])) {
            $response['success'] = false;
        }

        if (!isset($data['login_date'])) {
            $response['success'] = false;
        }

        if (!isset($data['points_rewarded'])) {
            $response['success'] = false;
        }

        if ($response['success'] === true) {
            DBi::$conn->query('INSERT INTO `user_daily_login` SET `user_id`="' . $data['user_id'] . '", `login_date`="' . $data['login_date'] . '", `points_rewarded`="' . $data['points_rewarded'] . '"');
        }

        return $response;
    }

    /**
     * Retrieve the reward for today.
     *
     * @return int
     */
    public static function GetTodayReward(User $user)
    {
        $yesterday = new \DateTime();
        $yesterday->sub(new \DateInterval('P1D'));

        $yesterdayLog = UserDailyLogin::GetAll('user_id = "' . $user->id . '" AND login_date = "' . $yesterday->format('Y-m-d') . '"');
        if (count($yesterdayLog) > 0 && isset($yesterdayLog[0])) {
            if ($yesterdayLog[0]->points_rewarded >= 200) {
                $rewardPoints = 250;
            } else {
                $rewardPoints = $yesterdayLog[0]->points_rewarded + 50;
            }
        } else {
            $rewardPoints = 50;
        }

        return $rewardPoints;
    }

    /**
     * Retrieve all the previous rewards the user has achieved.
     *
     * @return array
     */
    public static function GetPreviousRewards(User $user)
    {
        $now = new \DateTime();
        $fiveDays = new \DateTime();
        $fiveDays->sub(new \DateInterval('P5D'));

        $pastFive = UserDailyLogin::GetAll('user_id = "' . $user->id . '" AND login_date > "' . $fiveDays->format('Y-m-d') . '"');

        return $pastFive;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'user_id',
            'login_date',
            'points_rewarded',
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
