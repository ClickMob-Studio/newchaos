<?php 
// Include the lobby functions and hooks
require('includes/inc_lobby.php');

// Execute hooks at the start of the page
$addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'page_start'
));

// Include the header template
include 'templates/header.php';

// Execute hooks at the beginning of the HTML
echo $addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'html_start'
));

// Execute hooks for the tabs on the left side
$addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'tabs_left'
));

// Initialize variables for navigation and tab panel HTML
$navHtml = '';
$tabpanelHtml = '';

// Add a tab for the game tables
$opsTheme->addVariable('tab', array(
    'id'   => 'tables',
    'name' => 'All Tables',
    'html' => $opsTheme->viewPart('lobby-gamelist-tabpanel')
));

// Append tab panel HTML for display
$tabpanelHtml .= $opsTheme->viewPart('lobby-tabpanel');

// Add variables to the lobby
$opsTheme->addVariable('lobby', array(
    'tabs'      => $navHtml,
    'tabpanels' => $tabpanelHtml
));

// Display the lobby page content
echo $opsTheme->viewPage('lobby');

// Execute hooks at the end of the HTML
echo $addons->get_hooks(array(), array(
    'page'     => 'lobby.php',
    'location'  => 'html_end'
));

// Include the footer template
include 'templates/footer.php';
?>
