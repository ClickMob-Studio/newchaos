<?php

class JobRoleTask extends CachedObject
{
    public static $dataTable = 'job_role_task';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new JobRoleTask($id);
    }

    /*
     * Get the money_reward formatted
     *
     * @return string
     */
    public function getMoneyReward()
    {
        return '$' . number_format($this->money_reward);
    }

    /**
     * Return the length of the task.
     *
     * @return string
     */
    public function getFormattedLength()
    {
        $length = mktime(0, $this->length_in_minutes);
        $timeParts = [];
        if ($this->length_in_minutes >= 60) {
            $hours = date('g', $length);
            $timeParts[] = date('g', $length) . ' hour' . ($hours > 1 ? 's' : '');
        }
        $minutes = date('i', $length);
        if ($minutes > 0) {
            $timeParts[] = date('i', $length) . ' minute' . ($minutes > 1 ? 's' : '');
        }

        return join('<br>', $timeParts);
    }

    /*
     * Start the JobRoleTask for a User
     *
     * @param integer $userId
     * @param integer $id
     * @param UserJobProgress $userJobProgress
     *
     * @return array
     */
    public function start(int $userId, int $id, UserJobProgress $userJobProgress)
    {
        $response = [];
        $response['success'] = true;

        $user = UserFactory::getInstance()->getUser($userId);
        $jobRoleTask = JobRoleTask::SGet($id);

        if (!is_object($jobRoleTask) || !is_object($user)) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an Admin!';

            return $response;
        }

        if ($jobRoleTask->job_role_id !== $userJobProgress->job_role_id) {
            $response['success'] = false;
            $response['error'] = "The task you are trying to perform isn't for the job you are currently hired in! -" . $jobRoleTask->job_role_id . ' -' . $userJobProgress->job_role_id;

            return $response;
        }

        if ($user->getActiveUserJobRoleTask()) {
            $response['success'] = false;
            $response['error'] = "You can't perform multiple job tasks at once!";

            return $response;
        }

        $data = [];
        $data['user'] = $user;
        $data['job_role_task'] = $jobRoleTask;

        UserJobRoleTask::create($data);

        $response['success'] = true;
        $response['message'] = 'You have successfully started task: ' . $jobRoleTask->name;

        return $response;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'job_role_id',
            'name',
            'description',
            'length_in_minutes',
            'job_exp_reward',
            'money_reward',
            'respect_reward',
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
