<?php

    /**
     * discription: This class is used to manage User Information for payment.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: UserTries
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class UserInfo extends BaseObject
    {
        public static $idField = 'id'; //id field
        public static $dataTable = 'userinfo'; // table implemented

        /**
         * Constructor.
         */
        public function __construct()
        {
        }

        /**
         * Funtions used to add user tries.
         *
         * @return Boleean
         */
        public static function Add(User $user, array $data)
        {
            if (!isset($data['firstname']) || empty($data['firstname'])) {
                throw new FailedResult(sprintf(FIELD_EMPTY_ERR, FIRST_NAME));
            }
            if (!isset($data['lastname']) || empty($data['lastname'])) {
                throw new FailedResult(sprintf(FIELD_EMPTY_ERR, LAST_NAME));
            }
            if (!isset($data['city']) || empty($data['city'])) {
                throw new FailedResult(sprintf(FIELD_EMPTY_ERR, CITY));
            }
            if (!isset($data['region']) || empty($data['region'])) {
                throw new FailedResult(sprintf(FIELD_EMPTY_ERR, STATE));
            }
            if (!isset($data['postal']) || empty($data['postal'])) {
                throw new FailedResult(sprintf(FIELD_EMPTY_ERR, ZIP));
            }
            if (!isset($data['country']) || empty($data['country'])) {
                throw new FailedResult(sprintf(FIELD_EMPTY_ERR, COUNTRY));
            }
            if (isset($data['ccbin']) && !empty($data['ccbin']) && !Validation::IsInteger($data['ccbin'])) {
                throw new FailedResult(sprintf(FIELD_NUMERIC_ERR, CCBIN));
            }
            $insdata = [
                                'firstname' => Utility::SmartEscape($data['firstname']),
                                'lastname' => Utility::SmartEscape($data['lastname']),
                                'city' => Utility::SmartEscape($data['city']),
                                'region' => Utility::SmartEscape($data['region']),
                                'postal' => Utility::SmartEscape($data['postal']),
                                'country' => Utility::SmartEscape($data['country']),
                                'ccbin' => Utility::SmartEscape($data['ccbin']),
                            ];

            $userinfo = self::GetCurrentForUser($user);

            $changed = false;

            if (empty($userinfo)) {
                $changed = true;
            } else {
                foreach ($userinfo as $key => $value) {
                    if (isset($insdata[$key]) && $value != $insdata[$key]) {
                        $changed = true;
                        break;
                    }
                }
            }

            if ($changed) {
                $insdata['user_id'] = $user->id;
                $insdata['created'] = time();

                return parent::AddRecords($insdata, self::GetDataTable());
            }
        }

        /**
         * Funtions used to delete user tries.
         *
         * @return Boleean
         */
        public static function Delete($id)
        {
            return parent::sDelete(self::GetDataTable(), ['id' => $id]);
        }

        /**
         * Funtions return all records.
         *
         * @return array
         */
        public static function GetAll($where = '', $order = false, $dir = 'ASC', $page = false, $limit = false)
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, $page, $limit, $order, $dir);
        }

        public static function GetCurrentForUser(User $user)
        {
            $where = 'user_id = ' . $user->id;
            $objs = self::GetAll($where, 'created', 'DESC', 1, 1);

            if (empty($objs)) {
                return $objs;
            }

            return $objs[0];
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
                'user_id',
                'firstname',
                'lastname',
                'city',
                'region',
                'postal',
                'country',
                'ccbin',
                'created',
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
