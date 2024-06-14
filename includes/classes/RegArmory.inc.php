<?php


class RegArmory extends BaseObject
{
    public static $idField = 'userid';
    public static $dataTable = 'gangarmory';

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'userid',
            'itemid',
            'quantity',
            'borrowed',
            'awake',
        ];
    }
    public function GetArmory(){
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, itemid, gangid, borrowerid, borrowed_to_user_id')
            ->from('gangarmory')
            ->where('gangid = :gang_id')
            ->setParameter('gang_id', $this->id)
        ;
        $gangArmories = $queryBuilder->execute()->fetchAll();

        foreach ($gangArmories as $key => $gangArmory) {
            $gangArmories[$key]['item'] = new Item($gangArmory['itemid']);

            if (isset($gangArmory['borrowed_to_user_id']) && $gangArmory['borrowed_to_user_id']) {
                $borrowedToUser = UserFactory::getInstance()->getUser($gangArmory['borrowed_to_user_id']);
                $gangArmories[$key]['borrowed_to_user'] = $borrowedToUser;
            }
        }

        return $gangArmories;

    }
}