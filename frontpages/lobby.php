<?php 
require('includes/inc_lobby.php');

$addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'page_start'
));

include 'templates/header.php';

echo $addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'html_start'
));

$addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'tabs_left'
));

// Initialize variables
$navHtml = '';
$tabpanelHtml = '';

// Add tab
$opsTheme->addVariable('tab', array(
    'id'   => 'tables',
    'name' => 'All Tables',
    'html' => $opsTheme->viewPart('lobby-gamelist-tabpanel')
));

// Append tabpanel HTML
$tabpanelHtml .= $opsTheme->viewPart('lobby-tabpanel');

$opsTheme->addVariable('lobby', array(
    'tabs'      => $navHtml,
    'tabpanels' => $tabpanelHtml
));

echo $opsTheme->viewPage('lobby');

echo $addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'html_end'
));

include 'templates/footer.php';
?>
