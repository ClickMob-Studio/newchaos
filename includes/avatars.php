<?php

// These functions get the players avatars
// If you need the identifying session value to get your own avatars you need "select sessname from poker_players where.....
// You should then be able to pull images from your own db

function get_ava($usr)
{
    /*$usrq =DBi::$conn->query("select avatar from poker_players where username = '".$usr."' ");
$usrr = mysqli_fetch_array($usrq);
$avatar = $usrr['avatar'];
*/
    $usrq =DBi::$conn->query(
        "select id from grpgusers where username = '" . $usr . "' "
    );
    $usrr = mysqli_fetch_array($usrq);
    /*$objuser=UserFactory::getInstance()->getUser($usrr['id']);
     $avatar	=	$objuser->formattedname;*/
    $plainName = Utility::StripTags(User::SGetFormattedName($usrr['id']));
    $intStart = strpos($plainName, ']');
    $strGang = substr($plainName, 0, $intStart);
    if ($strGang != '') {
        $avatar =
            '<font size="-2">' .
            substr($plainName, 0, $intStart) .
            ']<br>' .
            substr($plainName, $intStart + 1, 12) .
            '</font>';
    } else {
        $avatar = '<font size="-2">' . $plainName . '</font>';
    }

    $avatar = str_replace("'", '"', $avatar);

    return $avatar;
}

function display_ava($usr)
{
    $usrq =DBi::$conn->query(
        "select avatar from poker_players where username = '" . $usr . "' "
    );
    $usrr = mysqli_fetch_array($usrq);
    $avatar = '<img src="images/avatars/' . $usrr['avatar'] . '" border="0">';

    return $avatar;
}

function display_ava_profile($usr)
{
    $time = time();
    $usrq =DBi::$conn->query(
        "select avatar from poker_players where username = '" . $usr . "' "
    );
    $usrr = mysqli_fetch_array($usrq);
    $avatar =
        '<img src="images/avatars/' .
        $usrr['avatar'] .
        '?x=' .
        $time .
        '" border="0">';

    return $avatar;
}
