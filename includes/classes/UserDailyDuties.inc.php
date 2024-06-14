<?php

class UserDailyDuties extends CachedObject
{
    public static $dataTable = 'user_daily_duties';
    public static $idField = 'id';

    private $dailyDuties;
    private $user;

    public function __construct($id)
    {
        parent::__construct($id);

        //$this->dailyDuties = $this->getDailyDuties();
        $this->user = $this->getUser();
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new UserDailyDuties($id);
    }

    public function getDailyDuties()
    {
        if ($this->dailyDuties) {
            return $this->dailyDuties;
        }
        return new DailyDuties($this->daily_duties_id);
    }

    public function getUser()
    {
        if ($this->user) {
            return $this->user;
        }
        return UserFactory::getInstance()->getUser($this->userid);
    }

    public static function create(int $dailyDutiesId, $user)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values(
                [
                    'daily_duties_id' => ':daily_duties_id',
                    'userid' => ':user_id'
                ]
            )
            ->setParameter('daily_duties_id', $dailyDutiesId)
            ->setParameter('user_id', $user->id)
        ;

        return $queryBuilder->execute();
    }

    public function checkIsComplete()
    {
        if ($this->attacks < $this->getDailyDuties()->attacks) {
            return false;
        }

        if ($this->crimes < $this->getDailyDuties()->crimes) {
            return false;
        }

        if ($this->mugs < $this->getDailyDuties()->mugs) {
            return false;
        }

        if ($this->busts < $this->getDailyDuties()->busts) {
            return false;
        }

        return true;
    }

    public static function hasActiveDailyDuties(int $userid)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_daily_duties')
            ->where('userid = :userid')
            ->setParameter('userid', $userid)
            ->andWhere('is_complete = 0')
        ;
        $startedDailyDuties = $queryBuilder->execute()->fetchAll();

        if (count($startedDailyDuties) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function hasCompleteDailyDuties(int $userid, int $dailyDutyId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_daily_duties')
            ->where('userid = :userid')
            ->setParameter('userid', $userid)
            ->andWhere('daily_duties_id = :daily_duties_id')
            ->setParameter('daily_duties_id', $dailyDutyId)
            ->andWhere('is_complete = 1')
        ;
        $startedDailyDuties = $queryBuilder->execute()->fetchAll();

        if (count($startedDailyDuties) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getStartedDailyDuties(int $userid, int $dailyDutyId = 0)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_daily_duties')
            ->where('userid = :userid')
            ->setParameter('userid', $userid)
        ;

        if ($dailyDutyId) {
            $queryBuilder
                ->andWhere('daily_duties_id = :daily_duties_id')
                ->setParameter('daily_duties_id', $dailyDutyId)
            ;
        }

        $startedDailyDuties = $queryBuilder->execute()->fetchAll();

        if (count($startedDailyDuties) > 0) {
            if ($dailyDutyId) {
                foreach ($startedDailyDuties as $sdd) {
                    return UserDailyDuties::SGet($sdd['id']);
                }
            } else {
                return $startedDailyDuties;
            }
        } else {
            return false;
        }
    }

    public static function recordAction(int $userid, string $action)
    {
        DBi::$conn->query("UPDATE user_daily_duties SET " . $action . " = " . $action . " + 1 WHERE `userid` = " . $userid);

        $startedDailyDuties = UserDailyDuties::getStartedDailyDuties($userid);

        if ($startedDailyDuties) {
            foreach ($startedDailyDuties as $startedDailyDuty) {
                $startedDailyDuty = UserDailyDuties::SGet($startedDailyDuty['id']);

                if (!$startedDailyDuty->is_complete) {
                    if ($startedDailyDuty->checkIsComplete()) {
                        $startedDailyDuty->SetAttribute('is_complete', 1);

                        $dailyDuty = $startedDailyDuty->getDailyDuties();
                        $user = $startedDailyDuty->getUser();
                        if ($dailyDuty->points > 0) {
                            $user->AddToAttribute('points', $dailyDuty->points);
                        }
                        if ($dailyDuty->item_id > 0) {
                            $user->AddItems($dailyDuty->item_id, 1);
                        }

                        Event::Add($user->id, 'You have successfully complete your active Daily Duty.');
                    }
                }
            }
        }
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'daily_duties_id',
            'userid',
            'attacks',
            'crimes',
            'mugs',
            'busts',
            'is_complete'
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