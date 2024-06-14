<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KingTasks.
 *
 * @author teixeira
 */
class KingTasks
{
    //put your code here

    public static $REQ_LEVEL = 0;
    public static $REQ_EXP = 1;
    public static $REQ_NMB_ATTACKS = 1;
    public static $REQ_NMB_MUGGS = 2;
    public static $REQ_NMB_CRIMES = 3;
    public static $REQ_NMB_CONCERTS = 4;
    public static $REQ_REPUTATION = 5;
    public static $REQ_MIN_HOUSE = 6;
    public static $REQ_NMB_BUSTS = 7;
    public static $REQ_HIT_CONTRACTS = 8;
    public static $REQ_HORSE_AMMOUNT = 9;
    public static $REQ_STRENGTH = 10;
    public static $REQ_SPEED = 11;
    public static $REQ_DEFENSE = 12;

    public static function MESSAGES()
    {
        return [
            self::$REQ_LEVEL => ['Fail' => 'You need to be at least level %s.', 'Ok' => 'Your level is high enough.'],
            self::$REQ_EXP => ['Fail' => 'You need at least %s of experience.', 'Ok' => 'You are experienced enough.'],
            self::$REQ_NMB_ATTACKS => ['Fail' => 'You need to have won at least %s fights.', 'Ok' => 'You have won enough fights.'],
            self::$REQ_NMB_MUGGS => ['Fail' => 'You need to have done at least %s mugs.', 'Ok' => 'You have done enough mugs.'],
            self::$REQ_NMB_CRIMES => ['Fail' => 'You need to have complete at least %s missions with success.', 'Ok' => 'You have completed enough missions.'],
            self::$REQ_NMB_CONCERTS => ['Fail' => 'You need to have taken part in at %s operations.', 'Ok' => 'You have completed enough operations.'],
            self::$REQ_REPUTATION => ['Fail' => 'Your operations level must be at least %s.', 'Ok' => 'Your operation level is high enough.'],
            self::$REQ_MIN_HOUSE => ['Fail' => 'You must live in your own %s or someplace better.', 'Ok' => 'Your house is good enough.'],
            self::$REQ_NMB_BUSTS => ['Fail' => 'You need to have done at least %s busts.', 'Ok' => 'You have busted enough people out of jail.'],
            self::$REQ_HIT_CONTRACTS => ['Fail' => 'You need to have completed at least %s hitman contracts.', 'Ok' => 'You are a true hitman.'],
            self::$REQ_HORSE_AMMOUNT => ['Fail' => 'You need to have betted at least $%s at the races.', 'Ok' => 'You are a true gambler.'],
            self::$REQ_STRENGTH => ['Fail' => 'You need at least %s of strength.', 'Ok' => 'You are strong enough.'],
            self::$REQ_SPEED => ['Fail' => 'You need at least %s of speed.', 'Ok' => 'You are fast enough.'],
            self::$REQ_DEFENSE => ['Fail' => 'You need at least %s of defense.', 'Ok' => 'You can take care of yourself.'],
        ];
    }

    public static function KingNeeds($king_id, User $user)
    {
        $needs = [
            '1' => [['id' => self::$REQ_NMB_CRIMES, 'value' => 75], ['id' => self::$REQ_MIN_HOUSE, 'value' => 21]],
            '2' => [['id' => self::$REQ_NMB_MUGGS, 'value' => 75], ['id' => self::$REQ_MIN_HOUSE, 'value' => 22]],
            '3' => [['id' => self::$REQ_NMB_ATTACKS, 'value' => 75], ['id' => self::$REQ_MIN_HOUSE, 'value' => 23]],
            '4' => [['id' => self::$REQ_NMB_BUSTS, 'value' => 75], ['id' => self::$REQ_MIN_HOUSE, 'value' => 24], ['id' => self::$REQ_REPUTATION, 'value' => 150]],
            '5' => [['id' => self::$REQ_NMB_CRIMES, 'value' => 375], ['id' => self::$REQ_MIN_HOUSE, 'value' => 25], ['id' => self::$REQ_REPUTATION, 'value' => 200]],
            '6' => [['id' => self::$REQ_NMB_MUGGS, 'value' => 225], ['id' => self::$REQ_MIN_HOUSE, 'value' => 26], ['id' => self::$REQ_REPUTATION, 'value' => 250]],
            '7' => [['id' => self::$REQ_NMB_ATTACKS, 'value' => 175], ['id' => self::$REQ_MIN_HOUSE, 'value' => 27], ['id' => self::$REQ_REPUTATION, 'value' => 300]],
            '8' => [['id' => self::$REQ_NMB_BUSTS, 'value' => 150], ['id' => self::$REQ_MIN_HOUSE, 'value' => 28], ['id' => self::$REQ_REPUTATION, 'value' => 350]],
            '9' => [['id' => self::$REQ_NMB_CRIMES, 'value' => 675], ['id' => self::$REQ_MIN_HOUSE, 'value' => 29], ['id' => self::$REQ_REPUTATION, 'value' => 400]],
            '10' => [['id' => self::$REQ_NMB_MUGGS, 'value' => 375], ['id' => self::$REQ_MIN_HOUSE, 'value' => 30], ['id' => self::$REQ_REPUTATION, 'value' => 450]],
            '11' => [['id' => self::$REQ_NMB_ATTACKS, 'value' => 275], ['id' => self::$REQ_MIN_HOUSE, 'value' => 31], ['id' => self::$REQ_REPUTATION, 'value' => 500]],
            '12' => [['id' => self::$REQ_NMB_BUSTS, 'value' => 225], ['id' => self::$REQ_MIN_HOUSE, 'value' => 32], ['id' => self::$REQ_REPUTATION, 'value' => 550]],
            '13' => [['id' => self::$REQ_NMB_CRIMES, 'value' => 975], ['id' => self::$REQ_MIN_HOUSE, 'value' => 33], ['id' => self::$REQ_REPUTATION, 'value' => 600], ['id' => self::$REQ_HIT_CONTRACTS, 'value' => 75]],
            '14' => [['id' => self::$REQ_NMB_MUGGS, 'value' => 525], ['id' => self::$REQ_MIN_HOUSE, 'value' => 34], ['id' => self::$REQ_REPUTATION, 'value' => 650], ['id' => self::$REQ_HIT_CONTRACTS, 'value' => 100]],
            '15' => [['id' => self::$REQ_NMB_ATTACKS, 'value' => 375], ['id' => self::$REQ_MIN_HOUSE, 'value' => 35], ['id' => self::$REQ_REPUTATION, 'value' => 700], ['id' => self::$REQ_HIT_CONTRACTS, 'value' => 125]],
            '16' => [['id' => self::$REQ_NMB_BUSTS, 'value' => 300], ['id' => self::$REQ_MIN_HOUSE, 'value' => 36], ['id' => self::$REQ_REPUTATION, 'value' => 750],  ['id' => self::$REQ_HIT_CONTRACTS, 'value' => 150]],
        ];
        $requirments = $needs[$king_id];
        $messages = self::MESSAGES();
        $message = [];
        $requirments = self::CheckRequirments($requirments, $user);
        for ($i = 0; $i < count($requirments); ++$i) {
            switch ($requirments[$i]['id']) {
                case  self::$REQ_MIN_HOUSE:
                    $house = new House($requirments[$i]['value']);
                    $requirments[$i]['message'] = sprintf($messages[$requirments[$i]['id']][$requirments[$i]['Stat']], $house->name);

                    break;
                default:
                    $requirments[$i]['message'] = sprintf($messages[$requirments[$i]['id']][$requirments[$i]['Stat']], $requirments[$i]['value']);
                    break;
            }
        }

        return $requirments;
    }

    public static function CheckRequirments($requirments, User $user)
    {
        $user2 = null;
        for ($i = 0; $i < count($requirments); ++$i) {
            $req = &$requirments[$i];
            $req['Stat'] = 'Fail';

            switch ($req['id']) {
                case self::$REQ_LEVEL:
                    if ($user->level >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case self::$REQ_EXP:
                    if ($user->exp >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case self::$REQ_NMB_ATTACKS:
                    if ($user->battlewon >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case self::$REQ_NMB_MUGGS:
                    if ($user2 == null) {
                        $user2 = SUserFactory::getInstance()->getUser($user->id);
                    }
                    if ($user2->number_of_muggs >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case self::$REQ_NMB_CRIMES:
                    if ($user->crimesucceeded >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;

                case self::$REQ_NMB_CONCERTS:
                    if ($user2 == null) {
                        $user2 = SUserFactory::getInstance()->getUser($user->id);
                    }
                    if ($user2->busts >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case self::$REQ_REPUTATION:
                    if ($user2 == null) {
                        $user2 = SUserFactory::getInstance()->getUser($user->id);
                    }
                    if ($user2->ConcertLevel >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case self::$REQ_MIN_HOUSE:
                    if ($req['value'] == 11) {
                        if ($user->house >= 9) {
                            $req['Stat'] = 'Ok';
                        }
                    } else {
                        if ($user->house == 11 && $req['value'] > 9) {
                            break;
                        }

                        if ($user->house >= $req['value']) {
                            $req['Stat'] = 'Ok';
                        }
                    }
                    break;
                case self::$REQ_NMB_BUSTS:
                    if ($user2 == null) {
                        $user2 = SUserFactory::getInstance()->getUser($user->id);
                    }
                    if ($user2->busts >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case  self::$REQ_HIT_CONTRACTS:
                    $sql = 'select count(id) as total from ' . HitList::$dataTable . ' where status=' . HitList::COMPLETED . ' and provider=' . $user->id;
                    $rs = DBi::$conn->query($sql);
                    $row = mysqli_fetch_object($rs);
                    if ($row->total >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case  self::$REQ_HORSE_AMMOUNT:
                    $sql = 'select sum(value) as total from horsebets where user_id=' . $user->id;
                    $rs = DBi::$conn->query($sql);
                    $row = mysqli_fetch_object($rs);
                    if ($row->total >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case  self::$REQ_STRENGTH:
                    if ($user->strength >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case  self::$REQ_SPEED:
                    if ($user->speed >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
                case  self::$REQ_DEFENSE:
                    if ($user->defense >= $req['value']) {
                        $req['Stat'] = 'Ok';
                    }
                    break;
            }
        }

        return $requirments;
    }
}
