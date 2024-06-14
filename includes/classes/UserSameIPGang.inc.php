<?php

    /**
     * discription: This class is used to manage UserSameIPGang.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: UserSameIPGang
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    final class UserSameIPGang extends BaseObject
    {
        public static $idField = 'user_id'; //id field
        public static $dataTable = 'user_ip_gang'; // table implemented

        /**
         * Constructor.
         */
        public function __construct()
        {
        }

        /**
         * Funtions used to detect users on same ip and in same gang.
         *
         * @return Boleean
         */
        public static function Detect()
        {
            //Full Query: 'SELECT u1.id, u1.ip, u1.gang, u1.strength, u1.defense, u1.speed, (u1.strength + u1.defense + u1.speed) as stat , u1.level, u2.id, u2.ip, u2.gang FROM grpgusers u1, grpgusers u2 WHERE u1.ip = u2.ip AND u1.gang = u2.gang AND u1.id != u2.id and u1.ip > 0 AND u1.gang > 0 AND u1.level < 10 HAVING stat < 25000';

            DBi::$conn->query('TRUNCATE user_ip_gang');
            DBi::$conn->query('LOCK TABLES user_ip_gang WRITE , grpgusers AS u1 READ , grpgusers AS u2 READ , bans READ , bans AS bans1 READ');

            $query = 'INSERT INTO user_ip_gang (user_id,ip, gang,detected) SELECT u1.id, u1.ip, u1.gang,now() FROM grpgusers u1, grpgusers u2 WHERE u1.ip = u2.ip AND u1.gang = u2.gang AND u1.id != u2.id and u1.ip > 0 AND u1.gang > 0 AND u1.level < 10 AND u2.level < 10  AND u1.id NOT IN (SELECT `bans`.`id` FROM `bans` WHERE `bans`.`id`=u1.id) AND u2.id NOT IN (SELECT `bans1`.`id` FROM `bans` as `bans1` WHERE `bans1`.`id`=u2.id) GROUP BY u1.id ORDER BY u1.ip';

            //AND (u1.strength + u1.defense + u1.speed) < 25000 AND (u2.strength + u2.defense + u2.speed) < 25000 removed the stats limit on 16 jul 2009

            DBi::$conn->query($query);
            DBi::$conn->query('UNLOCK TABLES');

            return true;
        }

        /**
         * Funtions return all categories.
         *
         * @return array
         */
        public static function GetAll()
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'ip', 'ASC');
        }

        /**
         * Function used to get the data table name which is implemented by class.
         *
         * @return string
         */
        protected static function GetDataTable()
        {
            return self::$dataTable;
        }

        /**
         * Returns the fields of table.
         *
         * @return array
         */
        protected static function GetDataTableFields()
        {
            return [
                self::$idField,
                'ip',
                'gang',
                'detected',
            ];
        }

        /**
         * Returns the identifier field name.
         *
         * @return mixed
         */
        protected function GetIdentifierFieldName()
        {
            return self::$idField;
        }

        /**
         * Function returns the class name.
         *
         * @return string
         */
        protected function GetClassName()
        {
            return __CLASS__;
        }
    }
