<?php
include_once "dbcon.php";
include_once "database/pdo_class.php";
include_once "classes.php";
include_once "codeparser.php";

if ($user_class->id == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

}
$uid = "'" . $_POST['userId'] . "'";
$resultArray = [];


if (isset($_POST['search']) && $_POST['search'] != '') {

    $searchString = '%' . $_POST['search'] . '%';
    $offset = intval($_POST['offset']);
    $rowsperpage = intval($_POST['rowsPerPage']);
    $res = mysql_query("SELECT * from `events` WHERE `to` = $uid AND `text` like '$searchString' ORDER BY `timesent` DESC LIMIT $offset, $rowsperpage");
    $row = mysql_fetch_array($res);
    if ($res) {
        $head = '<tr><th><b>Description</b></th><th><b>Recieved</b></th></tr>';
        array_push($resultArray, $head);
        while ($row = mysql_fetch_array($res)) {
            $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
            $div = "<tr><td width='67%'>" . $text . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete={$row['id']}'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
            array_push($resultArray, $div);
        }
        echo json_encode(array('events' => $resultArray));
    }
} else {
    $offset = intval($_POST['offset']);
    $rowsperpage = intval($_POST['rowsPerPage']);
    $res = mysql_query("SELECT * from `events` WHERE `to` = $uid ORDER BY `timesent` DESC LIMIT $offset, $rowsperpage");
    if ($res) {
        $head = '<tr><th><b>Description</b></th><th><b>Recieved</b></th></tr>';
        array_push($resultArray, $head);
        while ($row = mysql_fetch_array($res)) {
            $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
            $div = "<tr><td width='67%'>" . $text . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete={$row['id']}'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
            array_push($resultArray, $div);
        }
        echo json_encode(array('events' => $resultArray));
    }
}
?>