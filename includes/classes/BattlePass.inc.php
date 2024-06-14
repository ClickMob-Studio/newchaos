<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
final class BattlePass extends BaseObject
{
    public static $idField = 'userid';
    public static $dataTable = 'battlepass';

    public function __construct($id)
    {
        parent::__construct($id);
    }
    static function endDate(){
        $res = DBi::$conn->query('SELECT value FROM server_variables WHERE field ="battlepass_date"');
        $row = mysqli_fetch_assoc($res);

        return $row['value'];
    }
    static function updateTier($id, $name, $ivalue, $itype, $iid, $cost, $exp){
        $id = (int)$id;
        $name = DBi::$conn->real_escape_string($name);
        $ivalue = (int)$ivalue;
        $itype = DBi::$conn->real_escape_string($itype);
        $iid = (int)$iid;
        $cost = (int)$cost;
        $exp = (int) $exp;
        $res = DBi::$conn->query("UPDATE batllepass_tiers SET `name` = '{$name}', `item_value` = '{$ivalue}', `item_type` = '{$itype}', `item_id` = '{$iid}', `paid` = '{$cost}', `maxexp` = {$exp} WHERE `id` = '{$id}'");
        return true;

    }
    static function getLevel($id){
        $sql = DBi::$conn->query("SELECT * FROM battlepass WHERE userid = ".$id);
        $row = mysqli_fetch_assoc($sql);
        return number_format($row['level']);
    }
    static function truncateBattlePass(){
        DBi::$conn->query("TRUNCATE TABLE battlepass");
        DBi::$conn->query("TRUNCATE TABLE batllepass_tiers");
        return true;
    }
    static function addTier($name, $exp, $ivalue, $itype, $iid, $cost){
        $name = DBi::$conn->real_escape_string($name);
        $ivalue = (int)$ivalue;
        $exp = (int) $exp;
        $itype = DBi::$conn->real_escape_string($itype);
        $iid = (int)$iid;
        $cost = (int)$cost;
        $res = DBi::$conn->query("INSERT INTO batllepass_tiers (`name`, `maxexp`, `item_value`, `item_type`, `item_id`, `paid`) VALUES ('{$name}', '{$exp}', '{$ivalue}', '{$itype}', '{$iid}', '{$cost}')");
        return true;
    }
    static function getPrice(){
        $sql = DBi::$conn->query("SELECT * FROM server_variables WHERE field = 'battlepass_price'");
        $row = mysqli_fetch_assoc($sql);
        return $row['value'];
    }
    static function getTier($id){
        $result = DBi::$conn->query('SELECT battlepass.*, batllepass_tiers.* FROM battlepass 
        LEFT JOIN batllepass_tiers ON battlepass.level = batllepass_tiers.id   
         WHERE battlepass.userid = ' . $id);
        $tier = mysqli_fetch_object($result);
        if(mysqli_num_rows($result)){
         return $tier;
        }else{
            DBi::$conn->query("INSERT INTO battlepass (userid) VALUES (".$id.")");
            return false;
        }

    }
    static function isPassActive(){
        //check that the pass is currently active
        $vaildPass = DBi::$conn->query('SELECT value FROM server_variables WHERE field ="battlepass_date"'); // fetch battle pass end date
        $valid = mysqli_fetch_assoc($vaildPass);
        if($valid['value'] < time()){
            return false;
        }else{
            return true;
        }

    }
    static function getLastLevel(){
        $checkLevel = DBi::$conn->query("SELECT id FROM batllepass_tiers ORDER BY id DESC LIMIT 1"); //fetch highest level
        $check = mysqli_fetch_assoc($checkLevel);
        return $check['id'];
    }

    static function buyPass(int $user){
        if(self::getTier($user)){
            DBi::$conn->query("INSERT INTO battlepass (userid) VALUES($user)");
        }

        $currenTier = self::getTier($user);
        if($currenTier->status == 1){
            return false;
        }
        $result = DBi::$conn->query('SELECT * FROM batllepass_tiers WHERE id < '.$currenTier->level.' AND paid = 1' );

        $us = new User($user);
        while($reward = mysqli_fetch_array($result)) {

            if ($reward['item_type'] == 'item' || $reward['item_type'] == '') {
                $item = DBi::$conn->query("SELECT * FROM items WHERE id = " . $reward['item_id']);
                $item = mysqli_fetch_assoc($item);
                //add items to user inventory
                $us = new User($user);
                $us->AddItems($reward['item_id'], $reward['item_value']);
                $us->Notify('You have received ' . $reward['item_value'] . ' x ' . constant($item['itemname']) . ' from your battle pass!');
            } elseif ($reward['item_type'] == 'land') {
                $us = new User($user);
                $us->AddLand(1, $reward['item_value']);
                $us->Notify('You have received ' . $reward['item_value'] . ' acres of land from your battle pass!');
            } elseif ($reward['item_type'] == 'exp') {
                $us = new User($user);
                //exp as 10 percent of current level
                $maxExp = User::GetNeededXPForLevel($us->level);
                $exp = ($maxExp * ($reward['item_value'] / 100)) + 1; //add one to ensure they always level up
                $us->AddToAttribute('exp', $exp);
                $us->Notify('You have received ' . $exp . ' experience from your battle pass!');
            } elseif ($reward['item_type'] == 'book') {

                //check if user has book
                if (!UserBooks::UserHasBook($us->id, $reward['item_id'])) {
                    $book = DBi::$conn->query("SELECT * FROM books WHERE id = " . $reward['item_id']); //find book
                    $bo = mysqli_fetch_assoc($book);
                    $us->Notify('You have levelled up in the battle pass and you gained ' . constant($bo['name']) . ' book');
                    UserBooks::Add($user, $bo['id']); //give user book
                } else {
                    $us->Notify('You have received 200 points from your battle pass!');
                    $us->AddToAttribute('points', 200);
                }
            } else {
                $us = new User($user);
                $us->AddToAttribute($reward['item_type'], $reward['item_value']);
                //get reward posh name from battle_pass_types
                $type = DBi::$conn->query("SELECT * FROM battle_pass_types WHERE `db` = '" . $reward['item_type'] . "'");
                $type = mysqli_fetch_assoc($type);
                if ($reward['item_type'] == 'money') {
                    $rewards = "$" . number_format($reward['item_value']);
                } else {
                    $rewards = $reward['item_value'];
                }
                $us->Notify('You have received ' . $rewards . ' ' . $type['name'] . ' from your battle pass!');
            }
        }

        DBi::$conn->query("UPDATE battlepass SET status = 1 WHERE userid = $user");
    }
    static function addRewards($user){
        $currenTier = self::getTier($user);
        $level = $currenTier->level - 1;
        //get reward for current level
        $result = DBi::$conn->query("SELECT * FROM batllepass_tiers WHERE `id` = $level");
        $reward = mysqli_fetch_assoc($result);
        //if item type is empty or item type is ite
        //check to see if user has the correct pass
         if ($reward['paid'] <= $currenTier->status) {
             if ($reward['item_type'] == 'item' || $reward['item_type'] == '') {
                 $item = DBi::$conn->query("SELECT * FROM items WHERE id = " . $reward['item_id']);
                 $item = mysqli_fetch_assoc($item);
                 //add items to user inventory
                 $us = new User($user);
                 $us->AddItems($reward['item_id'], $reward['item_value']);
                 $us->Notify('You have received ' . $reward['item_value'] . ' x ' . constant($item['itemname']) . ' from your battle pass!');
             } elseif ($reward['item_type'] == 'land') {
                 $us = new User($user);
                 $us->AddLand(1, $reward['item_value']);
                 $us->Notify('You have received ' . $reward['item_value'] . ' acres of land from your battle pass!');
             } elseif ($reward['item_type'] == 'exp') { 
                 $us = new User($user);
                 //exp as 10 percent of current level
                 $maxExp = User::GetNeededXPForLevel($us->level);
                 $exp = $maxExp * ($reward['item_value'] / 100);
                 DBi::$conn->query("UPDATE grpgusers SET exp = exp + ".$exp." WHERE `id` = ".$user);
                 $us->Notify('You have received ' . $exp . ' experience from your battle pass!');
             }elseif ($reward == 'money') {
                 $rewards = "$". number_format($reward['item_value']);
                 $us = new User($user);
                 DBi::$conn->qeury("UPDATE grpgusers SET money = money + ".$reward['item_value']." WHERE `id` = ".$user);
                 $us->Notify('You have received $' . $reward['item_value']);
                
             } elseif ($reward['item_type'] == 'book') {

                 //check if user has book
                 if (!UserBooks::UserHasBook($us->id, $reward['item_id'])) {
                     $book = DBi::$conn->query("SELECT * FROM books WHERE id = " . $reward['item_id']); //find book
                     $bo = mysqli_fetch_assoc($book);
                     $us->Notify('You have levelled up in the battle pass and you gained ' . constant($bo['name']) . ' book');
                     UserBooks::Add($user, $bo['id']); //give user book
                 } else {
                     $us->Notify('You have received 200 points from your battle pass!');
                     $us->AddToAttribute('points', 200);
                 }
             } else {
                 $us = new User($user);
                 $us->AddToAttribute($reward['item_type'], $reward['item_value']);
                 //get reward posh name from battle_pass_types
                 $type = DBi::$conn->query("SELECT * FROM battle_pass_types WHERE `db` = '" . $reward['item_type'] . "'");
                 $type = mysqli_fetch_assoc($type);
                 if($reward['item_type'] == 'money'){
                     $rewards = "$".number_format($reward['item_value']);
                 }else{
                        $rewards = $reward['item_value'];
                 }
                 $us->Notify('You have received ' . $rewards . ' ' . $type['name'] . ' from your battle pass!');
             }
         }

    }
    /*
     * @param int $user pass user id
     * @param int $exp pass exp to add
     *
     */
    static function addExp(int $user, int $exp)
    {
        //return players current tier
        $currenTier = self::getTier($user);
        //check if the pass is active if not exit the function
        if (self::isPassActive() === false) {
            return;
        }
        if($currenTier->level == self::getLastLevel() + 1){
            return;
        }

        $cuser = new User($user);
        if($cuser->GetChristmasGiftTime() > time()) {
            $exp = $exp + ($exp / 100 * 20);
        }

        //set the new exp
        $newExp = $currenTier->exp + $exp;
        //check current level does not exceed the highest pass level
        if (self::getLastLevel() < $currenTier->level) {
            return false;
        }
        $highestLevel = self::getLastLevel() + 1;
        if ($newExp < $currenTier->maxexp && $currenTier->level < $highestLevel) {
            DBi::$conn->query("UPDATE battlepass SET exp = exp + $exp WHERE userid = {$user}");
        } elseif ($newExp >= $currenTier->maxexp) {
            DBi::$conn->query("UPDATE battlepass SET exp = 0, level = level + 1 WHERE userid = {$user}");
            if ($currenTier->level >= self::getLastLevel()) {
                DBi::$conn->query("UPDATE battlepass SET exp = maxexp, level = " . self::getLastLevel() . " WHERE userid = {$user}");
                if ($currenTier->collected == 0) {
                    DBi::$conn->query("UPDATE battlepass SET collected = 1 WHERE userid = {$user}");
                }
            }
            self::addRewards($user);

        }
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
                          'level',
                          'status',
                          'exp',
                          'collectted',
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
