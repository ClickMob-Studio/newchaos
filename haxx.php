<?php
$loop = array(
    'level' => array(
        10000,
        6000,
        4000
    ),
    'crimes' => array(
        4000,
        2000,
        1000
    ),
    'referrals' => array(
        15000,
        10000,
        5000
    ),
    'attacks won' => array(
        4000,
        2000,
        1000
    ),
    'attacks lost' => array(
        4000,
        2000,
        1000
    ),
    'defend won' => array(
        4000,
        2000,
        1000
    ),
    'defend lost' => array(
        4000,
        2000,
        1000
    ),
    'busts' => array(
        4000,
        2000,
        1000
    )
);
include 'header.php';
if ($user_class->admin != 1 && $user_class->id != 146)
    die();
if (isset($_GET['reward'])) {
    $reward = ((int) $_GET['reward']);
    $type = $_GET['type'];
    $num = ((int) $_GET['num']);
    $id = ((int) $_GET['id']);
    $placing = $_GET['placing'];
    $username = formatName($_GET['id']);
    if ($placing == 1)
        $placing = "1st";
    elseif ($placing == 2)
        $placing = "2nd";
    else
        $placing = "3rd";
    Send_event($id, "You placed $placing in the $type bloodbath with " . prettynum($num) . " $type. <span style='color:green'>[+" . prettynum($reward) . " points]</span>");
    Send_event(146, "$username placed $placing in the $type bloodbath with " . prettynum($num) . " $type. <span style='color:green'>[+" . prettynum($reward) . " points]</span>");
    Send_event(1, "$username placed $placing in the $type bloodbath with " . prettynum($num) . " $type. <span style='color:green'>[+" . prettynum($reward) . " points]</span>");
    perform_query("UPDATE grpgusers SET points = points + ? WHERE id = ?", [$reward, $id]);
    header("location: haxx.php");
    die();
}
$array = mysql_result(mysql_query("SELECT winners FROM bloodbath ORDER BY endtime DESC LIMIT 1,1"), 0, 0);
$array = unserialize($array);
$attribs = array('level', 'crimes', 'referrals', 'attacks won', 'attacks lost', 'defend won', 'defend lost', 'busts');
foreach ($attribs as $att) {
    $array = array_orderby($array, str_replace(' ', '', $att), SORT_DESC, 'userid', SORT_DESC);
    print "
<table id='newtables' style='width:100%;'>
<tr><th colspan='5' style='font-size:1.1em;'>$att</th></tr>
<tr>
<th><b>Rank</b></th>
<th><b>Username</b></th>
<th><b>Level</b></th>
<th><b>Reward</b></th>
<th><b>Payout</b></th>
</tr>
";
    for ($i = 0; $i < 3; $i++)
        print "<tr><td>" . ($i + 1) . "</td><td>" . formatName($array[$i]['userid']) . "</td><td>" . prettynum($array[$i][str_replace(' ', '', $att)]) . " $att</td><td>" . prettynum($loop[$att][$i]) . "</td><td><a href='?id={$array[$i]['userid']}&reward={$loop[$att][$i]}&type=" . str_replace(' ', '', $att) . "&num={$array[$i][str_replace(' ', '', $att)]}&placing=" . ($i + 1) . "'>[Reward Them]</a></td></tr>";
    print "</table>";
}
include 'footer.php';
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
?>