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

$uid = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);

$resultArray = [];

if (isset($_GET['search']) && $_GET['search'] != '') {
    $searchTerms = explode(' ', $_GET['search']);
    $searchQueryParts = [];
    $params = [];

    foreach ($searchTerms as $term) {
        $searchQueryParts[] = "`text` LIKE ?";
        $params[] = '%' . $term . '%';
    }
    $searchString = join(' AND ', $searchQueryParts);

    $db->query("SELECT * FROM `events` WHERE `to` = ? AND ($searchString) ORDER BY `timesent` DESC LIMIT $offset, $rowsperpage");
    $db->execute([$uid, ...$params]);
    $res = $db->fetch_row();
    if ($res) {
        $head = '<tr><th><b>Description</b></th><th><b>Recieved</b></th></tr>';
        array_push($resultArray, $head);

        foreach ($res as $row) {
            $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
            $div = "<tr><td width='67%'>" . $text . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete={$row['id']}'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
            array_push($resultArray, $div);
        }
    }
} else {
    $offset = intval($_POST['offset']);
    $rowsperpage = intval($_POST['rowsPerPage']);

    $db->query("SELECT * from `events` WHERE `to` = ? ORDER BY `timesent` DESC LIMIT $offset, $rowsperpage");
    $db->execute([$uid]);
    $res = $db->fetch_row();
    if ($res) {
        $head = '<tr><th><b>Description</b></th><th><b>Recieved</b></th></tr>';
        array_push($resultArray, $head);

        foreach ($res as $row) {
            $text = str_replace('[-_USERID_-]', formatName($row['extra']), $row['text']);
            $div = "<tr><td width='67%'>" . $text . "</td><td width='31%'>" . date("d F Y, g:ia", $row['timesent']) . "</td><td width='2%'><a href='events.php?delete={$row['id']}'><span class='delete'>&nbsp;X&nbsp;</span></a></td></tr>";
            array_push($resultArray, $div);
        }
    }
}

echo json_encode(array('events' => $resultArray));
?>