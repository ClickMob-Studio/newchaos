<?php
include_once "classes.php";
include_once "database/pdo_class.php";

$db->query("SELECT id, jail FROM grpgusers WHERE jail > 0");
$db->execute();
$rows = $db->fetch_row();

// Generate array of user ids [2, 5, 9]

$rowJailed = array_map(function ($a) {
    return $a['id'];
}, $rows);

// Generate cells available 1-12
$available_cells = range(0, 11);
$jailed = array();
$cells = array();

foreach ($rows as $row) {
    if (in_array($row['id'], $jailed))
        continue;

    $cell = array_rand($available_cells);
    $cells[] = array(
        'id' => $row['id'],
        'username' => str_replace('</a>', '', preg_replace('/<a[^>]*>/', '', formatName($row['id']))),
        'cell' => $cell
    );
    unset($available_cells[$cell]);
}

//$db->__destruct();
$db = null;

?>