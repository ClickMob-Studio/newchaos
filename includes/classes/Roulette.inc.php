<?php

final class Roulette
{
    public $objUser;

    public function __construct($objUser)
    {
        $this->objUser = $objUser;
    }

    public static function resetCount()
    {
        DBi::$conn->query('UPDATE userlimit	SET cnt=24');
    }

    public function remainPlay()
    {
        $rsUser = DBi::$conn->query("SELECT `cnt` FROM userlimit WHERE user_id='" . $this->objUser->id . "'");
        if (mysqli_num_rows($rsUser) == 0) {
            return 24;
        }
        $arrUser = mysqli_fetch_array($rsUser);

        return $arrUser['cnt'];
    }

    public function PlayRoulette($intShots, $intBet)
    {
        if ($this->updateCount() > 0) {
            /*
                1 bullet : starting bet * 1.24 (5/6 chance to win).
                2 bullets: starting bet * 1.6 (2/3 chance to win).
                3 bullets: starting bet * 2.2 (1/2 chance to win).
                4 bullets: starting bet * 3.4 (1/3 chance to win).
                5 bullets: starting bet * 7 (1/6 chance to win).
            */

            if ($intShots == 1) {
                $reward = $intBet * 1.24;
            } elseif ($intShots == 2) {
                $reward = $intBet * 1.55;
            } elseif ($intShots == 3) {
                $reward = $intBet * 2.1;
            } elseif ($intShots == 4) {
                $reward = $intBet * 3.2;
            } elseif ($intShots == 5) {
                $reward = $intBet * 6.5;
            } else {
                throw new SoftException(ROULETTE_WRONG_BULLET_NUM);
            }
            $rand = mt_rand(1, 6);

            if ($rand > $intShots) {
                $this->objUser->AddToAttribute('money', $reward - $intBet);

                $arrValue = [
                                'time' => time(),
                                'moneyReward' => $reward,
                                'user' => $this->objUser->id,
                                'result' => 'Won',
                                ];

                return $reward;
            }

            $this->objUser->RemoveFromAttribute('money', $intBet);
            $this->objUser->SetAttribute('hhow', 'Roulette');
            //Every lost game need to remove 25% of current health to the player.
            if ($this->objUser->hp > 0) {
                $this->objUser->RemoveFromAttribute('hp', ($this->objUser->hp * 25) / 100);
            }
            $this->objUser->AddHospitalMinutes(5);

            $arrValue = [
                                'time' => time(),
                                'moneyReward' => 0,
                                'user' => $this->objUser->id,
                                'result' => 'Failed',
                                ];
            //BaseObject::AddRecords($arrValue,'logs_mugs');
            throw new FailedResult(sprintf(ROULETTE_BULLET_REFLECTED, Utility::CNumberFormat($intBet, true)), 'You Lost!');
        }
    }

    public function updateCount()
    {
        $curDate = date('Y-m-d');
        $strSQL = "SELECT `user_id`, `cnt` FROM `userlimit`
									WHERE 
										`user_id`='" . $this->objUser->id . "'";
        $rsUserCount = DBi::$conn->query($strSQL);
        $arrUser = mysqli_fetch_array($rsUserCount, MYSQLI_ASSOC);
        $intCount = 0;

        if (DBi::$conn -> affected_rows > 0) {
            if ($arrUser['cnt'] > 0) {
                $strSQL = "UPDATE `userlimit`	
										SET `cnt` = `cnt` - 1
										WHERE `user_id` = '" . $this->objUser->id . "'";
                DBi::$conn->query($strSQL);
            } else {
                throw new FailedResult(ROULETTE_PLAYED_MAX);
            }
        } else {
            $strSQL = "INSERT INTO `userlimit`	
									SET `cnt` = 23,
										`user_id` = '" . $this->objUser->id . "'";
            DBi::$conn->query($strSQL);
        }

        return 1;
    }
}
