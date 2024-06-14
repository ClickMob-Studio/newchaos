<?php

class MiningDrone extends CachedObject
{
    public static $dataTable = 'mining_drone';
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
        return new MiningDrone($id);
    }

    public static function getForDesc($desc)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('mining_drone')
            ->where('description = :description')
            ->setParameter('description', $desc)
            ->setMaxResults(1)
        ;
        $miningDrone = $queryBuilder->execute()->fetchAssociative();

        if (isset($miningDrone['id'])) {
            return new MiningDrone($miningDrone['id']);
        }

        return null;
    }

    public function getRunCost()
    {
        if ($this->type == 'points') {
            return 9000000;
        } else {
            return $this->life_length * 100000;
        }
    }

    public function getEarningsCalc()
    {
        // NB: If updated, ensure getEarningsCalcAsText is updated

        if ($this->type === 'exp') {
            if ($this->level == 1) {
                return mt_rand(2, 3);
            } else if ($this->level == 3) {
                return mt_rand(2, 3);
            } else if ($this->level == 5) {
                return mt_rand(2, 3);
            } else if ($this->level == 7) {
                return mt_rand(3, 4);
            }
        } else if ($this->type === 'points') {
            if ($this->level == 7) {
                return mt_rand(15, 20);
            }
        } else if ($this->type === 'spy') {
            return null;
        } else {
            return mt_rand(50000, 75000);
        }
    }

    public function getEarningsCalcAsText()
    {
        if ($this->type === 'exp') {
            if ($this->level == 1) {
                return '6 - 9% of EXP Needed';
            } else if ($this->level == 3) {
                return '16 - 24% of EXP Needed';
            } else if ($this->level == 5) {
                return '24 - 36% of EXP Needed';
            } else if ($this->level == 7) {
                return '54 - 72% of EXP Needed';
            }
        } else if ($this->type === 'points') {
            if ($this->level == 7) {
                return '90 - 120 Points';
            }
        } else if ($this->type === 'spy') {
            return null;
        } else {
            return number_format(35000 * $this->life_length) . ' - ' . number_format(50000 * $this->life_length);
        }
    }

    public static function getMiningDroneTypes()
    {
        return array('strength', 'defense', 'speed', 'exp', 'spy');
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'dronename',
            'description',
            'image_file',
            'type',
            'level',
            'life_length'
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
?>