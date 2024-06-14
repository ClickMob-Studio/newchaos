<?php

class UserJobProgress extends CachedObject
{
    public static $dataTable = 'user_job_progress';
    public static $idField = 'id';

    private $jobRole = null;

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
        return new UserJobProgress($id);
    }

    /*
     * Create a UserJobProgress
     *
     * @param array $data
     *
     * @return UserJobProgress
     */
    public static function create(array $data)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('job_role')
            ->where('required_exp = :required_exp')
            ->setParameter('required_exp', 0)
            ->setMaxResults(1);
        $result = $queryBuilder->execute()->fetch();

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert('user_job_progress')
            ->values(
                [
                    'user_id' => ':user_id',
                    'job_role_id' => ':job_role_id',
                    'job_exp' => ':job_exp',
                ]
            )
            ->setParameter('user_id', $data['user_id'])
            ->setParameter('job_role_id', $result['id'])
            ->setParameter('job_exp', 0);

        return $queryBuilder->execute();
    }

    /*
     * Get the UserJobProgress for a User
     *
     * @param integer $userId
     *
     * @return mixed
     */
    public static function getUserJobProgressForUser(int $userId)
    {
        $userJobProgress = null;

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_job_progress')
            ->where('user_id = :user_id')
            ->andWhere('job_role_id IS NOT NULL')
            ->setParameter('user_id', $userId)
            ->setMaxResults(1);
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id']) && $result['id']) {
            $userJobProgress = UserJobProgress::SGet($result['id']);
        }

        return $userJobProgress;
    }

    /*
     * Get the JobRole for the UserJobProgress
     *
     * @return JobRole
     */
    public function getJobRole()
    {
        if ($this->jobRole) {
            return $this->jobRole;
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('job_role')
            ->where('id = :id')
            ->setParameter('id', $this->job_role_id)
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id']) && $result['id']) {
            $this->jobRole = JobRole::SGet($result['id']);
        }

        return $this->jobRole;
    }

    /*
     * Check if the UserJobProgress needs to move onto the next JobRole
     *
     * @return boolean
     */
    public function checkPromotion()
    {
        $orderedJobRoles = JobRole::getOrderedJobRoles();

        $jobRoleIdToUse = null;
        foreach ($orderedJobRoles as $exp => $id) {
            if ($this->job_exp >= $exp) {
                $jobRoleIdToUse = $id;
            }
        }

        if ((int) $this->job_role_id !== $jobRoleIdToUse) {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('user_job_progress')
                ->set('job_role_id', ':job_role_id')
                ->where('id = :id')
                ->setParameter('job_role_id', $jobRoleIdToUse)
                ->setParameter('id', $this->id)
                ->execute();

            // Only inform the user they've been promoted if they have more than 0 EXP
            if ($this->job_exp > 0) {
                $user = UserFactory::getInstance()->getUser($this->user_id);
                $user->Notify('You have been promoted in your job!');
            }

            return true;
        }

        return false;
    }

    /*
     * Get the required exp for promotion
     *
     * @return boolean
     */
    public function getRequiredExp()
    {
        $nextJobRole = $this->getNextJobRole();

        if ($nextJobRole) {
            return $nextJobRole->required_exp;
        }

        return null;
    }

    /*
     * Get the next job role
     *
     * @return boolean
     */
    public function getNextJobRole()
    {
        $orderedJobRoles = JobRole::getOrderedJobRoles();
        $currentJobRole = null;
        $nextJobRole = null;

        foreach ($orderedJobRoles as $exp => $id) {
            if ($currentJobRole && !$nextJobRole) {
                $nextJobRole = JobRole::SGet($id);
            }

            if ((int) $this->job_role_id === (int) $id) {
                $currentJobRole = $id;
            }
        }

        return $nextJobRole;
    }

    /*
     * Get the payout_hour choices
     *
     * @return array
     */
    public static function getPayoutHourChoices()
    {
        $payoutHourChoices = [
            '00' => '12am',
            '09' => '9am',
            '12' => '12pm',
            '21' => '9pm',
        ];

        return $payoutHourChoices;
    }

    /*
     * Calculate the addition daily payout
     *
     * @return integer
     */
    public function calculateAdditionalDailyPayout()
    {
        $now = new \DateTime();

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_job_role_task')
            ->where('is_complete = :is_complete')
            ->andWhere('date = :now')
            ->andWhere('user_id = :user_id')
            ->setParameter('is_complete', 1)
            ->setParameter('now', $now->format('Y-m-d'))
            ->setParameter('user_id', $this->user_id)
        ;
        $results = $queryBuilder->execute()->fetchAll();

        $additionalDailyPayout = 0;
        foreach ($results as $res) {
            $userJobRoleTask = UserJobRoleTask::SGet($res['id']);
            $additionalDailyPayout += $userJobRoleTask->getJobRoleTask()->money_reward;
        }

        return (int) $additionalDailyPayout;
    }

    /*
     * Calculate the next payout
     *
     * @return integer
     */
    public function calculateNextPayout()
    {
        return $this->getJobRole()->daily_money_salary + $this->calculateAdditionalDailyPayout();
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
            'job_role_id',
            'job_exp',
            'payout_hour',
            'last_payout_date',
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
