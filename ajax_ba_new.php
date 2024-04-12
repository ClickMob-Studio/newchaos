<?php

//header('Content-type: application/json');
session_start();

function error($msg)
{
    $response = array();
    $response['success'] = false;
    $response['error'] = $msg;

    return $response;
}

function success($msg)
{
    $response = array();
    $response['success'] = true;
    $response['message'] = $msg;

    return $response;
}

include "classes.php";
include "database/pdo_class.php";

$m = new Memcache();
$m->addServer('127.0.0.1', 11211, 33);

$user_class = new User($_SESSION['id']);
session_write_close();

if (!isset($_GET['alv'])) {
    echo json_encode(error('Something went wrong.'));
    exit;
}
if ($_GET['alv'] !== 'yes') {
    echo json_encode(error('Something went wrong.'));
    exit;
}

$energyneeded = floor($user_class->maxenergy / 5);
if ($user_class->energy < $energyneeded) {
    refill('e');

    if ($user_class->energy < $energyneeded) {
        echo json_encode(error('You failed to refill your energy in order to search the Back Alley.'));
        exit;
    } else {
        echo json_encode(error('You successfully refilled your energy, you can continue to search the Back Alley.'));
        exit;
    }

}

if ($user_class->energy < $energyneeded) {
    echo json_encode(error("You need at least 20% of your energy to explore the back alley!"));
    exit;
}
if ($user_class->jail > 0) {
    echo json_encode(error("You cannot go in the back alley if you are in Jail."));
    exit;
}
if ($user_class->hospital > 0) {
    echo json_encode(error("You cannot go in the back alley if you are in Hospital."));
    exit;
}


// ATTACKERS
$baAttackerNames = array();
$baAttackerNames[] = "Private Niev";
$baAttackerNames[] = "Private First Class Xali";
$baAttackerNames[] = "Sergeant Beck";
$baAttackerNames[] = "Sergeant First Class Walter";
$baAttackerNames[] = "Captain Jericho";
$baAttackerNames[] = "Colonel Pete";

// SCENARIOS
$baAttackerScenarios = array();

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You slowly walk down the alley and reach a dead end. You turn around to walk back and __ANAME__ is blocking your way, ready to fight!";
$baAttackerScenario['success'] = "You beat them up whilst they pleaded for mercy!";
$baAttackerScenario['fail'] = "They really kicked your butt, spiting in your face as they walk off in triumph.";
$baAttackerScenarios[] = $baAttackerScenario;

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You slowly walk down the alley and reach a dead end. You turn around to walk back and __ANAME__ is blocking your way, ready to fight!";
$baAttackerScenario['success'] = "You punch them into the wall and leave them bleeding on the street.";
$baAttackerScenario['fail'] = "They knock you back down on the alleyway, and instead of getting back up, you lay there as they laugh and walk away.";
$baAttackerScenarios[] = $baAttackerScenario;

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You go with a buddy down the alley and __ANAME__ walks in front of you ready to fight! Your buddy runs away, leaving you there to fight them!";
$baAttackerScenario['success'] = "They run away, chasing your friend down as they have a grudge against them. Well that was rather anti-climatic";
$baAttackerScenario['fail'] = "They knock you out with one blow. Your buddy was smart to run!";
$baAttackerScenarios[] = $baAttackerScenario;

$baAttackerScenario = array();
$baAttackerScenario['start'] = "You meet up with __ANAME__ in the alley to buy some contraband, but it turns out that they're wearing a wire!";
$baAttackerScenario['success'] = "You beat them up, tearing the wire apart! You then run away in order to not get caught!";
$baAttackerScenario['fail'] = "They knock you down, leaving you there for dead. Guess you were not as strong as you thought!";
$baAttackerScenarios[] = $baAttackerScenario;

$attacker = $baAttackerNames[mt_rand(0, (count($baAttackerNames) - 1))];
$scenario = $baAttackerScenarios[mt_rand(0, (count($baAttackerScenarios) - 1))];
$scenario['start'] = str_replace('__ANAME__', $attacker, $scenario['start']);


// 10 Outcomes
// - 10% Loose & Go Hosp
// - 20% Loose & Don't Hosp
// - 20% Win Cash & EXP
// - 20% Win Cash & BA Pill
// - 20% Win Cash & Med Pack
// - 10% Nothing, onto next turn

$outcome = mt_rand(1,100);
if ($outcome <= 10) {
    // 10% Loose & Go Hosp
    $hosp = 120;
    //$result = mysql_query("UPDATE `grpgusers` SET `hwho` = '{$attacker}', `hhow` = 'backalley', `hospital` = '" . $hosp . "' WHERE `id` = '" . $user_class->id . "'");

    $fullResponse = $scenario['start'];
    $fullResponse .= '<br />';
    $fullResponse .= '<br />';
    $fullResponse .= '<span style="color: red; font-weight:bold;">' . $scenario['fail'] . '</span>';
    $fullResponse .= $scenario['fail'];
    $fullResponse .= '<br /><br />';
    $fullResponse .= '<strong>You will need to spend some time in the hospital s!</strong>';
    $fullResponse .= '</span>';
    echo json_encode(success($fullResponse));
} else if ($outcome <= 30) {
    // 20% Loose & Don't Hosp
    $fullResponse = $scenario['start'];
    $fullResponse .= '<br />';
    $fullResponse .= '<br />';
    $fullResponse .= '<span style="color: red; font-weight:bold;">' . $scenario['fail'] . '</span>';
    $fullResponse .= $scenario['fail'];
    $fullResponse .= '</span>';
} else if ($outcome <= 50) {
    // 20% Win Cash & EXP
    $fullResponse = $scenario['start'];
    $fullResponse .= '<br />';
    $fullResponse .= '<br />';
    $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';

    echo json_encode(success($fullResponse));
} else if ($outcome <= 70) {
    // 20% Win Cash & BA Pill
    $fullResponse = $scenario['start'];
    $fullResponse .= '<br />';
    $fullResponse .= '<br />';
    $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';

    echo json_encode(success($fullResponse));
} else if ($outcome <= 90) {
    // 20% Win Cash & Med Pack
    $fullResponse = $scenario['start'];
    $fullResponse .= '<br />';
    $fullResponse .= '<br />';
    $fullResponse .= '<span style="color: green; font-weight:bold;">' . $scenario['success'] . '</span>';

    echo json_encode(success($fullResponse));
} else {
    $fullResponse = $scenario['start'];
    $fullResponse .= '<br />';
    $fullResponse .= '<br />';
    $fullResponse .= '<span style="color: red; font-weight:bold;">' . $scenario['fail'] . '</span>';

    echo json_encode(success($fullResponse));
}
exit;