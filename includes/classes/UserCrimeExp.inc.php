<?php

class UserCrimeExp extends CachedObject
{
    public static $dataTable = 'user_crime_exp';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);

        $this->userCrimeExp = null;
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new UserCrimeExp($id);
    }

    /*
     * Create a UserCrimeExp
     *
     * @param int $userid
     * @param int $crimeid
     */
    public static function create(int $userid, int $crimeid)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert('user_crime_exp')
            ->values(
                [
                    'userid' => ':userid',
                    'crimeid' => ':crimeid',
                    'level' => ':level',
                    'exp' => ':exp',

                ]
            )
            ->setParameter('userid', $userid)
            ->setParameter('crimeid', $crimeid)
            ->setParameter('level', 1)
            ->setParameter('exp', 0)
        ;
        $queryBuilder->execute();
    }

    /*
     * Retrieve for user indexed on crimeid
     *
     * @param int $userid
     */
    public static function getForUserIndexed(int $userid)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('user_crime_exp')
            ->where('userid = :userid')
            ->setParameter('userid', $userid)
        ;
        $result = $queryBuilder->execute()->fetchAllAssociative();

        $userCrimeExpIndexed = array();
        foreach ($result as $r) {
            $userCrimeExpIndexed[$r['crimeid']] = $r;
        }

        return $userCrimeExpIndexed;
    }

    /*
     * Add experience to a UserCrimeExp
     */
    public static function addExp($userCrimeExp, $user = null, $crime = null)
    {
        if (!$crime) {
            $crime = new Crime($userCrimeExp['crimeid']);
        }
        if (!$user) {
            $user = UserFactory::getInstance()->getUser($userCrimeExp['userid']);
        }

        if ($userCrimeExp['level'] == 1) {
            $expRequired =  60;
        } else if ($userCrimeExp['level'] == 2) {
            $expRequired =  150;
        } else {
            $expRequired =  300;
        }

        $newLevel = $userCrimeExp['level'];
        $maxLevels = 3;

        if ($userCrimeExp['level'] < $maxLevels) {
            $expGain = 1;
            $newExp = $userCrimeExp['exp'] + $expGain;

            if ($newExp > $expRequired) {
                $newLevel += 1;

                $exp = ($crime->nerve * 100) * $newLevel;

                $user->AddToAttribute('exp', $exp);
                $user->AddToAttribute('points', 100);

                Event::Add($user->id, 'Congrats, you have reached mission level ' . $newLevel . ' for the crime ' . $crime->name .'. You have gained an additional ' . $exp . ' exp for doing so and 100 points.');

                $queryBuilder = BaseObject::createQueryBuilder();
                $queryBuilder
                    ->update('user_crime_exp')
                    ->set('exp', ':new_exp')
                    ->set('level', ':new_level')
                    ->where('id = :id')
                    ->setParameter('new_exp', 0)
                    ->setParameter('new_level', $newLevel)
                    ->setParameter('id', $userCrimeExp['id'])
                    ->execute()
                ;
            } else {
                $queryBuilder = BaseObject::createQueryBuilder();
                $queryBuilder
                    ->update('user_crime_exp')
                    ->set('exp', ':new_exp')
                    ->where('id = :id')
                    ->setParameter('new_exp', $newExp)
                    ->setParameter('id', $userCrimeExp['id'])
                    ->execute()
                ;
            }
        }
    }

    /*
     * Calculate the percentage to the next level
     */
    public static function calculateNextLevelPerc($userCrimeExp)
    {
        $user = UserFactory::getInstance()->getUser($userCrimeExp['userid']);

        $maxLevels = 3;

        if ($userCrimeExp['level'] >= $maxLevels) {
            return 100;
        }

        if ($userCrimeExp['level'] == 1) {
            $expRequired =  60;
        } else if ($userCrimeExp['level'] == 2) {
            $expRequired =  150;
        } else {
            $expRequired =  300;
        }

        if ((($userCrimeExp['exp'] / $expRequired) * 100) > 100) {
            return 100;
        }

        return ($userCrimeExp['exp'] / $expRequired) * 100;
    }


    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'userid',
            'crimeid',
            'level',
            'exp',
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