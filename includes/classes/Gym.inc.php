<?php

/**
 * discription: This class is used to manage auctions placed by users.
 *
 * @author: Harish<harish282@gmail.com>
 * @name: Auction
 * @package: includes
 * @subpackage: classes
 * @final: Final
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
class Gym extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'auction_log'; // table implemented

    /**
     * Constructor.
     */
    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }

    public static function isCritical($perc = CRITICAL_TRAINING)
    {
        $user = new User($_SESSION['id']);
        if($user->GetSkill(9)){
            $perc = $user->GetSkill(9)->level * 2;
        }
        return (mt_rand(1, 100)) <= $perc ? true : false;
    }

    public static function trainAjax($user_class, $attribute, $attribGain)
    {
        $critical = Gym::isCritical();

        if ($critical) {
            $attribGain *= 2;
            $log = 'Critical: the energy used ' . $_POST['energy'];
        } else {
            $log = 'the energy used ' . $_POST['energy'];
        }

        switch ($attribute) {
            case 'speed':
                $attribGain = (int) floor($attribGain * $user_class->GetStatMultiplier(GangBonus::$BONUS_SPEED));
                break;
            case 'defense':
                $attribGain = (int) floor($attribGain * $user_class->GetStatMultiplier(GangBonus::$BONUS_DEFENSE));
                break;
            case 'strength':
                $attribGain = (int) floor($attribGain * $user_class->GetStatMultiplier(GangBonus::$BONUS_ATTACK));
                break;
        }
        if($user_class->securityLevel > 0){
            $a = ($attribGain /(10 * $user_class->securityLevel));
            $attribGain = $attribGain + $a;
        }

        MonthlyReward::UpdateTrain($attribute, $attribGain, $user_class->id);
        $user_class->AddToAttribute($attribute, $attribGain);
        ActionLogs::Log($user_class->id, $_POST['train'], $log, $attribGain);  //Log the action

        if ($critical) {
            return ['type' => 'Critical', 'attribute' => $attribute, 'value' => $attribGain];
        }

        return ['type' => 'Normal', 'attribute' => $attribute, 'value' => $attribGain];
    }

    public static function train($user_class, $attribute, $attribGain)
    {
        $critical = Gym::isCritical();
        $user2 = SUserFactory::getInstance()->getUser($user_class->id);

        if ($critical) {
            $attribGain *= 2;
            $log = 'Critical: the energy used ' . $_POST['energy'];
        } else {
            $log = 'the energy used ' . $_POST['energy'];
        }

        $levelGain = $attribGain / 100 * ($user_class->gym_level * 5);
        $attribGain = $attribGain + $levelGain;

        $doubleGymEvent = Utility::IsEventRunning('doublegym');
        if ($doubleGymEvent) {
            $attribGain = $attribGain * 2;
        }

        $exp = 3 * mt_rand(5,25);
        $gymExp = $user_class->gym_level + $exp;
        $user_class->AddToAttribute('gym_exp', $gymExp);

        switch ($attribute) {
            case 'speed':
                DailyTasks::recordUserTaskAction(DailyTasks::TRAIN_GYM_SPEED, $user_class, (int) $_POST['energy']);
                $attribGain = (int) floor($attribGain * $user_class->GetStatMultiplier(GangBonus::$BONUS_SPEED));
                if ($user_class->GetGymGreensPillTime() > time()) {
                    $attribGain = $attribGain + (($attribGain / 100) * 15);
                }
                break;
            case 'defense':
                DailyTasks::recordUserTaskAction(DailyTasks::TRAIN_GYM_DEFENSE, $user_class, (int) $_POST['energy']);
                $attribGain = (int) floor($attribGain * $user_class->GetStatMultiplier(GangBonus::$BONUS_DEFENSE));
                if ($user_class->GetGymSuperPillTime() > time()) {
                    $attribGain = $attribGain + (($attribGain / 100) * 15);
                }
                break;
            case 'strength':
                DailyTasks::recordUserTaskAction(DailyTasks::TRAIN_GYM_STRENGTH, $user_class, (int) $_POST['energy']);
                $attribGain = (int) floor($attribGain * $user_class->GetStatMultiplier(GangBonus::$BONUS_ATTACK));
                if ($user_class->GetGymProteinBarTime() > time()) {
                    $attribGain = $attribGain + (($attribGain / 100) * 15);
                }
                break;
        }

        $itemSet = ItemSets::checkHasItemSetEquipped($user_class);
        if ($itemSet && isset($itemSet['set_name']) && $itemSet['set_name'] === 'CUPIDS_SET') {
            $attribGain = $attribGain + (($attribGain / 100) * 10);
        }

        MonthlyReward::UpdateTrain($attribute, $attribGain, $user_class->id);
        $user_class->AddToAttribute($attribute, $attribGain);
        ActionLogs::Log($user_class->id, $_POST['train'], $log, $attribGain);  //Log the action

        $points = $_POST['energy'] / $user_class->GetMaxEnergy();

        if (!$user_class->IsAdmin()) {
            UserBarracksRecord::recordAction(UserBarracksRecord::TRAINING, $user_class->id, (int)$points);
        }
        $user_class->performUserQuestAction('trains', 1);

//        $randItemChance = mt_rand(1,40);
//        if ($randItemChance === 1) {
//            $itemNames = array();
//            $itemNames[] = 'LOVE_POTION_NAME';
//
//            $itemName = $itemNames[mt_rand(0, (count($itemNames) - 1))];
//
//            Event::Add($user_class->id, 'You won a ' . constant($itemName) . ' for constant efforts in the gym!');
//            Event::Add(2, $user_class->id . ' gym won ' . constant($itemName));
//
//            $user_class->AddItems(Item::GetItemId($itemName), 1);
//        }

        if ($critical) {
            throw new CriticalSuccessResult(sprintf(GYM_TRAINED_CRITICAL, $_POST['energy'], number_format($attribGain, 0), $attribute));
        }

        if ($user2->tutorial_v2 == 'gym_2') {
            $user2->SetAttribute('tutorial_v2', 'weapon_1');
        }

        BattlePass::addExp($user_class->id, $_POST['energy'] * 2);

        $activityCheck = $_POST['energy'] / $user_class->GetMaxEnergy();
        if ($activityCheck >= 1) {
            $user_class->addActivityPoint();
        }

        throw new SuccessResult(sprintf(GYM_TRAINED, $_POST['energy'], number_format($attribGain, 0), $attribute));
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
        //return array(
        //	self::$idField,
        //	'auction_id',
        //	'started',
        //	'finished',
        //	'closed'
        //);
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
