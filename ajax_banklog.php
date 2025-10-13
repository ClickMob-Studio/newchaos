<?php
include "ajax_header.php";

// Check if 'limit' is set and not empty, otherwise default to 0
$limit = isset($_POST['limit']) && $_POST['limit'] !== '' ? $_POST['limit'] : 0;

$id = $_SESSION['id'];
$limit = $_POST['limit'];
$format = $_POST['format'];
$show = $_POST['show'];
if ($limit != abs($limit))
    die();
if (!in_array($format, array('us', 'uk')))
    die();
if (!in_array($show, array('all', 'withs', 'deps', 'money', 'points')))
    die();
$db->query("REPLACE INTO banksettings VALUES(?, ?, ?, ?)");
$db->execute(array(
    $id,
    $format,
    $limit,
    $show
));
print banklog($limit, $show, $format);
?>