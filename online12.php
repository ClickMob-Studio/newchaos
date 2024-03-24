<?php
include 'headernew1.php';

?>

<style>
#div1 tr:hover {
    background-color: #4d4653; /* Change this to the color you want */
}
@media only screen and (max-width: 767px) {
 #newtables th:nth-of-type(1),
 #newtables th:nth-of-type(2),
 #newtables th:nth-of-type(5) {
    display: none;
  }
  #newtables td:nth-of-type(1),
  #newtables td:nth-of-type(2),
  #newtables td:nth-of-type(5)
  
  {
    display: none;
  }
}
</style>
<?php
echo '<div id="div1"></div>';

echo '<script>
let isHovering = false;

function get_olu(){
    if (!isHovering) {
        var olu = $.ajax({
            type: "POST",
            url: "ajax_onlineusers.php",
            data: {"page" : "online"},
            async: false
        }).success(function(){
            setTimeout(function(){get_olu();}, 1000);
        }).responseText;

        $("#div1").html(olu);
    } else {
        setTimeout(function(){get_olu();}, 1000);
    }
}

// When the mouse enters a table row, set the flag to true
$(document).on("mouseenter", "#div1 tr", function() {
    isHovering = true;
});

// When the mouse leaves a table row, set the flag to false
$(document).on("mouseleave", "#div1 tr", function() {
    isHovering = false;
});

get_olu();
</script>';

require 'footer.php';
die();

if (!$m->get('24hour')) {
    $db->query("SELECT id FROM grpgusers WHERE lastactive > unix_timestamp() - 3600 ORDER BY lastactive DESC AND id != 174");
    $db->execute();
    $rows = $db->fetch_row();
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
            'lastactive' => howlongago($user_online->lastactive)
        );
    }
    $m->set('24hour', $store, 10);
}
echo '<table id="newtables" style="width:100%;">
        <tr>
            <th colspan="8">Mobsters Online</th>
        </tr>
        <tr>
            <th>Avatar</th>
            <th>id</th>
            <th>Mobster</th>
            <th>Type</th>
            <th>Gang</th>
            <th>Level</th>
            <th>City</th>
            <th>Last Active</th>
        </tr>';

$users = $m->get('24hour');
foreach ($users as $user) {
if($user_class->nightvision > 1){
//get city name
$query = mysql_query("SELECT name FROM cities WHERE id = ".$user['cityid']);
$result = mysql_fetch_assoc($query);
$city = $result['name'];
}else{
$city = $user['cityname'];
}

    echo "<tr>
            <td><img src='{$user['avatar']}' height='50' width='50'></td>
            <td><b><i>{$user['id']}</i></b></td>
            <td>{$user['formattedname']}</td>
            <td>{$user['type']}</td>
            <td>{$user['formattedgang']}</td>
            <td>{$user['level']}</td>
            <td>{$city}</td>
            <td>{$user['lastactive']}</td>
        </tr>";
}

echo '</table></td></tr>';
include 'footer.php';
?>
