<?php
require "ajax_header.php";

// Initialize the tab tracking array if it doesn't exist
if (!isset($_SESSION['tabs'])) {
    $_SESSION['tabs'] = array();
}

// Get tabId from POST request
$tabId = isset($_POST['tabId']) ? $_POST['tabId'] : '';

// Track the tabId
if (!in_array($tabId, $_SESSION['tabs'])) {
    $_SESSION['tabs'][] = $tabId;
}

if (count($_SESSION['tabs']) > 2) {
    // Prepare SQL Statement
    $tabCount = count($_SESSION['tabs']);
    $db->query("INSERT INTO tab_counts (session_id, tab_count) VALUES (?, ?) ON DUPLICATE KEY UPDATE tab_count = ?");
    $db->execute(array(
        $_SESSION['id'],
        $tabCount,
        $tabCount)
    );
    
}
