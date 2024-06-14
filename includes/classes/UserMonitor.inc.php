<?php

    /**
     * discription: This class is used to manage monitoring for users placed by users.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: UserAds
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class UserMonitor extends BaseObject
    {
        /** Define constatns for status **/
        const FEE_SINGLE = 250000;	//Money for hire PS for monitoring single person by single user
        const FEE_GANG = 2000000;	//Money for hire PS for monitoring entire gang by single user
        const FEE_GANG_SINGLE = 2000000;	//Money for hire PS for monitoring single person by gang
        const FEE_GANG_GANG = 5000000;	//Money for hire PS for monitoring gang person by gang

        public static $idField = 'id'; //id field
        public static $dataTable = 'user_monitor'; // table implemented

        /**
         * Constructor.
         */
        public function __construct()
        {
        }

        public static function GetWhere($where, $sort = [])
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, $sort[0], $sort[1]);
        }

        public static function GetAllForUser(User $user, $sort = [])
        {
            $where = 'hire_user = ' . $user->id;

            return self::GetWhere($where, $sort);
        }

        public static function Add($hire_user, $monitor_user, $hire_forgang, $monitor_gang, $for_days)
        {
            if ($hire_user == 0) {
                throw new FailedResult(INVALID_INPUT);
            }
            if ($monitor_user == 0 && $monitor_gang == 0) {
                throw new FailedResult(MONITOR_INVALID_TARGET);
            }
            try {
                if ($monitor_gang > 0) {
                    $mgang = new Gang($monitor_gang);
                } elseif ($monitor_user > 0) {
                    $muser = UserFactory::getInstance()->getUser($monitor_user);
                }
            } catch (Exception $e) {
                throw new FailedResult(MONITOR_INVALID_TARGET);
            }

            $user = UserFactory::getInstance()->getUser($hire_user);

            if (0 >= $for_days) {
                throw new FailedResult(MONITOR_INVALID_DAYS);
            }
            if ($hire_forgang) {
                if ($user->GetGang()->leader != $user->username) {
                    throw new SoftException(USER_NOT_REQUIRED_GANG_PERM);
                }
                $gang = new Gang($hire_forgang);

                if ($monitor_gang) {
                    $fee = self::FEE_GANG_GANG;
                    $monitorname = $mgang->GetPublicFormattedName();
                    $formatedname = $mgang->GetFormattedName();
                } else {
                    $fee = self::FEE_GANG_SINGLE;
                    $monitorname = $muser->formattedname;
                    $formatedname = '';
                    if (!empty($muser->GetGang()->tag)) {
                        $formatedname .= '[' . $muser->GetGang()->tag . ']';
                    }
                    $formatedname .= $muser->username;
                }

                $fee *= $for_days;

                if ($fee > $gang->vault) {
                    throw new FailedResult(GANG_VAULT_NOT_ENOUGH_MONEY);
                }
                if (!$gang->RemoveFromAttribute('vault', $fee)) {
                    throw new FailedResult(GANG_VAULT_NOT_ENOUGH_MONEY);
                }
                //send pmail to gang members
                $subject = sprintf(MONITOR_GL_START_SUBJECT, $formatedname);
                $text = sprintf(MONITOR_GL_START_BODY, addslashes($user->formattedname), addslashes($monitorname));
                $from = MONITOR_USER_ID;
                $timesent = time();

                $memberes = Gang::SGetAllMembers($user->gang, ['id']);
                foreach ($memberes as $member) {
                    if ($member->id != $user->id) {
                        User::sSendPmail($member->id, $from, $timesent, $subject, $text);
                    }
                }
            } else {
                if ($hire_user == $monitor_user) {
                    throw new FailedResult(MONITOR_CANT_YOURSELF);
                }
                if ($monitor_gang) {
                    $fee = self::FEE_GANG;
                } else {
                    $fee = self::FEE_SINGLE;
                }

                $fee *= $for_days;

                if ($fee > $user->bank) {
                    throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
                }
                if (!$user->RemoveFromAttribute('bank', $fee)) {
                    throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
                }
            }

            $data = [
                        'hire_user' => $hire_user,
                        'monitor_user' => $monitor_user,
                        'hire_forgang' => $hire_forgang,
                        'monitor_gang' => $monitor_gang,
                        'for_days' => time() + $for_days * DAY_SEC,
                ];
            self::AddRecords($data, self::GetDataTable());

            return true;
        }

        public static function Delete(User $user, $id)
        {
            $monitor = self::GetWhere('id=' . $id);
            $monitor = $monitor[0];

            if ($monitor->hire_user != $user->id) {
                throw new SoftException(NOT_AUTHORIZED);
            }
            //$user = UserFactory::getInstance()->getUser($monitor->hire_user);
            if ($monitor->monitor_gang) {
                $mgang = new Gang($monitor->monitor_gang);
                $monitorname = $mgang->GetPublicFormattedName();
                $formatedname = $mgang->GetFormattedName();
            } else {
                $muser = UserFactory::getInstance()->getUser($monitor->monitor_user);
                $monitorname = $muser->formattedname;
                $formatedname = '';
                if (!empty($muser->GetGang()->tag)) {
                    $formatedname .= '[' . $muser->GetGang()->tag . ']';
                }
                $formatedname .= $muser->username;
            }

            if ($monitor->hire_forgang) {
                //send pmail to gang members to intimate end of service
                $subject = sprintf(MONITOR_GL_STOP_SUBJECT, $formatedname);
                $text = sprintf(MONITOR_GL_STOP_BODY, addslashes($user->formattedname), addslashes($monitorname));
                $from = MONITOR_USER_ID;
                $timesent = time();

                $memberes = Gang::SGetAllMembers($user->gang, ['id']);
                foreach ($memberes as $member) {
                    if ($member->id != $user->id) {
                        User::sSendPmail($member->id, $from, $timesent, $subject, $text);
                    }
                }
            }

            self::sDelete(self::GetDataTable(), ['id' => $id]);
        }

        public static function Monitor(User $user)
        {
            $where = '( monitor_user = \'' . $user->id . '\' OR ( monitor_gang =\'' . $user->gang . '\' AND monitor_gang != 0 ) ) AND for_days > ' . time();
            $monitors = self::GetWhere($where);

            if (count($monitors) > 0) {
                $formatedname = '';
                if (!empty($user->GetGang()->tag)) {
                    $formatedname .= '[' . $user->GetGang()->tag . ']';
                }
                $formatedname .= $user->username;
                $subject = sprintf(MONITOR_PMAIL_SUBJECT, $formatedname);
                $text = sprintf(MONITOR_PMAIL, addslashes($user->formattedname), date('F d, Y g:i:sa'));
                $from = MONITOR_USER_ID;
                $timesent = time();
            }

            foreach ($monitors as $monitor) {
                if ($monitor->hire_forgang > 0) {
                    $memberes = Gang::SGetAllMembers($monitor->hire_forgang, ['id']);
                    foreach ($memberes as $member) {
                        if ($member->id != $user->id) {
                            User::sSendPmail($member->id, $from, $timesent, $subject, $text);
                        }
                    }
                } else {
                    if ($monitor->hire_user != $user->id) {
                        User::sSendPmail($monitor->hire_user, $from, $timesent, $subject, $text);
                    }
                }
            }
        }

        public static function DeleteExpired()
        {
            return DBi::$conn->query('DELETE FROM ' . self::GetDataTable() . ' WHERE for_days <= ' . time());
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
                'hire_user',
                'monitor_user',
                'hire_forgang',
                'monitor_gang',
                'for_days',
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
