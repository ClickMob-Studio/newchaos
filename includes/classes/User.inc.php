<?php
use Mailgun\Mailgun;
class UserFactory extends Singleton
{

    /**
     * Store a cache of all users who have been previously loaded.
     *
     * @var array
     */
    private $users = [];

    /**
     * Retrieve a User.
     *
     * @return User
     */
    public function getUser($id)
    {
        if (!isset($this->users[$id])) {
            $this->users[$id] = new User($id);
        }

        return $this->users[$id];
    }
}

class User extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'grpgusers';

    public $id = null;
    public $formattedname = '';
    public $staff_rank = 0;
    public $is_infected = false;
    public $virus_infected_time = '';
    public $dog_tags = 0;

    public function __construct($id)
    {
        if (!$id) {
            return;
        }
        parent::__construct($id);

        // Special username handling
        $codes = self::GetSpecialUsername($id, 'High', $this->invert);
        $safeUsername = $this->username;
        if (strlen($codes) > 0) {
            $this->username = $codes;
        }
        $this->UpdateLevel();
        $this->maxexp = User::GetNeededXPForLevel($this->level);
        $this->maxexpold = User::GetNeededXPForLevel($this->level);

        $this->RenderXP();
        $this->quote = stripslashes($this->quote);
        $this->profile = stripslashes($this->profile);
        $this->avatar = stripslashes($this->avatar);
        $this->formattedgang = '';
        if ($this->IsInAGang()) {
            $this->formattedgang = '<a href=\'viewgang.php?id=' . $this->GetGang()->id . '\'>' . $this->GetGang()->name . '</a>';
        }
        $this->username = $safeUsername;
        $this->RenderFormattedName();
        $this->username = $safeUsername;
        $this->type = USER_REGULAR;
        if ($this->IsRespected()) {
            $this->type = USER_RESPECTED;
        }

        if ($this->IsAdmin()) {
            $this->type = USER_ADMIN;
        }
        $this->staff_rank = self::GetStaffRank($id);
        $this->is_infected = $this->IsInfected();
        $this->dog_tags = $this->getDogTags();
    }
    private function getDogTags(): int
    {
        try {
            $result = DBi::$conn->query('SELECT dog_tags FROM grpgusers WHERE id = ' . $this->id);
            $tags = mysqli_result($result, 0, 0);
        } catch (SQLException $e) {
            $tags = 0;
        }
        return (int) $tags;
    }
    public static function GetCrimeRewardMultiplier()
    {
        return true;
    }
    public function IsInfected()
    {
        if (Utility::IsEventRunning('virus') !== true) {
            return false;
        }
        if ($this->IsAdmin()) {
            $this->virus_infected_time = date('Y-m-d H:i:s');
            return true;
        }
        $select = DBi::$conn->query('SELECT ' . self::$idField . ', virus_infected_time FROM ' . self::$dataTable . ' WHERE id = ' . $this->id);
        $row = mysqli_fetch_assoc($select);
        if ($row === false || $row['virus_infected_time'] === null) {
            return false;
        }
        $this->virus_infected_time = $row['virus_infected_time'];
        $then = strtotime($row['virus_infected_time']);
        $now = time();
        return $now - $then <= 3600;
    }
    public static function GetStaffRank($id)
    {
        $select = DBi::$conn->query('SELECT ' . self::$idField . ', staff_rank FROM ' . self::$dataTable . ' WHERE ' . self::$idField . ' = ' . $id);
        $row = mysqli_fetch_assoc($select);
        return $row !== false ? (int) $row['staff_rank'] : 0;
    }

    /*
     * Inventory related methods
     */

    public function __destruct()
    {
        unset($this->notepadobj);
        unset($this->jobobj);
        unset($this->gangobj);
        unset($this->cityobj);
        unset($this->houseobj);
        unset($this->roulObj);
        unset($this->weaponobj);
        unset($this->armorobj);
        unset($this->armorobjhead);
        unset($this->armorobjchest);
        unset($this->armorobjlegs);
        unset($this->armorobjboots);
        unset($this->armorobjgloves);
        unset($this->speedobj);
    }

    public static function GetSpecialUsername($id, $detail, $invert = false)
    {
        // Special username handling
        $codes = '';
        $custInc = 'includes/classes/usernames/U' . $id . '.inc.php';
        if (file_exists($custInc)) {
            require_once $custInc;
            $tmpClassName = 'U' . $id;
            $sUsername = new $tmpClassName();
            if ($detail == 'High') {
                $codes = $sUsername->GetHiFiName();
                if ($invert):
                    $codes .= '<script>

                        compl="' . $sUsername->uid . '' . SpecialUsername::$internalCount . '";
                        elem=$("#m1uda"+compl);
                         if(elem.length ==0)
                                elem=$("#muda"+compl);
                        if(typeof c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . ' == "function")
                             elem.attr("onMouseOut", "c1hangeName"+compl+"()");
                        else
                            elem.attr("onMouseOut", "changeName"+compl+"()");

                        if(typeof c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . ' == "function")
                             elem.attr("onMouseOver",  "b1ackName"+compl+"()");
                        else
                            elem.attr("onMouseOver",  "backName"+compl+"()");



                        if(typeof c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . ' == "function")
                            c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . '();
                         else
                           changeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . '();

                    </script>';

                endif;
            } elseif ($detail == 'Medium') {
                if ($invert):
                    $codes = $sUsername->GetHiFiName();
                    $reflection = new ReflectionClass($tmpClassName);
                    $codes .= '<script>

                            compl="' . $sUsername->uid . '' . $reflection->getStaticPropertyValue('internalCount') . '";
                            elem=$("#m1uda"+compl);
                           if(elem.length ==0)
                                elem=$("#muda"+compl);


                        if(typeof c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . ' == "function")
                            c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . '();
                         else
                           changeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . '();

                        </script>';
                else:
                    $codes = $sUsername->GetMediumFiName();
                endif;
            } elseif ($detail == 'Low') {
                if ($invert):
                    $codes = $sUsername->GetHiFiName();
                    $reflection = new ReflectionClass($tmpClassName);
                    $codes .= '<script>

                            compl="' . $sUsername->uid . '' . $reflection->getStaticPropertyValue('internalCount') . '";

                            elem=$("#m1uda"+compl);
                            if(elem.length ==0)
                                elem=$("#muda"+compl);

                        if(typeof c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . ' == "function")
                            c1hangeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . '();
                         else
                           changeName' . $sUsername->uid . '' . SpecialUsername::$internalCount . '();

                        </script>';
                else:
                    $codes = $sUsername->GetLowFiName();
                endif;
            }
        }

        return $codes;
    }
    public function getRankName()
    {
        if ($this->level <= 40) {
            return "Officer Cadet";
        } elseif ($this->level <= 70) {
            return "Second Lieutenant";
        } elseif ($this->level <= 90) {
            return "Lieutenant";
        } elseif ($this->level <= 120) {
            return "Captain";
        } elseif ($this->level <= 170) {
            return "Major";
        } elseif ($this->level <= 200) {
            return "Lieutenant Colonel";
        } elseif ($this->level <= 220) {
            return "Colonel";
        } elseif ($this->level <= 230) {
            return "Brigadier";
        } elseif ($this->level < 240) {
            return "Major General";
        } else {
            return "Field Marshal";
        }
    }
    public function UpdateLevel()
    {

        if ($this->securityLevel < 5 && $this->level >= MAX_LVL) {
            return false;
        }

        $level = $this->level + 1;
        $umission = DBi::$conn->query("SELECT * FROM `user_missions` WHERE `user` = " . $this->id);
        if (($att = mysqli_fetch_assoc($umission)) == true) {
            $t1 = $att['task_one_amount'] += 1;
            $t2 = $att['task_two_amount'] += 1;
            $t3 = $att['task_three_amount'] += 1;
            $t4 = $att['task_four_amount'] += 1;
            $t5 = $att['task_five_amount'] += 1;
            $t6 = $att['task_six_amount'] += 1;
            $t7 = $att['task_seven_amount'] += 1;
            $t8 = $att['task_eight_amount'] += 1;

            if ($att['task_one'] == 'Level')
                DBi::$conn->query("UPDATE `user_missions` SET `task_one_amount` = {$t1} WHERE `user` = " . $this->id);
            else if ($att['task_two'] == 'Levels')
                DBi::$conn->query("UPDATE `user_missions` SET `task_two_amount` = {$t2} WHERE `user` = " . $this->id);
            else if ($att['task_three'] == 'Levels')
                DBi::$conn->query("UPDATE `user_missions` SET `task_three_amount` = {$t3} WHERE `user` = " . $this->id);
            else if ($att['task_four'] == 'Levels')
                DBi::$conn->query("UPDATE `user_missions` SET `task_four_amount` = {$t4} WHERE `user` = " . $this->id);
            else if ($att['task_five'] == 'Levels')
                DBi::$conn->query("UPDATE `user_missions` SET `task_five_amount` = {$t5} WHERE `user` = " . $this->id);
            else if ($att['task_six'] == 'Levels')
                DBi::$conn->query("UPDATE `user_missions` SET `task_six_amount` = {$t6} WHERE `user` = " . $this->id);
            else if ($att['task_seven'] == 'Levels')
                DBi::$conn->query("UPDATE `user_missions` SET `task_seven_amount` = {$t7} WHERE `user` = " . $this->id);
            else if ($att['task_eight'] == 'Levels')
                DBi::$conn->query("UPDATE `user_missions` SET `task_eight_amount` = {$t8} WHERE `user` = " . $this->id);
        }
        $level1 = $this->level;
        while ($this->exp > User::GetNeededXPForLevel($this->level)) {
            $this->RemoveFromAttribute('exp', User::GetNeededXPForLevel($this->level));
            DBi::$conn->query('update ' . self::$dataTable . ' set level=' . $level . ' where id=' . $this->id . ' and level=' . $level1);
            $this->Notify('Congratulations, you\'ve leveled up, you have gained 100 points. You\'re now level ' . $level . '.', COM_UPDATE_LEVEL);
            $this->level = $level;
            $this->AddPoints(100);
            $this->SetAttribute('awake', $this->GetMaxAwake());
            $this->SetAttribute('nerve', $this->GetMaxNerve());
            $this->SetAttribute('energy', $this->GetMaxEnergy());
            MonthlyReward::UpdateLevel($this->id);
            if ($level === 5) {
                $result = DBi::$conn->query('SELECT `referrer` FROM `referrals` WHERE `referred` = "' . $this->username . '" AND `credited` != 1');
                if (mysqli_num_rows($result) > 0) {
                    $arr = mysqli_fetch_array($result);

                    $referrer = UserFactory::getInstance()->getUser($arr[0]);
                    $referrer->addPoints(50);

                    $referrer->Notify('You have earned 50 points as your referral ' . $this->username . ' has reached level 5!');
                    DBi::$conn->query("UPDATE `referrals` SET `credited` = 1 WHERE `referred` = '" . $this->username . "'");
                }
            }
            $admin = UserFactory::getInstance()->getUser(2);
            //UserAds::Add($admin, $this->username . ' has levelled up!', false, true);

            break;
        }

        return false;
    }
    public function UnborrowHelmet()
    {
        if ($this->HasBorrowedEquippedHelmet() === false) {
            return false;
        }
        $helmet = $this->GetHelmet();
        $this->UnequipHelmet();
        $this->user->UnborrowItem($helmet);

        return true;
    }

    public function GetHelmet()
    {
        if ($this->HasEquippedHelmet() === false) {
            return null;
        }

        if (!isset($this->helmetobj) || $this->helmetobj === null) {
            $this->helmetobj = new Item($this->eqhelmet);
            if ($this->eqhelmetloan == 1) {
                $this->helmetobj->borrowed = 1;
            }
        }

        return $this->helmetobj;
    }

    public function HasEquippedHelmet($id = 0)
    {
        return ($id > 0 && $this->eqhelmet == $id) || ($id == 0 && $this->eqhelmet != 0);
    }

    public function HasBorrowedEquippedHelmet($id = 0)
    {
        return $this->HasEquippedHelmet($id) && $this->eqhelmetloan == 1;
    }

    public function IsEquippedHelmet($item)
    {
        if ($this->eqhelmet == $item->id) {
            return true;
        }

        return false;
    }

    public function EquipHelmet(Item $helmet, $borrowed)
    {
        if ($this->user->GetItemQuantity($helmet->id, $borrowed) <= 0) {
            throw new SoftException(sprintf(USER_NOT_HAVE_ITEM, $helmet->itemname));
        } elseif ($helmet->level > $this->user->level) {
            throw new SoftException(sprintf(USER_NOT_HAVE_LVL_USE_HEMET, $helmet->level));
        }
        if ($this->HasEquippedHelmet()) {
            $this->UnequipHelmet();
        }

        if ($this->user->RemoveItems($helmet, 1, $borrowed)) {
            $this->SetAttribute('eqhelmet', $helmet->id);
            $this->SetAttribute('eqhelmetloan', $borrowed);
            unset($this->helmetobj);
        } else {
            throw new SoftException('Error on EquipHelmet, please report in support center');
        }

        return true;
    }

    public function UnequipHelmet()
    {
        if ($this->HasEquippedHelmet() === false) {
            throw new SoftException(USER_HAVENT_EQUIPPED_HELMET);
        }
        $this->user->AddItems($this->eqhelmet, 1, $this->eqhelmetloan);
        $this->SetAttribute('eqhelmet', 0);
        $this->SetAttribute('eqhelmetloan', 0);

        if ($this->user->hp > $this->user->GetMaxHP()) {
            $this->user->SetAttribute('hp', $this->user->GetMaxHP());
        }

        unset($this->helmetobj);

        return true;
    }

    public function UnborrowShoes()
    {
        if ($this->HasBorrowedEquippedShoes() === false) {
            return false;
        }
        $shoes = $this->GetShoes();
        $this->UnequipShoes();
        $this->user->UnborrowItem($shoes);

        return true;
    }

    public function GetShoes()
    {
        if ($this->HasEquippedShoes() === false) {
            return null;
        }

        if (!isset($this->shoesobj) || $this->shoesobj === null) {
            $this->shoesobj = new Item($this->eqshoes);
            if ($this->eqshoesloan == 1) {
                $this->shoesobj->borrowed = 1;
            }
        }

        return $this->shoesobj;
    }

    public function HasEquippedShoes($id = 0)
    {
        return ($id > 0 && $this->eqshoes == $id) || ($id == 0 && $this->eqshoes != 0);
    }

    public function HasBorrowedEquippedShoes($id = 0)
    {
        return $this->HasEquippedShoes($id) && $this->eqshoesloan == 1;
    }

    public function IsEquippedShoes($item)
    {
        if ($this->eqshoes == $item->id) {
            return true;
        }

        return false;
    }

    public function EquipShoes(Item $shoes, $borrowed)
    {
        if ($this->user->GetItemQuantity($shoes->id, $borrowed) <= 0) {
            throw new SoftException(sprintf(USER_NOT_HAVE_ITEM, $shoes->itemname));
        } elseif ($shoes->level > $this->user->level) {
            throw new SoftException(sprintf(USER_NOT_HAVE_LVL_USE_HEMET, $shoes->level));
        }
        if ($this->HasEquippedShoes()) {
            $this->UnequipShoes();
        }

        if ($this->user->RemoveItems($shoes, 1, $borrowed)) {
            $this->SetAttribute('eqshoes', $shoes->id);
            $this->SetAttribute('eqshoesloan', $borrowed);
            unset($this->shoesobj);
        } else {
            throw new SoftException('Error on EquipShoes, please report in support center');
        }

        return true;
    }

    public function UnequipShoes()
    {
        if ($this->HasEquippedShoes() === false) {
            throw new SoftException(USER_HAVENT_EQUIPPED_SHOES);
        }
        $this->user->AddItems($this->eqshoes, 1, $this->eqshoesloan);
        $this->SetAttribute('eqshoes', 0);
        $this->SetAttribute('eqshoesloan', 0);
        unset($this->shoesobj);

        return true;
    }

    public static function GetNeededXPForLevel($level)
    {
        if ($level == 1) {
            return 250;
        }

        $a = 0;
        for ($x = 1; $x < $level; ++$x) {
            $a += floor($x + 1500 * (4 * ($x / 2.5)));

            if ($level === $x) {
                if ($a > 0) {
                    return $a;
                } else {
                    return 250;
                }
            }
        }

        return floor($a / 4);
    }

    public function Notify($text, $type = '')
    {
        return Event::Add($this->id, $text, $type);
    }

    public function IsInAGang()
    {
        return $this->gang != 0;
    }

    public function GetGang()
    {
        if (!isset($this->gangobj) || $this->gangobj === null) {
            $this->gangobj = new Gang($this->gang);
        }

        return $this->gangobj;
    }

    public function RenderFormattedName($mode = 'High')
    {
        if (!$this->id) {
            return;
        }

        $title = [];
        if ($mode != 'None') {
            $codes = self::GetSpecialUsername($this->id, $mode, $this->invert);
        }

        $this->formattedname = '';
        $result = DBi::$conn->query('SELECT `id`,`gang`,`level`,`city`,`username`,`rmdays`, `admin`, `mods`, `image_name`, `securityLevel`,`avatar`,`lastactive`, virus_infected_time FROM `grpgusers` WHERE `id`="' . $this->id . '"');
        $user = mysqli_fetch_array($result);
        $user['virus_is_infected'] = false;
        if (Utility::IsEventRunning('virus') === true) {
            if ($this->IsAdmin()) {
                $user['virus_is_infected'] = true;
            } elseif ($user['virus_infected_time'] !== null) {
                $then = strtotime($user['virus_infected_time']);
                $now = time();
                $user['virus_is_infected'] = $now - $then <= 3600;
            }
        }

        $tag = '';
        $gang = null;
        if ($user['gang'] != 0) {
            $gangQuery = DBi::$conn->query('SELECT `id`,`name`,`leader`,`tag`, Color_1,Color_2,Color_3,banner FROM `gangs` WHERE `id`="' . $user['gang'] . '"');
            $gang = mysqli_fetch_array($gangQuery);

            if ($gang !== false) {
                $colors = [];
                $colors[] = $gang['Color_1'];
                $colors[] = $gang['Color_2'];
                $colors[] = $gang['Color_3'];
                $tag = User::ProcessTag($gang['tag'], $colors);
            }
        }

        // If the user had a custom username, use it
        if (strlen($codes) > 0) {
            $user['username'] = $codes;
        }

        $class = 'normalplayer';

        $query = DBi::$conn->query('SELECT `donator`,`exp`,`total`,`newrefs` FROM `best`');
        $query1 = mysqli_fetch_array($query);

        //Top Donator
        if (strpos($query1['donator'], ',' . $user['id'] . ',') !== false) {
            $class = 'donatorplayer';
            $title[] = 'Top Donator';
        }

        //Top Level
        if (strpos($query1['exp'], ',' . $user['id'] . ',') !== false) {
            $class = 'topLevelPlayer';
            $title[] = 'Top Level';
        }

        //Top Inmate
        if (strpos($query1['total'], ',' . $user['id'] . ',') !== false) {
            $class = 'topPlayer';
            $title[] = 'Top Soldier';
        }

        //Top Referrals
        if (strpos($query1['newrefs'], ',' . $user['id'] . ',') !== false) {
            $class = 'topReferalsPlayer';
            $title[] = 'Top Referral';
        }

        $resultBan = DBi::$conn->query('SELECT `id` FROM `bans` WHERE id=\'' . $this->id . '\' LIMIT 1');

        if ($class == 'normalplayer' && $user['rmdays'] > 0) {
            $class = 'rmplayer';
        }
        if ($user['admin'] == 1) {
            $class = 'topAdmin';
            $title[] = 'Admin';
        }
        if ($user['admin'] == 3) {
            $class = 'supermoderator';
            $title[] = 'Mod';
        }

        // Set the default avatar if they don't have one
        if (!$user['avatar'] || empty($user['avatar'])) {
            $user['avatar'] = 'avatars/default.png';
        }

        $view = new View('components/username');
        $view->RegisterVariable('user', $user);
        $view->RegisterVariable('banned', (self::IsFrozen($this->id) || mysqli_num_rows($resultBan) > 0));
        $view->RegisterVariable('gangTag', $tag);
        $view->RegisterVariable('gangName', isset($gang['name']) ? $gang['name'] : null);
        $view->RegisterVariable('class', $class);
        $view->RegisterVariable('gangid', $gang['id'] ?? null);
        $view->RegisterVariable('title', $title);
        $this->formattedname = $view->Render(true);
    }

    /*
     * Create a flash alert
     *
     * @param string $type
     * @param string $content
     */
    public static function createFlashAlert(string $type, string $content)
    {
        $flashAlert = [
            'type' => $type,
            'content' => $content,
        ];

        $_SESSION['flash_alert'] = $flashAlert;
    }

    /*
     * Render the alerts view
     */
    public static function renderFlashAlert()
    {
        $flashAlert = null;
        if ($_SESSION['flash_alert']) {
            $flashAlert = $_SESSION['flash_alert'];
        }

        if ($flashAlert) {
            $typeClass = 'errorMessage';
            if ($flashAlert['type'] == 'success') {
                $typeClass = 'successMessage';
            }
            $content = $flashAlert['content'];

            unset($_SESSION['flash_alert']);
        }

        $view = new View('components/flash_alert');
        $view->RegisterVariable('typeClass', $typeClass);
        $view->RegisterVariable('content', $content);

        return $view->Render(true);
    }

    public static function ProcessTag($tag, $colors)
    {
        $str = '';

        $array = str_split($tag);
        $i = 0;
        foreach ($array as $letter) {
            if ($colors[$i] == '') {
                $str = $tag;

                break;
            }

            ++$i;
        }
        $i = 0;
        if ($str == '') {
            foreach ($array as $letter) {
                $str .= '<span style="color:' . $colors[$i] . '">' . $letter . '</span>';
                ++$i;
            }
        }

        return $str;
    }

    public static function IsFrozen($id)
    {
        $res = DBi::$conn->query('SELECT `id` FROM `freezes` WHERE `id`=\'' . $id . '\'');

        return mysqli_num_rows($res) > 0;
    }

    public function IsRespected($mixed = null)
    {
        if (is_null($mixed) && !is_null($this)) {
            $mixed = $this;
        } else {
            if (!is_object($mixed)) {
                $mixed = parent::Get(['rmdays'], self::GetDataTable(), 'id', $mixed);
            }
        }

        return isset($mixed->rmdays) && $mixed->rmdays > 0;
    }

    public function IsAdmin()
    {
        return $this->admin == 1;
    }

    public static function GetAdmins()
    {
        $admins = [];
        $sql = 'select id from ' . self::$dataTable . ' where admin>0';
        $query = DBi::$conn->query($sql);
        while ($row = mysqli_fetch_array($query)) {
            $admins[] = UserFactory::getInstance()->getUser($row['id']);
        }

        return $admins;
    }

    public static function GetAllActiveSinceTime($timestamp)
    {
        return parent::GetAll(['id', 'lastactive', 'level', 'gang'], self::GetDataTable(), '`lastactive` > \'' . $timestamp . '\'', false, false, 'lastactive', 'DESC');
    }

    public static function GetAllUsers()
    {
        return parent::GetAll(['id', 'lastactive', 'level', 'gang'], self::GetDataTable(), '', false, false, 'id', 'ASC');
    }

    public static function GetAllInHospital($page = 0, $search = '')
    {
        $where = "`hospital` > '" . time() . "'";
        if ($search != '') {
            if (is_numeric($search) && $search > 0) {
                $where .= " AND id='" . $search . "'";
            } else {
                $where .= " AND username like '%" . $search . "%'";
            }
        }
        $fields = ['id', 'level', 'hospital', 'hwho', 'hhow', 'hwhoID'];

        return parent::GetAll($fields, self::GetDataTable(), $where, $page, 50, 'hospital', 'ASC');
    }

    public static function GetAllFromIdentifiers(array $ids)
    {
        $objs = [];
        $idField = self::GetIdentifierFieldName();
        $query = 'SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::GetDataTable() . '` WHERE `' . $idField . '` IN (\'' . implode('\', \'', $ids) . '\')';
        $res = DBi::$conn->query($query);
        while ($obj = mysqli_fetch_object($res)) {
            $objs[$obj->$idField] = $obj;
        }

        return $objs;
    }

    public static function GetAllByExp()
    {
        return self::GetAllByFieldLimited1(self::GetDataTableFields(), self::GetDataTable(), 'level` DESC, `exp` DESC, `id', 'ASC', 50);
    }

    public static function GetAllByUserFieldLimited($field = 'id', $order = 'ASC', $limit = 50)
    {
        return self::GetAllByFieldLimited1(self::GetDataTableFields(), self::GetDataTable(), $field, $order, $limit);
    }

    public static function isUserValid($id)
    {
        $query = DBi::$conn->query("SELECT `id` FROM `grpgusers` WHERE `id` = '$id'");
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function sCountWarnings($uid)
    {
        $res = DBi::$conn->query('SELECT count(*) as total FROM `warns` WHERE `uid` = \'' . $uid . '\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function sAddWarning($uid, $reason = 'Rules violation.', $time = null)
    {
        if ($time === null) {
            $time = time();
        }
        DBi::$conn->query('INSERT INTO `warns` (`uid`, `reason`, `time`) VALUES (\'' . $uid . '\', \'' . $reason . '\', \'' . $time . '\')');
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function sCountAllInHospital()
    {
        $res = DBi::$conn->query('SELECT count(`id`) as total FROM `grpgusers` WHERE `hospital` > \'' . time() . '\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function sCountAllInJail()
    {
        $res = DBi::$conn->query('SELECT count(`id`) as total FROM `grpgusers` WHERE `jail` > ' . time());
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }
    //////// gang items



    function Gang_Item($itemid, $userid, $quantity = "1")
    {
        $resultaq = DBi::$conn->query("SELECT * FROM `gangarmory` WHERE `gangid`='$userid' AND `itemid`='$itemid'");
        $worked = mysqli_fetch_array($resultaq);
        $itemexist = mysqli_num_rows($resultaq);
        if ($itemexist == 0) {
            $resultal = DBi::$conn->query("INSERT INTO `gangarmory` (`itemid`, `gangid`, `quantity`)" . "VALUES ('$itemid', '$userid', '$quantity')");
        } else {
            $quantity = $quantity + $worked['quantity'];
            $resultap = DBi::$conn->query("UPDATE `gangarmory` SET `quantity` = '" . $quantity . "' WHERE `gangid`='$userid' AND `itemid`='$itemid'");
        }
    }

    function Gang_tItem($itemid, $userid, $quantity = "1")
    {
        $resultbc = DBi::$conn->query("SELECT * FROM `gangarmory` WHERE `gangid`='$userid' AND `itemid`='$itemid'");
        $worked = mysqli_fetch_array($resultbc);
        $itemexist = mysqli_num_rows($resultbc);
        if ($itemexist != 0) {
            $quantity = $worked['quantity'] - $quantity;
            if ($quantity > 0) {
                $resultbd = DBi::$conn->query("UPDATE `gangarmory` SET `quantity` = '" . $quantity . "' WHERE `gangid`='$userid' AND `itemid`='$itemid'");
            } else {
                $resultbg = DBi::$conn->query("DELETE FROM `gangarmory` WHERE `gangid`='$userid' AND `itemid`='$itemid'");
            }
        }
    }



    //// gang loan

    function Gang_loan($itemid, $userid, $gangid, $quantity = "1")
    {
        $resultaq = DBi::$conn->query("SELECT * FROM `inventory3` WHERE `userid`='$userid' AND `itemid`='$itemid' AND `gangid`='$gangid'");
        $worked = mysqli_fetch_array($resultaq);
        $itemexist = mysqli_num_rows($resultaq);
        if ($itemexist == 0) {
            $resultal = DBi::$conn->query("INSERT INTO `inventory3` (`itemid`, `userid`, `quantity`,`gangid`)" . "VALUES ('$itemid', '$userid', '$quantity','$gangid')");
        } else {
            $quantity = $quantity + $worked['quantity'];
            $resultap = DBi::$conn->query("UPDATE `inventory3` SET `quantity` = '" . $quantity . "' WHERE `userid`='$userid' AND `itemid`='$itemid' AND `gangid`='$gangid'");
        }
    }

    function Take_loan($itemid, $userid, $gangid, $quantity = "1")
    {
        $resultbc = DBi::$conn->query("SELECT * FROM `inventory3` WHERE `userid`='$userid' AND `itemid`='$itemid' AND `gangid`='$gangid'");
        $worked = mysqli_fetch_array($resultbc);
        $itemexist = mysqli_num_rows($resultbc);
        if ($itemexist != 0) {
            $quantity = $worked['quantity'] - $quantity;
            if ($quantity > 0) {
                $resultbd = DBi::$conn->query("UPDATE `inventory3` SET `quantity` = '" . $quantity . "' WHERE `userid`='$userid' AND `itemid`='$itemid'AND `gangid`='$gangid'");
            } else {
                $resultbg = DBi::$conn->query("DELETE FROM `inventory3` WHERE `userid`='$userid' AND `itemid`='$itemid' AND `gangid`='$gangid'");
            }
        }
    }

    public static function get_user_prop($className, $property)
    {
        if (!class_exists($className)) {
            return null;
        }
        if (!property_exists($className, $property)) {
            return null;
        }

        $vars = get_class_vars($className);

        return $vars[$property];
    }

    public static function GetSecurityStar($id)
    {
        if (file_exists('includes/classes/usernames/U' . $id . '.inc.php')) {
            require_once 'includes/classes/usernames/U' . $id . '.inc.php';

            if (method_exists('U' . $id, 'GetSecurityStar')) {
                return call_user_func(['U' . $id, 'GetSecurityStar'], $id);
            }
        }

        return '<i class="fa-solid fa-star" style="color:yellow; font-size: 10px;  vertical-align: top;"></i>';
    }

    public static function Find(User $user, $searchInput, $order = [])
    {
        // Initialize WHERE clause and objects array
        $whereClause = '';
        $objs = [];

        // Build WHERE clause based on search inputs
        if (!empty($searchInput)) {
            $whereClause = ' WHERE ';

            if ($searchInput['exclude'] == '1') {
                $whereClause .= ' `gang` != ' . $user->gang . ' AND ';
            }

            if ($searchInput['gang'] != 0) {
                $whereClause .= ' `gang` = \'' . $searchInput['gang'] . '\' AND ';
            } elseif ($searchInput['gang'] == -1) {
                $whereClause .= ' `gang` = 0 AND ';
            }

            if ($searchInput['money'] != '' && $searchInput['money'] > 0) {
                $whereClause .= ' `money` >= \'' . $searchInput['money'] . '\' AND ';
            }

            if ($searchInput['level'] != '' && $searchInput['level'] > 0) {
                $whereClause .= ' `level` >= \'' . $searchInput['level'] . '\' AND ';
            }

            if ($searchInput['level2'] != '' && $searchInput['level2'] < 1000) {
                $whereClause .= ' `level` <= \'' . $searchInput['level2'] . '\' AND ';
            }

            if ($searchInput['online'] == '1') {
                $whereClause .= ' `lastactive` > ' . (time() - 900) . ' AND ';
            } elseif ($searchInput['online'] == '0') {
                $whereClause .= ' `lastactive` < ' . (time() - 900) . ' AND ';
            }

            if ($searchInput['prisonS'] != 0) {
                $whereClause .= ' `city` = \'' . $searchInput['prisonS'] . '\' AND ';
            }

            if ($searchInput['username'] != '') {
                $whereClause .= ' `username` LIKE "%' . $searchInput['username'] . '%" AND ';
            }
        }

        // Remove the trailing 'AND' from WHERE clause
        $whereClause = rtrim($whereClause, 'AND ');

        // Build the complete query
        $query = 'SELECT `id`, `money`, `username`, `level`, `lastactive` FROM `grpgusers` ' . $whereClause;

        // Add additional conditions based on attack input
        if ($searchInput['attack'] == '0') {
            $query .= ($whereClause == '') ? ' WHERE ' : ' AND ';
            $query .= ' (`gang`=29 OR `jail` >= \'' . time() . '\' OR hospital > \'' . time() . '\' or gprot>\'' . time() . '\') ';
        } elseif ($searchInput['attack'] == '1') {
            $query .= ($whereClause == '') ? ' WHERE ' : ' AND ';
            $query .= ' jail <= \'' . time() . '\' AND hospital <= \'' . time() . '\' AND gprot<=\'' . time() . '\' AND (`gang`<>29)';
        }

        // Add ORDER BY clause
        if (!empty($order['by'])) {
            $query .= ' ORDER BY ' . $order['by'] . ' ' . $order['sort'];
        } else {
            $query .= ' ORDER BY `money` DESC';
        }

        // Execute the query and fetch results
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function FindById($uid)
    {
        try {
            $user_search = UserFactory::getInstance()->getUser($uid);

            return $user_search;
        } catch (SoftException $e) {
            // We do not show any error, it just means the user does not exist.
        }

        return null;
    }

    public static function Exists($uid)
    {
        $uid = (int) $uid;

        if (empty($uid)) {
            return false;
        }
        $res = DBi::$conn->query('SELECT 1 FROM `grpgusers` WHERE `id`=' . $uid . '');

        return mysqli_num_rows($res) == 1;
    }

    public static function NameExists($username)
    {
        if ($username == '') {
            return false;
        }
        $query = 'SELECT `id` FROM `grpgusers` WHERE `username`="' . $username . '"';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return false;
        }

        return true;
    }

    public static function IsSubscriber($uid)
    {
        $sub = Subscription::GetActive($uid);
        if ($sub && $sub->State == 1) {
            return true;
        }

        return false;
    }

    public static function GetStats($uid)
    {
        $res = DBi::$conn->query('SELECT (`strength`+`defense`+`speed`) AS total FROM `grpgusers` WHERE `id`=' . $uid);
        $arr = mysqli_fetch_array($res);

        return $arr['total'];
    }

    public static function GetAllByStats()
    {
        $sig50 = time() - 50 * 24 * 60 * 60;
        $objs = [];
        $res = DBi::$conn->query('SELECT strength+defense+speed as total, `id`, `level`, `money`, `lastactive`, `gang` FROM `grpgusers` where lastactive>' . $sig50 . ' AND id not in (SELECT `id` FROM `bans`)  AND admin=0 ORDER BY total DESC LIMIT 50');
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function sSendPmail($to, $from, $timesent, $subject, $text, $box = 1, $blocked = 0)
    {
        if ($subject == '') {
            throw new FailedResult(USER_CANT_PMAIL_EMPTY_SUBJECT);
        } elseif ($text == '') {
            throw new FailedResult(USER_CANT_PMAIL_EMPTY_TEXT);
        }

        return Pms::Add($to, $from, $timesent, $subject, $text, $box, $blocked);
    }

    public static function generateResetBitCode()
    {
        $dateTime = new \DateTime();
        $resetBitCode = mt_rand(1, 1000);
        $resetBitCode .= md5(Utility::SmartEscape($dateTime->format('YdmHis')));
        $resetBitCode .= mt_rand(1, 10);

        return $resetBitCode;
    }

    public static function SGetValidationCode($uid)
    {
        $query = 'SELECT `validC` from `grpgusers` where `id`=' . $uid;
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            throw new SoftException('User does not exist !');
        }
        $arr = mysqli_fetch_array($res);

        return $arr['validC'];
    }

    public static function SSetValidationCode($uid, $code)
    {
        $query = 'UPDATE `grpgusers` SET `validC`=' . $code . ' WHERE `id`=' . $uid;
        DBi::$conn->query($query);
        if (DBi::$conn->affected_rows == 0) {
            throw new SoftException(USER_CONFIRMATION_ERROR);
        }

        return true;
    }

    public static function GetPayment($uid)
    {
        $res = DBi::$conn->query('SELECT `amountM` FROM `pagamentos` WHERE `for`=' . $uid);
        $arr = mysqli_fetch_array($res);
        if ($arr['amountM'] == '') {
            return 0;
        }

        return $arr['amountM'];
    }

    public static function SGetLevel($uid)
    {
        $res = DBi::$conn->query('SELECT `level` FROM `grpgusers` WHERE `id`=' . $uid);
        $arr = mysqli_fetch_array($res);

        return $arr['level'];
    }

    public static function GetGangMembers($gangid, $rank = null)
    {
        $objs = [];
        $query = 'SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::GetDataTable() . '` WHERE `gang` = \'' . $gangid . '\'';

        if ($rank !== null && is_array($rank)) {
            $query .= ' AND `id_rank` IN (\'' . @implode("','", $rank) . '\')';
        } elseif ($rank !== null) {
            $query .= ' AND `id_rank`=\'' . $rank . '\'';
        }

        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetBestXPGangMember($gangid)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::GetDataTable() . '` WHERE `gang` = \'' . $gangid . '\' ORDER BY `exp` DESC LIMIT 1');
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function GetGangMembersUnderXP($gangid, $xp)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::GetDataTable() . '` WHERE `gang` = \'' . $gangid . '\' AND `exp` < \'' . $xp . '\'');
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetGangMembersUnderLevel($gangid, $xp)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::GetDataTable() . '` WHERE `gang` = \'' . $gangid . '\' AND `level` < \'' . $xp . '\'');
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetGangMembersByRankAndXp($gangid)
    {
        $sql = 'SELECT u.*, ifnull(gp.permorder,999999) as upo FROM ' . self::GetDataTable() . ' u LEFT JOIN ' . GangPermission::$dataTable . ' gp ON gp.name_rank = u.id_rank AND gp.id_gang = u.gang WHERE u.`gang` = \'' . $gangid . '\' GROUP BY u.id ORDER BY upo ASC, u.level DESC, u.exp desc';

        return parent::GetPaginationResults($sql);

        //return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`gang` = \'' .$gangid. '\'', false, false, 'id_rank` DESC, `exp', 'DESC');
    }

    public static function GetGangMembersByXp($gangid)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`gang` = \'' . $gangid . '\'', false, false, 'level', 'DESC');
    }

    public static function GetGangMembersByLevel($gangid, $order = 'DESC')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`gang` = \'' . $gangid . '\'', false, false, 'level', $order);
    }

    public static function GetGangMembersByLevelTotalStats($gangid)
    {
        $order = '';

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`gang` = \'' . $gangid . '\'', false, false, ' level desc, (strength+defense+speed) desc', $order, false, false);
    }

    public static function GetGangMembersByTotalStats($gangid, $order = 'DESC')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`gang` = \'' . $gangid . '\'', false, false, '(strength+defense+speed)', $order, false, false);
    }

    public static function GetGangMembersByItem($gangid, $item, $order = 'DESC')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`gang` = \'' . $gangid . '\'', false, false, $item, $order);
    }

    public static function GetTotalUsersInGang($gangId)
    {
        $res = DBi::$conn->query('SELECT count(`id`) as total FROM `grpgusers` WHERE `gang`=\'' . $gangId . '\'');
        $arr = mysqli_fetch_array($res);

        return $arr['total'];
    }

    public static function GetTotalOnlineUsersInGang($gangId)
    {
        $res = DBi::$conn->query('SELECT count(`id`) as total FROM `grpgusers` WHERE `gang`=\'' . $gangId . '\'  AND `lastactive`> ' . (time() - 900));
        $arr = mysqli_fetch_array($res);

        return $arr['total'];
    }

    public static function sIsAdmin($uid)
    {
        $user = parent::Get(self::GetDataTableFields(), self::GetDataTable(), 'id', $uid);
        if ($user === null) {
            return false;
        }
        if ($user->admin == 1) {
            return true;
        }

        return false;
    }

    public static function SGetFormattedName($id, $detail = 'High')
    {
        try {
            $a = UserFactory::getInstance()->getUser($id);

            return $a->formattedname;
        } catch (SoftException $e) {
            return '';
        }
    }

    public static function GetAvatarUrl($id)
    {
        try {
            $user = UserFactory::getInstance()->getUser($id);
            if ($user->avatar && !empty($user->avatar)) {
                return $user->avatar;
            }
        } catch (SoftException $e) {
            return 'avatars/default.png';
        }

        return 'avatars/default.png';
    }

    public static function SGetBank($uid)
    {
        $res = DBi::$conn->query('SELECT `whichbank` FROM `grpgusers` WHERE `id`=' . $uid);
        $arr = mysqli_fetch_array($res);

        return $arr['whichbank'];
    }

    public static function SAddMoney($uid, $amount)
    {
        return DBi::$conn->query('UPDATE `grpgusers` SET `money`=(`money`+' . $amount . ') WHERE `id`=\'' . $uid . '\'');
    }

    public static function SAddPoints($uid, $amount)
    {
        return DBi::$conn->query('UPDATE `grpgusers` SET `points`=(`points`+' . $amount . ') WHERE `id`=\'' . $uid . '\'');
    }

    public static function SAddBankMoney($uid, $amount, $checkMaxLimit = true)
    {
        /* try{
        if($amount>500000000)
            throw new Exception();
        }
        catch(Exception $e)
        {
            $fp=fopen("Hackslack.txt","a+");
            fwrite($fp,"User:".$uid.":\n");
                fwrite($fp,$e->getTraceAsString());
            fwrite($fp,"\n\n");
            fclose($fp);
        }*/
        if ($checkMaxLimit) {
            $currentmoney = self::SGetBankedMoney($uid);

            $maxBank = self::sGetBankLimit($uid);

            if ($currentmoney + $amount > $maxBank) {
                $max = $maxBank - $currentmoney;

                if ($max < 0) {
                    $max = 0;
                }
                $amount = $amount - $max;

                $query = 'UPDATE `grpgusers` SET `bank`=(`bank`+' . $max . '), `money`=(`money`+' . $amount . ') WHERE `id`=\'' . $uid . '\'';

                return DBi::$conn->query($query);
            }
        }

        $query = 'UPDATE `grpgusers` SET `bank`=(`bank`+' . $amount . ') WHERE `id`=\'' . $uid . '\'';

        return DBi::$conn->query($query);
    }

    public static function SGetBankedMoney($uid)
    {
        $res = DBi::$conn->query('SELECT `bank` FROM `grpgusers` WHERE `id`=' . $uid);
        $arr = mysqli_fetch_array($res);

        return $arr['bank'];
    }

    public static function SAddRealMoney($uid, $amount)
    {
        return DBi::$conn->query('UPDATE `grpgusers` SET `realmoney`=(`realmoney`+' . $amount . ') WHERE `id`=\'' . $uid . '\'');
    }

    public static function SRemoveMoney($uid, $amount)
    {
        $oldAmount = User::SGetMoney($uid);
        $newAmount = $oldAmount - $amount;
        if ($newAmount < 0) {
            return User::SSetMoney($uid, 0);
        }

        return DBi::$conn->query('UPDATE `grpgusers` SET `money`=(`money`-' . $amount . ') WHERE `id`=\'' . $uid . '\'');
    }

    public static function SGetMoney($uid)
    {
        $res = DBi::$conn->query('SELECT `money` FROM `grpgusers` WHERE `id`=' . $uid);
        $arr = mysqli_fetch_array($res);

        return $arr['money'];
    }

    public static function SSetMoney($uid, $amount)
    {
        return DBi::$conn->query('UPDATE `grpgusers` SET `money`=\'' . $amount . '\' WHERE `id`=\'' . $uid . '\'');
    }

    public static function SRemoveBankMoney($uid, $amount)
    {
        $oldAmount = User::SGetBankedMoney($uid);
        $newAmount = $oldAmount - $amount;
        if ($newAmount < 0) {
            return User::SSetBank($uid, 0);
        }
        $query = 'UPDATE `grpgusers` SET `bank`=(`bank`-' . $amount . ') WHERE `id`=\'' . $uid . '\' AND `bank`>=\'' . $amount . '\'';
        DBi::$conn->query($query);
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function SSetBank($uid, $amount)
    {
        return DBi::$conn->query('UPDATE `grpgusers` SET `bank`=\'' . $amount . '\' WHERE `id`=\'' . $uid . '\'');
    }

    public static function AddShares($stock, $userid, $quantity = 1)
    {
        $ownedShares = User::SGetShareQuantity($stock, $userid);

        if ($ownedShares == 0) {
            DBi::$conn->query('INSERT INTO `shares` (`companyid`, `userid`, `amount`) VALUES (\'' . $stock . '\', \'' . $userid . '\', \'' . $quantity . '\')');
        } else {
            $quantity = $quantity + $ownedShares;
            DBi::$conn->query('UPDATE `shares` SET `amount` = \'' . $quantity . '\' WHERE `userid` = \'' . $userid . '\' AND `companyid` = \'' . $stock . '\'');
        }
    }
    public static function AddCoins($stock, $userid, $quantity = 1)
    {
        $ownedShares = User::SGetCoinQuantity($stock, $userid);

        if ($ownedShares == 0) {
            DBi::$conn->query('INSERT INTO `coins` (`companyid`, `userid`, `amount`) VALUES (\'' . $stock . '\', \'' . $userid . '\', \'' . $quantity . '\')');
        } else {
            $quantity = $quantity + $ownedShares;
            DBi::$conn->query('UPDATE `coins` SET `amount` = \'' . $quantity . '\' WHERE `userid` = \'' . $userid . '\' AND `companyid` = \'' . $stock . '\'');
        }
    }

    public static function SGetCoinQuantity($stock, $userid)
    {
        $res = DBi::$conn->query('SELECT `amount` FROM `coins` WHERE `userid`=\'' . $userid . '\' AND `companyid`=\'' . $stock . '\'');

        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['amount'];
    }
    public static function SRemoveCoins($stock, $userid, $quantity = '1')
    {
        $ownedShares = User::SGetCoinQuantity($stock, $userid);

        $quantity = $ownedShares - $quantity;
        if ($quantity > 0) {
            DBi::$conn->query('UPDATE `coins` SET `amount` = \'' . $quantity . '\' WHERE `userid`=\'' . $userid . '\' AND `companyid`=\'' . $stock . '\'');
        } else {
            DBi::$conn->query('DELETE FROM `coins` WHERE `userid`=\'' . $userid . '\' AND `companyid`=\'' . $stock . '\'');
        }
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }


    public static function SGetShareQuantity($stock, $userid)
    {
        $res = DBi::$conn->query('SELECT `amount` FROM `shares` WHERE `userid`=\'' . $userid . '\' AND `companyid`=\'' . $stock . '\'');

        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['amount'];
    }

    public static function SRemoveShares($stock, $userid, $quantity = '1')
    {
        $ownedShares = User::SGetShareQuantity($stock, $userid);

        $quantity = $ownedShares - $quantity;
        if ($quantity > 0) {
            DBi::$conn->query('UPDATE `shares` SET `amount` = \'' . $quantity . '\' WHERE `userid`=\'' . $userid . '\' AND `companyid`=\'' . $stock . '\'');
        } else {
            DBi::$conn->query('DELETE FROM `shares` WHERE `userid`=\'' . $userid . '\' AND `companyid`=\'' . $stock . '\'');
        }
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function GetTotalUsersInCity($city)
    {
        $res = DBi::$conn->query('SELECT count(id) as total FROM `grpgusers` WHERE `city`=\'' . $city . '\'');
        $arr = mysqli_fetch_array($res);

        return $arr['total'];
    }

    public static function SAddLand($city, $userid, $quantity = 1)
    {
        $ownedLand = User::SGetLandQuantity($city, $userid);

        if ($ownedLand == 0) {
            DBi::$conn->query('INSERT INTO `land` (`city`, `userid`, `amount`) VALUES (\'' . $city . '\', \'' . $userid . '\', \'' . $quantity . '\')');
        } else {
            $quantity = $quantity + $ownedLand;
            DBi::$conn->query('UPDATE `land` SET `amount` = \'' . $quantity . '\' WHERE `userid`=\'' . $userid . '\' AND `city`=\'' . $city . '\'');
        }
    }

    public static function SGetLandQuantity($city, $userid)
    {
        $res = DBi::$conn->query('SELECT `amount` FROM `land` WHERE `userid`=\'' . $userid . '\' AND `city`=\'' . $city . '\'');
        if ($res->num_rows == 0) {
            return 0;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['amount'];
    }

    public static function SRemoveLand($city, $userid, $quantity = '1')
    {
        $ownedLand = User::SGetLandQuantity($city, $userid);

        $quantity = $ownedLand - $quantity;
        if ($quantity > 0) {
            DBi::$conn->query('UPDATE `land` SET `amount` = \'' . $quantity . '\' WHERE `userid`=\'' . $userid . '\' AND `city`=\'' . $city . '\'');
        } elseif ($quantity == 0) {
            DBi::$conn->query('DELETE FROM `land` WHERE `userid`=\'' . $userid . '\' AND `city`=\'' . $city . '\'');
        } else {
            return false;
        }
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function SAddItems($itemid, $userid, $quantity = 1, $borrowed = 0, $awake = 0)
    {
        $ownedItems = User::SGetItemQuantity($itemid, $userid, $borrowed, $awake);
        if ($ownedItems == 0) {
            $data = [
                'itemid' => $itemid,
                'userid' => $userid,
                'quantity' => $quantity,
                'borrowed' => $borrowed,
                'awake' => $awake,
            ];

            parent::AddRecords($data, 'inventory');
        } else {
            $quantity = $quantity + $ownedItems;
            $data = [
                'quantity' => $quantity,
            ];
            $cond = [
                'itemid' => $itemid,
                'userid' => $userid,
                'borrowed' => $borrowed,
                'awake' => $awake,
            ];
            User::sUpdate('inventory', $data, $cond);
        }
    }

    public static function SGetItemQuantity($itemid, $userid, $borrowed = 0, $awake = 0)
    {
        $res = DBi::$conn->query('SELECT `quantity` FROM `inventory` WHERE `userid`=\'' . $userid . '\' AND `itemid`=\'' . $itemid . '\' AND `borrowed`=\'' . $borrowed . '\'');
        if ($res->num_rows == 0) {
            return 0;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['quantity'];
    }

    public static function SRemoveItems($itemid, $userid, $quantity = 1, $borrowed = 0, $awake = 0)
    {
        $ownedItems = User::SGetItemQuantity($itemid, $userid, $borrowed, $awake);

        $quantity = $ownedItems - $quantity;
        if ($quantity > 0) {
            DBi::$conn->query('UPDATE `inventory` SET `quantity` = \'' . $quantity . '\' WHERE `userid`=\'' . $userid . '\' AND `itemid`=\'' . $itemid . '\' AND `borrowed`=\'' . $borrowed . '\'');
        } elseif ($quantity == 0) {
            DBi::$conn->query('DELETE FROM `inventory` WHERE `userid`=\'' . $userid . '\' AND `itemid`=\'' . $itemid . '\' AND `borrowed`=\'' . $borrowed . '\'');
        } else {
            return false;
        }
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        if ($awake > 0) {
            try {
                $user = UserFactory::getInstance()->getUser($userid);
                if ($user->awake > $user->GetMaxAwake()) {
                    $user->SetAttribute('awake', $user->GetMaxAwake());
                }
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    public static function GetFreezeReason($id)
    {
        $res = DBi::$conn->query('SELECT `reason` FROM `freezes` WHERE `id`=\'' . $id . '\'');
        $arr = mysqli_fetch_array($res);

        return $arr['reason'];
    }

    public static function GetUnfreezeTime($id)
    {
        $res = DBi::$conn->query('SELECT `melt_time` FROM `freezes` WHERE `id`=\'' . $id . '\'');

        return mysqli_result($res, 0, 0);
    }

    public static function GetBanReason($id)
    {
        $res = DBi::$conn->query('SELECT `reason` FROM `bans` WHERE `id`=\'' . $id . '\'');
        $arr = mysqli_fetch_array($res);

        return $arr['reason'];
    }

    public static function GetCurrentLevel($exp)
    {
        //return $this->level;
        $a = 0;

        for ($x = 1; $x < MAX_LVL; ++$x) {
            $a = floor(($x * 1000) + (pow($x, 3)));

            if ($a > $exp) {
                return $x;
            }
        }

        return $x;
    }

    public static function GetBestInmatesInCity($city_id)
    {
        return parent::GetAll(['id', 'user_id', 'level'], 'topusers', '`city_id`=\'' . $city_id . '\'', false, false, 'score', 'DESC');
    }

    public static function ResetAllRatings()
    {
        DBi::$conn->query('TRUNCATE TABLE `user_rates`');
    }

    public static function GetAllUserFields($dataFields, $whereClause = '', $orderBy = false, $dir = 'ASC')
    {
        return parent::GetAll($dataFields, self::GetDataTable(), $whereClause, false, false, $orderBy, $dir);
    }

    public static function UpdateRank($id, $rank)
    {
        $sql = 'UPDATE ' . self::GetDataTable() . ' SET id_rank = \'' . $rank . '\' WHERE id = \'' . $id . '\'';
        $res = DBi::$conn->query($sql);

        return true;
    }

    /*
     * Rank-related methods
     */

    public static function SGetName($id)
    {
        return MySQL::GetSingle('SELECT username FROM ' . self::GetDataTable() . ' WHERE id = \'' . $id . '\'');
    }

    /*
     * Level-related methods
     */

    public static function CountJointAttacks($userId)
    {
        $query = 'SELECT count(`defender`) FROM ganglog WHERE defender = \'' . $userId . '\' AND `timestamp` > \'' . (time() - DAY_SEC) . '\' AND jointattack = 1';

        return MySQL::GetSingle($query);
    }

    public static function SGetFields($id, $field)
    {
        switch (strtolower($field)) {
            case 'lastactiveicon':
                $member = self::XGet(['lastactive'], self::GetDataTable(), 'id=\'' . $id . '\'');
                if (empty($member)) {
                    return;
                }

                $activeTitle = Utility::GetDaysPassedSince($member->lastactive) . ' ago';
                if ((time() - $member->lastactive) < 900) {
                    $formattedonline = '<img title="' . $activeTitle . '" src="images/icons/15m.png">';
                } elseif ((time() - $member->lastactive) < DAY_SEC) {
                    $formattedonline = '<img title="' . $activeTitle . '" src="images/icons/24h.png">';
                } elseif ((time() - $member->lastactive) < 3 * DAY_SEC) {
                    $formattedonline = '<img title="' . $activeTitle . '" src="images/icons/3d.png">';
                } elseif ((time() - $member->lastactive) < 7 * DAY_SEC) {
                    $formattedonline = '<img title="' . $activeTitle . '" src="images/icons/7d.png">';
                } else {
                    $formattedonline = '<img title="' . $activeTitle . '" src="images/icons/over.png">';
                }

                return $formattedonline;
                break;
        }

        ///$obj = self::XGet($fields, )
    }

    public function GetField($field)
    {
        if (!isset($this->$field) || $this->$field !== null) {
            switch ($field) {
                case 'formattedHP':
                    return $this->hp . ' / ' . $this->GetMaxHP() . ' [' . Utility::GetPercent($this->hp, $this->GetMaxHP()) . '%]';
                    break;

                case 'formattedAwake':
                    return $this->awake . ' / ' . $this->GetMaxAwake() . ' [' . Utility::GetPercent($this->awake, $this->GetMaxAwake()) . '%]';
                    break;

                case 'formattedNerve':
                    return $this->nerve . ' / ' . $this->GetMaxNerve() . ' [' . Utility::GetPercent($this->nerve, $this->GetMaxNerve()) . '%]';
                    break;

                case 'formattedEnergy':
                    return $this->energy . ' / ' . $this->GetMaxEnergy() . ' [' . Utility::GetPercent($this->energy, $this->GetMaxEnergy()) . '%]';
                    break;

                case 'age':
                    $this->$field = Utility::GetDaysPassedSince($this->signuptime);
                    break;

                case 'totalCrimes':
                    $this->$field = $this->crimesucceeded + $this->crimefailed;
                    break;

                case 'totalBattles':
                    $this->$field = $this->battlewon + $this->battlelost;
                    break;

                case 'formattedLastActive':
                    $this->$field = Utility::GetDaysPassedSince($this->lastactive) . ' ago';
                    break;

                case 'onlineStatus':
                    $this->$field = self::GetOnlineStatus($this->lastactive);
                    break;
            }
        }

        return $this->$field;
    }

    public function GetMaxHP()
    {
        $maxHp = $this->level * 50;

        if ($this->hasPerkActivated('DURABILITY_NAME')) {
            $maxHp = $maxHp + ceil(($maxHp / 100 * 10));
        }


        return $maxHp;
    }
    public function GetEnergyDrink()
    {
        $query = DBi::$conn->query("SELECT energy_drink FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['energy_drink'];
        } else {
            return 0;
        }
    }
    public function GetEnergyDrinkTime()
    {
        $query = DBi::$conn->query("SELECT energy_drink_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['energy_drink_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    public function GetNerveDrink()
    {
        $query = DBi::$conn->query("SELECT nerve_drink FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['nerve_drink'];
        } else {
            return 0;
        }
    }
    public function GetNerveDrinkTime()
    {
        $query = DBi::$conn->query("SELECT nerve_drink_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['nerve_drink_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetAwakeDrink()
    {
        $query = DBi::$conn->query("SELECT awake_drink FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['awake_drink'];
        } else {
            return 0;
        }
    }
    public function GetAwakeDrinkTime()
    {
        $query = DBi::$conn->query("SELECT awake_drink_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['awake_drink_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetChristmasGift()
    {
        $query = DBi::$conn->query("SELECT christmas_gift FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['christmas_gift'];
        } else {
            return 0;
        }
    }
    public function GetChristmasGiftTime()
    {
        $query = DBi::$conn->query("SELECT christmas_gift_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['christmas_gift_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetChristmasCrackerTime()
    {
        $query = DBi::$conn->query("SELECT christmas_cracker_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['christmas_cracker_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetCosmoCocktailCount()
    {
        $query = DBi::$conn->query("SELECT cosmo_cocktail_count FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['cosmo_cocktail_count'];
        } else {
            return 0;
        }
    }

    public function GetGymProteinBarTime()
    {
        $query = DBi::$conn->query("SELECT protein_bar_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['protein_bar_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetGymGreensPillTime()
    {
        $query = DBi::$conn->query("SELECT greens_pill_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['greens_pill_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetGymSuperPillTime()
    {
        $query = DBi::$conn->query("SELECT super_pill_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['super_pill_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetLovePotionsCount()
    {
        $query = DBi::$conn->query("SELECT love_potions FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['love_potions'];
        } else {
            return 0;
        }
    }

    public function GetDailyLovePotionsCount()
    {
        $query = DBi::$conn->query("SELECT daily_love_potions FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['daily_love_potions'];
        } else {
            return 0;
        }
    }

    public function GetPerfumeCount()
    {
        // Test
        $query = DBi::$conn->query("SELECT perfume_count FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['perfume_count'];
        } else {
            return 0;
        }
    }

    public function GetLovePotionTime()
    {
        $query = DBi::$conn->query("SELECT love_potions_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['love_potions_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetCureVialTime()
    {
        $query = DBi::$conn->query("SELECT cure_vial_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['cure_vial_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetPoliceBadgeTime()
    {
        $query = DBi::$conn->query("SELECT police_badge_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['police_badge_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetFungalVialTime()
    {
        $query = DBi::$conn->query("SELECT fungal_vial_time FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if ($query == true) {
            if (mysqli_num_rows($query) > 0) {
                $row = mysqli_fetch_array($query);
                return $row['fungal_vial_time'];
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function GetTrainingDummyTokensCount()
    {
        $query = DBi::$conn->query("SELECT training_dummy_tokens FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['training_dummy_tokens'];
        } else {
            return 0;
        }
    }

    public function GetChocolateBunnyCount()
    {
        $query = DBi::$conn->query("SELECT chocolate_bunny_count FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['chocolate_bunny_count'];
        } else {
            return 0;
        }
    }

    public function GetBossTokensCount()
    {
        $query = DBi::$conn->query("SELECT boss_token_count FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            return $row['boss_token_count'];
        } else {
            return 0;
        }
    }

    public function GetMaxAwake($includeBonusAwake = true)
    {
        $awake = 100;
        if ($this->HasCell()) {
            $awake = $this->GetCell()->awake;
        } elseif ($this->HasLoanedCell()) {
            $awake = $this->GetLoanedCell()->awake;
        }
        if ($this->awake_time > time()) {
            $awake = $awake * 2;
        }
        if ($this->GetGang()->house) {
            $s = DBi::$conn->query("SELECT * FROM ganghouse WHERE id = " . $this->GetGang()->house);
            $r = mysqli_fetch_assoc($s);
            $per = ($awake / 100) * 2;
            $awake = $awake + $per;
        }

        if (!$includeBonusAwake) {
            return $awake;
        }


        //$bonusAwake = MySQL::GetSingle('SELECT SUM(awake) FROM `inventory` WHERE `userid` = \'' . $this->id . '\' AND `borrowed`=\'0\'');
//       $bonusAwake= MySQL::GetSingle("SELECT sum(i.quantity * it.awake) from inventory i LEFT JOIN items it on i.itemid = it.id WHERE i.userid = ".$this->id." AND it.awake > 0");
        $bonusAwake = MySQL::GetSingle("SELECT sum(i.quantity * it.awake) FROM inventory i LEFT JOIN items it ON i.itemid = it.id WHERE i.userid = " . $this->id . " AND it.awake > 0 AND it.id IN (" . implode(',', Item::getHIItemIds()) . ")");
        if ($bonusAwake > 0) {
            $bonusAwake *= $this->GetExpMultiplier();
            $awake += $bonusAwake;
        }
        if ($this->GetAwakeDrinkTime() > time()) {
            //20% awake bonus
            $awake = $awake + ($awake / 100) * 10;
        }

        if ($this->gang) {
            $gang = $this->GetGang();

            $awake += $gang->getGangCompoundTypeAwakeBonus();

        }

        return (int) $awake;
    }

    /*
     * Gang related methods
     */

    public function HasCell()
    {
        return $this->house > 0;
    }

    public function GetCell()
    {
        if (!isset($this->houseobj) || $this->houseobj === null) {
            $this->houseobj = new House($this->house);
        }

        return $this->houseobj;
    }

    public function HasLoanedCell()
    {
        return !empty($this->loanhouse);
    }

    public function GetLoanedCell()
    {
        if (!isset($this->loanhouseobj) || $this->loanhouseobj === null) {
            try {
                $gangHouse = new GangCell($this->loanhouse);
            } catch (Exception $e) {
                $this->SetAttribute('loanhouse', 0);
            }
            $this->loanhouseobj = $gangHouse->GetCell();
        }

        return $this->loanhouseobj;
    }

    public function GetExpMultiplier()
    {
        $query = DBi::$conn->query("SELECT xpboost FROM temp_items_use WHERE userid = '" . $this->id . "'");
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_array($query);
            if (time() < $row['xpboost']) {
                return 1.1;
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }
    public function SeasonPass()
    {
        $query = DBi::$conn->query("SELECT * FROM seasonpass WHERE userid = '" . $this->id . "' AND SeasonPass_own > 0");
        if (mysqli_num_rows($query) > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function SeasonTier()
    {
        $query = DBi::$conn->query("SELECT SeasonPass_tier FROM seasonpass WHERE userid = '" . $this->id . "'")->fetch_object();
        return $query->SeasonPass_tier;

    }
    public function GetMaxNerve()
    {
        $nervadjust = 0;
        if ($this->GetSkill(7)) {
            $nervadjust = 25 * $this->GetSkill(7)->level;
        }
        $maxNerve = ($this->level + 4) + $nervadjust;
        if ($maxNerve > (300 + $nervadjust)) {
            $maxNerve = (300 + $nervadjust);
        }

        $effectsOnUser = Drug::GetEffectsOnUser($this->id);
        if (count($effectsOnUser) > 0) {
            foreach ($effectsOnUser as $effect) {
                if ($effect->item->nerve_boost > 0) {
                    $maxNerve += ceil(($maxNerve / 100 * $effect->item->nerve_boost));
                }
            }
        }
        if ($this->energy_time > time()) {
            $maxNerve = $maxNerve * 2;
        }
        if ($this->GetNerveDrinkTime() > time()) {
            //max nerve + 20%
            $maxNerve = $maxNerve + ($maxNerve / 100) * 10;
        }

        if ($maxNerve > 400) {
            $maxNerve = 400;
        }

        return (int) $maxNerve;
    }

    public function GetMaxEnergy()
    {
        $maxEnergy = $this->level + 9;
        if ($this->rmdays > 0) {
            $maxEnergy = floor($maxEnergy * 1.5);
        }

        $effectsOnUser = Drug::GetEffectsOnUser($this->id);
        if (count($effectsOnUser) > 0) {
            foreach ($effectsOnUser as $effect) {
                if ($effect->item->energy_boost > 0) {
                    $maxEnergy += floor(($maxEnergy / 100 * $effect->item->energy_boost));
                }
            }
        }
        if ($this->GetEnergyDrinkTime() > time()) {
            //20% nerve bonus
            $maxEnergy = $maxEnergy + ($maxEnergy / 100) * 10;
        }
        return (int) $maxEnergy;
    }

    public static function GetOnlineStatus($lastActive)
    {
        if (time() - $lastActive < 900) {
            return '<font style="color:green;padding:2px;font-weight:bold;">Online</font>';
        }

        return '<font style="color:red;padding:2px;font-weight:bold;">Offline</font>';
    }

    public function addExpBonus($time, $exp)
    {
        if ($this->albuns_exp_time > time()) {
            $this->AddToAttribute('albuns_exp_time', $time * 60);
        } else {
            $this->SetAttribute('albuns_exp_time', time() + $time * 60);
        }
        $this->SetAttribute('albuns_exp_bonus', $exp);
    }

    /**
     *over riding the AddToAttribute so that check max point limit before adding the points.
     */
    public function AddToAttribute($attrName, $value, $max = null)
    {
        if ($attrName == 'points') {
            return $this->AddPoints($value);
        }
        if ($attrName == 'bank') {
            return $this->AddBankMoney($value);
        }

        return parent::AddToAttribute($attrName, $value, $max);
    }

    public function AddPoints($points)
    {
        $currentpoints = $this->points;

        if ($currentpoints + $points > MAX_POINTS) {
            $max = MAX_POINTS - $currentpoints;
            if ($max > 0 && $max <= $points) {
                $result = parent::AddToAttribute('points', $max);
            }

            if ($max < 0) {
                $max = 0;
            }

            DBi::$conn->query('UPDATE server_variables SET value = value + ' . ($points - $max) . ' WHERE field = \'pointsLottery\'');

            throw new FailedResult(sprintf(USER_POINTS_MAX_ERROR, number_format(MAX_POINTS)), 'POINTS_ERR|' . (MAX_POINTS - $currentpoints));
        }
        $result = parent::AddToAttribute('points', $points);

        return $result;
    }

    public function AddBankMoney($money, $checkMaxLimit = true)
    {
        if ($checkMaxLimit) {
            $currentmoney = $this->bank;

            $maxBank = $this->GetBankLimit();

            if ($currentmoney + $money > $maxBank) {
                $max = $maxBank - $currentmoney;
                if ($max > 0 && $max <= $money) {
                    $result = parent::AddToAttribute('bank', $max);
                }

                if ($max < 0) {
                    $max = 0;
                }

                return parent::AddToAttribute('money', $money - $max);
            }
        }

        return parent::AddToAttribute('bank', $money);
    }

    public function GetBankLimit()
    {
        return self::sGetBankLimit($this->id);
    }

    public static function sGetBankInterestPaymentLimit($userId)
    {
        $bankuser = new User($userId);

        $defaultMaxBank = MAX_BANK_INTEREST;
        if (UserBooks::UserHasStudied($userId, 41)) {
            $defaultMaxBank = 6000000;
        }
        if (UserBooks::UserHasStudied($userId, 42)) {
            $defaultMaxBank = 15000000;
        }
        if (UserBooks::UserHasStudied($userId, 43)) {
            $defaultMaxBank = 30000000;
        }

        return $defaultMaxBank;
    }

    public static function sGetBankLimit($userId)
    {
        $bankuser = new User($userId);
        $bonus = 0;
        if ($bankuser->securityLevel > 0) {
            $bonus = 200000000 * $bankuser->securityLevel;
        }
        $skill = self::sGetSkill($userId, SK_CORRUPT_WARDEN_ID);

        $defaultMaxBank = DEFAULT_MAX_BANK;
        if (UserBooks::UserHasStudied($userId, 34)) {
            // 250%
            $defaultMaxBank = $defaultMaxBank + (($defaultMaxBank / 100) * 250);
        } else if (UserBooks::UserHasStudied($userId, 33)) {
            // 150%
            $defaultMaxBank = $defaultMaxBank + (($defaultMaxBank / 100) * 150);
        } else if (UserBooks::UserHasStudied($userId, 32)) {
            // 50%
            $defaultMaxBank = $defaultMaxBank + (($defaultMaxBank / 100) * 50);
        }

        return $defaultMaxBank + SK_CORRUPT_WARDEN_BANK_BONUS * $skill->level + $bonus;
    }

    public static function sGetSkill($userId, $skillId)
    {
        $res = parent::XGet(['user_id', 'skill_id', 'level', 'activated', 'activationsToday'], 'user_skills', '`user_id`=' . $userId . ' AND `skill_id`=' . $skillId);
        if ($res == null) {
            parent::AddRecords(['user_id' => $userId, 'skill_id' => $skillId, 'level' => 0], 'user_skills');
        }
        if (DBi::$conn->affected_rows == 0) {
            return null;
        }

        return parent::XGet(['user_id', 'skill_id', 'level', 'activated', 'activationsToday'], 'user_skills', '`user_id`=' . $userId . ' AND `skill_id`=' . $skillId);
    }

    public function GetDataFields()
    {
        return self::GetDataTableFields();
    }

    public function GetTotalSkillActivationsPerDay(stdClass $skill)
    {
        if ($skill->type != 'active') {
            return 0;
        }
        $userSkill = $this->GetSkill($skill->id);
        if (!$userSkill) {
            return 0;
        }
        if ($userSkill->level == 0) {
            return 0;
        }

        return $skill->activationsPerDay + ($skill->bonusActivations * $userSkill->level);
    }

    public function GetSkill($skillId)
    {
        $res = parent::XGet(['user_id', 'skill_id', 'level', 'activated', 'activationsToday', 'activateAuto'], 'user_skills', '`user_id`=' . $this->id . ' AND `skill_id`=' . $skillId);
        if ($res == null) {
            parent::AddRecords(['user_id' => $this->id, 'skill_id' => $skillId, 'level' => 0], 'user_skills');
        }
        if (DBi::$conn->affected_rows == 0) {
            return null;
        }

        return parent::XGet(['user_id', 'skill_id', 'level', 'activated', 'activationsToday', 'activateAuto'], 'user_skills', '`user_id`=' . $this->id . ' AND `skill_id`=' . $skillId);
    }

    public function ActivateSkill(stdClass $skill)
    {
        if (!$this->CanActivateSkill($skill)) {
            throw new FailedResult(USER_CANT_ACTIVATE_SKILL);
        }
        $userSkill = $this->GetSkill($skill->id);
        DBi::$conn->query('UPDATE `user_skills` SET `activated` = 1, `activationsToday` = `activationsToday`+1
		WHERE `user_id`=' . $this->id . ' AND `skill_id`=' . $skill->id . ' AND `activated`=0 LIMIT 1');
        if (DBi::$conn->affected_rows == 0) {
            throw new SoftException(USER_ERROR_ACTIVATING_SKILL);
        }

        return true;
    }

    public function CanActivateSkill(stdClass $skill)
    {
        if ($skill->type != 'active') {
            return false;
        }
        $userSkill = $this->GetSkill($skill->id);
        if (!$userSkill) {
            return false;
        }
        if ($userSkill->level == 0) {
            return false;
        } elseif ($userSkill->activated == 1) {
            return false;
        } elseif ($userSkill->activationsToday >= $skill->activationsPerDay + ($skill->bonusActivations * $userSkill->level)) {
            return false;
        }

        return true;
    }

    public function AddPointToSecuritySkill(stdClass $skill)
    {
        if (!$this->CanRaiseSecuritySkill($skill)) {
            throw new FailedResult(USER_CANT_RAISE_SKILL_LEVEL);
        }
        if (!$this->RemoveFromAttribute('securityPoints', 1)) {
            throw new FailedResult(USER_NOT_ENOUGH_POINTS_RAISE_SEC_SKILL);
        }
        DBi::$conn->query('UPDATE `user_skills` SET `level` = `level` + 1 WHERE `user_id`=' . $this->id . ' AND `skill_id`=' . $skill->id . ' AND `level` < ' . $skill->maxLvl . ' LIMIT 1');
        if (DBi::$conn->affected_rows == 0) {
            throw new SoftException(USER_ERROR_INCREASING_SKILL);
        }

        return true;
    }

    public function CanRaiseSecuritySkill(stdClass $skill)
    {
        if ($this->securityPoints < 1) {
            return false;
        } elseif ($skill->securitySkill == 0) {
            return false;
        }
        $userSkill = $this->GetSkill($skill->id);
        if (!$userSkill) {
            return false;
        }
        if ($userSkill->level >= $skill->maxLvl) {
            return false;
        }

        return true;
    }

    public function GetSkills()
    {
        return parent::GetAllById('skill_id', ['user_id', 'skill_id', 'level', 'activated', 'activationsToday', 'activateAuto'], 'user_skills', '`user_id`=' . $this->id);
    }

    public function GetWarAgainst($targetUser)
    {
        $userWars = parent::GetAll(['originalGang', 'targetGang', 'GangWar'], 'gang_wars_members', '`User` = ' . $this->id);
        $targetUserWars = parent::GetAll(['originalGang', 'targetGang', 'GangWar'], 'gang_wars_members', '`User` = ' . $targetUser->id);
        foreach ($userWars as $userWar) {
            foreach ($targetUserWars as $targetUserWar) {
                if ($userWar->targetGang == $targetUserWar->originalGang && $userWar->GangWar == $targetUserWar->GangWar) {
                    return new GangWar($userWar->GangWar);
                }
            }
        }

        return null;
    }

    public function GetWarGang($targetUser)
    {
        $userWars = parent::GetAll(['originalGang', 'targetGang'], 'gang_wars_members', '`User` = ' . $this->id);
        $targetUserWars = parent::GetAll(['originalGang'], 'gang_wars_members', '`User` = ' . $targetUser->id);
        foreach ($userWars as $userWar) {
            foreach ($targetUserWars as $targetUserWar) {
                if ($userWar->targetGang == $targetUserWar->originalGang) {
                    return new Gang($userWar->originalGang);
                }
            }
        }

        return null;
    }

    public function GetAttributeSum()
    {
        return $this->speed + $this->strength + $this->defense;
    }

    public function GetNotepad()
    {
        if (!isset($this->notepadobj)) {
            $this->notepadobj = new Notepad($this->id);
        }

        return $this->notepadobj;
    }

    public function GetJob()
    {
        if (!isset($this->jobobj)) {
            $this->jobobj = new Job($this->job);
        }

        return $this->jobobj;
    }

    /*
     * Get the name of the Users current JobRole
     *
     * @return string
     */
    public function getJobRoleName()
    {
        $jobRoleName = 'N/A';

        $userJobProgress = UserJobProgress::getUserJobProgressForUser($this->id);
        if ($userJobProgress) {
            $jobRoleName = $userJobProgress->getJobRole()->name;
        }

        return $jobRoleName;
    }

    public function GetGuardProtectionDurationBonus()
    {
        $skill = $this->GetSkill(SK_GCONNECTIONS_ID);
        if ($skill->activated == 1) {
            return 3600 * $skill->level;
        }

        return 0;
    }

    public function UseGuardProtectionDurationBonus()
    {
        $skill = $this->GetSkill(SK_GCONNECTIONS_ID);
        if ($skill->activated == 1) {
            $this->DesactivateSkill($skill);

            return 3600 * $skill->level;
        }

        return 0;
    }

    public function DesactivateSkill(stdClass $skill) // user skill
    {
        DBi::$conn->query('UPDATE `user_skills` SET `activated` = 0	WHERE `user_id`=' . $this->id . ' AND `skill_id`=' . $skill->skill_id . ' AND `activated`=1 LIMIT 1');
        if (DBi::$conn->affected_rows == 0) {
            throw new SoftException(USER_ERROR_DISACTIVATING_SKILL);
        }

        return true;
    }

    public function GetHospitalDurationBonus()
    {
        return 0;
    }

    public function GetWarPointsMultiplier()
    {
        $skill = $this->GetSkill(SK_WARLORD_ID);

        return 1 + (0.5 * $skill->level);
    }

    public function GetStatMultiplier($status)
    {
        $statusBonus = GangBonus::getTrain($this, $status);

        return 1 + ($statusBonus / 100);
    }

    public function QuitJob()
    {
        if ($this->HasJob() === false) {
            throw new FailedResult(USER_CANT_QUIT_JOB);
        }

        return $this->SetAttribute('job', 0);
    }

    public function HasJob()
    {
        return $this->job > 0;
    }

    // Returns the full formatted name for given user

    public function ChangeJob(Job $job)
    {
        if ($job->id == 0) {
            throw new SoftException('Invalid job specified.');
        } elseif ($job->MatchUser($this) === false) {
            throw new FailedResult(sprintf(USER_NOT_MATCH_JOB_REQUIRMENT, $job->name));
        }

        return $this->SetAttribute('job', $job->id);
    }

    public function changeCollectTime($collectTime = 2)
    {
        return $this->SetAttribute('jobtime', $collectTime);
    }

    public function Mug(User $targetUser)
    {
        $isRiot = ((float) Variable::GetValue('riotStarted') > time());

        if ($targetUser->id == $this->id) {
            throw new SoftException(USER_NOT_MUG_YOURSELF);
        } elseif ($this->IsInJail()) {
            throw new FailedResult(USER_NOT_MUG_IN_SHOWERS);
        } elseif ($this->IsInHospital()) {
            throw new FailedResult(USER_HOSPITALIZED_NOT_MUG);
        } elseif ($targetUser->IsAdmin() && !$isRiot) {
            throw new FailedResult(USER_NOT_MUG_PG);
        } elseif ($this->nerve < 10) {
            throw new FailedResult(USER_NOT_MUG_ENOUGH_NERVE);
        }
        $this->CheckPrisons($targetUser);

        if ($this->gprot > time()) {
            $this->RemoveFromAttribute('gprot', 600);
        }
        if ($targetUser->IsInHospital()) {
            throw new FailedResult(USER_NOT_MUG_HOSPITALIZED);
        } elseif ($targetUser->IsProtectedByGuards()) {
            throw new FailedResult(USER_NOT_MUG_PROTECTED_PG);
        } elseif ($targetUser->IsInJail()) {
            throw new FailedResult(USER_NOT_MUG_INMATE_SHOWERS);
        } elseif ($this->level > 2 && $targetUser->level < 2 && ((time() - $targetUser->lastactive) < 432000)) {
            throw new FailedResult(USER_NOT_MUG_HIGHER_LEVEL);
        } elseif ($targetUser->IsProtectedByGuards()) {
            throw new FailedResult(ATK_CANT_PROTECTED_BY_GUARD);
        } elseif ($targetUser->signuptime > (time() - 259200)) {
            // throw new FailedResult("You can not mug a user who is less then 3 days old ");
        }

        if ($this->GetModdedSpeed() > $targetUser->GetModdedSpeed()) {
            //calculate exp won
            if ($targetUser->level > 500) {
                $targetUser->level = 500;
            }
            if ($this->level > 500) {
                $levels = 500;
            }
            $expwon = ($targetUser->level - $levels) * 7;

            // Success
            if ($expwon < 0) {
                $expwon = 0;
            }
            $xpbp = 250;
            BattlePass::addExp($this->id, 250);
            $this->addActivityPoint();

            $newexp = (int) floor($expwon);
            if ($newexp > MUG_MAX_XP) {
                $newexp = MUG_MAX_XP;
            }
            if ($this->securityLevel > 0) {
                $pwrc = $this->securityLevel * 0.1;
                $newexp = $newexp * $pwrc;
            }
            $newexp = $newexp * $targetUser->GetExpMultiplier();
            $newexp = $newexp * $targetUser->XGetExpMultiplier();


            if (UserBooks::UserHasStudied($this->id, 37)) {
                $critical_mug = (5 >= rand(1, 100)); //calculate critical mug. 5% possiblitiy
            } else {
                $critical_mug = (2 >= rand(1, 100)); //calculate critical mug. 2% possiblitiy
            }

            if ($critical_mug) { //if critical mug then multiply damage by 2
                $mugamount = (int) floor($targetUser->money / 2);
            } else {
                $mugamount = (int) floor($targetUser->money / 4);
            }

            if ($mugamount <= 0) {
                $this->RemoveFromAttribute('nerve', 10);
                MugLog::Add($this, $targetUser, 'Canceled');
                throw new FailedResult(USER_MUGGED_SOAP);
            }
            $newmuggeramount = $mugamount;
            $newmuggedamount = $mugamount;
            $gangmoney = 0;

            if ($this->level > 349 || $this->securityLevel == 1) {
                $black = DBi::$conn->query('SELECT * FROM prestige2 WHERE userid = ' . $this->id);
                if (mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE prestige2 SET mugs = mugs + 1 WHERE userid = ' . $this->id . ' AND mugs < 100');
                } else {
                    DBi::$conn->query('INSERT INTO prestige2 (userid, mugs) VALUES(' . $this->id . ', 1)');
                }
            }
            if ($this->level > 449 || $this->securityLevel == 2) {
                $black = DBi::$conn->query('SELECT * FROM prestige3 WHERE userid = ' . $this->id);
                if (mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE prestige3 SET mugs = mugs + 1 WHERE userid = ' . $this->id . ' AND mugs < 2001');
                } else {
                    DBi::$conn->query('INSERT INTO prestige3 (userid, mugs) VALUES(' . $this->id . ', 1)');
                }
            }
            if (!$targetUser->RemoveFromAttribute('money', $newmuggedamount)) {

                BattlePass::addExp($targetUser->id, 20);
                $targetUser->addActivityPoint();

                $this->RemoveFromAttribute('nerve', 10);

                MugLog::Add($this, $targetUser, 'Canceled');

                throw new FailedResult(USER_MUGGED_SOAP);

            } elseif (!$this->RemoveFromAttribute('nerve', 10)) {
                MugLog::Add($this, $targetUser, 'Canceled');
                throw new FailedResult(USER_MUG_OUT_OF_NERVE);
            }

            if ($this->IsInAGang()) {
                //Handle gang tax
                $gangmoney = (int) floor($mugamount * ($this->GetGang()->gangtax / 100));
                $newmuggeramount = (int) floor($mugamount * ((100 - $this->GetGang()->gangtax) / 100));
                $this->GetGang()->AddToAttribute('vault', $gangmoney);
            }

            $this->AddToAttribute('money', $newmuggeramount);
            //BattlePass::addExp($this->id, $exp);
            $this->AddToAttribute('exp', $newexp);

            if ($mugamount > 0) {
                HatePoints::AddPoint($this, HatePoints::TASK_MUG);
            }
            if ($this->securityLevel == 2 && $this->level > 249) {
                $black = DBi::$conn->query('SELECT * FROM prestige_tasks WHERE userid = ' . $this->id);
                if (mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE prestige_tasks SET mugs = mugs + 1 WHERE userid = ' . $this->id);
                } else {
                    DBi::$conn->query('INSERT INTO prestige_tasks (userid, mugs) VALUES(' . $this->id . ', 1)');
                }
            }
            if ($this->securityLevel == 3 && $this->level > 249) {
                $black = DBi::$conn->query('SELECT * FROM prestige4  WHERE userid = ' . $this->id);
                if (mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE prestige4 SET mugs = mugs + 1 WHERE userid = ' . $this->id);
                } else {
                    DBi::$conn->query('INSERT INTO prestige4 (userid, mugs) VALUES(' . $this->id . ', 1)');
                }
            }
            $targetUser->Notify(sprintf(USER_MUGGED_BY, '<a href="profiles.php?id=' . $this->id . '">' . $this->username . '</a>', $mugamount), COM_MUG);
            MugLog::Add($this, $targetUser, 'Won', $mugamount, $gangmoney, $newexp);
            $sql = 'update ' . SUser::$dataTable . ' set dailyMugsAmount=dailyMugsAmount+' . $mugamount . ' , mugged=mugged+' . $mugamount . ', number_of_muggs=number_of_muggs+1 where id=' . $this->id;
            DBi::$conn->query($sql);
            $user2 = SUserFactory::getInstance()->getUser($this->id);
            Objectives::set($user2->id, 'daily_mugs', $user2->dailyMugsAmount);

            UserBarracksRecord::recordAction(UserBarracksRecord::MUG, $this->id, $mugamount);
            UserDailyDuties::recordAction($this->id, 'mugs');
            $this->performUserQuestAction('mugs', 1);

            if ($critical_mug) {
                throw new SuccessResult(USER_CRITICAL_MUG . ' !&nbsp;<font color="red">' . sprintf(USER_MUGGED, $targetUser->formattedname, $mugamount, $gangmoney, $expwon) . '</font>');
            }
            throw new SuccessResult(sprintf(USER_MUGGED, $targetUser->formattedname, $mugamount, $gangmoney, $expwon));
        }

        //speed exp
        $spdExp = ($this->GetModdedSpeed() / $targetUser->GetModdedSpeed()) * 175;

        // Computing stat exp
        $statExp = ($this->GetModdedAttributeSum() / $targetUser->GetModdedAttributeSum()) * 125;

        // Computing total exp
        //$expwon = $spdExp + $statExp;

        $expwon = ($targetUser->level - $this->level) * 7;
        if ($expwon < 0) {
            $expwon = $expwon * -1;
        }
        // Failure
        $newexp = (int) floor($expwon);

        $newexp = (int) ($newexp * $targetUser->GetExpMultiplier());
        $newexp = (int) ($newexp * $targetUser->XGetExpMultiplier());

        if (!$this->RemoveFromAttribute('nerve', 10)) {
            MugLog::turnonAdd($this, $targetUser, 'Canceled');
            throw new FailedResult(USER_MUG_OUT_OF_NERVE);
        }

        $extra_target = '';
        $extra_me = '';
        if (isset($user2) && is_object($user2) && $user2->mugger == 1) {
            if (20 >= rand(0, 99)) {
                $extra_target = ' The mugger was sent to jail for 15 minutes.';
                $extra_me = ' Your target turned you over to the cops and they sent you to jail for 15 minutes.';
                $this->SetAttribute('jail', 900);
            } else {
                $extra_target = ' The mugger was set free for lack of evidence.';
                $extra_me = ' Your target turned you over to the cops but they set you free for lack of evidence.';
            }
        }
        $user3 = SUserFactory::getInstance()->getUser($targetUser->id);
        if ($user3->DontWantFailMugExp()) {
            // $newexp = 0;
        } else {
            $targetUser->AddToAttribute('exp', $newexp);
        }
        $targetUser->Notify(sprintf(USER_MUGGED_CAUGHT, '<a href="profiles.php?id=' . $this->id . '">' . $this->username . '</a>', $newexp) . '' . $extra_target, COM_MUG);
        $targetUser->AddToAttribute('exp', $newexp);
        MugLog::Add($this, $targetUser, 'Failed');
        throw new FailedResult(USER_MUGGED_FAILED_MSG . $extra_me);
    }


    /*
     * Attributes management
     */

    public function IsInJail()
    {
        return $this->jail > time();
    }

    public function IsInHospital()
    {
        return $this->hospital > time();
    }

    public function CheckPrisons($targetUser)
    {
        $underworldSkill = $this->GetSkill(SK_UCONNECTIONS_ID);
        if ($underworldSkill->activated == true) {
            $this->DesactivateSkill($underworldSkill);

            return true;
        }
        if ($this->city != $targetUser->city && $this->IsAtWarWith($targetUser) == false) {
            throw new SoftException(USER_MUST_IN_SAME_PRISON);
        }

        return true;
    }

    public function IsProtectedByGuards()
    {
        return $this->gprot > time();
    }

    public function GetModdedSpeed()
    {
        if (!isset($this->moddedspeed)) {
            $speedScore = 0;

            $armorTypes = ['head', 'chest', 'legs', 'boots', 'gloves'];
            foreach ($armorTypes as $armorType) {
                if ($this->getArmorForType($armorType)) {
                    $speedScore += $this->getArmorForType($armorType)->speed;
                }
            }

            $weapon = $this->getWeapon();
            if ($weapon) {
                $speedScore += $weapon->speed;
            }

            $effectsOnUser = Drug::GetEffectsOnUser($this->id);
            if (count($effectsOnUser) > 0) {
                foreach ($effectsOnUser as $effect) {
                    if ($effect->item->speed_boost > 0) {
                        $speedScore += $effect->item->speed_boost;
                    }
                }
            }

            $this->moddedspeed = $this->speed * ($speedScore * .01 + 1);

            if ($this->hasPerkActivated('FAST_MAG_NAME')) {
                $this->moddedspeed = $this->moddedspeed + ($this->moddedspeed / 100 * 10);
            }

            if ($this->GetCureVialTime() > time()) {
                $this->moddedspeed = $this->moddedspeed * 2;
            }

            if ($this->GetFungalVialTime() > time()) {
                $this->moddedspeed = $this->moddedspeed - ($this->moddedspeed / 10);
            }
        }
        if ($weapon) {
            if ($this->CheckForLinked($weapon->id)) {
                $get = DBi::$conn->query("SELECT `id` FROM items WHERE linked = " . $weapon->id);
                $gets = mysqli_fetch_assoc($get);
                $item = new Item($gets['id']);
                $score = ($this->moddedspeed / 100) * $item->speed_boost;
                $scorenew = $this->moddedspeed + $score;
                $this->moddedspeed = $scorenew;
            }
        }
        return round($this->moddedspeed);
    }

    public function HasEquippedSpeed($armorNo = 1)
    {
        if ($armorNo == 1 && $this->eqspeed != 0) {
            return true;
        }

        if ($armorNo == 2 && $this->eqnspeed != 0) {
            return true;
        }

        return false;
    }

    public function GetSpeed($armorNo = 1)
    {
        if ($this->HasEquippedSpeed($armorNo) === false) {
            return null;
        }

        if ($armorNo == 1) {
            if (!isset($this->speedobj) || $this->speedobj === null) {
                $this->speedobj = new Item($this->eqspeed);
                if ($this->eqspeedloan == 1) {
                    $this->speedobj->borrowed = 1;
                }
            }

            return $this->speedobj;
        }
        if (!isset($this->speedobjs[$armorNo]) || $this->speedobjs[$armorNo] === null) {
            $this->speedobjs[$armorNo] = new Item($this->eqnspeed);
            if ($this->eqnspeedloan == 1) {
                $this->speedobjs[$armorNo]->borrowed = 1;
            }
        }

        return $this->speedobjs[$armorNo];

        return null;
    }

    public function GetModdedAttributeSum()
    {
        return $this->GetModdedSpeed() + $this->GetModdedStrength() + $this->GetModdedDefense();
    }

    public function GetModdedStrength($recalc = false)
    {
        if (!isset($this->moddedstrength) || $recalc) {
            $strBonus = 0;
            $drugTaken = Drug::DrugTaken($this, 'Generic Steroids');

            if (!empty($drugTaken)) {
                $strGain = [0, 15, 22, 28, 33, 37, 40, 42];
                $strBonus = (int) ($this->strength * $strGain[$drugTaken->qty]) / 100;
            }

            $offenseScore = 0;
            if ($this->GetWeapon()) {
                $weapon = $this->GetWeapon();
                $offenseScore += $weapon->offense;
            }

            $armorTypes = ['head', 'chest', 'legs', 'boots', 'gloves'];
            foreach ($armorTypes as $armorType) {
                if ($this->getArmorForType($armorType)) {
                    if ($this->getArmorForType($armorType)->offense > 0) {
                        $offenseScore += $this->getArmorForType($armorType)->offense;
                    }
                }
            }

            $effectsOnUser = Drug::GetEffectsOnUser($this->id);
            if (count($effectsOnUser) > 0) {
                foreach ($effectsOnUser as $effect) {
                    if ($effect->item->strength_boost > 0) {
                        $offenseScore += $effect->item->strength_boost;
                    }
                }
            }

            $strbonus = $this->strengthbonus;
            $this->moddedstrength = $this->strength * ($offenseScore * .01 + 1) + $strBonus;
            if ($this->cocktailsteroid > time()) {
                $this->moddedstrength += $this->bonusstrength;
            }

            if ($this->hasPerkActivated('HALLOW_POINTS_NAME')) {
                $this->moddedstrength = $this->moddedstrength + ($this->moddedstrength / 100 * 10);
            }

            if ($this->GetCureVialTime() > time()) {
                $this->moddedstrength = $this->moddedstrength * 2;
            }

            if ($this->GetFungalVialTime() > time()) {
                $this->moddedstrength = $this->moddedstrength - ($this->moddedstrength / 10);
            }
        }
        if ($weapon) {
            if ($this->CheckForLinked($weapon->id)) {
                $get = DBi::$conn->query("SELECT `id` FROM items WHERE linked = " . $weapon->id);
                $gets = mysqli_fetch_assoc($get);
                $item = new Item($gets['id']);
                $score = ($this->moddedstrength / 100) * $item->strength_boost;
                $scorenew = $this->moddedstrength + $score;
                $this->moddedstrength = $scorenew;
            }
        }
        return round($this->moddedstrength);
    }

    public function HasEquippedWeapon($weaponNo = 1)
    {
        if ($weaponNo == 1 && $this->eqweapon != 0) {
            return true;
        }
        if ($weaponNo == 2 && $this->eqnweapon != 0) {
            return true;
        }

        return false;
    }

    public function GetWeapon()
    {
        if ($this->weaponobj && $this->weaponobj !== null) {
            return $this->weaponobj;
        }

        $weapon_id = UserEquipped::getUserEquippedItemForItemType($this->id, 'weapon');
        if ($weapon_id) {
            $this->weaponobj = new Item($weapon_id);

            return $this->weaponobj;
        }

        return null;
    }

    public function HasEquippedArmor($armorNo = 1)
    {
        if ($armorNo == 1 && $this->eqarmor != 0) {
            return true;
        }

        if ($armorNo == 2 && $this->eqnarmor != 0) {
            return true;
        }

        return false;
    }
    public static function GetTheirArmor($userid, $type)
    {
        $query = "select * from user_equipped join items on user_equipped.item_id = items.id WHERE user_equipped.user_id = $userid  AND items.armortype = '$type'";
        $result = DBi::$conn->query($query);
        $row = mysqli_fetch_object($result);
        return $row;
    }

    public function GetArmor($armorNo = 1)
    {
        if ($this->HasEquippedArmor($armorNo) === false) {
            return null;
        }

        if ($armorNo == 1) {
            if (!isset($this->armorobj) || $this->armorobj === null) {
                $this->armorobj = new Item($this->eqarmor);
                if ($this->eqarmorloan == 1) {
                    $this->armorobj->borrowed = 1;
                }
            }

            return $this->armorobj;
        }
        if (!isset($this->armorobjs[$armorNo]) || $this->armorobjs[$armorNo] === null) {
            $this->armorobjs[$armorNo] = new Item($this->eqnarmor);
            if ($this->eqnarmorloan == 1) {
                $this->armorobjs[$armorNo]->borrowed = 1;
            }
        }

        return $this->armorobjs[$armorNo];

        return null;
    }

    /*
     * Check if the User has an armor equipped for a type
     *
     * @param string $type
     *
     * @return boolean
     */
    public function hasEquippedArmorForType(string $type)
    {
        $fieldName = 'eqarmor' . $type;
        if ($this->$fieldName) {
            return true;
        }

        return false;
    }

    /*
     * Get the equipped armor for the specific armourtype
     *
     * @param string $type
     *
     * @return mixed
     */
    public function getArmorForType(string $type)
    {
        $armorobjFieldName = 'armorobj' . $type;
        if ($this->$armorobjFieldName) {
            return $this->$armorobjFieldName;
        }

        $armor_id = UserEquipped::getUserEquippedItemForItemType($this->id, 'armor', $type);
        if ($armor_id) {
            $this->$armorobjFieldName = new Item($armor_id);

            return $this->$armorobjFieldName;
        }

        return null;
    }

    /*
     * Get the inventory for the current user indexed on item_type
     *
     * @return array
     */
    public function getInventoryIndexedOnItemType()
    {
    }

    /*
     * Games management
     */

    public function GetModdedDefense($recalc = false)
    {
        if (!isset($this->moddeddefense) || $recalc) {
            $defenseScore = 0;

            $armorTypes = ['head', 'chest', 'legs', 'boots', 'gloves'];
            foreach ($armorTypes as $armorType) {
                if ($this->getArmorForType($armorType)) {
                    $defenseScore += $this->getArmorForType($armorType)->defense;
                }
            }

            if ($this->GetWeapon()) {
                $weapon = $this->GetWeapon();
                if ($weapon->defense > 0) {
                    $defenseScore += $weapon->defense;
                }
            }

            $effectsOnUser = Drug::GetEffectsOnUser($this->id);
            if (count($effectsOnUser) > 0) {
                foreach ($effectsOnUser as $effect) {
                    if ($effect->item->defense_boost > 0) {
                        $defenseScore += $effect->item->defense_boost;
                    }
                }
            }

            $this->moddeddefense = $this->defense * ($defenseScore * .01 + 1);
            if ($this->cocktailsteroid > time()) {
                $this->moddeddefense += $this->bonusdefense;
            }

            if ($this->hasPerkActivated('FMJ_NAME')) {
                $this->moddeddefense = $this->moddeddefense + ($this->moddeddefense / 100 * 10);
            }

            if ($this->GetCureVialTime() > time()) {
                $this->moddeddefense = $this->moddeddefense * 2;
            }

            if ($this->GetFungalVialTime() > time()) {
                $this->moddeddefense = $this->moddeddefense - ($this->moddeddefense / 10);
            }
        }
        if ($weapon) {
            if ($this->CheckForLinked($weapon->id)) {
                $get = DBi::$conn->query("SELECT `id` FROM items WHERE linked = " . $weapon->id);
                $gets = mysqli_fetch_assoc($get);
                $item = new Item($gets['id']);
                $score = ($this->moddeddefense / 100) * $item->defense_boost;
                $scorenew = $this->moddeddefense + $score;
                $this->moddeddefense = $scorenew;
            }
        }
        return round($this->moddeddefense);
    }

    public function XGetExpMultiplier()
    {
        $expbonus = 0;
        $expbonus += GangBonus::getTrain($this, GangBonus::$BONUS_EXP);
        $expbonus += $this->GetExpBonus();

        return 1 + $expbonus / 100;
    }

    public function GetExpBonus()
    {
        if ($this->securityLevel > 0) {
            return $this->securityLevel * 20;
        }
        if ($this->albuns_exp_time > time()) {
            return $this->albuns_exp_bonus;
        }

        return 0;
    }

    public function Attack($targetUser, &$attacks = null, &$hitdetails = null, $jointAttackers = [])
    {
        try {
            if (!empty($jointAttackers) && is_array($jointAttackers)) {
                new AttackJoin($this, $targetUser, $attacks, $hitdetails, $jointAttackers);
            } else {
                new Attack($this, $targetUser, $attacks, $hitdetails);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function CountUnreadEvents()
    {
        $res = DBi::$conn->query('SELECT count(`id`) as total FROM `events` WHERE `to`=\'' . $this->id . '\' and `viewed` = \'1\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public function CountUnreadPmails()
    {
        if ($this->mods > 0) {
            $res = DBi::$conn->query('SELECT count(`id`) as total FROM `pms` WHERE `to`=\'' . $this->id . '\' AND `viewed`=\'1\' AND blocked=0 AND deleted = 0 and box!=' . Pms::MODERATOR);
        } else {
            $res = DBi::$conn->query('SELECT count(`id`) as total FROM `pms` WHERE `to`=\'' . $this->id . '\' AND `viewed`=\'1\' AND blocked=0 AND deleted = 0 ');
        }
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    /*
     * Money management
     */

    public function CountUnreadPmailsMod()
    {
        $res = DBi::$conn->query('SELECT count(`id`) as total FROM `pms` WHERE `to`=\'' . $this->id . '\' AND `viewed`=\'1\' AND blocked=0 AND deleted = 0 and box=' . Pms::MODERATOR);
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public function Ban($reason)
    {
        if (self::IsBanned($this->id)) {
            throw new SoftException(USER_ALREADY_BANNED);
        } elseif ($this->IsSupportUser() === true || $this->IsAdmin() === true) {
            throw new SoftException(USER_CANT_BAN_ADMIN);
        }
        DBi::$conn->query('INSERT INTO `bans` (`id`,`reason`, `time`) VALUES (\'' . $this->id . '\',\'' . $reason . '\',\'' . time() . '\')');
        if (DBi::$conn->affected_rows == 0) {
            throw new SoftException(USER_CANT_BANNED);
        }

        return true;
    }

    public static function IsBanned($id)
    {
        $res = DBi::$conn->query('SELECT `id` FROM `bans` WHERE `id`=\'' . $id . '\'');

        return mysqli_num_rows($res);
    }

    public function IsSupportUser()
    {
        return $this->GetSupportStatus() !== null;
    }

    public function GetSupportStatus()
    {
        if (!isset($this->supportobj)) {
            $this->supportobj = SupportUser::Build($this->id);
        }

        return $this->supportobj;
    }

    public function Unban()
    {
        if (!self::IsBanned($this->id)) {
            throw new SoftException('User isn\'t banned.');
        }
        DBi::$conn->query('DELETE FROM `bans` WHERE `id`=\'' . $this->id . '\'');
        if (DBi::$conn->affected_rows == 0) {
            throw new SoftException(USER_CANT_UNBANNED);
        }

        return true;
    }

    public function Warn($reason)
    {
        $this->Notify(sprintf(USER_RECEIVED_WARNING, $reason), COM_WARNING);

        return true;
    }

    public function GetRanks()
    {
        $ranks = [
            'strength' => '>1000',
            'defense' => '>1000',
            'speed' => '>1000',
            'level' => '>1000',
            'total' => '>1000',
        ];
        $res = DBi::$conn->query('SELECT `rank` FROM `top1000str` WHERE `uid` = \'' . $this->id . '\'');
        if (mysqli_num_rows($res) != 0) {
            $arr = mysqli_fetch_array($res);
            $ranks['strength'] = $arr['rank'];
        }
        $res = DBi::$conn->query('SELECT `rank` FROM `top1000def` WHERE `uid` = \'' . $this->id . '\'');
        if (mysqli_num_rows($res) != 0) {
            $arr = mysqli_fetch_array($res);
            $ranks['defense'] = $arr['rank'];
        }
        $res = DBi::$conn->query('SELECT `rank` FROM `top1000spd` WHERE `uid` = \'' . $this->id . '\'');
        if (mysqli_num_rows($res) != 0) {
            $arr = mysqli_fetch_array($res);
            $ranks['speed'] = $arr['rank'];
        }
        $res = DBi::$conn->query('SELECT `rank` FROM `top1000lvl` WHERE `uid` = \'' . $this->id . '\'');
        if (mysqli_num_rows($res) != 0) {
            $arr = mysqli_fetch_array($res);
            $ranks['level'] = $arr['rank'];
        }
        $res = DBi::$conn->query('SELECT `rank` FROM `top1000tot` WHERE `uid` = \'' . $this->id . '\'');
        if (mysqli_num_rows($res) != 0) {
            $arr = mysqli_fetch_array($res);
            $ranks['total'] = $arr['rank'];
        }

        return $ranks;
    }

    public function GetAllOwnedItems()
    {
        $objs = [];
        $result = DBi::$conn->query('SELECT `userid`, `itemid`, `quantity`, `borrowed` FROM `inventory` WHERE `userid` = \'' . $this->id . '\'');
        while ($obj = mysqli_fetch_object($result)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public function GetAllSendItems()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('i.userid, i.itemid, i.quantity, i.borrowed')
            ->from('inventory', 'i')
            ->leftJoin('i', 'items', 'i2', 'i.itemid = i2.id')
            ->where('i.userid = :user_id')
            ->andWhere('i.borrowed = :borrowed')
            ->setParameter('user_id', $this->id)
            ->setParameter('borrowed', 0)
        ;

        return $queryBuilder->execute()->fetchAll();
    }

    public function GetAllSelfItems($where = '')
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('i.userid, i.itemid, i2.id, i.quantity, i.awake, i.borrowed')
            ->from('inventory', 'i')
            ->leftJoin('i', 'items', 'i2', 'i.itemid = i2.id')
            ->where('i.userid = :user_id')
            ->andWhere('i.borrowed = :borrowed')
            ->andWhere('(i2.item_type = :armor OR i2.item_type = :weapon OR i2.id = 51 OR i2.item_type = "house")')
            ->setParameter('user_id', $this->id)
            ->setParameter('borrowed', 0)
            ->setParameter('armor', 'armor')
            ->setParameter('weapon', 'weapon')
        ;
        $results = $queryBuilder->execute()->fetchAll();

        return $results;
    }
    public function GetAllSelfItem($where = '')
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('i.userid, i.itemid, i.quantity, i.borrowed')
            ->from('inventory', 'i')
            ->leftJoin('i', 'items', 'i2', 'i.itemid = i2.id')
            ->where('i.userid = :user_id')
            ->andWhere('i.borrowed = :borrowed')
            ->andWhere('(i2.item_type = :armor OR i2.item_type = :weapon OR i2.id = 51)')
            ->setParameter('user_id', $this->id)
            ->setParameter('borrowed', 0)
            ->setParameter('armor', 'armor')
            ->setParameter('weapon', 'weapon')
        ;
        $results = $queryBuilder->execute()->fetchAll();

        return $results;
    }
    public function GetAllDressItems($where = '')
    {
        $objs = [];
        $query = 'SELECT `userid`, `itemid`, `quantity`, `borrowed`, `awake` FROM `inventory` WHERE `userid` = \'' . $this->id . '\' AND `borrowed`=\'0\'';
        if (!empty($where)) {
            $query .= ' AND ' . $where;
        }

        $res = DBi::$conn->query($query);

        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public function GetAllGangInvites()
    {
        $invites = parent::GetAll(['User', 'invitedby', 'gangid', 'time'], 'ganginvites', '`User`=\'' . $this->id . '\'');
        foreach ($invites as $invite) {
            $invite->gang = new Gang($invite->gangid);
            $invite->formattedTime = date('F d, Y g:i:sa', $invite->time);
        }

        return $invites;
    }

    public function AcceptGangInvite(Gang $gang)
    {
        if ($this->IsInAGang() === true) {
            throw new FailedResult(USER_RESIGN_FROM_GANG);
        }
        $res = DBi::$conn->query('SELECT `User`, `invitedby` FROM `ganginvites` WHERE `User`=\'' . $this->id . '\' AND `gangid`=\'' . $gang->id . '\'');
        if (mysqli_num_rows($res) == 0) {
            throw new SoftException(USER_HAVENT_INVITED);
        }
        $invite = mysqli_fetch_object($res);
        DBi::$conn->query('DELETE FROM `ganginvites` WHERE `User`=\'' . $this->id . '\'');

        return $this->JoinGang($gang->id, $invite->invitedby);
    }

    public function JoinGang($gid, $inviterId = null)
    {
        if ($this->IsInAGang() === true) {
            throw new SoftException(USER_LEVE_GANG_BEFORE_JOIN_NEW);
        }
        $newGang = new Gang($gid);
        $this->SetAttribute('gang', $gid);
        $this->SetAttribute('gangcrimemoney', 0);
        $this->SetAttribute('id_rank', 0);
        $this->SetAttribute('jointattack', 0);

        $this->gangobj = new Gang($gid);
        if ($inviterId !== null) {
            Logs::SAddGangMemberLog($gid, $inviterId, 'invited', $this->id, time());
        } else {
            Logs::SAddGangMemberLog($gid, $this->id, 'join', $this->id, time());
        }

        Logs::SAddUserGangLog($gid, $this->id, 'join', time());
        $this->SetAttribute('gangentrance', time());

        $res = DBi::$conn->query('SELECT u.id FROM `gangs` g, `grpgusers` u WHERE g.leader=u.username AND g.id=\'' . $gid . '\'');
        $arr = mysqli_fetch_array($res);
        if ($arr) {
            User::SNotify($arr['id'], sprintf(USER_JOIND_UR_GANG, $this->formattedname), COM_JOIN_GANG);
            if ($newGang->welcomemsgstatus == 1) {
                $this->SendPmail($arr['id'], time(), $newGang->welcomesubject, $newGang->welcomemsg);
            }
        }

        $linkToAdd = 'LINK_YOUR_GANG';
        $result = DBi::$conn->query('SELECT id FROM `leftlinks` WHERE name=\'' . $linkToAdd . '\'');
        $array = mysqli_fetch_array($result);
        if ($array) {
            $this->addUserLeftLink($array[0]);
        }

        return true;
    }

    /*
     * Add a UserLeftLinks to the User
     *
     * @param integer $link_id
     * @param integer $order_id 0
     *
     * @return boolean
     */
    public function addUserLeftLink(int $link_id, int $order_id = 0)
    {
        $result = DBi::$conn->query('SELECT user_id FROM `user_leftlinks` WHERE user_id=\'' . $this->id . '\' AND link_id=\'' . $link_id . '\'');
        if (mysqli_num_rows($result) == 0) {
            if ($order_id === 0) {
                $result = DBi::$conn->query('SELECT user_id FROM `user_leftlinks` WHERE user_id=\'' . $this->id . '\'');
                $order_id = mysqli_num_rows($result) + 1;
            }

            $sql = "INSERT INTO `user_leftlinks` (`user_id`, `link_id`, `link_order`) VALUES ('" . $this->id . "', '$link_id', '$order_id')";
            DBi::$conn->query($sql);
        }

        return true;
    }

    public static function SNotify($id, $text, $type = '')
    {
        return Event::Add($id, $text, $type);
    }

    public function SendPmail($from, $timesent, $subject, $text, $box = 1, $blocked = 0)
    {
        return Pms::Add($this->id, $from, $timesent, $subject, $text, $box, $blocked);
    }

    public function DeclineGangInvite(Gang $gang, $reason)
    {
        $res = DBi::$conn->query('select invitedby FROM `ganginvites` WHERE `User`=\'' . $this->id . '\' AND `gangid`=\'' . $gang->id . '\'');
        $res = mysqli_fetch_array($res);

        if ($res['invitedby'] == null || $res['invitedby'] == '') {
            $res['invitedby'] = User::GetFromUsername($gang->leader);
        }
        User::SNotify($res['invitedby'], sprintf(INVITATION_REJECTED, $this->username, ($reason == '' ? "Soldier didn't give a reason" : $reason)), COM_INVITATION_REJECTED);
        DBi::$conn->query('DELETE FROM `ganginvites` WHERE `User`=\'' . $this->id . '\' AND `gangid`=\'' . $gang->id . '\'');
        if (DBi::$conn->affected_rows == 0) {
            throw new FailedResult(USER_GANG_INVITATION_CANT_DELETED);
        }

        return true;
    }

    /*
     ** User shares management
     */

    public static function GetFromUsername($username)
    {
        $res = DBi::$conn->query('SELECT `id` FROM `grpgusers` WHERE `username` = \'' . $username . '\'');
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['id'];
    }

    public function DeleteGangMembershipRequests()
    {
        return parent::sDelete('joingang', ['id' => $this->id]);
    }

    public function DeleteGangMembershipRequest($gid)
    {
        return parent::sDelete('joingang', ['id' => $this->id, 'gangid' => $gid]);
    }
    public function returnAllToGang()
    {
        $borr = DBi::$conn->query("SELECT * FROM inventory WHERE borrowed > 1 AND userid ");

        if (mysqli_num_rows($borr) > 0) {
            while ($worked = mysqli_fetch_array($borr)) {
                if (
                    $this->eqweapon == $_GET['id'] &&
                    $this->eqweaponloan == '1'
                ) {
                    $this->UnequipWeapon();
                } elseif (
                    $this->eqarmor == $_GET['id'] &&
                    $this->eqarmorloan == '1'
                ) {
                    $this->UnequipArmor();
                }
                $result = DBi::$conn->query(
                    "UPDATE `gangarmory` SET `borrowerid`=0 WHERE `itemid`='" .
                    Utility::SmartEscape($worked['itemid']) .
                    "' AND `borrowerid`='" .
                    $this->id .
                    "' AND `gangid`='" .
                    $this->gang .
                    "' LIMIT 1"
                );
                $works = DBi::$conn->query("SELECT * FROM items WHERE id = {$worked['itemid']}");
                $worked2 = mysqli_fetch_array($works);
                Logs::SAddVaultLog(
                    $this->gang,
                    $this->id,
                    $worked2['itemname'] . '|@|' . $this->formattedname,
                    time(),
                    'Recovered',
                    $this->id
                );
            }
        }
    }
    public function LeaveGang()
    {
        $gang = $this->gang;
        $this->returnAllToGang();
        $this->RemoveFromGang();
        Logs::SAddGangMemberLog($gang, $this->id, 'left', $this->id, time());
        Logs::SAddUserGangLog($gang, $this->id, 'left', time());
    }

    public function HasBorrowedEquippedWeapon($weaponNo = 1)
    {
        if ($weaponNo == 1 && $this->eqweapon != 0 && $this->eqweaponloan == 1) {
            return true;
        }

        if ($weaponNo == 2 && $this->eqnweapon != 0 && $this->eqnweaponloan == 1) {
            return true;
        }

        return false;
    }

    /*
     ** User land management
     */

    /*
     * Remove borrowed user_equipped
     */
    public function removeBorrowedUserEquippeds()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('item_id')
            ->from('user_equipped')
            ->where('borrowed = :borrowed')
            ->andWhere('user_id = :user_id')
            ->setParameter('borrowed', 1)
            ->setParameter('user_id', $this->id);
        $userEquippedItems = $queryBuilder->execute()->fetchAll();

        foreach ($userEquippedItems as $userEquipped) {
            // Un-equip the item from the user
            UserEquipped::unequipItemForUser($userEquipped['item_id'], $this->id);

            // Restore the item in the regiment armory, limit to 1
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('gangarmory')
                ->set('borrowed_to_user_id', ':set_borrowed_to_user_id')
                ->setParameter('set_borrowed_to_user_id', null)
                ->where('borrowed_to_user_id = :borrowed_to_user_id')
                ->setParameter('borrowed_to_user_id', $this->id)
                ->andWhere('itemid = :item_id')
                ->setParameter('item_id', $userEquipped['item_id'])
                ->setMaxResults(1)
                ->execute();
        }
    }

    public function UnborrowWeapon($weaponNo = 1)
    {
        if ($this->HasBorrowedEquippedWeapon($weaponNo) === false) {
            return false;
        }
        $weapon = $this->GetWeapon($weaponNo);
        $this->UnequipWeapon($weaponNo);
        $this->UnborrowItem($weapon);

        return true;
    }

    public function UnequipWeapon($weaponNo = 1)
    {
        if ($this->HasEquippedWeapon($weaponNo) === false) {
            throw new SoftException(USER_HAVENT_EQUIPPED_WEAPON);
        }
        if ($weaponNo == 1) {
            $this->AddItems($this->eqweapon, 1, $this->eqweaponloan);
            $this->SetAttribute('eqweapon', 0);
            $this->SetAttribute('eqweaponloan', 0);
            unset($this->weaponobj);
        } elseif ($weaponNo > 1) {
            $this->AddItems($this->eqnweapon, 1, $this->eqnweaponloan);
            $this->SetAttribute('eqnweapon', 0);
            $this->SetAttribute('eqnweaponloan', 0);
            unset($this->weaponobjs[$weaponNo]);
        }

        return true;
    }

    public function AddItems($itemid, $quantity = 1, $borrowed = 0, $awake = 0)
    {
        $ownedItems = $this->GetItemQuantity($itemid, $borrowed);
        if ($ownedItems == 0) {
            $data = [
                'itemid' => $itemid,
                'userid' => $this->id,
                'quantity' => $quantity,
                'borrowed' => $borrowed,
            ];


            parent::AddRecords($data, 'inventory');
        } else {
            $quantity = $quantity + $ownedItems;
            $data = [
                'quantity' => $quantity,
            ];
            $cond = [
                'itemid' => $itemid,
                'userid' => $this->id,
                'borrowed' => $borrowed,
            ];
            User::sUpdate('inventory', $data, $cond);
        }
    }
    public static function AddAwakeItems($user, $itemid, $quantity = 1)
    {
        $item = new Item($itemid);
        $awake = $item->awake * $quantity;
        $check = DBi::$conn->query("SELECT * FROM inventory WHERE userid = $user AND itemid = $itemid");
        if (mysqli_num_rows($check)) {
            DBi::$conn->query("UPDATE inventory SET quantity = quantity + $quantity, awake = awake + $awake WHERE userid = $user AND itemid = $itemid");
        } else {
            DBi::$conn->query("INSERT INTO inventory (userid, itemid, quantity, awake) VALUES ($user, $itemid, $quantity, $awake)");
        }
    }
    public static function RemoveAwakeItem($user, $itemid, $quantity = 1)
    {
        $item = new Item($itemid);
        $awake = $item->awake * $quantity;
        $check = DBi::$conn->query("SELECT * FROM inventory WHERE userid = $user AND itemid = $itemid");
        if (mysqli_num_rows($check)) {
            $qres = mysqli_fetch_array($check);
            if (isset($qres['quantity']) && $qres['quantity'] > 0) {
                $newQuantity = $qres['quantity'] - $quantity;
                if ($newQuantity == 0) {
                    DBi::$conn->query("DELETE FROM inventory WHERE userid = " . $user . " AND itemid = " . $itemid);
                } else {
                    DBi::$conn->query("UPDATE inventory SET quantity = quantity - $quantity, awake = awake - $awake WHERE userid = $user AND itemid = $itemid");
                }
            } else {
                DBi::$conn->query("DELETE FROM inventory WHERE userid = " . $user . " AND itemid = " . $itemid);
            }
        }
    }
    public function GetItemQuantity($itemid, $borrowed = '0', $awake = 0)
    {
        $res = DBi::$conn->query('SELECT `quantity` FROM `inventory` WHERE `userid`=\'' . $this->id . '\' AND `itemid`=\'' . $itemid . '\' AND `borrowed`=\'' . $borrowed . '\'');
        if ($res->num_rows == 0) {
            return 0;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['quantity'];
    }

    public function UnborrowItem($item, $quantity = 1)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('gangarmory')
            ->where('itemid = :item_id')
            ->setParameter('item_id', $item->id)
            ->andWhere('borrowed_to_user_id = :borrowed_to_user_id')
            ->setParameter('borrowed_to_user_id', $this->id)
            ->setMaxResults(1)
        ;
        $gangArmories = $queryBuilder->execute()->fetchAll();

        foreach ($gangArmories as $gangArmory) {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('gangarmory')
                ->set('borrowed_to_user_id', 0)
                ->where('id = :id')
                ->setParameter('id', $gangArmory['id'])
                ->execute()
            ;
        }

        Logs::SAddVaultLog($this->gang, $this->id, mysqli_real_escape_string(DBi::$conn, $item->itemname . '|@|' . $this->formattedname), time(), 'Recovered', $this->getAttribute('id'));

        return $this->RemoveItems($item, $quantity, 1);
    }

    public function RemoveItems(Item $item, $quantity = 1, $borrowed = 0, $awake = 0)
    {
        $ownedItems = $this->GetItemQuantity($item->id, $borrowed);
        $quantity = $ownedItems - $quantity;
        if ($quantity > 0) {
            $query = 'UPDATE `inventory` SET `quantity` = \'' . $quantity . '\' WHERE `userid`=\'' . $this->id . '\' AND `itemid`=\'' . $item->id . '\' AND `borrowed`=\'' . $borrowed . '\'';
        } elseif ($quantity == 0) {
            $query = 'DELETE FROM `inventory` WHERE `userid`=\'' . $this->id . '\' AND `itemid`=\'' . $item->id . '\' AND `borrowed`=\'' . $borrowed . '\'';
        } else {
            return false;
        }
        DBi::$conn->query($query);
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        if ($item->awake > 0) {
            $this->SetAttribute('awake', $this->GetMaxAwake());
        }

        return true;
    }

    public function HasBorrowedEquippedSpeed($armorNo = 1)
    {
        if ($armorNo == 1 && $this->eqspeed != 0 && $this->eqspeedloan == 1) {
            return true;
        }
        if ($armorNo == 2 && $this->eqnaspeed != 0 && $this->eqnspeedloan == 1) {
            return true;
        }

        return false;
    }

    public function UnborrowSpeed($weaponNo = 1)
    {
        if ($this->HasBorrowedEquippedSpeed($weaponNo) === false) {
            return false;
        }
        $weapon = $this->GetSpeed($weaponNo);
        $this->UnequipSpeed($weaponNo);
        $this->UnborrowItem($weapon);

        return true;
    }

    public function UnequipSpeed($armorNo = 1)
    {
        if ($this->HasEquippedSpeed($armorNo) === false) {
            throw new SoftException(USER_HAVENT_EQUIPPED_SPEED);
        }
        if ($armorNo == 1) {
            $this->AddItems($this->eqspeed, 1, $this->eqspeedloan);
            $this->SetAttribute('eqspeed', 0);
            $this->SetAttribute('eqspeedloan', 0);
            unset($this->speedobj);
        } elseif ($armorNo > 1) {
            $this->AddItems($this->eqnspeed, 1, $this->eqnspeedloan);
            $this->SetAttribute('eqnspeed', 0);
            $this->SetAttribute('eqnspeedloan', 0);
            unset($this->speedobjs[$armorNo]);
        }

        return true;
    }

    public function HasBorrowedEquippedArmor($armorNo = 1)
    {
        if ($armorNo == 1 && $this->eqarmor != 0 && $this->eqarmorloan == 1) {
            return true;
        }
        if ($armorNo == 2 && $this->eqnarmor != 0 && $this->eqnarmorloan == 1) {
            return true;
        }

        return false;
    }

    public function UnborrowArmor($armorNo = 1)
    {
        if ($this->HasBorrowedEquippedArmor($armorNo) === false) {
            return false;
        }
        $armor = $this->GetArmor($armorNo);
        $this->UnequipArmor($armor->armortype);
        $this->UnborrowItem($armor);

        return true;
    }

    public function UnequipArmor(string $armorType)
    {
        if ($this->HasEquippedArmorForType($armorType) === false) {
            throw new SoftException(USER_HAVENT_EQUIPPED_ARMOR);
        }

        $eqarmorFieldName = 'eqarmor' . $armorType;
        $eqarmorloanFieldName = 'eqarmor' . $armorType . 'loan';
        $armorobjFieldName = 'armorobj' . $armorType;

        $this->AddItems($this->$eqarmorFieldName, 1, $this->eqarmorloan);
        $this->SetAttribute($eqarmorFieldName, 0);
        $this->SetAttribute($eqarmorloanFieldName, 0);
        unset($this->$armorobjFieldName);

        return true;
    }

    public function GetAllBorrowedItems()
    {
        $objs = [];
        $result = DBi::$conn->query('SELECT `userid`, `itemid`, `quantity`, `borrowed` FROM `inventory` WHERE `userid` = \'' . $this->id . '\' AND `borrowed`=\'1\'');
        while ($obj = mysqli_fetch_object($result)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public function KickFromGang(User $kicker)
    {
        $gang = $this->gang;
        $this->RemoveFromGang();
        Logs::SAddGangMemberLog($gang, $kicker->id, 'kick', $this->id, time());
        Logs::SAddGangMemberLog($gang, $this->id, 'left', $this->id, time());
        Logs::SAddUserGangLog($gang, $this->id, 'kick', time());

        $this->Notify('You have been kicked from your regiment.');
    }

    /*
     ** User items management
     */

    public function CreateGang($name, $tag)
    {
        $gangPrice = 750000;
        if ($this->IsInAGang() === true) {
            throw new SoftException(CRTGANG_ALREADY_BELONG);
        } elseif ($this->money < $gangPrice) {
            throw new SoftException('<div>' . CRTGANG_NOT_ENOUGH_MONEY . '</div>');
        } elseif (strlen($name) < 3) {
            throw new SoftException('<div>' . CGN_NAME_LEAST_CHARS . '</div>');
        } elseif (strlen($name) > 21) {
            throw new SoftException('<div>' . CGN_NAME_MAX_CHARS . '.</div>');
        } elseif (strlen($tag) < 1) {
            throw new SoftException('<div>' . CGN_TAG_LEAST_CHARS . '</div>');
        } elseif (strlen($tag) > 3) {
            throw new SoftException('<div>' . CGN_TAG_MAX_CHARS . '</div>');
        } elseif (Gang::NameExists($name) === true) {
            throw new SoftException('<div>' . CGN_NAME_EXIST . '</div>');
        } elseif (Gang::TagExists($tag) === true) {
            throw new SoftException('<div>' . CGN_TAG_EXIST . '</div>');
        }
        if (!$this->RemoveFromAttribute('money', $gangPrice)) {
            throw new SoftException('<div>' . CRTGANG_NOT_ENOUGH_MONEY . '</div>');
        }
        $gid = Gang::Create($name, $tag, $this->username);
        $this->JoinGang($gid);
        throw new SuccessResult(CRTGANG_GANG_CREATED);
    }

    public function DeleteGang()
    {
        if ($this->GetGang()->leader != $this->username) {
            throw new SoftException(USER_CANT_DEL_GANG_NOT_LEADER);
        } elseif ($this->GetGang()->IsAtWar() === true) {
            throw new SoftException(USER_CANT_DEL_GANG_ACTIVE_WAR);
        }
        $res = DBi::$conn->query('SELECT id FROM `grpgusers` WHERE `gang`= \'' . $this->GetGang()->id . '\'');
        $members = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $members[] = $row['id'];
        }

        $res = DBi::$conn->query('SELECT itemid FROM `gangarmory` WHERE `gangid`= \'' . $this->GetGang()->id . '\'');
        $items = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $items[] = $row['itemid'];
        }
        DBi::$conn->query('DELETE FROM `inventory` WHERE `userid` IN(\'' . @implode("','", $members) . '\') AND `itemid` IN(\'' . @implode("','", $items) . '\') AND `borrowed`=\'1\'');

        DBi::$conn->query('UPDATE `grpgusers` SET `eqweapon` = \'0\', `eqweaponloan`=\'0\' WHERE `gang`= \'' . $this->GetGang()->id . '\' AND `eqweaponloan`=\'1\'');
        DBi::$conn->query('UPDATE `grpgusers` SET `eqarmor` = \'0\', `eqarmorloan`=\'0\' WHERE `gang`= \'' . $this->GetGang()->id . '\' AND `eqarmorloan`=\'1\'');

        DBi::$conn->query('UPDATE `grpgusers` SET `eqnweapon` = \'0\', `eqnweaponloan`=\'0\' WHERE `gang`= \'' . $this->GetGang()->id . '\' AND `eqnweaponloan`=\'1\'');

        DBi::$conn->query('UPDATE `grpgusers` SET `eqnarmor` = \'0\', `eqnarmorloan`=\'0\' WHERE `gang`= \'' . $this->GetGang()->id . '\' AND `eqnarmorloan`=\'1\'');

        DBi::$conn->query('UPDATE `grpgusers` SET `gang` = \'0\', `id_rank`=\'0\' WHERE `gang`= \'' . $this->GetGang()->id . '\'');

        DBi::$conn->query('DELETE FROM `gangs` WHERE `id`= \'' . $this->GetGang()->id . '\'');
        DBi::$conn->query('DELETE FROM `gangarmory` WHERE `gangid`= \'' . $this->GetGang()->id . '\'');
    }

    public function IsAtWarWith(User $targetUser)
    {
        return GangWarAttack::UserCanAttack($this, $targetUser);
    }

    public function IsGangMember($gang)
    {
        return $this->gang == $gang && $gang > 0;
    }

    public function IsGangPermitted(Gang $gang, $perm)
    {
        if ($this->IsInAGang() === false) {
            return false;
        }
        if ($gang->leader == $this->username) {
            return true;
        }
        if ($this->id_rank == '0') {
            return false;
        }

        $res = DBi::$conn->query('SELECT `state` from `gangperm` where `id_gang`="' . $gang->id . '" and `perm`=\'' . $perm . '\' and `name_rank`="' . $this->id_rank . '"');
        if (mysqli_num_rows($res) == 0) {
            DBi::$conn->query('INSERT INTO `gangperm` (`id_gang`, `name_rank`, `perm`, `state`) VALUES (\'' . $gang->id . '\', "' . $this->id_rank . '", \'' . $perm . '\', 0)');

            return false;
        }
        $arr = mysqli_fetch_array($res);
        if ($arr['state'] == 1) {
            return true;
        }

        return false;
    }

    public function IsGangManager(Gang $gang)
    {
        if ($this->IsInAGang() === false) {
            return false;
        }
        if ($gang->leader == $this->username) {
            return true;
        }
        if ($this->id_rank == '0') {
            return false;
        }
        $res = DBi::$conn->query('SELECT `state` from `gangperm` where `id_gang`="' . $gang->id . '" and `perm`=\'GANGM\' and `name_rank`="' . $this->id_rank . '"');
        $arr = mysqli_fetch_array($res);
        if ($arr['state'] == 1) {
            return true;
        }

        return false;
    }

    /*
     * City / Location management
     */

    public function IsGangLeader(Gang $gang)
    {
        if ($this->IsInAGang() === false) {
            return false;
        }
        if ($gang->leader == $this->username) {
            return true;
        }

        return false;
    }

    /*
     * Event / notifications management
     */

    public function IsModerator()
    {
        return $this->mods == 1;
    }

    public function IsSuperModerator()
    {
        return $this->mods == 2;
    }

    /**
     * Tick HP.
     *
     * @param $nbTicks
     *
     * @return int
     */
    public function TickHP($nbTicks)
    {
        $max = $this->GetMaxHP();
        if ($this->hp < $max) {
            $multiplier = 1;

            if ($this->rmdays > 0) {
                $multiplier = 2;
            }

            return (floor($max / 20) * $nbTicks) * $multiplier;
        }

        return 0;
    }

    /**
     * Tick awake.
     *
     * @param $nbTicks
     *
     * @return bool|float|int
     */
    public function TickAwake($nbTicks)
    {
        $max = $this->GetMaxAwake();
        if ($this->awake < $max) {
            $awake = ($max / 100);
            if ($this->rmdays > 0) {
                $awake = ($max / 100) * 5;
            }

            if ($this->caffeinetaken > time()) {
                $awake *= 2;
            }


            return $awake;
        }

        return 0;
    }

    /**
     * Tick the users energy as part of the cron.
     *
     * @return int
     */
    public function TickEnergy()
    {
        $max = $this->GetMaxEnergy();

        if ($this->energy < $max) {
            $multiplier = 1;
            if ($this->rmdays > 0) {
                $multiplier = 2;
            }

            // If the user is on caffeine, double the multiplier, again
            if ($this->caffeinetaken > time()) {
                $multiplier *= 2;
            }
            if ($this->chocolate > time()) {
                $multiplier *= 2;
            }
            // Increase energy by 25%, allow multiplier to increase energy gain
            $energyGained = ceil($max * 0.25 * $multiplier);
            if ($energyGained < 2) {
                $energyGained = 2;
            }

            return $energyGained;
        }

        return 0;
    }

    /**
     * Tick the users nerve.
     *
     * @param $nbTicks
     *
     * @return int
     */
    public function TickNerve()
    {
        $max = $this->GetMaxNerve();
        if ($this->nerve < $max) {
            $multiplier = 1;

            if ($this->rmdays > 0) {
                $multiplier = 2;
            }

            // Increase energy by 25%, allow multiplier to increase energy gain
            $nerveGained = ceil($max * 0.25 * $multiplier);
            if ($nerveGained < 2) {
                $nerveGained = 2;
            }
            return $nerveGained;
        }

        return 0;
    }

    public function RefillHP()
    {
        if ($this->hp >= $this->GetMaxHP()) {
            throw new FailedResult(USER_CANT_REFILL_HP_ALREADY_MAX);
        }
        $this->SetAttribute('hp', $this->GetMaxHP());
    }

    public function RefillAwake()
    {
        if ($this->awake >= $this->GetMaxAwake()) {
            throw new FailedResult(USER_CANT_REFILL_AWAKE_ALREADY_MAX);
        }
        $this->SetAttribute('awake', $this->GetMaxAwake());
    }

    public function RefillEnergy($attr = 'points', $value = 0)
    {
        if ($this->energy >= $this->GetMaxEnergy()) {
            throw new FailedResult(USER_CANT_REFILL_ENERGY_ALREADY_MAX);
        }
        if ($value <= 0 || ($value > 0 && $this->RemoveFromAttribute($attr, $value))) {
            ActionLogs::Log($this->id, 'Refill', 'Refill Energy'); //Log the action
            DailyTasks::recordUserTaskAction(DailyTasks::REFILL_ENERGY, $this, 1);

            return $this->SetAttribute('energy', $this->GetMaxEnergy());
        }

        return false;
    }

    public function RefillNerve($attr = 'points', $value = 0)
    {
        if ($this->nerve >= $this->GetMaxNerve()) {
            throw new FailedResult(USER_CANT_REFILL_NERVE_ALREADY_MAX);
        }
        if ($value <= 0 || ($value > 0 && $this->RemoveFromAttribute($attr, $value))) {
            ActionLogs::Log($this->id, 'Refill', 'Refill Nerve'); //Log the action
            DailyTasks::recordUserTaskAction(DailyTasks::REFILL_NERVE, $this, 1);
            return $this->SetAttribute('nerve', $this->GetMaxNerve());
        }

        return false;
    }

    public function Add5050MGBet($amount)
    {
        if ($amount > $this->money) {
            throw new FailedResult(GAME5050_NOT_MUCH_MONEY);
        } elseif ($amount < 1000) {
            throw new SoftException(GAME5050_NOT_VALID_MONEY);
        } elseif ($amount > 1000000000) {
            throw new SoftException(GAME5050_NOT_VALID_MONEY);
        } elseif (Game::Get5050MBetsCountByOwner($this->id) >= GAME5050_BETS_LIMIT) {
            throw new SoftException(sprintf(GAME5050_BET_IS_LIMITED, GAME5050_BETS_LIMIT));
        }
        if (!$this->RemoveFromAttribute('money', $amount)) {
            throw new FailedResult(GAME5050_NOT_MUCH_MONEY);
        }
        Game::Add5050MGBet($this->id, $amount);

        $admin = UserFactory::getInstance()->getUser(2);
        UserAds::Add($admin, $this->username . ' added a 5050 Money Game!', false, true);

        throw new SuccessResult(sprintf(GAME5050_ADDED_MONEY, $_POST['amount']));
    }

    public function Remove5050MGBet($bid)
    {
        $game = Game::Get5050MGBet($bid);
        if ($game == null) {
            throw new SoftException(GAME5050_BET_NOT_AVAILABLE);
        } elseif ($game->owner != $this->id) {
            throw new SoftException(GAME5050_BET_NOT_REMOVEABLE);
        } elseif (!Game::Delete5050MGBet($bid)) {
            throw new SoftException(GAME5050_BET_CANT_REMOVED);
        }
        $newmoney = Utility::SmartEscape((int) $game->amount);
        $newmoney = number_format($newmoney, 0, '.', '');
        $this->AddToAttribute('money', $newmoney);
        throw new SuccessResult(GAME5050_BET_REMOVED);
    }

    public function Take5050MGBet($bid)
    {
        $game = Game::Get5050MGBet($bid);
        if ($game == null) {
            throw new SoftException(GAME5050_BET_NOT_AVAILABLE);
        } elseif ($game->owner == $this->id) {
            throw new SoftException(GAME5050_CANT_TAKE_OWN);
        } elseif ($game->amount > $this->money) {
            throw new FailedResult(GAME5050_NOT_ENOUGH_MONEY);
        } elseif (!Game::Delete5050MGBet($bid)) {
            throw new SoftException(GAME5050_BET_NOT_AVAILABLE);
        }
        $amount = $game->amount;
        $user_points = UserFactory::getInstance()->getUser($game->owner);
        $newmoney = Utility::SmartEscape($amount);
        $newmoney = number_format($newmoney, 0, '.', '');
        if (!$this->RemoveFromAttribute('money', $newmoney)) {
            throw new FailedResult(GAME5050_NOT_ENOUGH_MONEY);
        }
        $winner = mt_rand(0, 1);
        $amount = $amount * 2;

        $newmoney = Utility::SmartEscape($amount);
        $newmoney = number_format($newmoney, 0, '.', '');
        $moneybefore1 = $user_points->money;
        $moneybefore2 = $this->money;
        if ($winner == 0) {
            //original poster wins
            $user_points->AddToAttribute('money', $newmoney);

            BattlePass::addExp($user_points->id, 100);

            $moneyafter1 = $user_points->money;
            $moneyafter2 = $this->money;
            $notifyMsg = $this->formattedname . ' took your 5050 bet of $' . number_format($amount / 2) . ' and you won $' . number_format($amount);
            $user_points->Notify($notifyMsg, COM_BET);
            DBi::$conn->query('INSERT INTO `5050logs` SET `useridput`="' . $user_points->id . '", `useridtake`="' . $this->id . '", `amount`="' . ($amount / 2) . '", `type`="money", `whowon`="' . $user_points->id . '", `moneybefore1`="' . $moneybefore1 . '", `moneyafter1`="' . $moneyafter1 . '", `moneybefore2`="' . $moneybefore2 . '", `moneyafter2`="' . $moneyafter2 . '", `time`="' . time() . '"');
            throw new FailedResult(GAME5050_YOU_LOST);
        }
        //the person who accepted the bid won
        $this->AddToAttribute('money', $newmoney);

        BattlePass::addExp($this->id, 100);

        $moneyafter1 = $user_points->money;
        $moneyafter2 = $this->money;
        $notifyMsg = $this->formattedname . ' took your 5050 bet of $' . number_format($amount / 2) . ' and you lost!';
        $notifyMsg = sprintf(GAME5050_NOTIFY_LOST, $amount, $amount / 2);
        $user_points->Notify($notifyMsg, COM_BET);
        DBi::$conn->query('INSERT INTO `5050logs` SET `useridput`="' . $user_points->id . '", `useridtake`="' . $this->id . '", `amount`="' . ($amount / 2) . '", `type`="money", `whowon`="' . $this->id . '", `moneybefore1`="' . $moneybefore1 . '", `moneyafter1`="' . $moneyafter1 . '", `moneybefore2`="' . $moneybefore2 . '", `moneyafter2`="' . $moneyafter2 . '", `time`="' . time() . '"');
        throw new SuccessResult(GAME5050_YOU_WON);
    }

    public function Add5050PGBet($amount)
    {
        if ($amount < 10) {
            throw new SoftException(GAME15050_NOT_VALID_POINTS);
        } elseif ($amount > (MAX_POINTS / 2)) {
            throw new SoftException(GAME15050_NOT_VALID_POINTS);
        } elseif (Game::Get5050PBetsCountByOwner($this->id) >= GAME5050_BETS_LIMIT) {
            throw new SoftException(sprintf(GAME5050_BET_IS_LIMITED, GAME5050_BETS_LIMIT));
        } elseif ($amount > $this->points) {
            throw new FailedResult(GAME15050_NOT_MUCH_POINTS);
        } elseif (!$this->RemoveFromAttribute('points', $amount)) {
            throw new FailedResult(GAME15050_NOT_MUCH_POINTS);
        }
        Game::Add5050PGBet($this->id, $amount);

        $admin = UserFactory::getInstance()->getUser(2);
        UserAds::Add($admin, $this->username . ' added a 5050 Points Game!', false, true);

        throw new SuccessResult(sprintf(GAME15050_ADDED_POINTS, $_POST['amount']));
    }

    public function Remove5050PGBet($bid)
    {
        $game = Game::Get5050PGBet($bid);
        if ($game == null) {
            throw new SoftException(GAME5050_BET_NOT_AVAILABLE);
        } elseif ($game->owner != $this->id) {
            throw new SoftException(GAME5050_BET_NOT_REMOVEABLE);
        } elseif (!Game::Delete5050PGBet($bid)) {
            throw new SoftException(GAME5050_BET_CANT_REMOVED);
        }
        $newpoints = Utility::SmartEscape((int) $game->amount);
        $newpoints = number_format($newpoints, 0, '.', '');

        if ($this->points + $newpoints > MAX_POINTS) {
            throw new FailedResult(sprintf(USER_POINTS_MAX_ERROR, number_format(MAX_POINTS)), 'POINTS_ERR|' . (MAX_POINTS - $newpoints));
        }
        $this->AddToAttribute('points', $newpoints);
        throw new SuccessResult(GAME5050_BET_REMOVED);
    }

    public function Take5050PGBet($bid)
    {
        $game = Game::Get5050PGBet($bid);
        if ($game == null) {
            throw new SoftException(GAME5050_BET_NOT_AVAILABLE);
        } elseif ($game->owner == $this->id) {
            throw new SoftException(GAME5050_CANT_TAKE_OWN);
        } elseif ($game->amount > $this->points) {
            throw new FailedResult(GAME15050_NOT_ENOUGH_POINTS);
        } elseif (!Game::Delete5050PGBet($bid)) {
            throw new SoftException(GAME5050_BET_NOT_AVAILABLE);
        }
        $amount = $game->amount;
        $user_points = UserFactory::getInstance()->getUser($game->owner);
        $newpoints = Utility::SmartEscape($amount);
        $newpoints = number_format($newpoints, 0, '.', '');
        if (!$this->RemoveFromAttribute('points', $newpoints)) {
            throw new FailedResult(GAME15050_NOT_ENOUGH_POINTS);
        }
        $winner = mt_rand(0, 1);
        $amount = $amount * 2;

        $newpoints = Utility::SmartEscape($amount);
        $newpoints = number_format($newpoints, 0, '.', '');
        $moneybefore1 = $user_points->points;
        $moneybefore2 = $this->points;
        if ($winner == 1) {
            //original poster wins
            try {
                $user_points->AddToAttribute('points', $newpoints);
            } catch (Exception $e) {
            }

            BattlePass::addExp($user_points->id, 100);

            $moneyafter1 = $user_points->points;
            $moneyafter2 = $this->points;
            $notifys = $this->username . ' took your 5050 bet of ' . number_format($amount / 2) . ' points and you won ' . number_format($amount) . ' points';
            $notifyMsg = $this->formattedname . ' took your 5050 bet of ' . number_format($amount / 2) . ' points and you won ' . number_format($amount) . ' points';
            $user_points->Notify($notifyMsg, COM_BET);
            DBi::$conn->query('INSERT INTO `5050logs` SET `useridput`="' . $user_points->id . '", `useridtake`="' . $this->id . '", `amount`="' . ($amount / 2) . '", `type`="points", `whowon`="' . $user_points->id . '", `moneybefore1`="' . $moneybefore1 . '", `moneyafter1`="' . $moneyafter1 . '", `moneybefore2`="' . $moneybefore2 . '", `moneyafter2`="' . $moneyafter2 . '", `time`="' . time() . '"');
            throw new FailedResult(GAME5050_YOU_LOST);
        }
        //the person who accepted the bid won
        try {
            $this->AddToAttribute('points', $newpoints);

        } catch (Exception $e) {
            $this->Notify(sprintf(MAXPOINTS_USER_NOTIFY_1, MAX_POINTS, MAX_POINTS), COM_ERROR);
        }

        BattlePass::addExp($this->id, 100);

        $moneyafter1 = $user_points->points;
        $moneyafter2 = $this->points;
        $notifys = $this->username . ' took your 5050 bet of ' . number_format($amount / 2) . ' points and you lost!';
        $notifyMsg = $this->formattedname . ' took your 5050 bet of ' . number_format($amount / 2) . ' points and you lost!';
        $user_points->Notify($notifyMsg, COM_BET);

        DBi::$conn->query('INSERT INTO `5050logs` SET `useridput`="' . $user_points->id . '", `useridtake`="' . $this->id . '", `amount`="' . ($amount / 2) . '", `type`="points", `whowon`="' . $this->id . '", `moneybefore1`="' . $moneybefore1 . '", `moneyafter1`="' . $moneyafter1 . '", `moneybefore2`="' . $moneybefore2 . '", `moneyafter2`="' . $moneyafter2 . '", `time`="' . time() . '"');
        throw new SuccessResult(GAME5050_YOU_WON);
    }

    public function OpenBankAccount()
    {
        $bankAccountPrice = 5000;
        if ($this->HasBankAccount() === true) {
            throw new SoftException(BANK_CANT_MULTI_ACT);
        } elseif ($this->money < $bankAccountPrice) {
            throw new FailedResult(sprintf(BANK_NOT_MONEY_TO_OPEN, number_format($bankAccountPrice)));
        }
        if (!$this->RemoveFromAttribute('money', $bankAccountPrice)) {
            throw new FailedResult(sprintf(BANK_NOT_MONEY_TO_OPEN, number_format($bankAccountPrice)));
        }

        return $this->SetAttribute('whichbank', 1);
    }

    public function HasBankAccount()
    {
        return $this->whichbank > 0;
    }

    public function CloseBankAccount()
    {
        if ($this->HasBankAccount() === false) {
            throw new SoftException(BANK_NOT_HAVT_ACT);
        }
        $this->WithdrawAllMoney();
        DBi::$conn->query('UPDATE `grpgusers` SET `whichbank` = \'0\' WHERE `id`=' . $this->id);
        $this->whichbank = 0;
        throw new SuccessResult(BANK_ACT_CLOSED);
    }

    public function WithdrawAllMoney()
    {
        return $this->WithdrawMoney($this->bank);
    }

    public function WithdrawMoney($amount = 0)
    {
        if ($this->HasBankAccount() === false) {
            throw new SoftException(BANK_WITHDRA_NO_ACT);
        } elseif (!is_numeric($amount) || $amount <= 0) {
            throw new SoftException(BANK_NOT_VALID_AMT);
        } elseif ($amount > $this->bank) {
            throw new FailedResult(sprintf(BANK_WITHDRA_NOT_ENOUGHT_MONEY, number_format($amount)));
        }
        $amount = (int) $amount;

        if (!$this->RemoveFromAttribute('bank', $amount)) {
            throw new FailedResult(sprintf(BANK_WITHDRA_NOT_ENOUGHT_MONEY, number_format($amount)));
        }

        BankLog::create($this, 'withdraw', $amount);

        return $this->AddToAttribute('money', $amount);
    }

    public function DepositAllMoney($tries = 1)
    {
        $tried = 1;
        while ($tries--) {
            try {
                $this->DepositMoney($this->money);

                return $tried;
            } catch (ConcurrencyException $c) {
                $this->SyncFields(['money', 'bank']);
            }
            ++$tried;
        }
        throw new FailedResult(BANK_DEPOSITE_FAILED_2);
    }

    public function DepositMoney($amount)
    {
        $maxBank = User::sGetBankLimit($this->id);
        if ($this->HasBankAccount() === false) {
            throw new SoftException(BANK_NO_ACT);
        } elseif ($amount <= 0) {
            throw new SoftException(BANK_NOT_VALID_AMT);
        } elseif ($amount > $this->money) {
            throw new FailedResult(sprintf(BANK_NOT_ENOUGH_AMT, number_format($amount)));
        } elseif (round(($this->bank + $amount) * 0.995) > $maxBank) {
            throw new FailedResult(sprintf(BANK_DEPOSITE_FAILED_MAX_AMT, number_format($maxBank)));
        }
        $amount = number_format($amount, 0, '.', '');
        $newamount = round($amount * 0.995);
        DBi::$conn->query("UPDATE grpgusers SET bank = bank + " . $newamount . ", money = money - $amount WHERE id = {$this->id}");

        //if (!$this->RemoveFromAttribute('money', $amount)) {
        //  throw new ConcurrencyException(BANK_DEPOSITE_FAILED_1, true);
        //}
        //$this->AddToAttribute('bank', (round($amount * 0.995)));

        BankLog::create($this, 'deposit', (round($amount * 0.995)));
    }

    /*
     * Retreive the BankLogs for the User
     *
     * @param integer $limit = 10
     */
    public function getBankLogs(int $limit = 10)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, user_id, date_time, type, amount, balance')
            ->from('bank_log')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->orderBy('date_time', 'DESC')
            ->setMaxResults($limit)
        ;

        return $queryBuilder->execute()->fetchAll();
    }

    public function RemoveShares($stock, $quantity = '1')
    {
        $ownedShares = $this->GetShareQuantity($stock);

        $quantity = $ownedShares - $quantity;
        if ($quantity > 0) {
            DBi::$conn->query('UPDATE `shares` SET `amount` = \'' . $quantity . '\' WHERE `userid`=\'' . $this->id . '\' AND `companyid`=\'' . $stock . '\'');
        } else {
            DBi::$conn->query('DELETE FROM `shares` WHERE `userid`=\'' . $this->id . '\' AND `companyid`=\'' . $stock . '\'');
        }
    }

    public function GetShareQuantity($stock)
    {
        $res = DBi::$conn->query('SELECT `amount` FROM `shares` WHERE `userid`=\'' . $this->id . '\' AND `companyid`=\'' . $stock . '\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $stock = mysqli_fetch_object($res);

        return $stock->amount;
    }

    public function GetTotalShareQuantity()
    {
        $res = DBi::$conn->query('SELECT sum(amount) as total FROM `shares` WHERE `userid`=' . $this->id);
        $arr = mysqli_fetch_array($res);

        return $arr['total'];
    }
    public function GetTotalCoinQuantity()
    {
        $res = DBi::$conn->query('SELECT sum(amount) as total FROM `coins` WHERE `userid`=' . $this->id);
        $arr = mysqli_fetch_array($res);

        return $arr['total'];
    }
    public function GetCoinQuantity($stock)
    {
        $res = DBi::$conn->query('SELECT `amount` FROM `coins` WHERE `userid`=\'' . $this->id . '\' AND `companyid`=\'' . $stock . '\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $stock = mysqli_fetch_object($res);

        return $stock->amount;
    }

    public function PlantLand($city, $amount)
    {
        $newamont = DBi::$conn->query('SELECT sum(amount) as total FROM growing where userid = ' . $this->id);
        if (mysqli_num_rows($newamont) > 0) {
            $fe = mysqli_fetch_assoc($newamont);
            $am = ($amount + $fe['total']);
            if ($am > 7000) {
                throw new SoftException('You can only plant on 7000 acres at a time.');

            }

        }
        if ($amount < 1) {
            throw new SoftException(FIELD_VALID_AMT_LAND);
        } elseif (($amount * 100) > $this->potseeds) {
            throw new FailedResult(FIELD_NOT_ENOUGH_MARIJUANA);
        } elseif ($amount > $this->GetLandQuantity($city->id)) {
            throw new FailedResult(FIELD_NOT_ENOUGH_LAND);
        }
        /*else if ($city->levelreq > $this->level)
            throw new FailedResult(FIELD_NOT_REQ_LVL);*/

        GrowingLand::Add($this->id, $city->id, $amount, 'pot');
        $this->RemoveFromAttribute('potseeds', $amount * 100);
        $this->RemoveLand($city->id, $amount);
    }
    public function TotalGetLandQuantity()
    {
        $res = DBi::$conn->query('SELECT sum(`amount`) as total FROM `land` WHERE `userid`=\'' . $this->id . '\'');
        if ($res->num_rows == 0) {
            return 0;
        }
        $land = mysqli_fetch_object($res);
        return $land;
    }
    public function GetLandQuantity($city)
    {
        $res = DBi::$conn->query('SELECT `amount` FROM `land` WHERE `userid`=\'' . $this->id . '\' AND `city`=\'' . $city . '\'');
        if ($res->num_rows == 0) {
            return 0;
        }
        $land = mysqli_fetch_object($res);

        return $land->amount;
    }

    public function RemoveLand($city, $quantity = '1')
    {
        $ownedLand = $this->GetLandQuantity($city);

        $quantity = $ownedLand - $quantity;
        if ($quantity > 0) {
            DBi::$conn->query('UPDATE `land` SET `amount` = \'' . $quantity . '\' WHERE `userid`=\'' . $this->id . '\' AND `city`=\'' . $city . '\'');
        } elseif ($quantity == 0) {
            DBi::$conn->query('DELETE FROM `land` WHERE `userid`=\'' . $this->id . '\' AND `city`=\'' . $city . '\'');
        } else {
            return false;
        }
        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }

    public function HarvestAllLand()
    {
        $sumHarvest = 0;
        $harvestableLands = $this->GetAllHarvestableLand();
        foreach ($harvestableLands as $harvestableLand) {
            $land = new GrowingLand($harvestableLand->id);
            $sumHarvest += $land->cropamount;
            $this->AddToAttribute('marijuana', $land->cropamount);
            $this->AddLand($land->cityid, $land->amount);
            $land->Delete();
        }

        return $sumHarvest;
    }

    public function GetAllHarvestableLand()
    {
        return GrowingLand::GetAllHarvestableForUser($this);
    }

    public function AddLand($city, $quantity = 1)
    {
        $ownedLand = $this->GetLandQuantity($city);

        if ($ownedLand == 0) {
            DBi::$conn->query('INSERT INTO `land` (`city`, `userid`, `amount`) VALUES (\'' . $city . '\', \'' . $this->id . '\', \'' . $quantity . '\')');
        } else {
            $quantity = $quantity + $ownedLand;
            DBi::$conn->query('UPDATE `land` SET `amount` = \'' . $quantity . '\' WHERE `userid`=\'' . $this->id . '\' AND `city`=\'' . $city . '\'');
        }
    }

    public function HarvestLand($lid)
    {
        $growingLand = new GrowingLand($lid);
        if ($this->id != $growingLand->userid) {
            throw new SoftException(FIELD_NOT_HARVEST_ANOTHER_LAND);
        } elseif ($growingLand->timedone > time()) {
            throw new SoftException(FIELD_NOT_FINISHED_GROWING);
        }
        GrowingLand::Delete($growingLand->id);
        $this->AddToAttribute('marijuana', $growingLand->cropamount);
        $this->AddLand($growingLand->cityid, $growingLand->amount);
        throw new SuccessResult(sprintf(FIELD_RECEIVED_MARIJUANA, $growingLand->cropamount));
    }

    public function GetAllShares()
    {
        return parent::GetAllById('companyid', ['userid', 'companyid', 'amount'], 'shares', '`userid`=\'' . $this->id . '\'');
    }
    public function GetAllCoin()
    {
        return parent::GetAllById('companyid', ['userid', 'companyid', 'amount'], 'coins', '`userid`=\'' . $this->id . '\'');
    }
    public function GetAllLand()
    {
        return parent::GetAll(['city', 'amount'], 'land', '`userid`=\'' . $this->id . '\'');
    }

    public function GetAllGrowingLand()
    {
        return GrowingLand::GetAllForUser($this->id);
    }

    public function ChangeCity($newCityId)
    {
        if ($newCityId == $this->GetCity()->id) {
            throw new SoftException(sprintf(USER_ALREADY_IN_CITY, $this->GetCity()->name));
        }
        $city = new City($newCityId);
        $cost = $city->levelreq * 200;
        if ($cost < 500) {
            $cost = 500;
        }
        if ($this->level < $city->levelreq) {
            throw new SoftException(USER_NOT_LVL_TO_GO_CITY);
        }
        if ($this->money + $this->bank < $cost) {
            throw new SoftException(USER_NOT_MONEY_TO_GUARDS);
        }
        $free = 0;
        if ($this->HasEquippedSpeed()) {
            $mobile = $this->GetSpeed();
        }
        if (isset($mobile) && $mobile && $mobile->dailypayment > 0) {
            $free = 1;
        }
        if (!$free) {
            if ($this->money >= $cost) {
                $this->RemoveFromAttribute('money', $cost);
            } elseif ($this->bank >= $cost) {
                $this->RemoveFromAttribute('bank', $cost);
            } else {
                $sum = $this->money;
                $this->RemoveFromAttribute('money', $this->money);
                $this->RemoveFromAttribute('bank', $cost - $sum);
            }
        }
        $this->SetAttribute('city', $city->id);
        unset($this->cityobj);
    }

    public function GetCity()
    {
        if (!isset($this->cityobj) || $this->cityobj === null) {
            $this->cityobj = new City($this->city);
        }

        return $this->cityobj;
    }

    public function GetAllNotifications($search = [])
    {
        return Event::GetAllForUser($this->id, $search);
    }

    public function DeleteAllNotifications($box = 0)
    {
        return Event::DeleteAllForUser($this->id, $box);
    }

    public function MarkNotificationsRead()
    {
        return Event::MarkAsReadForUser($this->id);
    }

    public function Freeze($reason, $durationHours = 1, $whoUserId = 0)
    {
        $meltTime = 3600 * $durationHours;
        // unban_time contains a timestamp when user should be unbanned
        $meltTime += time();

        $query = 'REPLACE INTO `freezes` (`id`,`whodid`,`reason`, `time`,`melt_time`) VALUES (\'' . $this->id . '\', \'' . $whoUserId . '\'  ,  \'' . $reason . '\',\'' . time() . '\', \'' . $meltTime . '\')';

        DBi::$conn->query($query);
    }

    public function Unfreeze()
    {
        DBi::$conn->query('DELETE FROM `freezes` WHERE `id`=\'' . $this->id . '\'');

        return DBi::$conn->affected_rows;
    }

    public function IsEquippedWeapon($item, $weaponNo = 1)
    {
        if ($weaponNo == 1 && $this->eqweapon == $item->id) {
            return true;
        }

        if ($weaponNo == 2 && $this->eqnweapon == $item->id) {
            return true;
        }

        return false;
    }

    public function IsEquippedArmor($item, $armorNo = 1)
    {
        if ($armorNo == 1 && $this->eqarmor == $item->id) {
            return true;
        }
        if ($armorNo == 2 && $this->eqnarmor == $item->id) {
            return true;
        }

        return false;
    }

    public function IsEquippedSpeed($item, $armorNo = 1)
    {
        if ($armorNo == 1 && $this->eqspeed == $item->id) {
            return true;
        }
        if ($armorNo == 2 && $this->eqnspeed == $item->id) {
            return true;
        }

        return false;
    }

    public function EquipWeapon(Item $weapon, $borrowed)
    {
        if ($this->GetItemQuantity($weapon->id, $borrowed) <= 0) {
            throw new SoftException(sprintf(USER_NOT_HAVE_ITEM, $weapon->itemname));
        } elseif ($weapon->level > $this->level) {
            throw new SoftException(sprintf(USER_NOT_HAVE_LVL_USE_WEAPON, $weapon->level));
        }
        /*if($weapon->type == Item::UPGRADE_ITEM_ARMOR)
            throw new FailedResult(INVENTORY_CANT_USE_WEAPON);*/

        if ($weapon->type == Item::NORMAL_ITEM) {
            $weaponNo = 1;
        } else {
            $weaponNo = 2;
        }

        if ($this->HasEquippedWeapon($weaponNo)) {
            $this->UnequipWeapon($weaponNo);
        }

        if ($weaponNo == 1) {
            $this->RemoveItems($weapon, 1, $borrowed);
            $this->SetAttribute('eqweapon', $weapon->id);
            $this->SetAttribute('eqweaponloan', $borrowed);
            unset($this->weaponobj);
        } elseif ($weaponNo > 1) {
            $this->RemoveItems($weapon, 1, $borrowed);
            $this->SetAttribute('eqnweapon', $weapon->id);
            $this->SetAttribute('eqnweaponloan', $borrowed);
            unset($this->weaponobjs[$weaponNo]);
        }

        return true;
    }

    public function EquipArmor(Item $armor, $borrowed)
    {
        if ($this->GetItemQuantity($armor->id, $borrowed) <= 0) {
            throw new SoftException(sprintf(USER_NOT_HAVE_ITEM, $armor->itemname));
        } elseif ($armor->level > $this->level) {
            throw new SoftException(sprintf(USER_NOT_HAVE_LVL_USE_ARMOR, $armor->level));
        }
        /*if($weapon->type == Item::UPGRADE_ITEM_WEAPON)
            throw new FailedResult(INVENTORY_CANT_USE_ARMON);*/

        if ($armor->type == Item::NORMAL_ITEM) {
            $armorNo = 1;
        } else {
            $armorNo = 2;
        }

        $armortype = $armor->armortype;
        if ($this->hasEquippedArmorForType($armortype)) {
            $this->UnequipArmor($armortype);
        }

        $eqarmorFieldName = 'eqarmor' . $armortype;
        $eqarmorloanFieldName = 'eqarmor' . $armortype . 'loan';
        $armorobjFieldName = 'armorobj' . $armortype;

        if ($armorNo == 1) {
            $this->RemoveItems($armor, 1, $borrowed);
            $this->SetAttribute($eqarmorFieldName, $armor->id);
            $this->SetAttribute($eqarmorloanFieldName, $borrowed);
            unset($this->$armorobjFieldName);
        } else {
            $this->RemoveItems($armor, 1, $borrowed);
            $this->SetAttribute($eqarmorFieldName, $armor->id);
            $this->SetAttribute($eqarmorloanFieldName, $borrowed);
            unset($this->armorobjs[$armorNo]);
        }

        return true;
    }

    public function EquipSpeed(Item $armor, $borrowed)
    {
        if ($this->GetItemQuantity($armor->id, $borrowed) <= 0) {
            throw new SoftException(sprintf(USER_NOT_HAVE_ITEM, $armor->itemname));
        } elseif ($armor->level > $this->level) {
            throw new SoftException(sprintf(USER_NOT_HAVE_LVL_USE_SPEED, $armor->level));
        }
        if ($armor->dailypayment > $this->bank && $armor->dailypayment > $this->money) {
            throw new SoftException(sprintf(USER_NOT_HAVE_ENOUGH_MONEY, $armor->dailypayment));
        }
        /*if($weapon->type == Item::UPGRADE_ITEM_WEAPON)
            throw new FailedResult(INVENTORY_CANT_USE_ARMON);*/

        if ($armor->type == Item::NORMAL_ITEM) {
            $armorNo = 1;
        } else {
            $armorNo = 2;
        }

        if ($this->HasEquippedSpeed($armorNo)) {
            $this->UnequipSpeed($armorNo);
        }

        if ($armorNo == 1) {
            $this->RemoveItems($armor, 1, $borrowed);
            $this->SetAttribute('eqspeed', $armor->id);
            $this->SetAttribute('eqspeedloan', $borrowed);
            unset($this->speedobj);
        } else {
            $this->RemoveItems($armor, 1, $borrowed);
            $this->SetAttribute('eqnspeed', $armor->id);
            $this->SetAttribute('eqnspeedloan', $borrowed);
            unset($this->speedobjs[$armorNo]);
        }
        if ($this->money > $armor->dailypayment) {
            $this->RemoveFromAttribute('money', $armor->dailypayment);
        } else {
            $this->RemoveFromAttribute('bank', $armor->dailypayment);
        }
        $this->SetAttribute('asEqquipedAnSpeedItem', 1);

        return true;
    }

    public function AddHospitalMinutes($minutes)
    {
        $seconds = $minutes * 60;
        $time = time();
        if ($this->hospital > $time) {
            $this->AddToAttribute('hospital', $seconds);
        } else {
            $this->SetAttribute('hospital', $time + $seconds);
        }

        return true;
    }

    public function UseHealthPill()
    {
        if ($this->IsInHospital()) {
            // 76 - Health Pill
            $item = new Item(76);
            $qty = $this->GetItemQuantity(76);
            if ($qty <= 0) {
                throw new FailedResult(USER_NOT_HAVING_DRUG);
            }
            //$usageLimit = $item->GetItemUsageLimitForUser($this->id);
            //if($usageLimit > time())
            //	throw new FailedResult(USER_ALLOWED_HEALTH_PILL_HOUR);
            $val = intval((int) $this->GetMaxHP() / 2);
            $this->AddToAttribute('hp', $val, $this->GetMaxHP());
            $this->ReduceHospitalByMinutes(10);
            $this->RemoveItems($item, 1);
            //$this->UpdateItemUsageLimit(76);
            return true;
        }
        throw new FailedResult(USER_USE_HEALTH_PILL_IN_HOSPITAL);
    }

    public function ReduceHospitalByMinutes($minutes)
    {
        if ($this->hospital == 0) {
            return false;
        }

        $seconds = $minutes * 60;

        $newHospital = $this->hospital - (time() + $seconds);
        $newHospital = $newHospital < 1 ? 0 : $this->hospital - $seconds;

        parent::sUpdate($this->GetDataTable(), ['hospital' => $newHospital], ['id' => $this->id]);
    }
    public function CheckForLinked($item)
    {
        $item = (int) $item;
        $check = DBi::$conn->query("SELECT * FROM items WHERE linked = $item");

        if (mysqli_num_rows($check)) {
            $fetch = mysqli_fetch_assoc($check);
            $checkinv = Inventory::getItemQuantity($this->id, $fetch['id']);
            if ($checkinv > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function checkUsageLimit($item_id)
    {
        $query = DBi::$conn->query("SELECT usage_limit FROM `item_usage` WHERE user_id = " . $this->id . " AND item_id =$item_id");
        $row = mysqli_fetch_assoc($query);
        if ($row) {
            $current_usage = $row['usage_limit'];
        } else {
            $current_usage = 0;
        }
        if ($current_usage > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function removeUsage($item_id)
    {
        $item_id = (int) $item_id;
        $item = new Item($item_id);
        $query = DBi::$conn->query("SELECT usage_limit FROM `item_usage` WHERE user_id = " . $this->id . " AND item_id =$item_id");
        $check = mysqli_fetch_assoc($query);
        if ($check['usage_limit'] > 1) {
            DBi::$conn->query("UPDATE item_usage SET usage_limit = usage_limit -1 WHERE user_id = " . $this->id . " AND item_id = $item_id LIMIT 1");
        } else {
            DBi::$conn->query("DELETE FROM item_usage WHERE user_id = " . $this->id . " AND item_id = $item_id AND usage_limit = 1 LIMIT 1 ");
            $this->RemoveItems($item, 1);
        }
    }

    public function HitItemUageLimit($item_id)
    {

    }

    public function RateUser($rate, $uid)
    {
        /* check for member is in same gang **/
        $rank_on = $this->HasRatedUser($uid);

        if ($rank_on == 1) {
            throw new FailedResult('You have already rated this user today.');
        }

        if ($rate != 1 && $rate != -1) {
            throw new SoftException(USER_INVALID_RATING);
        }
        if ($rank_on != 0) {
            $query = 'UPDATE ' . self::GetDataTable() . ' SET rate = rate - ' . $rank_on . ' WHERE id = ' . $uid;
            DBi::$conn->query($query);
        }
        $query = 'UPDATE ' . self::GetDataTable() . ' SET rate = rate + ' . $rate . ' WHERE id = ' . $uid;
        DBi::$conn->query($query);

        if ($rank_on == 0) {
            $data = [
                'user_id' => $uid,
                'user_rated' => $this->id,
                'rate' => $rate,
                'date_rated' => time(),
            ];
            self::AddRecords($data, 'user_rates');

            return true;
        }
        $query = 'UPDATE user_rates SET rate = ' . $rate . ' WHERE user_id = ' . $uid . ' AND user_rated = ' . $this->id;
        DBi::$conn->query($query);
    }

    public function HasRatedUser($uid)
    {
        /** check whether user has rated for day or not **/
        $query = 'SELECT rate FROM user_rates WHERE user_id = ' . $uid . ' AND user_rated = ' . $this->id . ' AND DATE(FROM_UNIXTIME(date_rated)) = CURDATE()';

        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_object($res);

            return $row->rate;
        }

        return 0;
    }

    public function BuySteroidCocktail($strength = 0, $defense = 0, $speed = 0)
    {
        $cost = $this->level * 1000;

        if (!is_numeric($strength) || !is_numeric($defense) || !is_numeric($speed)) {
            throw new FailedResult('Invalid Input!');
        }
        if ($strength <= 0 && $defense <= 0 && $speed <= 0) {
            throw new FailedResult('Invalid Input!');
        }
        if ($strength > 0) {
            $cstrength = $cost + $strength * ($strength / (int) ($this->strength > 0 ? $this->strength : 1));
        }    //Calculate Strength
        if ($defense > 0) {
            $cdefense = $cost + $defense * ($defense / (int) ($this->defense > 0 ? $this->defense : 1));
        }    //Calculate defense
        if ($speed > 0) {
            $cspeed = $cost + $speed * ($speed / (int) ($this->speed > 0 ? $this->speed : 1));
        }    //Calculate Strength

        $cost = ceil($cstrength + $cdefense + $cspeed);

        if ($this->money < $cost) {
            throw new FailedResult(USER_NOT_MONEY_BUY_STEROID_COCKTAIL);
        }
        if (!$this->RemoveFromAttribute('money', $cost)) {
            throw new FailedResult(USER_NOT_MONEY_BUY_STEROID_COCKTAIL);
        }
        $data = [
            'bonusstrength' => (int) $strength,
            'bonusdefense' => (int) $defense,
            'bonusspeed' => (int) $speed,
            'cocktailsteroid' => time() + 3600,
        ];

        self::sUpdate(self::$dataTable, $data, ['id' => $this->id]);
        Drug::AddEffect($this->id, 'Steroid Cocktail', '60');

        return true;
    }

    public function GetAllAttacks(array $order = ['oby' => 'timestamp', 'sort' => 'DESC'])
    {
        $objs = [];

        if (!isset($order['oby']) || empty($order['oby'])) {
            $order['oby'] = 'timestamp';
        }

        if (!isset($order['sort']) || empty($order['sort'])) {
            $order['sort'] = 'DESC';
        }

        $query = 'SELECT `id`, `timestamp`, `gangid`, `attacker`, `defender`, `winner`, `gangidatt`, `expwon`, `moneywon`, `atkexp`,`status`, if(`attacker` = `winner`, `atkmoney`, -`atkmoney` ) as `atkmoney` FROM `ganglog` WHERE `attacker` = \'' . $this->id . '\' ORDER BY `' . $order['oby'] . '` ' . $order['sort'];

        return self::GetPaginationResults($query);
    }

    public function IsAPokerPlayer()
    {
        $query = 'SELECT `id` FROM poker_stats where user_id=' . $this->id;
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return false;
        }

        return true;
    }

    public function DepositPokerMoney($amount = 0)
    {
        if ($this->HasBankAccount() === false) {
            throw new SoftException(BANK_NO_ACT);
        } elseif (!is_numeric($amount) || $amount <= 0) {
            throw new SoftException(BANK_NOT_VALID_AMT);
        } elseif ($amount > $this->bank) {
            throw new FailedResult(sprintf(BANK_NOT_ENOUGH_AMT, number_format($amount)));
        }
        $amount = (int) $amount;
        if (!$this->RemoveFromAttribute('bank', $amount)) {
            throw new FailedResult(sprintf(BANK_NOT_ENOUGH_AMT, number_format($amount)));
        }
        $query = 'UPDATE poker_stats SET winpot=winpot+' . $amount . ' where user_id=' . $this->id;
        $res = DBi::$conn->query($query);
    }

    public function WithdrawPokerMoney($amount)
    {
        $maxBank = User::sGetBankLimit($this->id);
        $objPokerStat = new PokerStats($this->id);
        $currentWinpot = $objPokerStat->getWinpot();
        if ($this->HasBankAccount() === false) {
            throw new SoftException(BANK_WITHDRA_NO_ACT);
        } elseif ($amount <= 0) {
            throw new SoftException(BANK_NOT_VALID_AMT);
        } elseif ($amount > $currentWinpot) {
            throw new FailedResult(sprintf(BANK_WITHDRA_NOT_ENOUGHT_POKER_MONEY, number_format($amount)));
        } elseif ($this->bank + $amount > $maxBank) {
            throw new FailedResult(sprintf(BANK_DEPOSITE_FAILED_MAX_AMT, number_format($maxBank)));
        }
        $amount = number_format($amount, 0, '.', '');

        $query = 'UPDATE poker_stats SET winpot=winpot-' . $amount . ' where user_id=' . $this->id . ' AND `winpot` >= ' . $amount;
        $res = DBi::$conn->query($query);
        if (DBi::$conn->affected_rows == 0) {
            throw new FailedResult(POKER_MONEY_NOT_ENOUGH);
        }
        $this->AddToAttribute('bank', $amount);

        return true;
    }

    public function AvailableForJointAttack()
    {
        return $this->jointattack == 1;
    }

    public function UseCaffeine()
    {
        if ($this->caffeine >= 3) {
            throw new FailedResult(USER_CAFFEINE_MAX_USED);
        }
        $itemQty = $this->GetItemQuantity(117);

        if ($itemQty <= 0) {
            throw new FailedResult(USER_NOT_HAVING_DRUG);
        }
        $this->RemoveItems(new Item(117), 1);
        $this->AddToAttribute('caffeine', 1);

        if ($this->caffeinetaken < time()) {
            $this->SetAttribute('caffeinetaken', time() + 5400);
        }             //90 min = 5400 sec
        else {
            $this->AddToAttribute('caffeinetaken', 5400);
        }

        throw new SuccessResult(USER_POPPED_CAFFEINE);
    }

    /*
     * Check the UserDailyLogin for the User
     *
     * @return boolean
     */
    public function hasUserDailyLogin()
    {
        $now = new \DateTime();

        $todayLog = UserDailyLogin::GetAll('user_id = "' . $this->id . '" AND login_date = "' . $now->format('Y-m-d') . '"');
        if ($todayLog) {
            return true;
        }

        return false;
    }

    /*
     * Get a active UserJobRoleTask for the User
     *
     * @return mixed
     */
    public function getActiveUserJobRoleTask()
    {
        $query = DBi::$conn->query('SELECT `id` FROM user_job_role_task WHERE user_id=' . $this->id . ' AND is_complete <> 1 LIMIT 1');
        $result = mysqli_fetch_row($query);

        if (isset($result[0])) {
            return UserJobRoleTask::SGet($result[0]);
        }

        return false;
    }

    /*
     * Get the UserJobRoleTasks for the User for the current day
     *
     * @return array
     */
    public function getUserJobRoleTasksForToday()
    {
        $userJobRoleTasksForToday = [];

        $now = new \DateTime();
        $query = DBi::$conn->query('SELECT `id` FROM user_job_role_task WHERE user_id=' . $this->id . ' AND date="' . $now->format('Y-m-d') . '"');
        while ($res = mysqli_fetch_assoc($query)) {
            $userJobRoleTasksForToday[] = UserJobRoleTask::SGet($res['id']);
        }

        return $userJobRoleTasksForToday;
    }

    /*
     * Get the modded stat difference for the User as a string
     *
     * @return string
     */
    public function getModdedStatDifference()
    {
        $moddedStatsParts = [];
        if ($this->GetModdedSpeed() != $this->speed) {
            $speedDifference = $this->GetModdedSpeed() - $this->speed;
            $color = 'negative';
            $modifier = '';
            if ($speedDifference > 0) {
                $modifier = '+';
                $color = 'positive';
            }
            if (round($speedDifference) != 0) {
                $moddedStatsParts[] = '<span class="itemStat ' . $color . '">' . $modifier . number_format(round($speedDifference)) . ' speed</span>';
            }
        }
        if ($this->GetModdedDefense() != $this->defense) {
            $defenseDifference = $this->GetModdedDefense() - $this->defense;
            $color = 'negative';
            $modifier = '';
            if ($defenseDifference > 0) {
                $modifier = '+';
                $color = 'positive';
            }
            if (round($defenseDifference) != 0) {
                $moddedStatsParts[] = '<span class="itemStat ' . $color . '">' . $modifier . number_format(round($defenseDifference)) . ' defense</span>';
            }
        }
        if ($this->GetModdedStrength() != $this->strength) {
            $strengthDifference = $this->GetModdedStrength() - $this->strength;
            $color = 'negative';
            $modifier = '-';
            if ($strengthDifference > 0) {
                $modifier = '+';
                $color = 'positive';
            }
            if (round($strengthDifference) != 0) {
                $moddedStatsParts[] = '<span class="itemStat ' . $color . '">' . $modifier . number_format(round($strengthDifference)) . ' strength</span>';
            }
        }

        $string = '';
        if (count($moddedStatsParts) > 0) {
            $string = join('', $moddedStatsParts);
        }

        return $string;
    }

    public static function SendConfirmation($id)
    {
        $user = UserFactory::getInstance()->getUser($id);
        $sendGridEmail = new \SendGrid\Mail\Mail();
        $sendGridEmail->setTemplateId('d-3e8be136d7ab48d8a156b816b73d8460');
        $sendGridEmail->setFrom('support@generalforces.com', 'elite-forces');
        $sendGridEmail->addTo(
            $user->email,
            $user->username,
            [
                'name' => $user->username,
                'confirmUrl' => getenv('URL') . '/conf.php?code=' . User::SGetValidationCode($id) . '&id=' . $id,
            ]
        );
        $sendGrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $sendGrid->send($sendGridEmail);
    }

    /*
     * Send the forgot password email
     *
     * @param integer $id
     */
    public static function SendForgotPassword(int $id)
    {
        $user = UserFactory::getInstance()->getUser($id);
        $bit = $user->generateResetBitCode();
        $user->SetAttribute('resetbit', $bit);
        # Instantiate the client.
        $mg = Mailgun::create('64558ff4cedecd2010e00792a7f023a8-8845d1b1-4629ea6b');

        // Now, compose and send your message.
// $mg->messages()->send($domain, $params);
        $url = 'https://generalforces.com/forgot_reset.php?token=' . $bit . '&id=' . $id;
        $mg->messages()->send('generalforces.com', [
            'from' => 'General Forces <support@generalforces.com>',
            'to' => $user->email,
            'subject' => 'Password Rest!',
            'text' => 'You have requested a password reset from GeneralForces, if this was not you please ignore this .',
            'template' => 'reset-password',
            'h:X-Mailgun-Variables' => '{"url": "' . $url . '"}'

        ]);
    }
    /**
     * Determine if the user requires to pass captcha within game for protected pages.
     *
     * @return bool
     */
    public function requiresCaptcha()
    {
        return is_null($this->lastCaptcha) || $this->lastCaptcha === 0 || $this->lastCaptcha < time() - 21600;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected static function GetAllByFieldLimited1($dataFields, $dataTable, $field = 'id', $order = 'ASC', $limit = 50)
    {
        $sig50 = time() - 50 * 24 * 60 * 60;
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` where `id` NOT IN (SELECT `id` FROM `bans`) AND lastactive>' . $sig50 . ' AND admin < 1 ORDER BY `' . $field . '` ' . $order . ' LIMIT ' . $limit);
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'username',
            'password',
            'exp',
            'money',
            'bank',
            'whichbank',
            'hp',
            'energy',
            'nerve',
            'workexp',
            'strength',
            'defense',
            'speed',
            'battlewon',
            'battlelost',
            'battlemoney',
            'crimesucceeded',
            'crimefailed',
            'crimemoney',
            'points',
            'rmdays',
            'signuptime',
            'lastactive',
            'awake',
            'email',
            'jail',
            'hospital',
            'hwho',
            'hwhen',
            'hhow',
            'house',
            'gang',
            'quote',
            'avatar',
            'city',
            'admin',
            'searchdowntown',
            'job',
            'cpaearned',
            'awake',
            'email',
            'jail',
            'hospital',
            'hwho',
            'hwhen',
            'hhow',
            'house',
            'gang',
            'quote',
            'avatar',
            'city',
            'admin',
            'searchdowntown',
            'job',
            'cpaearned',
            'ip',
            'hookers',
            'cocaine',
            'genericsteroids',
            'nodoze',
            'eqweapon',
            'eqarmor',
            'eqarmorhead',
            'eqarmorchest',
            'eqarmorlegs',
            'eqarmorboots',
            'eqarmorgloves',
            'potseeds',
            'marijuana',
            'style',
            'profile',
            'first_ip',
            'numLogins',
            'realmoney',
            'level',
            'working',
            'capt',
            'loginname',
            'protection',
            'hwhoID',
            'forum',
            'pms',
            'shouts',
            'shoutTime',
            'jobmoney',
            'mods',
            'id_rank',
            'slots',
            'validC',
            'dicegame',
            'numbersearch',
            'eqweaponloan',
            'eqarmorloan',
            'eqarmorheadloan',
            'eqarmorchestloan',
            'eqarmorlegsloan',
            'eqarmorbootsloan',
            'eqarmorglovesloan',
            'lottery',
            'gprot',
            'numbergprot',
            'soldpoints',
            'resetStatus',
            'securityPoints',
            'securityLevel',
            'rate',
            'bonusstrength',
            'bonusdefense',
            'bonusspeed',
            'cocktailsteroid',
            'gangcrimemoney',
            'pointshop',
            'itemshop',
            'rpshop',
            'loanhouse',
            'jointattack',
            'tutorial',
            'eqnweapon',
            'eqnarmor',
            'eqnweaponloan',
            'playerautostart',
            'eqnarmorloan',
            'musiclevel',
            'caffeine',
            'caffeinetaken',
            'eqspeed',
            'eqspeedloan',
            'eqnspeed',
            'eqnspeedloan',
            'asEqquipedAnSpeedItem',
            'theme',
            'contest_permit',
            'albuns_exp_bonus',
            'albuns_exp_time',
            'gangentrance',
            'expfailatck',
            'invert',
            'jobtime',
            'lastCaptcha',
            'resetBit',
            'lastreadannouncements',
            'credits',
            'image_name',
            'image_temp',
            'name_credits',
            'virus_infected_time',
            'virus_infected_points',
            'attackexp',
            'android-token',
            'int_time',
            'care_time',
            'santa',
            'awake_time',
            'energy_time',
            'npcbeat',
            'tutorial_step',
            'npcbeat2',
            'chocolate',
            'care_packages',
            'freepack',
            'chatview',
            'heist',
            'apple_time',
            'lolly_time',
            'candy_time',
            'gym_level',
            'gym_exp',
            'is_automated_user',
            'view_preference',
            'missions_pref',
            'drone_crates',
            'SkillReset'
        ];
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    private function RenderXP()
    {
        $this->exppercent = ($this->exp == 0) ? 0 : floor((($this->exp) / ($this->maxexp)) * 100);
        $this->formattedexp = number_format($this->exp) . ' / ' . number_format($this->maxexp) . ' [' . $this->exppercent . '%]';
    }
    public function formattedexp()
    {
        $this->formattedexp = number_format($this->exp) . ' / ' . number_format($this->maxexp) . ' [' . $this->exppercent . '%]';
    }
    private function RemoveFromGang()
    {
        if ($this->GetGang()->leader == $this->username) {
            throw new SoftException(USER_CANT_LEAVE_GANG_LEADING);
        }
        Objectives::set($this->id, 'gangentrance', time() - $this->gangentrance);

        $this->removeBorrowedUserEquippeds();

        $borrowedItems = $this->GetAllBorrowedItems();
        foreach ($borrowedItems as $borrowedItem) {
            $item = new Item($borrowedItem->itemid);
            $this->UnborrowItem($item, $borrowedItem->quantity);
            $this->Notify(sprintf(USER_BORROWED_ITEM_RETURNED, $item->itemname), GANG_LEAVE);
        }

        /**If user has purchased houses for gang, simply remove them from gang */
        GangCell::DeleteAllForBuyer($this);
        GangCell::RecoverFromUser($this);

        $this->SetAttribute('id_rank', 0);
        $this->SetAttribute('gang', 0);
        unset($this->gangobj);

        return true;
    }
    public function HasItem(int $id): bool
    {
        $result = DBi::$conn->query('SELECT quantity FROM inventory WHERE userid = ' . $this->id . ' AND itemid = ' . $id);
        if (!mysqli_num_rows($result)) {
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        return (bool) $row['quantity'];
    }
    public function getItemsForCity(int $cityId, $type = null, $armortype = null)
    {
        $sql = "
            SELECT 
              it.id as it_id, 
              inv.borrowed as inv_borrowed
            FROM 
              inventory inv
              LEFT JOIN items it ON inv.itemid = it.id
            WHERE
              inv.userid = " . $this->id . " 
              AND it.prison LIKE '%," . $cityId . "%'
        ";
        if ($type) {
            $sql .= "
                AND
                    it.item_type = '" . $type . "'
            ";
        }
        if ($armortype) {
            $sql .= "
                AND
                    it.armortype = '" . $armortype . "'
            ";
        }
        $sql .= "
            LIMIT 1
        ";
        $result = DBi::$conn->query($sql);
        if (!mysqli_num_rows($result)) {
            return false;
        }
        $row = mysqli_fetch_assoc($result);

        return $row;
    }
    public function HasItems(array $ids, bool $all = true): bool
    {
        if (empty($ids)) {
            return false;
        }
        $idCount = count($ids);
        $result = DBi::$conn->query('SELECT itemid, quantity FROM inventory WHERE userid = ' . $this->id . ' AND quantity > 0 AND itemid IN (' . implode(', ', $ids) . ')');
        if (!mysqli_num_rows($result)) {
            return false;
        }
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row['itemid'];
        }
        if ($all === true) {
            $keys = array_intersect($ids, $items);
            if (count($keys) === $idCount) {
                return true;
            }
        } elseif (in_array($items, $ids, ustrue)) {
            return true;
        }

        return false;
    }

    public function hasUserMiningDroneInUse($type)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('user_mining_drone', 'umd')
            ->leftJoin('umd', 'mining_drone', 'md', 'umd.mining_drone_id = md.id')
            ->where('umd.user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->andWhere('umd.is_in_use > 0')
            ->andWhere('md.type = :type')
            ->setParameter('type', $type)
            ->setMaxResults(1)
        ;
        $userMiningDrone = $queryBuilder->execute()->fetch();

        if ($userMiningDrone) {
            return true;
        } else {
            return false;
        }
    }

    public function isInActiveCityBossFight()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('city_boss_fight', 'cbf')
            ->leftJoin('cbf', 'city_boss_fight_user', 'cbfu', 'cbf.id = cbfu.city_boss_fight_id')
            ->where('cbfu.user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->andWhere('(cbf.is_fight_complete IS NULL OR cbf.is_fight_complete = 0)')
            ->setMaxResults(1)
        ;
        $cityBossFight = $queryBuilder->execute()->fetch();

        if ($cityBossFight) {
            return true;
        } else {
            return false;
        }
    }

    public function getActiveCityBossFight()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('cbf.id')
            ->from('city_boss_fight', 'cbf')
            ->leftJoin('cbf', 'city_boss_fight_user', 'cbfu', 'cbf.id = cbfu.city_boss_fight_id')
            ->where('cbfu.user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->andWhere('(cbf.is_fight_complete IS NULL OR cbf.is_fight_complete = 0)')
            ->setMaxResults(1)
        ;
        $id = $queryBuilder->execute()->fetch();

        if (isset($id['id'])) {
            return new CityBossFight($id['id']);
        }

        return false;
    }

    public function getOrganisedCityBossFight()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('city_boss_fight', 'cbf')
            ->where('cbf.organised_by_user_id = :organised_by_user_id')
            ->setParameter('organised_by_user_id', $this->id)
            ->andWhere('(cbf.is_fight_complete IS NULL OR cbf.is_fight_complete = 0)')
            ->setMaxResults(1)
        ;
        return $queryBuilder->execute()->fetch();
    }

    public function isInCityBossFightCooldown()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('cbf.time_completed')
            ->from('city_boss_fight', 'cbf')
            ->leftJoin('cbf', 'city_boss_fight_user', 'cbfu', 'cbf.id = cbfu.city_boss_fight_id')
            ->where('cbf.organised_by_user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->andWhere('cbf.is_fight_complete = 1')
            ->orderBy('time_completed', 'DESC')
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if (isset($result['time_completed'])) {
            if ($this->hasPerkActivated('ONE_MAN_ARMY_NAME')) {
                $time = time() - 600;
            } else {
                $time = time() - 900;
            }

            if ($result['time_completed'] >= $time) {
                return true;
            }
        }

        return false;
    }

    public function cityBossFightCooldownTime()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('cbf.time_completed')
            ->from('city_boss_fight', 'cbf')
            ->leftJoin('cbf', 'city_boss_fight_user', 'cbfu', 'cbf.id = cbfu.city_boss_fight_id')
            ->where('cbf.organised_by_user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->andWhere('cbf.is_fight_complete = 1')
            ->orderBy('time_completed', 'DESC')
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if (isset($result['time_completed'])) {
            if ($this->hasPerkActivated('ONE_MAN_ARMY_NAME')) {
                $time = time() - 600;
            } else {
                $time = time() - 900;
            }

            return ($result['time_completed'] - $time) / 60;
        }
    }

    public function hasActiveCandyCanes()
    {
        $query = DBi::$conn->query("SELECT * FROM temp_items_use WHERE candy_cane > 0 AND userid = " . $this->id);
        if (mysqli_num_rows($query) > 0) {
            return true;
        }

        return false;
    }

    public function getPastCityBossFights()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('cbf.id')
            ->from('city_boss_fight', 'cbf')
            ->leftJoin('cbf', 'city_boss_fight_user', 'cbfu', 'cbf.id = cbfu.city_boss_fight_id')
            ->where('cbfu.user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->andWhere('cbf.is_fight_complete = 1')
        ;
        $ids = $queryBuilder->execute()->fetchAll();

        $pastCityBossFights = array();
        foreach ($ids as $id) {
            if (isset($id['id'])) {
                $pastCityBossFights[] = new CityBossFight($id['id']);
            }
        }

        return $pastCityBossFights;
    }

    public function getCurrentQuestSeason()
    {
        //
        // Season One
        //
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_quest_season_one')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id'])) {
            $userQuestSeasonOne = new UserQuestSeasonOne($result['id']);

            if (!$userQuestSeasonOne->getIsSeasonComplete()) {
                return new UserQuestSeasonOne($result['id']);
            } else {
                // Check future seasons - Season one is complete
            }
        } else {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('user_quest_season_one')
                ->values(
                    [
                        'user_id' => ':user_id',
                    ]
                )
                ->setParameter('user_id', $this->id)
            ;
            $queryBuilder->execute();

            return $this->getCurrentQuestSeason();
        }

        //
        // Season Two
        //
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_quest_season_two')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id'])) {
            $userQuestSeasonTwo = new UserQuestSeasonTwo($result['id']);

            if (!$userQuestSeasonTwo->getIsSeasonComplete()) {
                return new UserQuestSeasonTwo($result['id']);
            } else {
                // Check future seasons - Season one is complete
            }
        } else {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('user_quest_season_two')
                ->values(
                    [
                        'user_id' => ':user_id',
                    ]
                )
                ->setParameter('user_id', $this->id)
            ;
            $queryBuilder->execute();

            return $this->getCurrentQuestSeason();
        }

        //
        // Season Three
        //
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_quest_season_three')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id'])) {
            $userQuestSeasonThree = new UserQuestSeasonThree($result['id']);

            if (!$userQuestSeasonThree->getIsSeasonComplete()) {
                return new UserQuestSeasonThree($result['id']);
            } else {
                // Check future seasons - Season one is complete
            }
        } else {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('user_quest_season_three')
                ->values(
                    [
                        'user_id' => ':user_id',
                    ]
                )
                ->setParameter('user_id', $this->id)
            ;
            $queryBuilder->execute();

            return $this->getCurrentQuestSeason();
        }

        //
        // Season Four
        //
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_quest_season_four')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id'])) {
            $userQuestSeasonFour = new UserQuestSeasonFour($result['id']);

            if (!$userQuestSeasonFour->getIsSeasonComplete()) {
                return new UserQuestSeasonFour($result['id']);
            } else {
                // Check future seasons - Season one is complete
            }
        } else {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('user_quest_season_four')
                ->values(
                    [
                        'user_id' => ':user_id',
                    ]
                )
                ->setParameter('user_id', $this->id)
            ;
            $queryBuilder->execute();

            return $this->getCurrentQuestSeason();
        }

        //
        // Season Five
        //
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_quest_season_five')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $result = $queryBuilder->execute()->fetch();

        if ($result && isset($result['id'])) {
            $userQuestSeasonFive = new UserQuestSeasonFive($result['id']);

            if (!$userQuestSeasonFive->getIsSeasonComplete()) {
                return new UserQuestSeasonFive($result['id']);
            } else {
                // Check future seasons - Season one is complete
            }
        } else {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('user_quest_season_five')
                ->values(
                    [
                        'user_id' => ':user_id',
                    ]
                )
                ->setParameter('user_id', $this->id)
            ;
            $queryBuilder->execute();

            return $this->getCurrentQuestSeason();
        }

        return null;
    }

    public function randGiveNewYearItem()
    {
        $chance = mt_rand(1, 5);

        if ($chance === 1) {
            $items = array(
                'NEW_YEAR_NAPKINS_NAME',
                'NEW_YEAR_PAPER_CUP_NAME',
                'NEW_YEAR_PAPER_PLATE_NAME',
            );
            $itemName = $items[mt_rand(0, (count($items) - 1))];

            $this->AddItems(Item::GetItemId($itemName), 1);

            $type = false;
            if ($itemName === 'NEW_YEAR_NAPKINS_NAME') {
                $type = 'napkin';
            } else if ($itemName === 'NEW_YEAR_PAPER_CUP_NAME') {
                $type = 'cup';
            } else if ($itemName === 'NEW_YEAR_PAPER_PLATE_NAME') {
                $type = 'plate';
            }

            if ($type) {
                $this->addToNewyearleaderboard($type);
            }

            Event::Add($this->id, 'You found a ' . constant($itemName) . '! You\'ll be able to save this and give it to the Officers for their New Year Party!');
        }
    }

    public function addToNewyearleaderboard($type)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('newyearleaderboard')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $id = $queryBuilder->execute()->fetch();

        if (isset($id['id']) && $id['id']) {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('newyearleaderboard')
            ;
            if ($type === 'napkin') {
                $queryBuilder
                    ->set('napkin_count', 'napkin_count+1')
                ;
            } else if ($type === 'plate') {
                $queryBuilder
                    ->set('paper_plate_count', 'paper_plate_count+1')
                ;
            } else if ($type === 'cup') {
                $queryBuilder
                    ->set('paper_cup_count', 'paper_cup_count+1')
                ;
            }
            $queryBuilder
                ->set('total_count', 'total_count+1')
                ->where('id = :id')
                ->setParameter('id', $id['id'])
                ->execute()
            ;
        } else {
            $values = [
                'user_id' => ':user_id',
                'total_count' => ':count'
            ];
            if ($type === 'napkin') {
                $values['napkin_count'] = ':count';
            } else if ($type === 'plate') {
                $values['paper_plate_count'] = ':count';
            } else if ($type === 'cup') {
                $values['paper_cup_count'] = ':count';
            }



            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('newyearleaderboard')
                ->values($values)
                ->setParameter('user_id', $this->id)
                ->setParameter('count', 1)
            ;
            $queryBuilder->execute();
        }
    }

    public function addToLovepotionleaderboard()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('lovepotion_leaderboard')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $id = $queryBuilder->execute()->fetch();

        if (isset($id['id']) && $id['id']) {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('lovepotion_leaderboard')
                ->set('count', 'count+1')
                ->where('id = :id')
                ->setParameter('id', $id['id'])
                ->execute()
            ;
        } else {
            $values = [
                'user_id' => ':user_id',
                'count' => ':count'
            ];

            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('lovepotion_leaderboard')
                ->values($values)
                ->setParameter('user_id', $this->id)
                ->setParameter('count', 1)
            ;
            $queryBuilder->execute();
        }
    }

    public function performUserQuestAction(string $action, int $quantity)
    {
        $currentSeason = $this->getCurrentQuestSeason();

        if ($currentSeason) {
            if ($currentSeason->getIsSeasonComplete()) {
                return false;
            }

            if ($currentSeason->getIsCurrentMissionComplete()) {
                return true;
            }

            $missionStatistics = $currentSeason->getMissionStatistics();
            $missionString = 'mission_' . $currentSeason->current_mission;

            if (isset($missionStatistics[$missionString])) {
                if (isset($missionStatistics[$missionString]['requirements'][$action]['complete'])) {
                    if ($action === 'boss_fights' || $action === 'interrogation') {
                        if ($quantity == $missionStatistics[$missionString]['requirements'][$action]['required']) {
                            $missionStatistics[$missionString]['requirements'][$action]['complete'] = $quantity;
                        }
                    } else {
                        $missionStatistics[$missionString]['requirements'][$action]['complete'] = $missionStatistics[$missionString]['requirements'][$action]['complete'] + $quantity;
                    }

                    $currentSeason = $currentSeason->setMissionStatisticsSerialised($missionStatistics);
                }
            }

        }

        return false;
    }

    public function getUserPerks()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_perks')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
            ->setMaxResults(1)
        ;
        $id = $queryBuilder->execute()->fetch();

        if (isset($id['id'])) {
            return new UserPerks($id['id']);
        } else {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->insert('user_perks')
                ->values(
                    [
                        'user_id' => ':user_id',
                    ]
                )
                ->setParameter('user_id', $this->id)
            ;
            $queryBuilder->execute();

            return $this->getUserPerks();
        }

        return false;
    }

    public function hasPerkActivated($perkName)
    {
        $userPerks = $this->getUserPerks();

        if ($userPerks) {
            $perkOneType = $userPerks->getPerkOneType();
            if ($perkOneType && $perkOneType->name === $perkName) {
                return true;
            }

            $perkTwoType = $userPerks->getPerkTwoType();
            if ($perkTwoType && $perkTwoType->name === $perkName) {
                return true;
            }

            $perkThreeType = $userPerks->getPerkThreeType();
            if ($perkThreeType && $perkThreeType->name === $perkName) {
                return true;
            }
        }

        return false;
    }

    public function getActiveGangTerritoryZoneBattle()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('gang_territory_zone_battle')
            ->where('
                (strength_defending_user_id = :user_id OR
                defense_defending_user_id = :user_id OR
                speed_defending_user_id = :user_id OR
                strength_attacking_user_id = :user_id OR
                defense_attacking_user_id = :user_id OR
                speed_attacking_user_id = :user_id)
            ')
            ->setParameter('user_id', $this->id)
            ->andWhere('(is_complete IS NULL OR is_complete = 0)')
            ->setMaxResults(1)
        ;
        $gangTerritoryZoneBattle = $queryBuilder->execute()->fetchOne();


        return $gangTerritoryZoneBattle;
    }

    public function getBackAlleyBotUserLevels()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, back_alley_bot_id')
            ->from('back_alley_bot_user_level')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $this->id)
        ;
        $results = $queryBuilder->execute()->fetchAll();

        $backAlleyBotUserLevels = array();
        foreach ($results as $res) {
            $backAlleyBotUserLevels[$res['back_alley_bot_id']] = new BackAlleyBotUserLevel($res['id']);
        }

        return $backAlleyBotUserLevels;
    }

    public function getLastBackAlleyBotLevelBeaten()
    {
        $lastBackAlleyBotLevel = 0;

        foreach ($this->getBackAlleyBotUserLevels() as $backAlleyBotUserLevel) {
            if ($backAlleyBotUserLevel->is_fight_today_complete && $backAlleyBotUserLevel->getBackAlleyBot()->level > $lastBackAlleyBotLevel) {
                $lastBackAlleyBotLevel = $backAlleyBotUserLevel->getBackAlleyBot()->level;
            }
        }

        return $lastBackAlleyBotLevel;
    }

    public function getWarPointsEarnedForMonth()
    {
        $firstDayOfLastMonth = new DateTime('first day of last month');
        $timestamp = $firstDayOfLastMonth->getTimestamp();

        $res = DBi::$conn->query('SELECT sum(earnedWPoints) as total FROM gang_wars_logs WHERE attackingUser = ' . $this->id . ' AND date >= "' . $timestamp . '"');
        $total = mysqli_fetch_array($res);

        return $total['total'];
    }

    public function getWarPointsEarnedForWar($warId)
    {
        $res = DBi::$conn->query('SELECT sum(earnedWPoints) as total FROM gang_wars_logs WHERE attackingUser = ' . $this->id . ' AND  War = ' . $warId . '');
        $total = mysqli_fetch_array($res);

        return $total['total'];
    }

    public function addActivityPoint()
    {
        DBi::$conn->query("UPDATE `grpgusers2` SET `monthly_prize_points` = `monthly_prize_points` + 1 WHERE `id` = " . $this->id);
    }
}
