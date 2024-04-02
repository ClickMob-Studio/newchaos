<?php
include "ajax_header.php";
//mysql_select_db('aa', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));

$method = mysql_real_escape_string($_POST['method']);
$data = mysql_real_escape_string($_POST['data']);

$order = explode(',', $data);
$orderCount = count($order);

$orig = array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,29,30);
$orderCount = count($orig);

for ($i=0; $i < $orderCount; $i++) {
    if (!in_array($orig[$i], $order))
        array_splice( $order, $i, 0, $orig[$i]);
}

$data = implode(',', $order);

$user_class = new User($_SESSION['id']);

if ($method == "update") {
    $db->query("UPDATE grpgusers SET menuorder = ? WHERE id = ?");
    $db->execute(
        array(
            $data,
            $user_class->id
        )
    );
} else if ($method == "reset") {
    $db->query("UPDATE grpgusers SET menuorder = DEFAULT(menuorder) WHERE id = ?");
    $db->execute(array($user_class->id));
}

?>