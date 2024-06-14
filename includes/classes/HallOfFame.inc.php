<?php
error_reporting(0);
    /**
     * discription: This class is used to manage hall of fame.
     *
     * @author: Harish<harish282@gmail.com>

     * @name: HallOfFame

     * @package: includes

     * @subpackage: classes

     * @final: Final

     * @access: Public

     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class HallOfFame extends BaseObject
    {
        /** Static variables **/
        public static $idField = '';

        public static $dataTable = '';

        /**
         * singleton Constructor.
         */
        public function __construct($id = null)
        {
            parent::__construct($id);
        }

        public static function GetAllByField($field = 'id', $order = 'ASC', $limit = 50)
        {
            return User::GetAllByUserFieldLimited($field, $order, $limit);
        }

        public static function GetAllByFieldForUser($userid, $field = 'exp', $order = 'ASC', $limit = 7)
        {
            $users = self::GetRankFor($userid, $field, $limit);

            if (empty($users)) {
                return [];
            }

            $players = parent::GetAllById(User::GetIdentifierFieldName(), User::GetDataTableFields(), User::GetDataTable(), 'id IN (' . implode(',', $users) . ')');

            //print_r

            foreach ($users as $rank => $user) {
                $users[$rank] = $players[$user];
            }

            return $users;
        }

        public static function GetRankFor($userid, $field, $limit = 7)
        {
            switch ($field) {
                case 'strength':

                    $table = 'top1000str';

                    break;

                case 'defense':

                    $table = 'top1000def';

                    break;

                case 'speed':

                    $table = 'top1000spd';

                    break;

                case 'tags':

                        $table = 'top1000tags';

                        break;
                case 'total':

                    $table = 'top1000tot';

                    break;

                  case 'exp':

                default:

                    $table = 'top1000lvl';

                    break;
            }

            $sql = 'SELECT uid, rank FROM `' . $table . '` WHERE rank >= (SELECT rank-3 FROM `' . $table . '` WHERE uid = \'' . $userid . '\') LIMIT ' . $limit;

            $result = DBi::$conn->query($sql);

            $return = [];
            if(mysqli_num_rows($result)) {


                while ($row = mysqli_fetch_object($result)) {
                    $return[$row->rank] = $row->uid;
                }
            }

            return $return;
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
