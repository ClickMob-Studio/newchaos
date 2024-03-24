<?php
include "ajax_header.php";
mysql_select_db('game', mysql_connect('localhost', 'chaoscity_co', '3lrKBlrfMGl2ic14'));

/*if (isset($_POST['term'])) {
    $term = mysql_real_escape_string($_POST['term']);
    $users = mysql_query("SELECT * FROM grpgusers WHERE username LIKE '%$term%'");
    while ($user = mysql_fetch_array($users, MYSQL_ASSOC)) {
        $return_arr[] =  $user['username'];
    }

    echo json_encode($return_arr);

}*/

if (isset($_GET['term'])) {

    $term = mysql_real_escape_string($_GET['term']);

    if (is_numeric($term)) {
        $users = mysql_query("SELECT * FROM grpgusers WHERE id ='$term'");
    } else {
        $users = mysql_query("SELECT * FROM grpgusers WHERE username LIKE '%$term%'");
    }

    $userData = array();
    while ($user = mysql_fetch_array($users, MYSQL_ASSOC)) {
        $userData[] = array(
            "id" => $user['id'],
            "label" => $user['username'],
            "value" => $user['username']
        );
    }

    echo json_encode($userData);

}

?>