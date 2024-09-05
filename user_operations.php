<?php
include 'header.php';

$db->query("SELECT * FROM `operations`");
$db->execute();
$operations = $db->fetch_row();

$indexedOperations = array();
foreach ($operations as $operation) {
    if (!isset($indexedOperations[$operation['category']])) {
        $indexedOperations[$operation['category']] = array();
    }
    $indexedOperations[$operation['category']][$operation['id']] = $operation;
}
print_r($indexedOperations); exit;

$db->query("SELECT * FROM `user_operations` WHERE `user_id` = ? AND (`is_complete` = 1 OR `is_skipped` = 1) ORDER BY `id` DESC LIMIT 1");
$db->execute(array($user_class->id));
$lastUserOperation = $db->fetch_row(true);

if ($lastUserOperation && isset($lastUserOperation['id'])) {
    $nextUserOperation = $lastUserOperation['id'] + 1;
} else {
    $nextUserOperation = 1;
}

if (isset($indexedOperations[$nextUserOperation])) {
    $currentOperation = $indexedOperations[$nextUserOperation];
} else {
    $currentOperation = null;
}
?>

<h1>Operations</h1>

<div class="row">
    <div class="col-md-12">
        <h1>Crimes</h1><hr />
        <div class="row">
            <div class="col-md-6">

            </div>
        </div>

    </div>
</div>

<?php
require "footer.php";
?>