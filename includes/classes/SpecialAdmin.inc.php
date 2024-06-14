<?php
//admin class for the admin panel
class SpecialAdmin extends BaseObject
{
    public static function GetAllPoints(){
        //loop through all users in grpgusers and get their points
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder->select('points')
            ->from('grpgusers');
        $users = $queryBuilder->execute()->fetchAll();
        $points = 0;
        foreach($users as $user){
            $points += $user['points'];
        }
        return $points;

    }
    public static function AddPointsLog(){
        //insert into database total users points and todays date
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder->insert('admin_daily_logs')
            ->values(
                array(
                    'points' => '?',
                    'date' => '?'
                )
            )
            ->setParameter(0, SpecialAdmin::GetAllPoints())
            ->setParameter(1, date('Y-m-d'));
        $queryBuilder->execute();

    }
    //retireve last 7 days of points
    public static function GetPointsLog(){
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder->select('points, date')
            ->from('admin_daily_logs')
            ->orderBy('date', 'DESC')
            ->setMaxResults(7);
        $points = $queryBuilder->execute()->fetchAll();
        return $points;
    }

    //



    protected function GetIdentifierFieldName()
    {
        // TODO: Implement GetIdentifierFieldName() method.
    }

    protected function GetClassName()
    {
        // TODO: Implement GetClassName() method.
    }

    protected static function GetDataTable()
    {
        // TODO: Implement GetDataTable() method.
    }

    protected static function GetDataTableFields()
    {
        // TODO: Implement GetDataTableFields() method.
    }
}