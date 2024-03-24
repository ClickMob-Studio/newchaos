<?php
include 'header.php';
if(!$m->get('24hour')){
    $db->query("SELECT id FROM grpgusers WHERE lastactive > unix_timestamp() - 86400 ORDER BY lastactive DESC");
    $db->execute();
    $rows = $db->fetch_row();
    foreach($rows as $row){
            $user_online = new User($row['id']);
            $store[] = array(
                'avatar' => $user_online->avatar,
                'formattedname' => $user_online->formattedname,
                'level' => $user_online->level,
                'cityname' => $user_online->cityname,
                'lastactive' => howlongago($user_online->lastactive)
            );
    }
    $m->set('24hour', $store, 10);
}
echo '
<table id="newtables" style="width:100%;">
    <tr>
        <th colspan="5">Mobsters Online In The Last 24 Hours</th>
    </tr>
    <tr>
        <th>Avatar</th>
        <th>Mobster</th>
        <th>Level</th>
        <th>City</th>
        <th>Last Active</th>
    </tr>
';
$users = $m->get('24hour');
foreach($users as $user)
        echo "
    <tr>
        <td><img src='{$user['avatar']}' height='50' width='50'></td>
        <td>{$user['formattedname']}</td>
        <td>{$user['level']}</td>
        <td>{$user['cityname']}</td>
        <td>{$user['lastactive']}</td>
    </tr>
    ";
echo '</table></td></tr>';
include 'footer.php';
?>