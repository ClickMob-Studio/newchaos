<?php

class UserJobRoleTask extends CachedObject
{
    public static $dataTable = 'user_job_role_task';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);

        $this->jobRoleTask = null;
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new UserJobRoleTask($id);
    }

    /*
     * Create a UserJobRoleTask
     *
     * @param array $data
     */
    public static function create(array $data)
    {
        if (isset($data['user']) && isset($data['job_role_task'])) {
            $user = $data['user'];
            $jobRoleTask = $data['job_role_task'];
            $now = new \DateTime();
            $completionDateTime = new \DateTime();
            $completionDateTime->add(new \DateInterval('PT' . $jobRoleTask->length_in_minutes . 'M'));

            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('user_job_role_task')
                ->values(
                    [
                        'user_id' => ':user_id',
                        'job_role_task_id' => ':job_role_task_id',
                        'date' => ':date',
                        'completion_datetime' => ':completion_datetime',
                        'is_complete' => ':is_complete',
                    ]
                )
                ->setParameter('user_id', $user->id)
                ->setParameter('job_role_task_id', $jobRoleTask->id)
                ->setParameter('date', $now->format('Y-m-d'))
                ->setParameter('completion_datetime', $completionDateTime->format('Y-m-d H:i:s'))
                ->setParameter('is_complete', 0)
            ;
            $queryBuilder->execute();
        }
    }

    /*
    * Get the JobRoleTask for the UserJobRoleTask
    *
    * @return JobRoleTask
    */
    public function getJobRoleTask()
    {
        if ($this->jobRoleTask) {
            return $this->jobRoleTask;
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('job_role_task')
            ->where('id = :id')
            ->setParameter('id', $this->job_role_task_id)
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        $this->jobRoleTask = JobRoleTask::SGet($result['id']);

        return $this->jobRoleTask;
    }

    /*
     * Get the completion_datetime as a DateTime
     *
     * @return DateTime
     */
    public function getCompletionDatetime()
    {
        $completionDatetime = \DateTime::createFromFormat('Y-m-d H:i:s', $this->completion_datetime);

        return $completionDatetime;
    }

    /*
     * Get the time remaining for the UserJobRoleTask to be complete
     *
     * @return int
     */
    public function getTimeRemaining()
    {
        $now = new \DateTime();
        $endDateTime = $this->getCompletionDatetime();

        $difference = $endDateTime->diff($now);

        $timeParts = [];
        $hours = (int) $difference->format('%h');
        $minutes = (int) $difference->format('%i');
        if ($hours > 0) {
            $timeParts[] = $difference->format('%h') . ' hour' . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $timeParts[] = $difference->format('%i') . ' minute' . ($minutes > 1 ? 's' : '');
        }

        return join('<br>', $timeParts);
    }

    /*
     * Check if the UserJobRoleTask is now complete
     *
     * @return UserJobRoleTask
     */
    public function performCompletionCheck()
    {
        $now = new \DateTime();
        $endDateTime = $this->getCompletionDatetime();

        if ($now > $endDateTime) {
            $user = UserFactory::getInstance()->getUser($this->user_id);
            $jobRoleTask = $this->getJobRoleTask();
            $userJobProgress = UserJobProgress::getUserJobProgressForUser($user->id);

            $notifyContent = 'You have completed job task ' . $jobRoleTask->name . ' and gained ';
            if ($jobRoleTask->job_exp_reward) {
                $notifyContent .= $jobRoleTask->job_exp_reward . ' job EXP';

                $newJobExp = $userJobProgress->job_exp + $jobRoleTask->job_exp_reward;
                $userJobProgress->job_exp = $newJobExp;

                $queryBuilder = BaseObject::createQueryBuilder();
                $queryBuilder
                    ->update('user_job_progress')
                    ->set('job_exp', ':job_exp')
                    ->where('id = :id')
                    ->setParameter('job_exp', $newJobExp)
                    ->setParameter('id', $userJobProgress->id)
                    ->execute()
                ;
            }
            if ($jobRoleTask->respect_reward) {
                $notifyContent .= ', ' . $jobRoleTask->respect_reward . ' reputation';

                /** @var SUser $user2 */
                $user2 = SUserFactory::getInstance()->getUser($user->id);
                // Increase the users "respect" which is called ConcertLevel, don't ask
                $user2->AddToAttribute('ConcertLevel', $jobRoleTask->respect_reward);
            }
            if ($jobRoleTask->money_reward) {
                $notifyContent .= ' and ' . $jobRoleTask->getMoneyReward() . ' has been added to your daily payout!';
            }
            $user->Notify($notifyContent);

            DailyTasks::recordUserTaskAction(DailyTasks::COMPLETE_JOB_TASKS, $user, 1);

            $this->is_complete = 1;

            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('user_job_role_task')
                ->set('is_complete', ':is_complete')
                ->where('id = :id')
                ->setParameter('is_complete', 1)
                ->setParameter('id', $this->id)
                ->execute()
            ;
        }

        return $this;
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
            'job_role_task_id',
            'date',
            'completion_datetime',
            'is_complete',
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
