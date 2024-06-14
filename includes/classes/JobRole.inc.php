<?php

class JobRole extends CachedObject
{
    public static $dataTable = 'job_role';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);

        $this->job_role_tasks = null;
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new JobRole($id);
    }

    /*
     * Get JobRoles ordered by required_exp
     *
     * @return array
     */
    public static function getOrderedJobRoles()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, required_exp')
            ->from('job_role')
            ->orderBy('required_exp')
        ;
        $result = $queryBuilder->execute()->fetchAll();

        $orderedJobRoles = [];
        foreach ($result as $res) {
            $orderedJobRoles[$res['required_exp']] = $res['id'];
        }

        return $orderedJobRoles;
    }

    /*
     * Get the daily_money_salary formatted
     *
     * @return string
     */
    public function getDailyMoneySalary()
    {
        return '$' . number_format($this->daily_money_salary);
    }

    /*
     * Get the daily_exp_salary formatted
     *
     * @return string
     */
    public function getDailyExpSalary()
    {
        return number_format($this->daily_exp_salary, 0) . '%';
    }

    /*
     * Get the JobRoleTasks for this JobRole
     *
     * @return array
     */
    public function getJobRoleTasks()
    {
        if ($this->job_role_tasks) {
            return $this->job_role_tasks;
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('job_role_task')
            ->where('job_role_id = :job_role_id')
            ->setParameter('job_role_id', $this->id)
        ;
        $result = $queryBuilder->execute()->fetchAll();

        foreach ($result as $res) {
            $this->job_role_tasks[] = JobRoleTask::SGet($res['id']);
        }

        return $this->job_role_tasks;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'name',
            'description',
            'required_exp',
            'daily_money_salary',
            'daily_exp_salary',
            'boss_name',
            'boss_avatar',
            'boss_complete_text',
            'boss_incomplete_text',
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
