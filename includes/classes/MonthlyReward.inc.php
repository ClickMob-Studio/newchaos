<?php

final class MonthlyReward extends BaseObject
{
    public static $idField = 'userid';

    public static $dataTable = 'user_rewards';

    private $houseObj = null;

    /**
     * Constructor.
     *

     * @param mixed $id
     *
     * @return GangCell
     */
    public function __construct($id = 0)
    {
        if (!empty($id)) {
            parent::__construct($id);
        }
    }

    /**
     * Get All record.
     *

     * @param mixed $where
     *
     * @return mixed
     */
    public static function GetAll()
    {
        $sql = "SELECT lw.month, lw.year, lw.userid as levelwinner, lw.levelchanged as level, 

				   sp.userid as speedwinner, sp.speedchanged as speed,

				   st.userid as strengthwinner, st.strengthchanged as strength,

				   de.userid as defensewinner, de.defensechanged as defense,

				   rf.userid as referralwinner, rf.referralschanged as referrals

			FROM user_rewards lw 

			LEFT JOIN user_rewards rf ON lw.month = rf.month and lw.year = rf.year AND rf.winner = 1 AND rf.winnerfor LIKE '%,referrals%'

			 ,  user_rewards sp, user_rewards st, user_rewards de 

			WHERE lw.month = sp.month and lw.year = sp.year

			 AND lw.month = st.month and lw.year = st.year

			 AND lw.month = de.month and lw.year = de.year   	 

				 AND lw.winner = 1 AND lw.winnerfor LIKE '%,level%'

				 AND sp.winner = 1 AND sp.winnerfor LIKE '%,speed%'

				 AND st.winner = 1 AND st.winnerfor LIKE '%,strength%'

				 AND de.winner = 1 AND de.winnerfor LIKE '%,defense%'	 		 		 		 		 

			 ORDER BY lw.year DESC, lw.month DESC	 		 		 		 		 

			 ";

        self::$usePaging = true;

        Paginator::$recordsOnPage = 5;

        $records = self::GetPaginationResults($sql);

        self::$usePaging = false;

        return $records;
    }

    public static function updateTempMontlyRewards()
    {
        $month = date('n');

        $year = date('Y');

        $last_update = mktime(0, 0, 0, $month - 1, 1, $year);

        $lastmonth = $month;

        $lastyear = date('Y', mktime(0, 0, 0, $lastmonth, 1, $year));

        DBi::$conn->query('delete from `temp_user_rewards`');

        $query = 'INSERT INTO `temp_user_rewards` (`userid`, `month`, `year`, `level`, `speed`, `strength`, `defense`, `levelchanged`, `speedchanged`, `strengthchanged`, `defensechanged`)

                SELECT id, ' . $month . ' as month, ' . $year . ' as year, (200 * u.securityLevel + u.level) as userlvl, u.speed, u.strength, u.defense, ((200 * u.securityLevel + u.level) - IF(isnull(lmr.level),0,lmr.level)) as levelchanged, (u.speed - IF(isnull(lmr.speed),0,lmr.speed)) as speedchanged, (u.strength - IF(isnull(lmr.strength),0,lmr.strength)) as strengthchanged, (u.defense - IF(isnull(lmr.defense),0,lmr.defense)) as defensechanged

                FROM grpgusers u LEFT JOIN `user_rewards` lmr ON u.id = lmr.userid AND lmr.month = ' . $lastmonth . ' AND lmr.year = ' . $lastyear;

        DBi::$conn->query($query);

        $query = "SELECT `referrer`, count(id) as total FROM `referrals` where `credited`=1 and referrer not in ('10531','2329','2000','2001','2002','2003','2004','2005','2217') group by referrer order by total desc";

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_assoc($result)) {
            $lmreferrals = (int) MySQL::GetSingle('SELECT referrals FROM `user_rewards` lmr WHERE userid = \'' . $row['referrer'] . '\' AND lmr.month = ' . $lastmonth . ' AND lmr.year = ' . $lastyear . '');

            $query = 'UPDATE `temp_user_rewards` SET referrals = \'' . $row['total'] . '\', referralschanged = \'' . ($row['total'] - $lmreferrals) . '\' WHERE userid = \'' . $row['referrer'] . '\' AND month = \'' . $month . '\' AND year = \'' . $year . '\'';

            DBi::$conn->query($query);
        }
    }

    public static function GetActualMonth()
    {
        $records = [];

        $limit = 15;

        $query = 'SELECT userid, level, levelchanged, winner, winnerfor FROM `temp_user_rewards` ORDER BY levelchanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['level'][] = $row->level;

            $records['levelchanged'][] = $row->levelchanged;

            $records['leveluser'][] = $row->userid;
        }

        $query = 'SELECT userid, speed, speedchanged, winner, winnerfor FROM `temp_user_rewards`  ORDER BY speedchanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['speed'][] = $row->speed;

            $records['speedchanged'][] = $row->speedchanged;

            $records['speeduser'][] = $row->userid;
        }

        $query = 'SELECT userid, strength, strengthchanged, winner, winnerfor FROM `temp_user_rewards` ORDER BY strengthchanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['strength'][] = $row->strength;

            $records['strengthchanged'][] = $row->strengthchanged;

            $records['strengthuser'][] = $row->userid;
        }

        $query = 'SELECT userid, defense, defensechanged, winner, winnerfor FROM `temp_user_rewards` ORDER BY defensechanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['defense'][] = $row->defense;

            $records['defensechanged'][] = $row->defensechanged;

            $records['defenseuser'][] = $row->userid;
        }

        $query = 'SELECT userid, referrals, referralschanged, winner, winnerfor FROM `temp_user_rewards` ORDER BY referralschanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['referrals'][] = $row->referrals;

            $records['referralschanged'][] = $row->referralschanged;

            $records['referralsuser'][] = $row->userid;
        }

        return $records;
    }
    public static function UpdateLevel($userid){
        DBi::$conn->query("UPDATE user_rewards SET levelchanged = levelchanged + 1 WHERE userid = ".$userid);
    }
    public static function UpdateTrain($att, $gain, $userid){
        $attr = $att.'changed';
        DBi::$conn->query("UPDATE user_rewards SET ".$attr." = '".$attr."' + '".$gain."' WHERE userid = ".$userid);
    }
    public static function GetForMonth($month, $year, $limit = 5)
    {
        $records = [];

        $query = 'SELECT userid, level, levelchanged, winner, winnerfor FROM `user_rewards` WHERE month = \'' . $month . '\' AND year = \'' . $year . '\' ORDER BY levelchanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['level'][] = $row->level;

            $records['levelchanged'][] = $row->levelchanged;

            $records['leveluser'][] = $row->userid;
        }

        $query = 'SELECT userid, speed, speedchanged, winner, winnerfor FROM `user_rewards` WHERE month = \'' . $month . '\' AND year = \'' . $year . '\' ORDER BY speedchanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['speed'][] = $row->speed;

            $records['speedchanged'][] = $row->speedchanged;

            $records['speeduser'][] = $row->userid;
        }

        $query = 'SELECT userid, strength, strengthchanged, winner, winnerfor FROM `user_rewards` WHERE month = \'' . $month . '\' AND year = \'' . $year . '\' ORDER BY strengthchanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['strength'][] = $row->strength;

            $records['strengthchanged'][] = $row->strengthchanged;

            $records['strengthuser'][] = $row->userid;
        }

        $query = 'SELECT userid, defense, defensechanged, winner, winnerfor FROM `user_rewards` WHERE month = \'' . $month . '\' AND year = \'' . $year . '\' ORDER BY defensechanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['defense'][] = $row->defense;

            $records['defensechanged'][] = $row->defensechanged;

            $records['defenseuser'][] = $row->userid;
        }

        $query = 'SELECT userid, referrals, referralschanged, winner, winnerfor FROM `user_rewards` WHERE month = \'' . $month . '\' AND year = \'' . $year . '\' ORDER BY referralschanged DESC LIMIT ' . $limit;

        $result = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($result)) {
            $records['referrals'][] = $row->referrals;

            $records['referralschanged'][] = $row->referralschanged;

            $records['referralsuser'][] = $row->userid;
        }

        return $records;
    }

    /**
     * Get the table name.
     */
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    /**
     * Get table fields.
     */
    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'month',

            'year',

            'level',

            'speed',

            'strength',

            'defense',

            'referrals',

            'levelchanged',

            'speedchanged',

            'strengthchanged',

            'defensechanged',

            'referralschanged',

            'winner',

            'winnerfor',
        ];
    }

    /**
     * Get id field.
     */
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    /**
     * return Class name.
     */
    protected function GetClassName()
    {
        return __CLASS__;
    }
}



