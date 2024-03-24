<?php
include "classes.php";
include "database/pdo_class.php";
$m = new Memcache();
$m->addServer('127.0.0.1', 11212, 33);

$db->query("SELECT id, jail FROM grpgusers WHERE jail > 0");
$db->execute();
$rows = $db->fetch_row();

// Generate array of user ids [2, 5, 9]

$rowJailed = array_map(function($a) {
    return $a['id'];
}, $rows);

$m->set('jail_count', count($rowJailed));

// Generate cells available 1-12
$available_cells = range(0, 11);
$jailed = array();

// Fetch cached data of cells
$cells = $m->get('cells');
if ($cells) {
    $cells = array_values($cells);
    $cells_count = count($cells);
    for ($i=0; $i < $cells_count; $i++) {
        if (!in_array($cells[$i]['id'], $rowJailed))
            unset($cells[$i]);

        $jailed[] = $cells[$i]['id'];
        unset($available_cells[$cells[$i]['cell']]);
    }
} else {
    $cells = array();
    $m->delete('cells');
}

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
$m->set('cells', $cells);

//$db->__destruct();
$db = null;

?>
