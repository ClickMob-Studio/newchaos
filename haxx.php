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

if ($user_class->admin != 1)
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

$db->query("SELECT winners FROM bloodbath ORDER BY endtime DESC LIMIT 1,1");
$db->execute();
$row = $db->fetch_row(true);

$array = unserialize($row['winners']);

$attribs = ['level', 'crimes', 'referrals', 'attacks won', 'attacks lost', 'defend won', 'defend lost', 'busts'];

foreach ($attribs as $att) {
    $key = str_replace(' ', '', $att); // e.g., "attackswon"

    // Sort array by selected attribute and userid
    $sorted = array_orderby($array, $key, SORT_DESC, 'userid', SORT_DESC);

    echo "<table id='newtables' style='width:100%;'>
    <tr><th colspan='5' style='font-size:1.1em;'>$att</th></tr>
    <tr>
        <th><b>Rank</b></th>
        <th><b>Username</b></th>
        <th><b>Level</b></th>
        <th><b>Reward</b></th>
        <th><b>Payout</b></th>
    </tr>";

    for ($i = 0; $i < 3 && isset($sorted[$i]); $i++) {
        $userid = $sorted[$i]['userid'];
        $statVal = prettynum($sorted[$i][$key]);
        $reward = prettynum($loop[$att][$i]); // Assumed to exist

        echo "<tr>
            <td>" . ($i + 1) . "</td>
            <td>" . formatName($userid) . "</td>
            <td>$statVal $att</td>
            <td>$reward</td>
            <td><a href='?id={$userid}&reward={$loop[$att][$i]}&type={$key}&num={$sorted[$i][$key]}&placing=" . ($i + 1) . "'>[Reward Them]</a></td>
        </tr>";
    }

    echo "</table>";
}

include 'footer.php';

// Helper: Multidimensional sort
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);

    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = [];
            foreach ($data as $key => $row) {
                $tmp[$key] = $row[$field];
            }
            $args[$n] = $tmp;
        }
    }

    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
?>