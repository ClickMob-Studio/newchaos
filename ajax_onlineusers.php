<?php
include_once "classes.php";
include_once "codeparser.php";
include_once "database/pdo_class.php";

mysql_select_db('ml2', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));

$db->query("SELECT id FROM grpgusers WHERE lastactive > unix_timestamp() - 3600 ORDER BY lastactive DESC");
$db->execute();
$rows = $db->fetch_row();

$online = count($rows);

if ($_POST['page'] == 'online') {

    foreach ($rows as $row) {
        $user_online = new User($row['id']);

        $store[] = array(
            'avatar' => $user_online->avatar,
            'formattedname' => $user_online->formattedname,
            'level' => $user_online->level,
            'money' => $user_online->money,
            'id' => $user_online->id,
            'formattedgang' => $user_online->formattedgang,
            'type' => $user_online->type,
            'cityname' => $user_online->cityname,
            'cityid' => $user_online->city,

            'hospital' => $user_online->hospital, // Assuming the User class has hospital property
            'jail' => $user_online->jail,         // Assuming the User class has jail property
            'lastactive' => howlongago($user_online->lastactive)
        );
    }

    echo '
<div class="box_top">
<div class="box_middle">
<div class="pad">
<table id="newtables" style="width:100%;">
            <tr>
                <th colspan="9">Mobsters Online</th>
            </tr>
            <tr>
                <th>Avatar</th>
                <th>id</th>
                <th>Mobster</th>
                <th>Money</th>
                <th>Type</th>
                <th>Gang</th>
                <th>Level</th>
                <th>City</th>
                <th>Last Active</th>
            </tr>';

    foreach ($store as $user) {
        if ($user_class->nightvision > 1) {
            //get city name
            $query = mysql_query("SELECT name FROM cities WHERE id = " . $user['cityid']);
            $result = mysql_fetch_assoc($query);
            $city = $result['name'];
        } else {
            $city = $user['cityname'];
        }

        $formatted_money = '$' . (floor($user['money']) == $user['money'] ? number_format($user['money'], 0, '.', ',') : number_format($user['money'], 2, '.', ','));

        // Determine the CSS class based on hospital and jail status
        $row_class = '';
        if ($user['hospital']) {
            $row_class = 'inHospital'; // CSS class for users in hospital
        } elseif ($user['jail']) {
            $row_class = 'inJail';     // CSS class for users in jail
        }

        echo "<tr class='{$row_class}'>
                <td><img src='{$user['avatar']}' height='50' width='50'></td>
                <td><b><i>{$user['id']}</i></b></td>
                <td>{$user['formattedname']}</td>
                <td>{$formatted_money}</td>
                <td>{$user['type']}</td>
                <td>{$user['formattedgang']}</td>
                <td>{$user['level']}</td>
                <td>{$city}</td>
                <td>{$user['lastactive']}</td>
              </tr>";
    }

    echo '</table></td></tr>';

} else if ($_POST['page'] == 'home') {

    $db->query("SELECT id, lastactive FROM grpgusers ORDER BY lastactive DESC LIMIT 5");
    $rows2 = $db->fetch_row();

    $html = '<table class="mtable" style="margin:auto;width:100%;text-align:left;"><tr><th colspan="3">Last 5 Active Players</th></tr>';
    $i = 1;
    foreach ($rows2 as $row) {
        $html .= '<tr><td>' . $i++ . '.</td><td>' . formatName($row['id']) . '</td><td style="text-align:center;">' . howLongAgo($row['lastactive']) . '</td></tr>';
    }
    $html .= '</table>

</div>

</div>
</div>
';


    echo json_encode(array('count' => $online, 'html' => $html));

}
