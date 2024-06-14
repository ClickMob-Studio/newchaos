<?php

/**
 * Descritption: New tutorial and reward system.
 *
 * @author Harish Kumar Chauhan <harish@sohamgreens.com>
 *
 * @version 1.0.0
 */
class NTutorial
{
    const TUT_COUNT = 11;

    private function __construct()
    {
    }

    public static function display(User $user)
    {
        $user2 = SUserFactory::getInstance()->getUser($user->id);
        $step = $user2->ntut + 1;
        if ($step > self::TUT_COUNT) {
            return false;
        }

        try {
            self::check($user);
        } catch (SuccessResult $e) {
            return self::styleMessage(constant('NT_STEP_' . ($step + 1)), $step + 1, 1) . self::styleMessage($e->getMessage(), $step, 2);
        }

        return self::styleMessage(constant('NT_STEP_' . $step), $step, 1);
    }

    public static function styleMessage($message, $step, $type = 1)
    {
        $script = '';

        $style = $step % 4;

        if ($type == 2) {
            $message .= '<br> <a href="#" class="golden" onclick="hide_nt();return false;">' . COM_NEXT . '</a>';

            $script = '<script>

                    function  hide_nt(){

                        $("#talking-ps-2").hide();

                        $("#talking-ps-1").show();

                    }

                    window.setTimeout(function(){$("#talking-ps-1").hide();}, 10);

                </script>

                ';
        }

        return '<div class="golem"> <div id="talking-ps-' . $type . '" class="talking-ps talking-ps-' . $style . '">

                       <div>

                            ' . $message . '

                       </div>

                      </div>' . $script . '</div>';
    }

    public static function check(User $user)
    {
        return true;

        $user2 = SUserFactDBi::$conn->querynce()->getUser($user->id);

        $step = $user2->ntut + 1;

        if ($step > self::TUT_COUNT) {
            return false;
        }

        switch ($step) {
            case 1:
                if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'train' && isset($_POST['energy']) && $_POST['energy'] == $user->energy) {
                    $user->AddToAttribute('strength', 100);
                    $user->AddToAttribute('defense', 100);
                    $user->AddToAttribute('speed', 100);
                    $user2->SetAttribute('ntut', $step);
                    throw new SuccessResult(constant('NT_RW_' . $step));
                }
                break;
            case 2:

                $votedAll = false;

                $result = DBi::$conn->query('SELECT * from `votingsites` WHERE `user`=' . $user->id . ' and `active`=0');

                if (mysqli_num_rows($result) >= 9) {
                    $votedAll = true;
                }

                if ((isset($_REQUEST['on']) && $_REQUEST['on'] >= 1 || $votedAll)
                    && basename($_SERVER['PHP_SELF']) == 'vote.php') {
                    $user->AddPoints(100);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 3:

                if ($_POST['crimeId'] == 1
                    && basename($_SERVER['PHP_SELF']) == 'Missions.php') {
                    $user->AddToAttribute('exp', 150);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 4:

                if (basename($_SERVER['PHP_SELF']) == 'store.php') {
                    $user->AddItems(1);

                    $user->AddItems(39);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 5:

                if (basename($_SERVER['PHP_SELF']) == 'inventory.php'
                    && ($user->eqweapon == 1 || ($_POST['eq'] == 'weapon' && $_POST['id'] == 1))
                    && ($user->eqarmor == 39 || ($_POST['eq'] == 'armor' && $_POST['id'] == 39))) {
                    $user->AddItems(14);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 6:

                if (basename($_SERVER['PHP_SELF']) == 'house.php') {
                    $user2->SetAttribute('ntut', $step);

                    if ($user->HasCell()) {
                        throw new SuccessResult(NT_RW_6_1);
                    }
                    $user->SetAttribute('house', 1);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 7:

                if (basename($_SERVER['PHP_SELF']) == 'inventory.php'
                    && $_POST['use'] == 14) {
                    $user->AddItems(14, 2);

                    $user->AddItems(76);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 8:

                if (basename($_SERVER['PHP_SELF']) == 'downtown.php') {
                    $user->SetAttribute('whichbank', 1);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 9:

                if (basename($_SERVER['PHP_SELF']) == 'bank.php'
                    && $_REQUEST['dep'] == 1) {
                    $user->AddToAttribute('money', 25000);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 10:

                if (basename($_SERVER['PHP_SELF']) == 'profiles.php'
                    && $_REQUEST['id'] != $user->id) {
                    $user->AddPoints(100);

                    $user2->SetAttribute('ntut', $step);

                    throw new SuccessResult(constant('NT_RW_' . $step));
                }

                break;

            case 11:

                if (isset($_REQUEST['fnt']) && $_REQUEST['fnt'] == 1) {
                    $user2->SetAttribute('ntut', $step);
                    Utility::redirect('./');
                }

                break;
        }

        return false;
    }
}
