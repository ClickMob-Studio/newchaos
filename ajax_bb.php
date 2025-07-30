<?php
include "ajax_header.php";

$db->query("SELECT endtime FROM bloodbath ORDER BY endtime DESC LIMIT 1");
$db->execute();
$bb = $db->fetch_row(true);
if (isset($bb['endtime'])) {
    echo howlongtil($bb['endtime']);
}