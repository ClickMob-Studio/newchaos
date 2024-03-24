<?php
include"header.php";
genHead("Rob House");
security($_GET['uid']);
$rob = new User($_GET['uid']);
if ($rob->id == $user_class->id)
    diefun("Ass Clown.");
if ($rob->relplayer != 0)
    $robrel = new User($rob->relplayer);
if($robrel->id == 146)
	$robrel->money = 0;
if ($robrel->id == $user_class->id)
    diefun("Ass Clown.");
if ($rob->admin == 1 || $rob->id == 146)
    diefun("You cannot rob an admin.");
if ($rob->house == 0 && $robrel->house == 0)
    diefun("This Yobster does not own a house!");
if ($user_class->energy != $user_class->maxenergy)
    diefun("You need full energy in order to rob a house.");
if ($user_class->money < 10000 && $user_class->bank < 10000)
    diefun("You do not have enough money to rob a house!");
$robrobinfo = explode("|", $rob->robInfo);
if ($robrobinfo[1] >= time() - 43200)
    diefun("This Yobster has already been robbed in the last 12 hours.");
if ($rob->city != $user_class->city)
    diefun("You must be in the same city.");
$which = $user_class->money >= 10000 ? "money" : "bank";
$who = ($rob->relplayer) ? formatName($rob->id) . " and " . formatName($robrel->id) : formatName($rob->id);
$presql = ", energy = 0, $which = $which - 10000";
$user_class->energy = 0;
$user_class->$which -= 10000;
if (rand(1, 100) <= 5 || (($user_class->moddedspeed >= $rob->speed) && ($rob->relplayer == 0 || $user_class->moddedspeed >= $robrel->speed))) {
    $money = $rob->money;
    $awakeloss = rand(15, 45);
    $sec = $robrobinfo[0];
    if ($rob->relplayer) {
        $whoArray = array($rob->id, $robrel->id);
        $money += $robrel->money;
        if ($sec == 0) {
            $robrelrobinfo = explode("|", $robrel->robInfo);
            $sec = $robrelrobinfo[0];
        }
        $you = "you and/or your spouse";
    } else {
        $whoArray = array($rob->id);
        $you = "you";
    }
    if ($sec && rand(1, 100) <= 25)
        lossFunc();
    $whoRobbed = ($sec) ? formatName($user_class->id) : "Someone";
    Send_Event($rob->id, "$whoRobbed just robbed you! They took $money from $you, also $you have been sent to the hospital for 10 minutes, and lost $awakeloss% awake!");
    Send_Event($rob->relplayer, "$whoRobbed just robbed you! They took $money from $you, also $you have been sent to the hospital for 10 minutes, and lost $awakeloss% awake!");
    $postsql = ",robInfo = '$sec|" . time() . "'";
    mysql_query("UPDATE grpgusers SET money = 0, hospital = 600, awake = GREATEST(awake * $awakeloss / 100, 0), hhow = 'robbed'$postsql WHERE id IN(" . implode(",", $whoArray) . ")");
    mysql_query("UPDATE grpgusers SET money = money + $money$presql WHERE id = $user_class->id");
    diefun("You successfully robbed $who. You sent them to the hospital for 10 minutes, stole " . prettynum($money, 1) . " and they lost $awakeloss% awake.");
} else
    lossFunc();
function lossFunc() {
    global $rob, $robrel, $user_class, $sec, $robrobinfo;
    $sec = $robrobinfo[0];
    if ($rob->relplayer) {
        $whoArray = array($rob->id, $robrel->id);
        $money += $robrel->money;
        if ($sec == 0) {
            $robrelrobinfo = explode("|", $robrel->robInfo);
            $sec = $robrelrobinfo[0];
        }
    }
    if($user_class->money >= 10000){
        $presql = ", money = money - 10000";
        $user_class->money -= 10000;
    } elseif($user_class->bank >= 10000) {
        $presql = ",bank = bank - 10000";
        $user_class->bank -= 10000;
    } else 
        $presql = "";
    mysql_query("UPDATE grpgusers SET jail = 420, energy = 0$presql WHERE id = $user_class->id");
    $whoEvent = "You";
    $who = ($sec) ? formatName($user_class->id) : "Someone";
    if ($rob->relplayer) {
        $whoEvent = "You and " . formatName($robrel->id);
        $whoEventR = "You and " . formatName($rob->id);
        Send_Event($robrel->id, "$who just attempted to rob your house, however $whoEventR stopped held them down until the cops came and arrested them!");
    }
    Send_Event($rob->id, "$who just attempted to rob your house, however $whoEvent stopped held them down until the cops came and arrested them!");
    diefun("You failed to rob " . formatName($rob->id) . ". You were put in jail for 7 minutes, you wasted all your energy".(!empty($presql) ? " and lost \$10,000" : "").".");
}
include"footer.php";
?>