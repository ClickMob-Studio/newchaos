<?php

class UserDailyTasks extends BaseObject
{
    public static $dataTable = 'user_daily_tasks';
    public static $idField = 'id';

    /**
     * Record a user daily task action.
     *
     * @throws Exception
     */
    public static function recordAction(int $taskType, User $user, int $amount)
    {
        global $conn;

        $today = new \DateTime();
        $todayDate = $today->format('Y-m-d');
        $stmt = $conn->prepare('SELECT * FROM user_daily_tasks WHERE user_id = ? AND task_date = ? AND task_type = ?');
        $stmt->bind_param('isi', $user->id, $todayDate, $taskType);
        $stmt->execute();
        $result = $stmt->get_result();

        $taskComplete = 0;
        $required = DailyTasks::getAmountRequired($taskType, $user);

        if ($result->num_rows > 0) {
            $result = $result->fetch_assoc();

            if ($result['task_complete'] === 1) {
                return;
            }

            if ((int) $result['task_total'] + $amount >= $required) {
                $taskComplete = 1;
                if ($user->securityLevel == 5) {

                        $black = DBi::$conn->query('SELECT * FROM prestige6 WHERE userid = ' . $user->id);

                    if ($black && mysqli_num_rows($black)) {
                        DBi::$conn->query('UPDATE prestige6 SET daily = daily + 1 WHERE userid = ' . $user->id . ' AND daily < 21');
                    } else {
                        DBi::$conn->query('INSERT INTO prestige6 (userid, daily) VALUES(' . $user->id . ', 1)');
                    }
                }
                if ($user->securityLevel == 6) {

                    $black = DBi::$conn->query('SELECT * FROM prestige7 WHERE userid = ' . $user->id);

                    if ($black && mysqli_num_rows($black)) {
                        DBi::$conn->query('UPDATE prestige7 SET daily = daily + 1 WHERE userid = ' . $user->id . ' AND daily < 21');
                    } else {
                        DBi::$conn->query('INSERT INTO prestige7 (userid, daily) VALUES(' . $user->id . ', 1)');
                    }
                }

                $user->performUserQuestAction('daily_tasks', 1);
            }
            UserDailyTasks::sUpdate(
                static::GetDataTable(),
                [
                    'task_total' => (int) $result['task_total'] + $amount,
                    'task_complete' => $taskComplete,
                ],
                [
                    'user_id' => $user->id,
                    'task_date' => $todayDate,
                    'task_type' => $taskType,
                ]
            );
        } else {
            if ($amount >= $required) {
                $taskComplete = 1;
                $user->performUserQuestAction('daily_tasks', 1);
            }
            UserDailyTasks::AddRecords([
                'user_id' => $user->id,
                'task_date' => $todayDate,
                'task_total' => (int) $amount,
                'task_type' => $taskType,
                'task_complete' => $taskComplete,
            ], static::GetDataTable());
        }

        if ($taskComplete) {
            self::taskComplete($taskType, $user);
        }

        // Clear the cache for the view
        View::clearCacheForUser('sidebar/daily_tasks');
    }

    /**
     * When a task is completed reward the user.
     *
     * @throws FailedResult
     */
    public static function taskComplete(int $taskType, User $user)
    {
        $reward = DailyTasks::getTaskReward($user);
        $user->AddToAttribute('exp', $reward['exp']);
        $user->AddToAttribute('points', $reward['points']);
        User::SNotify(
            $user->id,
            'You have completed the daily task: ' . DailyTasks::getMessage($taskType, $user) . '. ' .
            'You have been rewarded with ' . number_format($reward['points'], 0) . 'points and ' . $reward['exp'] . ' EXP!'
        );

        if (self::completedAllToday($user)) {
            if ($user->securityLevel == 5) {

                $black = DBi::$conn->query('SELECT * FROM prestige6 WHERE userid = ' . $user->id);

                if ($black && mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE prestige6 SET daily = daily + 1 WHERE userid = ' . $user->id . ' AND daily < 21');
                } else {
                    DBi::$conn->query('INSERT INTO prestige6 (userid, daily) VALUES(' . $user->id . ', 1)');
                }
            }
            if ($user->securityLevel == 6) {

                $black = DBi::$conn->query('SELECT * FROM prestige7 WHERE userid = ' . $user->id);

                if ($black && mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE prestige7 SET daily = daily + 1 WHERE userid = ' . $user->id . ' AND daily < 21');
                } else {
                    DBi::$conn->query('INSERT INTO prestige7 (userid, daily) VALUES(' . $user->id . ', 1)');
                }
            }
            // Sleep for 10ms to ensure second event comes after
            sleep(1);
//User::AddAwakeItems($user->id, 36, 1);
            
            //$user->AddItems(Item::getAwakePillId(), 1);
            $newtime = time() + 2678400;
            $check = DBi::$conn->query("SELECT * FROM temp_items_use WHERE userid = $user->id");
            if(mysqli_num_rows($check) > 0) {
                $check = $check->fetch_assoc();
                if(time() < $check['time']) {
                    $newtime = $check['time'] + 3600;
                }else {
                    $newtime = time() + 3600;
                }
                DBi::$conn->query("UPDATE temp_items_use SET dailyxpboost = $newtime WHERE userid = $user->id");
            } else {
                DBi::$conn->query("INSERT INTO temp_items_use (userid, dailyxpboost) VALUES ($user->id, $newtime)");
            }
            //$user->AddPoints(20);
            User::SNotify(
                $user->id,
                'Congratulations you\'ve completed all tasks for today! You\'ve been rewarded with 10% Exp boost for 1 hour'
            );
        }
    }

    /**
     * Determine if a user has completed all tasks today.
     *
     * @throws Exception
     *
     * @return bool
     */
    public static function completedAllToday(User $user)
    {
        global $conn;

        $today = new \DateTime();
        $tasks = join(', ', DailyTasks::getTodaysDailyTasks());
        $todayDate = $today->format('Y-m-d');
        $stmt = $conn->prepare('SELECT * FROM user_daily_tasks WHERE user_id = ? AND task_date = ? AND task_type IN (' . $tasks . ') AND task_complete = 1');
        $stmt->bind_param('is', $user->id, $todayDate);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows === 3;
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
            'task_type',
            'task_date',
            'task_total',
            'task_complete',
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
